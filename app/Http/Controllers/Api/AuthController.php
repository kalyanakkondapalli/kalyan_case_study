<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Exceptions\NoAccountFoundException;
use App\Exceptions\InvalidCredentialException;

class AuthController extends Controller
{
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Authenticate user.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse|mixed
     */
    public function login(LoginRequest $request): mixed
    {
        try {
            $user = $this->user->whereEmail($request->email)->first();

            if (!$user) {
                throw new NoAccountFoundException;
            }

            if (!Hash::check($request->password, $user->password)) {
                throw new InvalidCredentialException;
            }

            $data = [
                'message' => "You are successfully logged in!",
                'data'    => [
                    'user'         => $user,
                    'access_token' => $user->createToken($request->email)->plainTextToken,
                ],
            ];

            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * Logout given user.
     *
     * @return JsonResponse|mixed
     */
    public function logout(): mixed
    {
        try {
            request()->user()->currentAccessToken()->delete();

            return $this->successResponse([
                'message' => "You are successfully logged out.",
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * Register new user.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse|mixed
     */
    public function register(RegisterRequest $request): mixed
    {
        $data = $request->all();
        if($data['password']!==$data['password_confirmation']){
            return $this->errorResponse([
                'message' => "Password and confirm password should be same.",
            ]);
        } 

        unset($data['password_confirmation']);

        if(!empty($this->getByEmail($data['email']))){
            return $this->errorResponse([
                'message' => "User account already exist with same user.",
            ]);
        }  
        
        try {
            $data['password'] = bcrypt($data['password']);
            $user = $this->user->updateOrCreate($data);

            return $this->successResponse([
                'message' => "You are successfully registered with us.",
                'data'    => $user,
            ]);
        } catch (Exception $e) {
            return $this->errorMessage($e);
        }
    }

    /**
     * Get user by email
     * 
     * @param $email
     * @return User|empty
    */
    public function getByEmail(string $email): User | null
    {
        return $this->user->whereEmail($email)->first();
    }
}
