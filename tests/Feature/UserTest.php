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
        $this->seed();
    }

    public function user(array $attributes = [])
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

    public function testUserRetrievalWithWrongId()
    {
        $this->getJson(route('admin.users.show', ['user' => Str::uuid()]))
                ->assertNotFound()
                ->assertJsonStructure(['status', 'message']);
    }

    public function testUserServiceGetReturnsUser()
    {
        $user_id = User::factory()->create()->uuid;

        $user = (new UserService())->getUser($user_id);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testUserRetrieval()
    {
        $user = $this->user();

        $this->getJson(route('admin.users.show', ['user' => $user->uuid]))
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data'])
                ->assertJsonFragment(['status' => 'success', 'message' => 'User retrieved successfully.'])
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'uuid' => $user->uuid,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'created_at' => $user->created_at->toDayDateTimeString(),
                        'updated_at' => $user->updated_at->diffForHumans(),
                        'deleted_at' => $user->deleted_at?->toDayDateTimeString(),
                    ]
                ]);
    }

    public function testUserUpdate()
    {
        $user = $this->user();

        $data = $this->userData();

        $this->putJson(route('admin.users.update', ['user' => $user->uuid]), $data)
                ->assertOk()
                ->assertJsonStructure(['status', 'message', 'data'])
                ->assertJsonFragment(['status' => 'success', 'message' => 'User account updated successfully.']);
    }
}
