<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_equipment_table.php

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
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            $table->json('attributes')->nullable();

            $table->timestamps();

            $table->index('hierarchy_scope_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
