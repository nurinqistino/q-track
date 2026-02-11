<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KwspEmailDomain implements ValidationRule
{
    /**
     * Staff login is ONLY allowed for email ending with @kwsp.gov.my
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! str_ends_with(strtolower($value), '@kwsp.gov.my')) {
            $fail('Only KWSP staff email addresses (@kwsp.gov.my) are allowed to log in.');
        }
    }
}
