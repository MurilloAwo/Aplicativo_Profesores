<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeEmailRule implements ValidationRule
{
    /**
     * Valida que el correo sea válido y mitiga CRLF (CVE-2026-48019 / PKSA-3r5d-mb8f-1qw9).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('El campo :attribute debe ser una cadena de texto.');

            return;
        }

        if (str_contains($value, "\r") || str_contains($value, "\n")) {
            $fail('El correo electrónico contiene caracteres no permitidos (CRLF).');

            return;
        }

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('El correo electrónico debe ser una dirección válida.');
        }
    }
}
