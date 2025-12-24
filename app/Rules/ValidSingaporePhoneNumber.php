<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSingaporePhoneNumber implements Rule
{
    public function passes($attribute, $value)
    {
        $cleaned = str_replace(' ', '', $value);

        return preg_match('/^\+65\d{8}$/', $cleaned);
    }

    public function message()
    {
        return 'The :attribute must be a valid Singapore phone number starting with +65 followed by 8 digits.';
    }
}
