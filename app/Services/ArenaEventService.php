<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\ArenaEvent;
use App\Models\Challenge;
use InvalidArgumentException;

final class ArenaEventService
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function eventChallenges(int $eventId, ?int $userId = null): array
    {
        $rows = Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, c.points, c.status, c.is_active,
                    cat.name AS category_name,
                    (SELECT COUNT(*) FROM challenge_solves s WHERE s.challenge_id = c.id) AS solve_count
             FROM arena_event_challenges ec
             INNER JOIN challenges c ON c.id = ec.challenge_id
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE ec.event_id = ?
             ORDER BY cat.sort_order, c.points ASC, c.id ASC",
            [$eventId]
        );

        if ($userId !== null) {
            $ids = array_map(static fn(array $r): int => (int) $r['id'], $rows);
            $solved = \App\Models\ChallengeSolve::solvedSetForUser($userId, $ids);
            foreach ($rows as &$row) {
                $row['solved'] = isset($solved[(int) $row['id']]);
            }
            unset($row);
        }

        // Hide unpublished from non-staff
        if (!ChallengeService::canManageArena()) {
            $rows = array_values(array_filter(
                $rows,
                static fn(array $r): bool => ($r['status'] ?? '') === 'published' && (bool) ($r['is_active'] ?? false)
            ));
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $challengeIds
     */
    public static function create(int $actorId, array $data, array $challengeIds = []): ArenaEvent
    {
        ChallengeService::requireManageArena();

        $slug = Challenge::slugify((string) ($data['slug'] ?: $data['name']));
        $type = (string) ($data['event_type'] ?? 'practice');
        if (!in_array($type, ['practice', 'ctf', 'competition', 'workshop'], true)) {
            throw new InvalidArgumentException('Invalid event type.');
        }
        $status = (string) ($data['status'] ?? 'draft');
        if (!in_array($status, ['draft', 'upcoming', 'active', 'ended', 'archived'], true)) {
            throw new InvalidArgumentException('Invalid status.');
        }
        $visibility = (string) ($data['visibility'] ?? 'public');
        if (!in_array($visibility, ['public', 'private'], true)) {
            $visibility = 'public';
        }

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO arena_events
                 (name, slug, description, event_type, status, visibility, start_at, end_at, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    (string) $data['name'],
                    $slug . '-' . substr(bin2hex(random_bytes(2)), 0, 4),
                    (string) $data['description'],
                    $type,
                    $status,
                    $visibility,
                    (string) $data['start_at'],
                    (string) $data['end_at'],
                    $actorId,
                ]
            );
            $id = (int) Database::lastInsertId();
            self::syncChallenges($id, $challengeIds);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'event_created', 'arena_event', $id);
        $event = ArenaEvent::find($id);
        if ($event === null) {
            throw new \RuntimeException('Event missing after create.');
        }
        return $event;
    }

    /**
     * @param list<int> $challengeIds
     */
    public static function syncChallenges(int $eventId, array $challengeIds): void
    {
        Database::execute('DELETE FROM arena_event_challenges WHERE event_id = ?', [$eventId]);
        foreach ($challengeIds as $cid) {
            $cid = (int) $cid;
            if ($cid <= 0 || Challenge::find($cid) === null) {
                continue;
            }
            Database::execute(
                'INSERT IGNORE INTO arena_event_challenges (event_id, challenge_id) VALUES (?, ?)',
                [$eventId, $cid]
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function adminList(): array
    {
        return Database::fetchAll(
            'SELECT e.*, (SELECT COUNT(*) FROM arena_event_challenges ec WHERE ec.event_id = e.id) AS challenge_count
             FROM arena_events e ORDER BY e.start_at DESC'
        );
    }
}
