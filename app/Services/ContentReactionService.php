<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use InvalidArgumentException;

final class ContentReactionService
{
    public static function react(int $userId, string $contentType, int $contentId, string $reactionType): void
    {
        if (!in_array($contentType, ['writeup', 'knowledge_article'], true)) {
            throw new InvalidArgumentException('Invalid content type.');
        }
        if (!in_array($reactionType, ['helpful', 'clear', 'practical'], true)) {
            throw new InvalidArgumentException('Invalid reaction.');
        }

        Database::execute(
            'INSERT IGNORE INTO content_reactions (user_id, content_type, content_id, reaction_type)
             VALUES (?, ?, ?, ?)',
            [$userId, $contentType, $contentId, $reactionType]
        );

        if ($reactionType === 'helpful') {
            if ($contentType === 'writeup') {
                Database::execute(
                    'UPDATE writeups SET helpful_count = (
                        SELECT COUNT(*) FROM content_reactions
                        WHERE content_type = \'writeup\' AND content_id = ? AND reaction_type = \'helpful\'
                     ) WHERE id = ?',
                    [$contentId, $contentId]
                );
            } else {
                Database::execute(
                    'UPDATE knowledge_articles SET helpful_count = (
                        SELECT COUNT(*) FROM content_reactions
                        WHERE content_type = \'knowledge_article\' AND content_id = ? AND reaction_type = \'helpful\'
                     ) WHERE id = ?',
                    [$contentId, $contentId]
                );
            }
        }
    }
}
