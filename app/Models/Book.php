<?php

namespace App\Models;

use App\QueryFilters\Sort;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pipeline\Pipeline;
use Laravel\Scout\Searchable;

class Book extends Model
{
    use HasFactory, HasUUID, Searchable;

    protected $fillable = [
        'title', 'author', 'isbn', 'description', 'publisher', 'published_on',
    ];

    protected $casts = [
        'published_on' => 'date',
    ];

    public function toSearchableArray(): array
    {
        return ['title', 'author', 'isbn', 'description', 'publisher'];
    }

    public static function getBooks()
    {
        return app(Pipeline::class)
                ->send(Book::query())
                ->through([
                    Sort::class,
                ])->thenReturn();
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'book_id', 'uuid');
    }
}
