<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use InvalidArgumentException;

final class LabValidationService
{
    public static function hashAnswer(string $answer, bool $caseSensitive = true): string
    {
        $normalized = $caseSensitive ? $answer : mb_strtolower($answer);
        return hash('sha256', $normalized);
    }

    /**
     * Staff-only fetch — never call from student list endpoints.
     *
     * @return array<string, mixed>|null
     */
    public static function fetchValidation(int $taskId): ?array
    {
        return Database::fetch(
            'SELECT * FROM lab_task_validations WHERE task_id = ? LIMIT 1',
            [$taskId]
        );
    }

    /**
     * @param array<string, mixed> $instance
     * @return array{result:string,score:int,message:string}
     */
    public static function validate(int $taskId, string $submission, array $instance, int $taskPoints): array
    {
        $validation = self::fetchValidation($taskId);
        if ($validation === null) {
            throw new InvalidArgumentException('Task validation is not configured.');
        }

        $type = (string) $validation['validation_type'];
        $config = json_decode((string) $validation['validation_config'], true);
        if (!is_array($config)) {
            throw new InvalidArgumentException('Invalid validation configuration.');
        }

        $submission = trim($submission);
        if ($submission === '' && $type !== 'manual') {
            throw new InvalidArgumentException('Answer cannot be empty.');
        }
        if (mb_strlen($submission) > 2000) {
            throw new InvalidArgumentException('Answer is too long.');
        }

        return match ($type) {
            'manual' => [
                'result' => 'manual_review',
                'score' => 0,
                'message' => 'Submitted for instructor review.',
            ],
            'multiple_choice' => self::checkMultipleChoice($submission, $config, $taskPoints),
            'exact' => self::checkExact($submission, $config, $taskPoints),
            'regex' => self::checkRegex($submission, $config, $taskPoints),
            'flag', 'instance_secret' => self::checkInstanceSecret($submission, $config, $instance, $taskPoints, $type === 'flag'),
            default => throw new InvalidArgumentException('Unsupported validation type.'),
        };
    }

    /**
     * @param array<string, mixed> $config
     * @return array{result:string,score:int,message:string}
     */
    private static function checkMultipleChoice(string $submission, array $config, int $points): array
    {
        $correct = $config['correct_option_ids'] ?? [];
        if (!is_array($correct) || $correct === []) {
            throw new InvalidArgumentException('Invalid multiple-choice config.');
        }
        $ok = in_array($submission, array_map('strval', $correct), true);
        return [
            'result' => $ok ? 'correct' : 'incorrect',
            'score' => $ok ? $points : 0,
            'message' => $ok ? 'Correct.' : 'Incorrect. Review the environment and try again.',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return array{result:string,score:int,message:string}
     */
    private static function checkExact(string $submission, array $config, int $points): array
    {
        $caseSensitive = (bool) ($config['case_sensitive'] ?? true);
        $expected = (string) ($config['value_hash'] ?? '');
        if ($expected === '') {
            throw new InvalidArgumentException('Invalid exact validation config.');
        }
        $ok = hash_equals($expected, self::hashAnswer($submission, $caseSensitive));
        return [
            'result' => $ok ? 'correct' : 'incorrect',
            'score' => $ok ? $points : 0,
            'message' => $ok ? 'Correct.' : 'Incorrect answer.',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return array{result:string,score:int,message:string}
     */
    private static function checkRegex(string $submission, array $config, int $points): array
    {
        $pattern = (string) ($config['pattern'] ?? '');
        if ($pattern === '' || @preg_match('/' . $pattern . '/u', '') === false) {
            throw new InvalidArgumentException('Invalid regex validation config.');
        }
        $ok = preg_match('/' . $pattern . '/u', $submission) === 1;
        return [
            'result' => $ok ? 'correct' : 'incorrect',
            'score' => $ok ? $points : 0,
            'message' => $ok ? 'Correct.' : 'Incorrect answer.',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $instance
     * @return array{result:string,score:int,message:string}
     */
    private static function checkInstanceSecret(
        string $submission,
        array $config,
        array $instance,
        int $points,
        bool $isFlag
    ): array {
        $key = (string) ($config['secret_key'] ?? ($isFlag ? 'flag' : ''));
        if ($key === '') {
            throw new InvalidArgumentException('Invalid secret validation config.');
        }
        $secrets = json_decode((string) ($instance['runtime_secrets'] ?? '{}'), true);
        if (!is_array($secrets) || !isset($secrets[$key])) {
            throw new InvalidArgumentException('Lab secret unavailable.');
        }
        $expected = (string) $secrets[$key];
        $caseSensitive = (bool) ($config['case_sensitive'] ?? true);
        $left = $caseSensitive ? $submission : mb_strtolower($submission);
        $right = $caseSensitive ? $expected : mb_strtolower($expected);
        $ok = hash_equals($right, $left);
        return [
            'result' => $ok ? 'correct' : 'incorrect',
            'score' => $ok ? $points : 0,
            'message' => $ok
                ? ($isFlag ? 'Flag accepted.' : 'Correct.')
                : ($isFlag ? 'Incorrect flag.' : 'Incorrect answer.'),
        ];
    }
}
