<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, HasUUID, SoftDeletes;

    protected $fillable = [
        'name', 'manufacturer', 'description', 'serial_number', 'model_number', 'image',
    ];

    protected $table = 'equipments';
}
