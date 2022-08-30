<?php

namespace App\Models;

use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory, HasUUID;

    protected $fillable = [
        'user_id', 'book_id', 'equipment_id', 'due_date', 'price', 'book_returned_at', 'equipment_returned_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id', 'uuid');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'uuid');
    }

}
