<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

	'rvm_system' => [
	    'auth_url' => env('RVM_AUTH_URL', 'https://revend.rvmsystems.com/o/token/'),
		'base_url' => env('RVM_BASE_URL', 'https://revend.rvmsystems.com/extern/api/v2'),
	    'client_id' => env('RVM_SYSTEM_CLIENT_ID', ''),
	    'client_secret' => env('RVM_SYSTEM_CLIENT_SECRET', ''),
	],

	'envipco' => [
        'username' => env('ENVIPCO_USER_NAME'),
        'password' => env('ENVIPCO_PASSWORD'),
        'test_url' => env('ENVIPCO_TEST_URL'),
        'production_url' => env('ENVIPCO_PRODUCTION_URL'),
    ],

];
