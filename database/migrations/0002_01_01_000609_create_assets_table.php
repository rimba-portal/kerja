<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('inventory_number')->nullable()->index();
            $table->string('main_asset_number')->nullable()->index();
            $table->string('asset_class')->nullable();
            $table->text('description')->nullable();
            $table->text('description2')->nullable();
            $table->string('cost_center')->nullable()->index();
            $table->string('group')->nullable();
            $table->string('serial_number')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
