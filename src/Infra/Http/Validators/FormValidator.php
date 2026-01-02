<?php

namespace App\Infra\Http\Validators;

class FormValidator
{
    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $error = $this->validateField($field, $data[$field] ?? null, $rule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        return $errors;
    }

    private function validateField(string $field, mixed $value, string $rule): ?string
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $param] = explode(':', $rule);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        return match ($ruleName) {
            'required' => empty($value) ? "Field {$field} is required" : null,
            'min' => strlen((string) $value) < (int) $param ? "Field {$field} must be at least {$param} characters long" : null,
            'email' => !filter_var($value, FILTER_VALIDATE_EMAIL) ? "Field {$field} must be a valid email" : null,
            default => null
        };
    }
}
