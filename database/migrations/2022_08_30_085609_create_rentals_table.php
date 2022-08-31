<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignUUid('user_id');
            $table->foreignUUid('book_id')->nullable();
            $table->foreignUUid('equipment_id')->nullable();
            $table->date('due_date');
            $table->dateTime('book_returned_at')->nullable();
            $table->dateTime('equipment_returned_at')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->index(['user_id', 'book_id', 'equipment_id', 'due_date', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rentals');
    }
};
