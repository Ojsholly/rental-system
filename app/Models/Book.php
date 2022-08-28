<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, HasUUID;

    protected $fillable = [
      'title', 'author', 'isbn', 'description', 'publisher', 'published_on'
    ];
}
