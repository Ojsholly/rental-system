<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function user(array $attributes)
    {
        return User::factory()->create($attributes);
    }

    public function userData(): array
    {
        $password = Str::random(10);

        return [
            'name' => fake()->name,
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'password' => $password,
            'password_confirmation' => $password,
        ];
    }

    public function testUserCreationValidation()
    {
        $this->postJson(route('admin.users.store'), [])
                ->assertUnprocessable()
                ->assertJsonValidationErrors(['name', 'email', 'phone', 'address', 'password']);
    }

    public function testUserServiceCreateReturnsUser()
    {
        $data = $this->userData();

        $user = (new UserService())->createUser($data);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testUserCreation()
    {
        $password = Str::random(10);

        $usersCount = User::count();

        $data = $this->userData();

        $this->postJson(route('admin.users.store'), $data)
                ->assertCreated()
                ->assertJsonStructure(['status', 'message', 'data'])
                ->assertJsonFragment(['status' => 'success', 'message' => 'User created successfully.']);

        $this->assertDatabaseCount('users', $usersCount + 1);
        $this->assertDatabaseHas('users', [
            'name' => data_get($data, 'name'),
            'email' => data_get($data, 'email'),
            'phone' => data_get($data, 'phone'),
            'address' => data_get($data, 'address')
        ]);
    }
}
