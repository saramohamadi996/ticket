<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Request\User\CreateUserRequest;
use App\Http\Request\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get a list of users.
     *
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view', User::class);

        $users = User::paginate(10);
        return UserResource::collection($users);
    }

    /**
     * Create a new user.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'data' => new UserResource($user),
            'message' => trans('User created successfully'),
        ], 201);
    }

    /**
     * Update an existing user.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $userData = [
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ];
        $user->update($userData);
        return response()->json([
            'data' => new UserResource($user),
            'message' => trans('User updated successfully'),
        ]);
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        $this->userRepository->delete($user);
        return response()->json(['message' => trans('User deleted successfully')]);
    }
}




