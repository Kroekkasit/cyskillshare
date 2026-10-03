<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Challenge;
use App\Models\ChallengeFile;
use InvalidArgumentException;
use RuntimeException;

final class ChallengeFileService
{
    public static function storageRoot(): string
    {
        $path = storage_path('challenges');
        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
        return $path;
    }

    /**
     * @param array<string, mixed> $file $_FILES element
     */
    public static function upload(int $challengeId, array $file, int $actorId): ChallengeFile
    {
        ChallengeService::requireManageArena();

        $challenge = Challenge::find($challengeId);
        if ($challenge === null) {
            throw new InvalidArgumentException('Challenge not found.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Upload failed.');
        }

        $max = (int) config('arena.challenge_files.max_bytes', 10485760);
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > $max) {
            throw new InvalidArgumentException('Invalid file size.');
        }

        $original = (string) ($file['name'] ?? 'file');
        $original = basename(str_replace(["\0", '\\'], '', $original));
        $original = preg_replace('/[^\w.\- ()\[\]]+/', '_', $original) ?? 'file';
        $original = mb_substr($original, 0, 180);

        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $blocked = config('arena.challenge_files.blocked_extensions', []);
        $allowed = config('arena.challenge_files.allowed_extensions', []);
        if (!is_array($blocked) || !is_array($allowed)) {
            throw new RuntimeException('Invalid file config.');
        }
        if ($ext === '' || in_array($ext, $blocked, true) || !in_array($ext, $allowed, true)) {
            throw new InvalidArgumentException('File type not allowed.');
        }

        // Reject double extensions like shell.php.jpg
        $nameLower = strtolower($original);
        foreach ($blocked as $bad) {
            if (str_contains($nameLower, '.' . $bad . '.')) {
                throw new InvalidArgumentException('File type not allowed.');
            }
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('Invalid upload.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: 'application/octet-stream';
        $allowedMimes = config('arena.challenge_files.allowed_mimes', []);
        if (!is_array($allowedMimes) || !in_array($mime, $allowedMimes, true)) {
            // Allow octet-stream for pcap/bin-like files with allowed extension
            if (!($mime === 'application/octet-stream' && in_array($ext, ['pcap', 'pcapng', 'bin', 'raw', 'hex'], true))) {
                throw new InvalidArgumentException('MIME type not allowed.');
            }
        }

        $stored = bin2hex(random_bytes(8)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
        $relDir = 'challenge_' . $challengeId;
        $absDir = self::storageRoot() . DIRECTORY_SEPARATOR . $relDir;
        if (!is_dir($absDir) && !mkdir($absDir, 0775, true) && !is_dir($absDir)) {
            throw new RuntimeException('Unable to create storage directory.');
        }

        $absPath = $absDir . DIRECTORY_SEPARATOR . $stored;
        if (!move_uploaded_file($tmp, $absPath)) {
            throw new RuntimeException('Failed to store file.');
        }
        @chmod($absPath, 0640);

        $relPath = $relDir . '/' . $stored;
        Database::execute(
            'INSERT INTO challenge_files
             (challenge_id, original_name, stored_name, storage_path, file_size, mime_type)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$challengeId, $original, $stored, $relPath, $size, $mime]
        );

        $id = (int) Database::lastInsertId();
        ActivityLogService::log($actorId, 'challenge_file_uploaded', 'challenge', $challengeId, [
            'file_id' => $id,
            'original_name' => $original,
        ]);

        $cf = ChallengeFile::find($id);
        if ($cf === null) {
            throw new RuntimeException('File record missing.');
        }
        return $cf;
    }

    public static function delete(ChallengeFile $file, int $actorId): void
    {
        ChallengeService::requireManageArena();

        $abs = self::absolutePath($file);
        Database::execute('DELETE FROM challenge_files WHERE id = ?', [$file->id]);
        if (is_file($abs)) {
            @unlink($abs);
        }
        ActivityLogService::log($actorId, 'challenge_file_deleted', 'challenge', $file->challenge_id, [
            'file_id' => $file->id,
        ]);
    }

    public static function absolutePath(ChallengeFile $file): string
    {
        // Never trust client paths — only DB storage_path under storage root
        $rel = str_replace(['..', '\\', "\0"], '', $file->storage_path);
        $rel = ltrim($rel, '/');
        if (!preg_match('#^challenge_\d+/[A-Za-z0-9._-]+$#', $rel)) {
            throw new RuntimeException('Invalid storage path.');
        }
        $root = realpath(self::storageRoot());
        if ($root === false) {
            throw new RuntimeException('Storage unavailable.');
        }
        $full = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $real = realpath($full);
        if ($real === false || !str_starts_with($real, $root . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('File not found.');
        }
        return $real;
    }

    public static function streamDownload(ChallengeFile $file, Challenge $challenge): never
    {
        if (!$challenge->isSolvable() && !ChallengeService::canManageArena()) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }

        $path = self::absolutePath($file);
        $mime = $file->mime_type !== '' ? $file->mime_type : 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($path));
        header('Content-Disposition: attachment; filename="' . rawurlencode($file->original_name) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
