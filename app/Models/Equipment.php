<?php

namespace App\Models;

use App\QueryFilters\Sort;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        return ['name', 'manufacturer', 'description', 'serial_number', 'model_number'];
    }
}
