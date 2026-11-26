<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_assets_table.php

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
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            $table->json('attributes')->nullable();

            $table->timestamps();

            $table->index('equipment_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
