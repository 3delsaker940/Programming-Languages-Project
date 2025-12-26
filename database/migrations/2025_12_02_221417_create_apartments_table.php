<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('rooms')->index();
            $table->integer('bathrooms')->index();
            $table->enum('type', ['apartment', 'house', 'villa', 'otherwise']);
            $table->integer('area')->index();
            $table->decimal('price', 10, 2)->index();
            $table->enum('city', [
                'damascus',
                'rif_dimashq',
                'aleppo',
                'homs',
                'hama',
                'latakia',
                'tartus',
                'idlib',
                'deir_ez_zor',
                'raqqa',
                'al_hasakah',
                'daraa',
                'as_suwayda',
                'quneitra',
            ])->index();
            $table->enum('status', ['available', 'rented'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
