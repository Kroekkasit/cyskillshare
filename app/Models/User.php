<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public int $id;
    public string $username;
    public string $email;
    public string $password_hash;
    public ?string $full_name;
    public ?string $student_id;
    public ?int $year_level;
    public ?string $program;
    public ?string $bio;
    public ?string $avatar;
    public string $status;
    public string $skills_visibility;
    public string $created_at;
    public string $updated_at;
    public ?string $last_login_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->username = (string) $row['username'];
        $this->email = (string) $row['email'];
        $this->password_hash = (string) $row['password_hash'];
        $this->full_name = $row['full_name'] !== null ? (string) $row['full_name'] : null;
        $this->student_id = $row['student_id'] !== null ? (string) $row['student_id'] : null;
        $this->year_level = $row['year_level'] !== null ? (int) $row['year_level'] : null;
        $this->program = $row['program'] !== null ? (string) $row['program'] : null;
        $this->bio = $row['bio'] !== null ? (string) $row['bio'] : null;
        $this->avatar = $row['avatar'] !== null ? (string) $row['avatar'] : null;
        $this->status = (string) $row['status'];
        $this->skills_visibility = (string) ($row['skills_visibility'] ?? 'public');
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
        $this->last_login_at = $row['last_login_at'] !== null ? (string) $row['last_login_at'] : null;
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM users WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findByLogin(string $login): ?self
    {
        $row = self::fetch(
            'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1',
            [$login, $login]
        );
        return $row ? new self($row) : null;
    }

    public static function findByUsername(string $username): ?self
    {
        $row = self::fetch('SELECT * FROM users WHERE username = ? LIMIT 1', [$username]);
        return $row ? new self($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $row = self::fetch('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        return $row ? new self($row) : null;
    }

    /**
     * @param array{
     *   username: string,
     *   email: string,
     *   password_hash: string,
     *   full_name?: ?string,
     *   student_id?: ?string,
     *   year_level?: ?int,
     *   program?: ?string
     * } $data
     */
    public static function create(array $data): self
    {
        self::execute(
            'INSERT INTO users (username, email, password_hash, full_name, student_id, year_level, program, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['username'],
                $data['email'],
                $data['password_hash'],
                $data['full_name'] ?? null,
                $data['student_id'] ?? null,
                $data['year_level'] ?? null,
                $data['program'] ?? null,
                'active',
            ]
        );

        $id = (int) self::lastInsertId();
        $user = self::find($id);
        if ($user === null) {
            throw new \RuntimeException('Failed to create user.');
        }
        return $user;
    }

    public static function touchLastLogin(int $userId): void
    {
        self::execute('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$userId]);
    }

    /**
     * @return list<string>
     */
    public function roleNames(): array
    {
        $rows = self::fetchAll(
            'SELECT r.name
             FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = ?
             ORDER BY r.name',
            [$this->id]
        );

        return array_map(static fn(array $row): string => (string) $row['name'], $rows);
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::findByName($roleName);
        if ($role === null) {
            throw new \InvalidArgumentException("Unknown role: {$roleName}");
        }

        self::execute(
            'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)',
            [$this->id, $role->id]
        );
    }

    public function removeRole(string $roleName): void
    {
        $role = Role::findByName($roleName);
        if ($role === null) {
            throw new \InvalidArgumentException("Unknown role: {$roleName}");
        }

        self::execute(
            'DELETE FROM user_roles WHERE user_id = ? AND role_id = ?',
            [$this->id, $role->id]
        );
    }

    public function updateStatus(string $status): void
    {
        if (!in_array($status, ['active', 'suspended', 'banned'], true)) {
            throw new \InvalidArgumentException('Invalid status.');
        }
        self::execute('UPDATE users SET status = ? WHERE id = ?', [$status, $this->id]);
        $this->status = $status;
    }

    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->roleNames(), true);
    }

    public function discussionCount(): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM threads WHERE user_id = ? AND deleted_at IS NULL',
            [$this->id]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    public function replyCount(): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM replies WHERE user_id = ? AND deleted_at IS NULL',
            [$this->id]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    public function bestAnswerCount(): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM replies
             WHERE user_id = ? AND is_best_answer = 1 AND deleted_at IS NULL',
            [$this->id]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    public function updateProfile(?string $bio, ?string $fullName): void
    {
        self::execute(
            'UPDATE users SET bio = ?, full_name = ? WHERE id = ?',
            [$bio, $fullName, $this->id]
        );
        $this->bio = $bio;
        $this->full_name = $fullName;
    }

    public function updateSkillsVisibility(string $visibility): void
    {
        self::execute(
            'UPDATE users SET skills_visibility = ? WHERE id = ?',
            [$visibility, $this->id]
        );
        $this->skills_visibility = $visibility;
    }

    public static function countActive(): int
    {
        $row = self::fetch("SELECT COUNT(*) AS cnt FROM users WHERE status = 'active'");
        return (int) ($row['cnt'] ?? 0);
    }

    public function primaryRoleLabel(): string
    {
        $roles = $this->roleNames();
        foreach (['admin', 'moderator', 'instructor', 'mentor', 'student'] as $role) {
            if (in_array($role, $roles, true)) {
                return ucfirst($role);
            }
        }
        return 'Member';
    }
}
