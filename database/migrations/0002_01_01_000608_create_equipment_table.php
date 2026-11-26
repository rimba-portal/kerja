<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hierarchy_scope_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('state')->nullable();
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
