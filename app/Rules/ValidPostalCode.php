<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Helpers\Helper;

class ValidPostalCode implements Rule
{
    public function passes($attribute, $value)
    {
		$response = Helper::singaporeOneMapAPI($value);

        if ($response->successful()) {
            $results = $response->json()['results'] ?? [];
			if (!$results) {
				return false;
			}

			if ($results[0]['POSTAL'] != $value) {
				return false;
			}

			return true;
        }

        return false;
    }

    public function message()
    {
        return 'The postal code is invalid or not found.';
    }
}
