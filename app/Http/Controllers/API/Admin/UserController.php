<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getUsers();
        } catch (Throwable $exception) {
            report($exception);

            return response()->error('Error fetching users.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new UserResourceCollection($users), 'Users retrieved successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest  $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());
        } catch (Throwable $exception) {
            report($exception);

            return response()->error('Error creating user.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new UserResource($user), 'User created successfully.', ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->getUser($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }
            report($exception);

            return response()->error('Error fetching user.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new UserResource($user), 'User retrieved successfully.', ResponseAlias::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->only('name', 'email', 'phone', 'address');

            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user = $this->userService->updateUser($data, $id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }
            report($exception);

            return response()->error('Error updating user account.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->success(new UserResource($user), 'User account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(string $id)
    {
        try {
            $delete = $this->userService->deleteUser($id);
        } catch (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return response()->error($exception->getMessage(), $exception->getCode());
            }
            report($exception);

            return response()->error('Error deleting user.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return match ($delete) {
            true => response()->success([], 'User account deleted successfully.', ResponseAlias::HTTP_OK),
            false => response()->error('Error deleting user.', ResponseAlias::HTTP_BAD_REQUEST)
        };
    }
}
