<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Mail\VerifyEmail;
use App\Mail\RequestAccountMail;
use App\Mail\RegisterEntityAlertMail;
use App\Models\User;
use App\Models\BannedList;
use App\Models\VerificationCode;
use App\Rules\UserEmailValidation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Twilio\Rest\Client;
use App\Services\VerificationService;
use App\Helpers\ApiResponse;
use App\Rules\ValidSingaporePhoneNumber;
use App\Rules\ValidPostalCode;
use App\Http\Requests\RegisterEntityFormRequest;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Requests\ValidateUserProfileRequest;
use App\Http\Requests\ValidateEntityProfileRequest;
use App\Http\Requests\ValidateCredential;
use App\Http\Requests\ValidatePhoneNumber;
use App\Http\Requests\ResetPasswordFormRequest;
use Illuminate\Support\Facades\Log;
use App\Helpers\ProfanityDetector;

class AuthenticationController extends ApiController
{
	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/Login",
	 *     tags={"Authentication"},
	 *     summary="Login to get verify code",
	 *     description="type = user, school or enterprise",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="type",
	 *                          enum={"user", "school", "enterprise"},
	 *                          type="string",
	 *                          description="Type of the account"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="key",
	 *                          type="string",
	 *                      ),
	 *                      @OA\Property(
	 *                          property="password",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "type":"user",
	 *                     "key":"test@mail.com",
	 *                     "password":"password123"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="Password",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "password":"password123"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function login(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'type' => 'required|in:user,school,enterprise',
				'key' => 'required|string',
				'password' => 'required|string',
			]);

			if ($validator->fails()) {
				return ApiResponse::validation($validator->errors());
			}

			$type = $request->input('type');
			$key = $request->input('key');
			$password = $request->input('password');

			if ($type === 'school' && !(
			    str_starts_with($key, 'sch') ||
			    str_starts_with($key, 'etpr')
			)) {
			    throw new \Exception('Use unique ID to login', 422);
			}

			// if ($type === 'enterprise' && !str_starts_with($key, 'etpr')) {
			// 	throw new \Exception('Use unique ID to login', 422);
			// }

			if ($type === 'user') {
				$user = User::where(function ($query) use ($key) {
			        $query->where('email', $key)
			              ->orWhere('phone', $key);
			    })
			    ->whereHas('roles', function ($query) {
			        $query->where('name', 'Public');
			    })
			    ->first();
			} else {
				$user = User::where('username', $key)->first();
			}

			if (!$user) {
				throw new \Exception('Account not found. Please check your login credentials.', 404);
			}

			if ($user->status == User::STATUS_BANNED) {
				return ApiResponse::banned($user);
			}

			if ($user->is_old_user) {
				throw new \Exception("You account is detected as old user. Please setup your new password and nickname", 403);
			}

			if (!Hash::check($password, $user->password)) {
				throw new \Exception('Your password is incorrect.', 401);
			}

			if (!$user->status) {
				throw new \Exception('Account inactive. Please contact admin to activate your account.', 400);
			}

			$accessTokenExpiresAt = Carbon::now()->addSeconds(config('sanctum.expiration'));
			$refreshTokenExpiresAt = Carbon::now()->addSeconds(config('sanctum.rt_expiration'));

			$user->tokens()->delete();

			$accessToken = $user->createToken('access_token', ['access'], $accessTokenExpiresAt)->plainTextToken;
			$refreshToken = $user->createToken('refresh_token', ['refresh'], $refreshTokenExpiresAt)->plainTextToken;

			if ($user->roles->count() > 0) {
				$user->role_name = $user->roles->first()->name;
			}

			return ApiResponse::success([
				'access_token' => $accessToken,
				'access_token_expires_at' => $accessTokenExpiresAt->toDateTimeString(),
				'refresh_token' => $refreshToken,
				'refresh_token_expires_at' => $refreshTokenExpiresAt->toDateTimeString(),
				'user' => $user
			], 'Login Success');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
		}
	}



	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/ResendCode",
	 *     tags={"Authentication"},
	 *     summary="Resend verify code",
	 *     description="Resend verify code for registration, password reset, or profile update",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 type="object",
	 *                 @OA\Property(
	 *                     property="phone",
	 *                     type="string",
	 *                     example="+61 123890209"
	 *                 ),
	 *                 @OA\Property(
	 *                     property="action",
	 *                     type="string",
	 *                     example="register, forgot_password, or update_profile",
	 *                     enum={"register", "forgot_password", "update_profile"},
	 *                     description="Specify the context for resending the verification code"
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Successful operation"
	 *     )
	 * )
	 */

	public function resendCode(Request $request)
	{
		try {
			$action = $request->input('action', 'register');

			$rules = [
				'phone' => ['required', 'string', 'min:8', 'max:13'],
				'action' => ['required', 'in:register,forgot_password,update_profile'],
			];

			if ($action !== 'register') {
				$rules['phone'][] = Rule::exists('users', 'phone');
			}

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				return ApiResponse::validation($validator->errors());
			}

			$verificationCode = $this->generateVerificationCode(6);

			// Send the new verification code to the user's phone number
			// if (!empty($request->phone)) $this->sendVerificationCode($verificationCode, $request->phone);

			VerificationCode::create([
				'phone' => $request->phone,
				'code' => $verificationCode,
				'expires_at' => Carbon::now()->addMinutes(10),
			]);

			return ApiResponse::success([
				'code' => $verificationCode,
			], 'Your verification code will be valid for 10 minutes');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode());
		}
	}


	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/Verify",
	 *     tags={"Authentication"},
	 *     summary="Verify",
	 *     description="Verify",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="phone",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="verify_code",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "phone":"+62 999222",
	 *                     "verify_code":"963411"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function verify(Request $request)
	{

		try {
			if (empty($request->phone)) {
				throw new \Exception('Please enter phone number.', 400);
			}

			$validator = Validator::make($request->all(), [
				'phone' => ['required', 'string', 'min:8', 'max:13', 'exists:verification_codes'],
				'verify_code' => ['required', 'string', 'size:6'],
			]);

			if ($validator->fails()) {
				return ApiResponse::validation($validator->errors());
			}

			$response = VerificationService::verifyUserCode($request->phone, $request->verify_code);

			if (!$response['success']) {
				throw new \Exception($response['message'], 400);
			}

			return ApiResponse::success(null, 'Verification Success');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @OA\Post(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/auth/RefreshToken",
	 *     tags={"Authentication"},
	 *     summary="Get new token",
	 *     description="Get new token",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="refresh_token",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "refresh_token":"10d2505c780d59bd9e09197265badef215d36cab03f81c93e8d3f0ee9bd2501b"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function refresh(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'refresh_token' => 'required'
			]);

			if ($validator->fails()) {
				return ApiResponse::validation($validator->errors());
			}

			$refreshTokenInput = $request->input('refresh_token');
			$token = PersonalAccessToken::findToken($refreshTokenInput);

			if (!$token || $token->name !== 'refresh_token' || Carbon::parse($token->expires_at)->lt(Carbon::now())) {
				return ApiResponse::error('Invalid or expired refresh token.', 401);
			}

			$user = $token->tokenable;

			$user->tokens()->delete();

			// Create new access token
			$accessExpiresAt = Carbon::now()->addSeconds(config('sanctum.expiration'));
			$accessTokenResult = $user->createToken('access_token', ['access']);
			$accessToken = $accessTokenResult->plainTextToken;

			$accessTokenModel = $user->tokens()
				->where('name', 'access_token')
				->latest()
				->first();
			$accessTokenModel->expires_at = $accessExpiresAt;
			$accessTokenModel->save();

			// Create new refresh token
			$refreshExpiresAt = Carbon::now()->addSeconds(config('sanctum.rt_expiration'));
			$refreshTokenResult = $user->createToken('refresh_token', ['refresh']);
			$refreshToken = $refreshTokenResult->plainTextToken;

			$refreshTokenModel = $user->tokens()
				->where('name', 'refresh_token')
				->latest()
				->first();
			$refreshTokenModel->expires_at = $refreshExpiresAt;
			$refreshTokenModel->save();

			$user->role_name = $user->roles->first()->name;

			return ApiResponse::success([
				'access_token' => $accessToken,
				'access_token_expires_at' => $accessExpiresAt->toDateTimeString(),
				'refresh_token' => $refreshToken,
				'refresh_token_expires_at' => $refreshExpiresAt->toDateTimeString(),
				'user' => $user,
			]);
		} catch (\Exception $e) {
			return $this->result('', $e->getMessage(), $e->getCode() ?: 500);
		}
	}

	/**
	 * @OA\Get(
	 *     security={{"bearerAuth":{}}},
	 *     path="/api/v1/auth/Logout",
	 *     tags={"Authentication"},
	 *     summary="Logout",
	 *     description="Logout",
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function logout(Request $request)
	{
		try {
			$token = $request->bearerToken();
			if (!$token) throw new \Exception('Access denied.', 401);

			$personalAccessToken = PersonalAccessToken::findToken($token);
			if (!$personalAccessToken) throw new \Exception('Access denied.', 401);
			// Revoke all tokens...
			optional(User::find($personalAccessToken->tokenable_id))->tokens()->delete();

			return $this->result();
		} catch (\Exception $e) {
			return $this->result('', $e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/Register",
	 *     tags={"Authentication"},
	 *     summary="Register",
	 *     description="Register",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="multipart/form-data",
	 *             @OA\Schema(
	 *                  @OA\Property(
	 *                      property="first_name",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="last_name",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="display_name",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="email",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="phone",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="password",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="password_confirmation",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="dob",
	 *                      type="date"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="address",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="postal_code",
	 *                      type="string"
	 *                  )
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function register(StoreUserFormRequest $request)
	{
		try {
			$fullname = $request->first_name . ' ' . $request->last_name;
			$newUser = User::create([
				'name' => $fullname,
				'first_name' => $request->input('first_name'),
				'last_name' => $request->input('last_name'),
				'display_name' => $request->display_name,
				'phone' => str_replace(' ', '', $request->input('phone')),
				'email' => $request->input('email'),
				'password' => Hash::make($request->input('password')),
				'dob' => $request->input('dob'),
				'address' => $request->input('address'),
				'postal_code' => $request->input('postal_code'),
				'verified' => true
			]);

			$newUser->assignRole('Public');

			$requestLogin = new Request([
				'type' => 'user',
				'key' => $request->input('email'),
				'password' => $request->input('password'),
			]);

			return $this->login($requestLogin);
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/RegisterEntity/{entity}",
	 *     tags={"Authentication"},
	 *     summary="Register Entity",
	 *     description="Register entity such as school or enterprise",
	 *     @OA\Parameter(
	 *         name="entity",
	 *         in="path",
	 *         required=true,
	 *         description="school or enterprise",
	 *         @OA\Schema(type="string")
	 *     ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="multipart/form-data",
	 *             @OA\Schema(
	 *                  @OA\Property(
	 *                      property="name",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="email",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="phone",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="password",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="password_confirmation",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="address",
	 *                      type="string"
	 *                  ),
	 *                  @OA\Property(
	 *                      property="postal_code",
	 *                      type="string"
	 *                  )
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function registerEntity($entity, RegisterEntityFormRequest $request)
	{
		try {
			if (!in_array(strtolower($entity), ['school', 'enterprise'])) {
				return ApiResponse::error('Invalid entity type. Must be either school or enterprise.', 400);
			}

			$unique_id = User::generateUniqueID($entity);

			$newUser = User::create([
				'name' => $request->name,
				'username' => $unique_id,
				'phone' => str_replace(' ', '', $request->input('phone')),
				'email' => $request->input('email'),
				'password' => Hash::make($request->input('password')),
				'address' => $request->input('address'),
				'postal_code' => $request->input('postal_code'),
				'verified' => true,
				'status' => 0
			]);

			$newUser->assignRole(ucfirst($entity));
			$newUser->password_plain = $request->password;

			Mail::to(config('mail.admin'))->send(new RegisterEntityAlertMail($entity, $newUser));

			return ApiResponse::success([], ucfirst($entity) . ' account request has been submitted successfully. Please wait for an administrator to activate your account.');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/ResetPassword",
	 *     tags={"Authentication"},
	 *     summary="Reset Password: enter phone number",
	 *     description="Reset Password: enter phone number",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="phone",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="display_name",
	 *                          type="string"
	 *                      ),
	 *                       @OA\Property(
	 *                           property="password",
	 *                           type="string"
	 *                       ),
	 *                        @OA\Property(
	 *                            property="password_confirmation",
	 *                            type="string"
	 *                        )
	 *                 ),
	 *                 example={
	 *                     "phone":"+6512341234",
	 *                     "display_name":"testuser123",
	 *                     "password":"password",
	 *                     "password_confirmation":"password",
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function resetPassword(ResetPasswordFormRequest $request)
	{
		try {
			if (empty($request->phone)) {
				return ApiResponse::error('Please enter phone number.', 400);
			}

			$user = User::where('phone', $request->input('phone'))->first();

			$data = [
				'is_old_user' => 0,
				'password' => Hash::make($request->input('password'))
			];
			if (isset($request->display_name)) {
				$data['display_name'] = $request->display_name;
			}
			$user->update($data);

			return ApiResponse::success(null, 'Password reset successfully');
		} catch (\Exception $e) {
			$code = $e->getCode() ?: 500;
			return ApiResponse::error($e->getMessage(), $code);
		}
	}


	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/RequestAccount",
	 *     tags={"Authentication"},
	 *     summary="Send email to alba admin to request an account for school or enterprise",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="type",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="name",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="address",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="postal_code",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="email",
	 *                          type="string"
	 *                      ),
	 *                      @OA\Property(
	 *                          property="phone",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "type": "School or Enterprise",
	 *                     "name": "School or Enterprise name",
	 *                     "address": "Test address",
	 *                     "postal_code": "123456",
	 *                     "email":"test@mail.com",
	 *                     "phone": "+63 8899221"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                      type="object",
	 *                      @OA\Property(
	 *                          property="Password",
	 *                          type="string"
	 *                      )
	 *                 ),
	 *                 example={
	 *                     "password":"password123"
	 *                }
	 *             )
	 *         )
	 *      ),
	 *      @OA\Response(
	 *          response=200,
	 *          description="Successful operation"
	 *       )
	 * )
	 */
	public function requestAccount(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'type' => ['required', 'string', 'in:school,enterprise'],
			'name' => ['required', 'string', 'max:255'],
			'address' => ['required'],
			'postal_code' => ['required', 'string', 'max:6', new ValidPostalCode()],
			'email' => ['required', 'email', 'max:255', 'unique:users'],
			'phone' => ['required', 'string', 'min:8', 'max:13', 'unique:users', new ValidSingaporePhoneNumber()],
		], [
			'type.in' => 'The selected type is invalid. It must be either school or enterprise',
		]);

		if ($validator->fails()) {
			return ApiResponse::validation($validator->errors());
		}

		try {
			Mail::to(config('mail.admin'))->send(new RequestAccountMail($request->all()));
			return ApiResponse::success(null, 'Account request has been sent');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/ValidateProfile",
	 *     tags={"Authentication"},
	 *     summary="Validate user or entity profile",
	 *     description="Validate user or entity profile fields. Use 'type' field to specify 'user' or 'entity'.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="type", type="string", enum={"user", "entity"}),
	 *                 @OA\Property(property="first_name", type="string", maxLength=255, description="Required if type=user"),
	 *                 @OA\Property(property="last_name", type="string", maxLength=255, description="Required if type=user"),
	 *                 @OA\Property(property="postal_code", type="string", maxLength=6, description="Singapore postal code, required for entity, optional for user"),
	 *                 @OA\Property(property="name", type="string", maxLength=255, description="Required if type=entity"),
	 *                 @OA\Property(property="address", type="string", description="Required if type=entity")
	 *             ),
	 *             example={
	 *                 "type": "user",
	 *                 "first_name": "John",
	 *                 "last_name": "Doe",
	 *                 "postal_code": "123456"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     )
	 * )
	 */
	public function validateProfile(Request $request)
	{
		if ($request->type === 'user') {
			$validated = app(ValidateUserProfileRequest::class)->validated();
		} else {
			$validated = app(ValidateEntityProfileRequest::class)->validated();
		}
		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/ValidateCredential",
	 *     tags={"Authentication"},
	 *     summary="Validate user credentials",
	 *     description="Validate email and password for registration.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="email", type="string", format="email", maxLength=255),
	 *                 @OA\Property(property="password", type="string", format="password"),
	 *                 @OA\Property(property="password_confirmation", type="string", format="password")
	 *             ),
	 *             example={
	 *                 "email": "test@mail.com",
	 *                 "password": "password123",
	 *                 "password_confirmation": "password123"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     )
	 * )
	 */
	public function validateCredential(ValidateCredential $request)
	{
		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/validatePhoneNumber",
	 *     tags={"Authentication"},
	 *     summary="Validate phone number",
	 *     description="Validate Singapore phone number for registration.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="phone", type="string", description="Singapore phone number, unique, required")
	 *             ),
	 *             example={
	 *                 "phone": "+65 81234567"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     )
	 * )
	 */
	public function validatePhoneNumber(ValidatePhoneNumber $request)
	{
		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/validateEmail",
	 *     tags={"Authentication"},
	 *     summary="Validate email",
	 *     description="Validate email format and uniqueness for registration.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="email", type="string", format="email", description="Email address, unique, required")
	 *             ),
	 *             example={
	 *                 "email": "test@example.com"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     ),
	 *     @OA\Response(
	 *          response=422,
	 *          description="Validation error"
	 *     )
	 * )
	 */
	public function validateEmail(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => ['required', 'email', 'max:255', new UserEmailValidation()],
		]);

		if ($validator->fails()) {
			return ApiResponse::validation($validator->errors());
		}

		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/validatePostalCode",
	 *     tags={"Authentication"},
	 *     summary="Validate postal code",
	 *     description="Validate Singapore postal code.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="postal_code", type="string", description="Singapore postal code")
	 *             ),
	 *             example={
	 *                 "postal_code": "123456"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     ),
	 *     @OA\Response(
	 *          response=422,
	 *          description="Validation error"
	 *     )
	 * )
	 */
	public function validatePostalCode(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'postal_code' => ['required', 'string', 'max:6', new ValidPostalCode()],
		]);

		if ($validator->fails()) {
			return ApiResponse::validation($validator->errors());
		}

		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/validatePassword",
	 *     tags={"Authentication"},
	 *     summary="Validate password",
	 *     description="Validate password confirmation with minimum 8 characters.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="password", type="string", format="password"),
	 *                 @OA\Property(property="password_confirmation", type="string", format="password")
	 *             ),
	 *             example={
	 *                 "password": "password123",
	 *                 "password_confirmation": "password123"
	 *             }
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Validation passed"
	 *     ),
	 *     @OA\Response(
	 *          response=422,
	 *          description="Validation error"
	 *     )
	 * )
	 */
	public function validatePassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		if ($validator->fails()) {
			return ApiResponse::validation($validator->errors());
		}

		return ApiResponse::success([], 'Validation passed');
	}

	/**
	 * @OA\Post(
	 *     path="/api/v1/auth/validateNickname",
	 *     tags={"Authentication"},
	 *     summary="Validate nickname with advanced profanity and leetspeak detection",
	 *     description="Validate nickname against profanity, inappropriate content, and leetspeak patterns like 'sh1t', 'f4ck', '@ss', etc. Uses comprehensive multi-layer detection system.",
	 *     @OA\RequestBody(
	 *         required=true,
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(
	 *                     property="nickname",
	 *                     type="string",
	 *                     format="text",
	 *                     minLength=3,
	 *                     maxLength=20,
	 *                     pattern="^[a-zA-Z0-9_-]+$",
	 *                     description="Nickname to validate (alphanumeric, underscore, and hyphen only)"
	 *                 ),
	 *             ),
	 *             @OA\Examples(
	 *                 example="valid_nickname",
	 *                 summary="Valid nickname",
	 *                 value={"nickname": "testuser123"}
	 *             ),
	 *             @OA\Examples(
	 *                 example="leetspeak_profanity",
	 *                 summary="Leetspeak profanity that will be detected",
	 *                 value={"nickname": "test123sh1t"}
	 *             ),
	 *             @OA\Examples(
	 *                 example="symbol_substitution",
	 *                 summary="Symbol substitution profanity",
	 *                 value={"nickname": "user@ss123"}
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Nickname is valid and available",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="success", type="boolean", example=true),
	 *             @OA\Property(property="message", type="string", example="Nickname available"),
	 *             @OA\Property(property="data", type="array", @OA\Items())
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Nickname contains inappropriate content",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="valid", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="Nickname contains inappropriate content"),
	 *             @OA\Property(property="nickname", type="string", example="test123sh1t"),
	 *             @OA\Property(property="filtered_nickname", type="string", example="test123****"),
	 *             @OA\Property(
	 *                 property="bad_words_found",
	 *                 type="array",
	 *                 @OA\Items(type="string"),
	 *                 example={"sh1t"}
	 *             ),
	 *             @OA\Property(property="detection_method", type="string", example="leetspeak_detection"),
	 *             @OA\Property(property="normalized_text", type="string", example="test123shit", description="Text after leetspeak normalization (if applicable)")
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *         description="Validation error - Invalid nickname format",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="valid", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="Invalid nickname format"),
	 *             @OA\Property(
	 *                 property="errors",
	 *                 type="object",
	 *                 @OA\Property(
	 *                     property="nickname",
	 *                     type="array",
	 *                     @OA\Items(type="string"),
	 *                     example={"The nickname must be at least 3 characters."}
	 *                 )
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=500,
	 *         description="Internal server error",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="success", type="boolean", example=false),
	 *             @OA\Property(property="message", type="string", example="An error occurred while validating nickname")
	 *         )
	 *     )
	 * )
	 */
	public function validateNickname(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), [
				'nickname' => 'required|string|min:3|max:20|regex:/^[a-zA-Z0-9_-]+$/|unique:users,display_name'
			], [
				'nickname.regex' => 'Nickname may only contain letters and numbers.'
			]);

			if ($validator->fails()) {
				return ApiResponse::validation($validator->errors());
			}

            $nickname = $request->nickname;

            $customBadWords = [
                ''
            ];

            $profanityDetector = new ProfanityDetector($customBadWords);

			if ($profanityDetector->check($nickname)) {
		        $validator->getMessageBag()->add('nickname', 'Nickname contains inappropriate language.');
		        return ApiResponse::validation($validator->errors());
		    }

			return ApiResponse::success(null, 'Nickname available');
		} catch (\Exception $e) {
			return ApiResponse::error($e->getMessage(), 500);
		}
	}

	private function generateVerificationCode($length = 6)
	{

		return '123456'; //for development

		// Generate a random 6-digit code
		return str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT);
	}

	private function sendVerificationCode($code, $phoneNumber)
	{
		// Send verification code using your chosen service (e.g., Twilio)
		$client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
		$client->messages->create(
			$phoneNumber,
			[
				'from' => env('TWILIO_NUMBER'),
				'body' => "Your verification code is: $code",
			]
		);
	}

	private function generateRandomPassword($length = 10)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=';
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $password;
	}
}
