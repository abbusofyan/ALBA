<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Info(
 *    title="ALBA API",
 *    version="1.0.0",
 *    description="API for ALBA Mobile Apps",
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function toSelect2Data($args, $key_id, $key_text, $key_text_adds = null, $spec = null): array
    {
        if (!is_array($args)) {
            try {
                $args->toArray();
            } catch (\Exception $exception) {
                return [];
            }
        }

        return array_map(function ($item) use ($key_id, $key_text, $key_text_adds, $spec) {
            if ($key_text == 'running_number_text') {
                return [
                    'id' => $item[$key_id],
                    'text' => $item[($item['job_stage'] == 'incoming' ? 'running_number_inc' : 'running_number')] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                ];
            } else {
                if (!empty($item[$key_text])) {
                    return [
                        'id' => $item[$key_id],
                        'text' => $item[$key_text] . ($key_text_adds ? $spec . $item[$key_text_adds] : null)
                    ];
                }
            }
        }, $args);
    }

    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
