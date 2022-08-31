<?php

namespace App\Models;

use App\QueryFilters\Sort;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pipeline\Pipeline;
use Laravel\Scout\Searchable;

class Equipment extends Model
{
    use HasFactory, HasUUID, SoftDeletes, Searchable;

    protected $fillable = [
        'name', 'manufacturer', 'description', 'serial_number', 'model_number', 'image',
    ];

    protected $table = 'equipments';

    public static function getEquipments()
    {
        return app(Pipeline::class)
            ->send(Equipment::query())
            ->through([
                Sort::class,
            ])->thenReturn();
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'manufacturer' => $this->manufacturer,
            'description' => $this->description,
            'serial_number' => $this->serial_number,
            'model_number' => $this->model_number,
        ];
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'equipment_id', 'uuid');
    }
}
