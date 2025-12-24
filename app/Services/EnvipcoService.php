<?php

namespace App\Services;

use SoapClient;
use SoapHeader;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Bin;
use App\Models\Recycling;
use App\Models\RVMTransaction;
use App\Models\RVMTransactionQueue;
use App\Models\User;
use App\Helpers\ApiResponse;
use DB;

class EnvipcoService
{
    private $wsdlPath;
    private $serviceUrl;
    private $username;
    private $password;
    private $client;

    public function __construct()
    {
        if (!extension_loaded('soap')) {
            throw new Exception('PHP SOAP extension is not loaded. Please install and enable the SOAP extension.');
        }

		$this->wsdlPath = base_path('envipco.wsdl');

        $this->serviceUrl = config('app.env') === 'production'
            ? config('services.envipco.production_url')
            : config('services.envipco.test_url');

        $this->username = config('services.envipco.username');
        $this->password = config('services.envipco.password');
    }

    /**
     * Create SOAP client with WS-Security authentication
     *
     * @return SoapClient
     * @throws Exception
     */
    private function createClient()
    {
        try {
            $options = [
                'location' => $this->serviceUrl,
                'uri' => $this->serviceUrl,
                'trace' => true,
                'exceptions' => true,
                'cache_wsdl' => defined('WSDL_CACHE_NONE') ? WSDL_CACHE_NONE : 0,
                'soap_version' => defined('SOAP_1_1') ? SOAP_1_1 : 1,
                'stream_context' => stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'Laravel SOAP Client'
                    ]
                ])
            ];

            $this->client = new SoapClient($this->wsdlPath, $options);

            $this->setWSSecurity();

            return $this->client;
        } catch (Exception $e) {
            throw new Exception('Failed to create SOAP client: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Set WS-Security authentication headers
     */
    private function setWSSecurity()
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $nonce = base64_encode(random_bytes(16));

        $wsSecurityHeader = '
            <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
                          xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <wsse:UsernameToken wsu:Id="UsernameToken-1">
                    <wsse:Username>' . htmlspecialchars($this->username) . '</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' .
                        htmlspecialchars($this->password) . '</wsse:Password>
                    <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' .
                        $nonce . '</wsse:Nonce>
                    <wsu:Created>' . $timestamp . '</wsu:Created>
                </wsse:UsernameToken>
            </wsse:Security>';

        $header = new SoapHeader(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            'Security',
            new \SoapVar($wsSecurityHeader, XSD_ANYXML),
            true
        );

        $this->client->__setSoapHeaders($header);
    }

    /**
     * Ping the Envipco service to test connectivity
     *
     * @return array|null
     */
    public function ping()
    {
        try {
            $client = $this->createClient();

            Log::info('Calling Envipco Ping');

            $result = $client->Ping([]);

            Log::info('Envipco Ping response: ' . json_encode($result));
			if ($result == 0) {
				return 'Reached envipco successful';
			}
        } catch (Exception $e) {
            Log::error('Error calling Envipco Ping: ' . $e->getMessage());
            throw new Exception('Error calling Ping: ' . $e->getMessage());
        }
    }

	public function scan(User $user, $qrCodeValue)
	{
		$decoded = $this->decode($qrCodeValue);
		$bin = Bin::with('type')->where('code', $decoded['rvm_id'])->first();

	    try {
	        return DB::transaction(function () use ($user, $decoded, $bin, $qrCodeValue) {
	            $client = $this->createClient();
	            $data = $client->Redeem($decoded['transaction_id']);
				if (
				    empty($data['deposit']) ||
				    (isset($data['httpStatusCode']) && !in_array($data['httpStatusCode'], [0, 1], true))
				) {
				    throw new \Exception(json_encode($data), 400);
				}


				BinService::submitRVMTransaction($user, $bin, $qrCodeValue, $data, 'ENVIPCO');

				return ApiResponse::success([
					'additional_point' => $data['deposit'],
					'total_point' => $user->point,
					'bin_type' => $bin->type->name
				], 'Transaction recorded, pending.');
	        }, 5); // End of DB::transaction closure

	    } catch (\Exception $e) {
			RVMTransactionQueue::storeOrRetry(
			    $qrCodeValue,
			    $user,
			    $bin,
			    'ENVIPCO',
			    $e->getMessage()
			);
			return ApiResponse::success([
				'additional_point' => 'calculating.',
				'total_point' => $user->point,
				'bin_type' => $bin->type->name
			], 'Reward Pending. Thank you for recycling with us, your transaction has been recorded and is currently being processed.');
	    }
	}

    /**
     * Get the last SOAP request XML (for debugging)
     *
     * @return string|null
     */
    public function getLastRequest()
    {
        return $this->client ? $this->client->__getLastRequest() : null;
    }

    /**
     * Get the last SOAP response XML (for debugging)
     *
     * @return string|null
     */
    public function getLastResponse()
    {
        return $this->client ? $this->client->__getLastResponse() : null;
    }

	public static function decode($url) {
	    $path = parse_url($url, PHP_URL_PATH);

	    if ($path === null) {
	        return null;
	    }

	    $segments = explode('/', trim($path, '/'));

	    if (isset($segments[1]) && isset($segments[2])) {
			return [
				'rvm_id' => $segments[1],
				'transaction_id' => $segments[2]
			];
	    }

	    return null;
	}

	public function isRedeemed(User $user, $qrCodeValue)
	{
		$decoded = $this->decode($qrCodeValue);
		$bin = Bin::with('type')->where('code', $decoded['rvm_id'])->first();

        return DB::transaction(function () use ($user, $decoded, $bin, $qrCodeValue) {
            $client = $this->createClient();
            $data = $client->Redeem($decoded['transaction_id']);
			if (isset($data['httpStatusCode']) && $data['httpStatusCode'] == 1) {
				return true;
			}
			return false;
        }, 5); // End of DB::transaction closure

	}
}
