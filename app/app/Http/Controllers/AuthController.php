<?php

namespace App\Http\Controllers;
use App\Http\Request\User\UserLoginRequest;
use App\Http\Request\User\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];
        $user = $this->authRepository->register($userData);
        return response()->json([
            'data' => new UserResource($user),
            'message' => trans('User created successfully'),
        ], 201);
    }

    /**
     * Authenticate user and generate access token.
     *
     * @param UserLoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    }


}
