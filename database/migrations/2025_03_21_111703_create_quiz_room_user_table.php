<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_room_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_room_id')->constrained('quiz_rooms')->casecadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->casecadeOnDelete();
            $table->integer('role')->default(1); // participant, host
            $table->integer('team')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_room_user');
    }
};
