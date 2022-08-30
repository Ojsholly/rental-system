<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Services\Equipment\EquipmentService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function equipment(array $attributes = [])
    {
        return Equipment::factory()->create($attributes);
    }

    public function equipmentData(): array
    {
        return [
            'name' => fake()->unique()->sentence(),
            'description' => fake()->paragraph(),
            'manufacturer' => fake()->company(),
            'model_number' => fake()->word(),
            'serial_number' => fake()->word(),
        ];
    }

    public function testEquipmentCreationValidation()
    {
        $this->postJson(route('admin.equipments.store'), [])
                ->assertUnprocessable()
                ->assertJsonValidationErrors(['name', 'description', 'manufacturer', 'model_number', 'serial_number'])
                ->assertJsonStructure([
                    'message', 'errors' => [
                        'name', 'description', 'manufacturer', 'model_number', 'serial_number',
                    ],
                ]);
    }

    public function testEquipmentServiceCreateReturnsEquipment()
    {
        $data = $this->equipmentData();

        $equipment = (new EquipmentService())->create($data);

        $this->assertInstanceOf(Equipment::class, $equipment);
    }

    public function testEquipmentCreation()
    {
        $equipmentsCount = Equipment::count();

        $data = $this->equipmentData();

        $equipmentsCount = Equipment::count();

        $this->postJson(route('admin.equipments.store'), $data)
                ->assertCreated()
                ->assertJsonStructure([
                    'status', 'message', 'data' => [
                        'id', 'uuid', 'name', 'description', 'manufacturer', 'model_number', 'serial_number', 'created_at', 'updated_at', 'deleted_at',
                    ],
                ]);

        $this->assertEquals($equipmentsCount + 1, Equipment::count());
    }

    public function testEquipmentServiceUpdateReturnsEquipment()
    {
        $data = $this->equipmentData();
        $equipment = $this->equipment();

        $updatedEquipment = (new EquipmentService())->updateEquipment($data, $equipment->uuid);
        $this->assertInstanceOf(Equipment::class, $updatedEquipment);
    }

    public function testEquipmentUpdate()
    {
        $data = $this->equipmentData();
        $equipment = $this->equipment();
        $this->putJson(route('admin.equipments.update', $equipment->uuid), $data)
                ->assertOk()
                ->assertJsonStructure([
                    'status', 'message', 'data' => [
                        'id', 'uuid', 'name', 'description', 'manufacturer', 'model_number', 'serial_number', 'created_at', 'updated_at', 'deleted_at',
                    ],
                ]);
    }

    public function testEquipmentUpdateWithInvalidUuid()
    {
        $data = $this->equipmentData();
        $equipment = $this->equipment();

        $this->putJson(route('admin.equipments.update', ['equipment' => Str::uuid()]), $data)
                ->assertNotFound()
                ->assertJsonStructure(['status', 'message'])
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Requested equipment not found.',
                ]);
    }

    public function testEquipmentServiceDeleteReturnsBool()
    {
        $equipment = $this->equipment();
        $deletedEquipment = (new EquipmentService())->deleteEquipment($equipment->uuid);

        $this->assertTrue($deletedEquipment);
    }

    public function testEquipmentDelete()
    {
        $equipment = $this->equipment();
        $this->deleteJson(route('admin.equipments.destroy', $equipment->uuid))
                ->assertOk()
                ->assertJsonStructure([
                    'status', 'message', 'data' => [],
                ]);
    }
}
