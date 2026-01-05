<?php

namespace App\Infra\Env;

class Env
{
    private static array $validated = [];

    public static function validate(): void
    {
        $schema = [
            'DB_HOST' => ['type' => 'string', 'required' => true],
            'DB_PORT' => ['type' => 'int', 'required' => true, 'min' => 1, 'max' => 65535],
            'DB_USER' => ['type' => 'string', 'required' => true],
            'DB_PASSWORD' => ['type' => 'string', 'required' => true],
            'DB_NAME' => ['type' => 'string', 'required' => true],
            'JWT_SECRET' => ['type' => 'string', 'required' => true],
            'JWT_EXPIRES_IN' => ['type' => 'int', 'required' => true, 'default' => 86400],
            'APP_ENV' => ['type' => 'enum', 'required' => true, 'values' => ['development', 'production', 'test'], 'default' => 'development'],
            'CORS_ORIGIN' => ['type' => 'string', 'required' => false, 'default' => '*'],
        ];

        $errors = [];

        foreach ($schema as $key => $rules) {
            $value = $_ENV[$key] ?? null;

            if (empty($value) && isset($rules['default'])) {
                self::$validated[$key] = $rules['default'];
                continue;
            }

            if ($rules['required'] && empty($value)) {
                $errors[] = "Missing required env variable: {$key}";
                continue;
            }

            if (!$rules['required'] && empty($value)) {
                continue;
            }

            switch ($rules['type']) {
                case 'string':
                    if (!is_string($value)) {
                        $errors[] = "{$key} must be a string";
                    }
                    break;

                case 'int':
                    if (!is_numeric($value)) {
                        $errors[] = "{$key} must be a number";
                    } else {
                        $value = (int) $value;

                        if (isset($rules['min']) && $value < $rules['min']) {
                            $errors[] = "{$key} must be at least {$rules['min']}";
                        }

                        if (isset($rules['max']) && $value > $rules['max']) {
                            $errors[] = "{$key} must be at most {$rules['max']}";
                        }
                    }
                    break;

                case 'enum':
                    if (!in_array($value, $rules['values'])) {
                        $allowed = implode(', ', $rules['values']);
                        $errors[] = "{$key} must be one of: {$allowed}";
                    }
                    break;

                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[] = "{$key} must be a valid URL";
                    }
                    break;
            }

            if (isset($rules['minLength']) && strlen($value) < $rules['minLength']) {
                $errors[] = "{$key} must be at least {$rules['minLength']} characters";
            }

            self::$validated[$key] = $value;
        }

        if (!empty($errors)) {
            throw new \RuntimeException(
                "Environment validation failed:\n" . implode("\n", $errors)
            );
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$validated[$key] ?? $default;
    }

    public static function getString(string $key): string
    {
        return (string) self::get($key);
    }

    public static function getInt(string $key): int
    {
        return (int) self::get($key);
    }
}
