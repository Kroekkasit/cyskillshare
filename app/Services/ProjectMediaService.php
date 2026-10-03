<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class ProjectMediaService
{
    public static function storageRoot(): string
    {
        $path = storage_path('projects');
        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
        return $path;
    }

    /**
     * @param array<string, mixed> $file $_FILES element
     */
    public static function upload(int $projectId, int $ownerId, array $file, ?string $caption = null): array
    {
        $project = ProjectService::find($projectId);
        if ($project === null) {
            throw new InvalidArgumentException('Project not found.');
        }
        ProjectService::requireOwner($project, $ownerId);

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Upload failed.');
        }

        $max = (int) config('portfolio.project_images.max_bytes', 5242880);
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > $max) {
            throw new InvalidArgumentException('Invalid image size.');
        }

        $original = basename(str_replace(["\0", '\\'], '', (string) ($file['name'] ?? 'image')));
        $original = preg_replace('/[^\w.\- ()\[\]]+/', '_', $original) ?? 'image';
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowedExt = config('portfolio.project_images.allowed_extensions', []);
        $blocked = config('portfolio.project_images.blocked_extensions', []);
        if (!is_array($allowedExt) || !in_array($ext, $allowedExt, true)
            || (is_array($blocked) && in_array($ext, $blocked, true))
        ) {
            throw new InvalidArgumentException('Image type not allowed.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new InvalidArgumentException('Invalid upload.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: '';
        $allowedMime = config('portfolio.project_images.allowed_mimes', []);
        if (!is_array($allowedMime) || !in_array($mime, $allowedMime, true)) {
            throw new InvalidArgumentException('MIME type not allowed.');
        }

        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new InvalidArgumentException('File is not a valid image.');
        }

        $stored = bin2hex(random_bytes(8)) . '.' . $ext;
        $relDir = 'project_' . $projectId;
        $absDir = self::storageRoot() . DIRECTORY_SEPARATOR . $relDir;
        if (!is_dir($absDir) && !mkdir($absDir, 0775, true) && !is_dir($absDir)) {
            throw new RuntimeException('Unable to create storage directory.');
        }
        $abs = $absDir . DIRECTORY_SEPARATOR . $stored;
        if (!move_uploaded_file($tmp, $abs)) {
            throw new RuntimeException('Failed to store image.');
        }
        @chmod($abs, 0640);

        $rel = $relDir . '/' . $stored;
        Database::execute(
            'INSERT INTO project_images
             (project_id, stored_name, storage_path, original_name, mime_type, file_size, caption)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$projectId, $stored, $rel, mb_substr($original, 0, 180), $mime, $size, $caption]
        );

        return [
            'id' => (int) Database::lastInsertId(),
            'storage_path' => $rel,
        ];
    }

    public static function absolutePath(array $image): string
    {
        $rel = str_replace(['..', '\\', "\0"], '', (string) $image['storage_path']);
        $rel = ltrim($rel, '/');
        if (!preg_match('#^project_\d+/[A-Za-z0-9._-]+$#', $rel)) {
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

    public static function stream(int $imageId, ?int $viewerId): never
    {
        $image = Database::fetch('SELECT * FROM project_images WHERE id = ? LIMIT 1', [$imageId]);
        if ($image === null) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }
        $project = ProjectService::find((int) $image['project_id']);
        if ($project === null || !PortfolioVisibilityService::canViewProject($project, $viewerId)) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }

        $path = self::absolutePath($image);
        header('Content-Type: ' . (string) $image['mime_type']);
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: inline; filename="' . rawurlencode((string) $image['original_name']) . '"');
        readfile($path);
        exit;
    }

    public static function delete(int $imageId, int $ownerId): void
    {
        $image = Database::fetch('SELECT * FROM project_images WHERE id = ? LIMIT 1', [$imageId]);
        if ($image === null) {
            return;
        }
        $project = ProjectService::find((int) $image['project_id']);
        if ($project === null) {
            return;
        }
        ProjectService::requireOwner($project, $ownerId);
        try {
            $abs = self::absolutePath($image);
            @unlink($abs);
        } catch (\Throwable) {
            // ignore missing file
        }
        Database::execute('DELETE FROM project_images WHERE id = ?', [$imageId]);
    }
}
