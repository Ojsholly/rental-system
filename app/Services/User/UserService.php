<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserService extends Service
{
    /**
     * Create a new user account.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $password = Hash::make($data['password']);
        data_set($data, 'password', $password);

        return User::create($data);
    }

    /**
     * Fetch all users that match a given query.
     *
     * @param array $params
     * @param array $relations
     * @param array $pagination
     * @return mixed
     */
    public function getUsers(array $params = [], array $relations = [], array $pagination = []): mixed
    {
        return User::getUsers()->with($relations)->where($params)
            ->paginate(data_get($pagination, 'perPage', 50));
    }

    /**
     * Retrieve a user account by their uuid.
     *
     * @param string $uuid
     * @param array $relations
     * @return User
     * @throws Throwable
     */
    public function getUser(string $uuid, array $relations = []): User
    {
        return Cache::remember('user.' . $uuid, now()->addDays(3), function () use ($uuid, $relations) {
            $user = User::findByUuid($uuid);

            throw_if(!$user, new ModelNotFoundException("User not found.", ResponseAlias::HTTP_NOT_FOUND));

            $user->load($relations);

            return $user;
        });
    }

    /**
     * Update a user account.
     *
     * @param array $data
     * @param string $uuid
     * @return User
     * @throws Throwable
     */
    public function updateUser(array $data, string $uuid): User
    {
        $user = $this->getUser($uuid);

        $user->update($data);

        $user->refresh();

        Cache::put('user.' . $uuid, $user, now()->addDays(3));

        return $user;
    }

    /**
     * @param string $uuid
     * @return bool
     * @throws Throwable
     */
    public function deleteUser(string $uuid): bool
    {
        $user = $this->getUser($uuid);

        $user->delete();

        Cache::forget('user.' . $uuid);

        return $user->trashed();
    }
}
