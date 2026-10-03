<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple server-side input validator.
 */
final class Validator
{
    /** @var array<string, list<string>> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string|list<string>> $rules
     */
    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    /**
     * @param array<string, string|list<string>> $rules
     */
    public function validate(array $rules): self
    {
        foreach ($rules as $field => $ruleSet) {
            $ruleList = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, (string) $rule);
            }
        }

        return $this;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $name = $rule;
        $param = null;

        if (str_contains($rule, ':')) {
            [$name, $param] = explode(':', $rule, 2);
        }

        $label = str_replace('_', ' ', $field);

        switch ($name) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && $value === [])) {
                    $this->addError($field, "The {$label} field is required.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$label} must be a valid email address.");
                }
                break;

            case 'string':
                if ($value !== null && $value !== '' && !is_string($value)) {
                    $this->addError($field, "The {$label} must be a string.");
                }
                break;

            case 'integer':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, "The {$label} must be an integer.");
                }
                break;

            case 'min_length':
                if ($value !== null && $value !== '' && mb_strlen((string) $value) < (int) $param) {
                    $this->addError($field, "The {$label} must be at least {$param} characters.");
                }
                break;

            case 'max_length':
                if ($value !== null && $value !== '' && mb_strlen((string) $value) > (int) $param) {
                    $this->addError($field, "The {$label} must not exceed {$param} characters.");
                }
                break;

            case 'enum':
                $allowed = array_map('trim', explode(',', (string) $param));
                if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
                    $this->addError($field, "The {$label} is invalid.");
                }
                break;

            case 'confirmed':
                $confirmation = $this->data[$field . '_confirmation'] ?? null;
                if ((string) $value !== (string) $confirmation) {
                    $this->addError($field, "The {$label} confirmation does not match.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return !$this->fails();
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return list<string>
     */
    public function flatErrors(): array
    {
        $flat = [];
        foreach ($this->errors as $messages) {
            foreach ($messages as $message) {
                $flat[] = $message;
            }
        }
        return $flat;
    }

    /**
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->data;
    }
}
