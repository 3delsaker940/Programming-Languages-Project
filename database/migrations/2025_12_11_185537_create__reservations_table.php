<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('_reservations', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignID('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignID('apartment_id')->constrained('apartments')->cascadeOnDelete();
            $table->enum('status', ['pending','confirmed','cancelled'])->default('confirmed');
            $table->index(['apartment_id', 'start_at', 'end_at']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('_reservations');
    }
};
