<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use lluminate\Support\Collection;

use App\Models\AuthSMSToken;
use App\Models\User;
use App\Models\Option;
use App\Helpers\smsRuHelper;
use Dirape\Token\Token;
use App\Helpers\stdObject;
use App\Helpers\BitrixApi;
use App\Helpers\SendPulse;
use App\Helpers\Geo;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use stdClass;

/**
 * Crypt::encryptString(serialize([
 *      '_token' => base64_encode( (new Token())->Unique('users_token', 'token', '60') ),
 *      'type' => 'register',
 * ]))
 * Template Encrypt
 */

class AuthController extends Controller
{
    use AuthorizesRequests;

    protected $redirectTo = '';
    protected $auth;
    protected $errorMsg = '';
    protected $tokenUser;
    protected $authUser;
    protected $bookId;

    protected $bitrix24;
    protected $geo;


    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->bitrix24 = new BitrixApi('043rdb1o3rqtfvvy');
        $this->smsRu = new smsRuHelper(ENV('SMS_RU_TOKEN'));
        $this->sendPulse = new SendPulse;
        $this->bookId = Option::getValue('bookIdRegister');
        $this->geo = new Geo;
    }

    /**
     * @return JsonResponse
     */
    public function authorizeToken(Request $request)
    {
        if ($request->has('noSMS')) 
        {
            return $this->authorizeLogin($request);
        }
        if ($request->headers->has('Authorize-Validation')) {
            $authorizeValidation = json_decode(
                Crypt::decryptString(
                    $request->header('Authorize-Validation')
                )
            );
            if (
                isset($authorizeValidation->token)
                && empty($authorizeValidation->token) == false
            ) {
                if ($request->has('passphrase')) {
                    if (
                        $this->validateSMSToken(
                            $authorizeValidation->token,
                            preg_replace('![^0-9]+!', '', $request->passphrase)
                        )
                    ) {
                        try {
                            $token = $this->auth->fromUser($this->authUser);

                            $this->tokenUser->is_verified = true;
                            $this->tokenUser->save();

                            if ($this->authUser->is_registration == false) {

                                $this->sendPulse->addEmails($this->bookId, [
                                    [
                                        'email' => $this->authUser->email,
                                        'variables' => [
                                            'phone' => $this->authUser->phone,
                                            'name' => $this->authUser->name,
                                        ]
                                    ]
                                ]);
                                $this->authUser->is_registration = true;
                                $this->authUser->save();
                            }

                            return response()->json([
                                'success' => true,
                                'user' => $this->authUser,
                                'token' => $token
                            ], 200);
                        } catch (JWTException $e) {
                            return response()->json([
                                'success' => false,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    return response()->json([
                        'success' => false,
                        'error' => $this->errorMsg
                    ], 200);
                }
                return response()->json([
                    'success' => false,
                    'error' => 'Введите код!'
                ], 422);
            }
            return response()->json([
                'success' => false,
                'redirect' => '/login'
            ], 302);
        }
    }

    /**
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        $Response = new stdObject([
            'success' => false,
            'token' => null,
            'errors' => ''
        ]);

        if (!$validator->fails()) {
            $smsData = new stdClass();
            $smsToken = $this->generateSMStoken(false);
            $phone = preg_replace('![^0-9]+!', '', $request->phone);

            $smsData->to = $phone;
            $smsData->text = $smsToken->passphrase;

            $smsResponse = $this->smsRu->send_one($smsData);

            if ($smsResponse->status == 'OK') {
                $smsToken->response = [
                    'status' => 'OK',
                    "status_code" => $smsResponse->status_code,
                    'sms_id' => $smsResponse->sms_id,
                    'cost' => $smsResponse->cost
                ];

                $this->geo->get($request);
                $user = $this->createUser($request);
                $smsToken->user_id = $user->id;

                /**
                 * Интеграция Bitrix24
                 */
                $this->bitrix24
                    ->lead('register', $request->header('referer')
                        ?? $request->input('referer')
                        ?? 'http://lk.leadz.monster')
                    ->utm($request->all())
                    ->field('NAME', $user->name)
                    ->field('PHONE', $user->phone, 'WORK')
                    ->add();

                $Response->success = true;
                $Response->token = Crypt::encryptString(
                    json_encode([
                        'token' => $smsToken->token,
                        'created_at' => date("Y-m-d H:i:s")
                    ])
                );
                $this->saveSMSToken($smsToken);
            } else {
                $smsToken->response = [
                    'status' => 'ERROR',
                    "status_code" => $smsResponse->status_code,
                    'status_text' => $smsResponse->status_text
                ];
                $Response->success = false;
                $Response->errors = ['phone' => 'Ошибка! ' . $smsResponse->status_text];
            }

            return response()->json($Response, 200);
        }

        $Response->success = false;
        $Response->errors = $validator->errors();

        return response()->json($Response, 200);
    }

    /**
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $Response = new stdObject([
            'success' => false,
            'token' => null,
            'errors' => ''
        ]);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            $Response->success = false;
            $Response->errors = "Вы заблокированы";
        } else {

            $validator = $this->validatorLogin($request->all());

            if (!$validator->fails()) {

                $smsData = new stdClass();
                $smsToken = $this->generateSMStoken(false);
                $phone = preg_replace('![^0-9]+!', '', $request->phone);

                if ($this->hasUser([
                    'phone' => $request->phone,
                    'is_block' => false
                ])) {
                    $smsData->to = $phone;
                    $smsData->text = $smsToken->passphrase;

                    $smsResponse = $this->smsRu->send_one($smsData);

                    if ($smsResponse->status == 'OK') {
                        $smsToken->response = [
                            'status' => 'OK',
                            "status_code" => $smsResponse->status_code,
                            'sms_id' => $smsResponse->sms_id,
                            'cost' => $smsResponse->cost
                        ];

                        $smsToken->user_id = $this->authUser->id;

                        $Response->success = true;
                        $Response->token = Crypt::encryptString(
                            json_encode([
                                'token' => $smsToken->token,
                                'created_at' => date("Y-m-d H:i:s")
                            ])
                        );
                        $this->saveSMSToken($smsToken);
                    } else {
                        $smsToken->response = [
                            'status' => 'ERROR',
                            "status_code" => $smsResponse->status_code,
                            'status_text' => $smsResponse->status_text
                        ];
                        $Response->success = false;
                        $Response->errors = 'Ошибка! ' . $smsResponse->status_text;
                    }
                } else {
                    $Response->success = false;
                    $Response->errors = 'Такого номера телефона не существует!';
                }
            } else {
                $Response->success = false;
                $Response->errors = $validator->errors();
            }
        }
        return response()->json($Response, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function authorizeLogin(Request $request)
    {
        $Response = new stdObject([
            'success' => false,
            'token' => null,
            'errors' => ''
        ]);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            $Response->success = false;
            $Response->errors = "Вы заблокированы";
        } else {

            $validator = $this->validatorLogin($request->all());

            if (!$validator->fails()) {

                $phone = preg_replace('![^0-9]+!', '', $request->phone);

                if ($this->hasUser([
                    'phone' => $request->phone,
                    'is_block' => false
                ])) {
                    $credentials = $request->only(['phone', 'password']);
                    try {
                        $token = $this->auth->attempt($credentials); //->fromUser($this->authUser);

                        return response()->json([
                            'success' => true,
                            'token' => $token
                        ], 200);
                    } catch (JWTException $e) {
                        return response()->json([
                            'success' => false,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    $Response->success = false;
                    $Response->errors = 'Такого номера телефона не существует!';
                }
            } else {
                $Response->success = false;
                $Response->errors = $validator->errors();
            }
        }
        return response()->json($Response, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerLogin(Request $request)
    {
        $validator = $this->validator($request->all());
        $Response = new stdObject([
            'success' => false,
            'token' => null,
            'errors' => ''
        ]);

        if (!$validator->fails()) {            
            $this->geo->get($request);
            $user = $this->createUser($request);
            
            try {
                $token = $this->auth->fromUser($user);

                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'token' => $token
                ], 200);
            } catch (JWTException $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
            return response()->json($Response, 200);
        }
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        $this->auth->invalidate();

        return response()->json([
            'success' => true
        ], 200);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:50'],
            'phone' => [
                'required',
                'regex:/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/',
                'string',
                'max:25',
                'unique:users'
            ],
            'email' => [
                'required',
                'unique:users'
            ]
        ], [
            'phone.required' => 'Обязательно введите телефон',
            'phone.regex' => 'Введите коректно телефон',
            'phone.max' => 'Максимальное количество символов 25',
            'phone.unique' => 'Такой телефон уже существует',
            'name.required' => 'Обязательно введите имя',
            'name.max' => 'Максимальное количество символов 50',
            'email.required' => 'Обязательно введите почту',
            'email.unique' => 'Такая почта уже существует'
        ]);
    }

    protected function validatorLogin(array $data)
    {
        return Validator::make($data, [
            'phone' => [
                'required',
                'regex:/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/',
                'string',
                'max:25',
            ]
        ], [
            'phone.required' => 'Обязательно введите телефон',
            'phone.regex' => 'Введите коректно телефон',
            'phone.max' => 'Максимальное количество символов 25',
            'name.required' => 'Обязательно введите имя',
            'name.max' => 'Максимальное количество символов 50'
        ]);
    }

    protected function getRandManager()
    {
        return User::where('role', 'ROLE_MANAGER')
            ->inRandomOrder()->first();
    }

    protected function hasUser(array $data)
    {
        $user = User::where($data)->first();

        if ($user) {
            $this->authUser = $user;
            return true;
        }
        return false;
    }

    protected function generateSMStoken(bool $is_verified = false)
    {
        $token = (new Token())->Unique('users_token', 'token', '60');
        $passphrase = (new Token())->randomNumber(6);

        return new stdObject([
            'passphrase' => $passphrase,
            'token' => $token,
            'user_id' => null,
            'is_verified' => $is_verified,
            'response' => [],
        ]);
    }

    protected function saveSMSToken(stdObject $authData)
    {
        return AuthSMSToken::create([
            'passphrase' => $authData->passphrase,
            'token' => $authData->token,
            'user_id' => $authData->user_id,
            'is_verified' => $authData->is_verified,
            'response' => json_encode($authData->response),
        ]);
    }

    protected function validateSMSToken(string $token, string $passphrase)
    {
        $tokenUser = AuthSMSToken::with('user')
            ->where('token', $token)
            ->where('passphrase', $passphrase)
            ->where('is_verified', false)
            ->whereDate('created_at', '<=', date("Y-m-d"))
            ->first();
        if ($tokenUser) {
            $this->tokenUser = $tokenUser;
            $this->authUser = $tokenUser->user;
            return true;
        }
        $this->errorMsg = 'Не верный код!';
        return false;
    }

    protected function createUser(Request $request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => is_null($request->password) ? null : Hash::make($request->password),
            'role'  =>  'ROLE_USER',
            'category_id' => $request->category_id,
            'is_demo' => true,
            'balance' => 0.0,
            'bonus' => 0.0,
            'region_id' => $this->geo->region->id ?? 1,
            'manager_id' => null, //$this->getRandManager()->id ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'was_online' => Carbon::now()
        ]);
    }
}
