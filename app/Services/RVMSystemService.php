<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Bin;
use App\Models\Recycling;
use App\Models\RVMTransaction;
use App\Models\RVMTransactionQueue;
use App\Models\User;
use App\Helpers\ApiResponse;
use DB;
use Illuminate\Support\Facades\Log;

class RVMSystemService
{
	protected string $authUrl;
	protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

	public function __construct()
    {
		$this->authUrl = config('services.rvm_system.auth_url');
        $this->baseUrl = config('services.rvm_system.base_url');
        $this->clientId = config('services.rvm_system.client_id');
        $this->clientSecret = config('services.rvm_system.client_secret');
    }

	protected function generateAccessToken(): ?string
    {
        $response = Http::asForm()->post($this->authUrl, [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
			'scope' => 'extern_app alba'
        ]);

        if ($response->successful()) {
            $accessToken = $response->json('access_token');
            $expiresIn   = $response->json('expires_in', 3600);

            $ttl = now()->addSeconds($expiresIn - 300);
            Cache::put('rvm_system_access_token', $accessToken, $ttl);

            return $accessToken;
        }

        return null;
    }

    protected function getAccessToken(): ?string
    {
        return Cache::remember('rvm_access_token', now()->addMinutes(60), function () {
            return $this->generateAccessToken();
        });
    }

	public function getTransaction($rvmId, $transactionId)
	{
	    return $this->makeAuthenticatedRequest(function ($token) use ($rvmId, $transactionId) {
	        $maxAttempts = 2; // how many times to retry
	        $delay = 1000;    // delay in milliseconds between retries

	        for ($i = 1; $i <= $maxAttempts; $i++) {
	            $response = Http::withToken($token)
	                ->get("{$this->baseUrl}/rvms/{$rvmId}/transactions/{$transactionId}");

	            if ($response->successful()) {
	                return $response; // success, return immediately
	            }

	            // If not found (404), wait then retry
	            if ($response->status() === 404) {
	                usleep($delay * 1000); // convert ms → microseconds
	                continue;
	            }

	            // Other error (not 404), break early
	            return $response;
	        }

	        // If still not found after retries, return last response
	        return $response;
	    }, $rvmId, $transactionId);
	}


	protected function makeAuthenticatedRequest(callable $callback, $rvmId = null, $transactionId = null)
	{
	    try {
	        $token = $this->getAccessToken();

	        if (!$token) {
	            throw new \Exception('No access token available.', 400);
	        }

	        $response = $callback($token);

	        if ($response && $response->status() === 401) {
	            $token = $this->generateAccessToken();

	            if (!$token) {
	                throw new \Exception('Failed to regenerate access token.', 400);
	            }

	            $response = $callback($token);
	        }

	        if (!$response) {
	            throw new \Exception("No response received from RVM API.", 400);
	        }

	        if ($response->failed()) {
	            throw new \Exception("RVM API call failed: " . $response->body(), 400);
	        }

	        return $response->json();

	    } catch (\Throwable $e) {
	        throw $e;
	    }
	}

	public function scan(User $user, $qrCodeValue)
	{
		$decoded = self::decode($qrCodeValue);
		$bin = Bin::with('type')->where('code', $decoded['rvm_id'])->first();

	    try {
	        return DB::transaction(function () use ($user, $decoded, $bin, $qrCodeValue) {
	            $data = $this->getTransaction($decoded['rvm_id'], $decoded['transaction_id']);

	            BinService::submitRVMTransaction($user, $bin, $qrCodeValue, $data, 'RVM_SYSTEM');

				return ApiResponse::success([
					'additional_point' => $data['deposit'],
					'total_point' => $user->point,
					'bin_type' => $bin->type->name
				], 'Transaction recorded, pending.');
	        }, 5);
	    } catch (\Exception $e) {
			RVMTransactionQueue::storeOrRetry(
			    $qrCodeValue,
			    $user,
			    $bin,
			    'RVM_SYSTEM',
			    json_encode($e->getMessage())
			);
			return ApiResponse::success([
				'additional_point' => 'calculating.',
				'total_point' => $user->point,
				'bin_type' => $bin->type->name
			], 'Reward Pending. Thank you for recycling with us, your transaction has been recorded and is currently being processed.');
	    }
	}

	public static function decode($url) {

		$parsedUrl = parse_url($url);
		$path = $parsedUrl['path']; // e.g. /rvm/20203311398/transactions/6233
		$query = $parsedUrl['query']; // s=2025-08-06T22:11:28.419000+08:00

		$segments = explode('/', trim($path, '/')); // ['rvm', '20203311398', 'transactions', '6233']

		$rvmId = $segments[1]; // 20203311398
		$transactionId = $segments[3]; // 6233
		$fullTransaction = $transactionId . '?' . $query;
		return [
			'rvm_id' => $rvmId,
			'transaction_id' => $fullTransaction
		];
	}
}
