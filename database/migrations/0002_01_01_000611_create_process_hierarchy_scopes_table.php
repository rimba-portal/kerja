<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_process_hierarchy_scopes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'process_hierarchy_scopes',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('process_id')
                    ->constrained('processes')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->foreignId('hierarchy_scope_id')
                    ->constrained('locations')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->string('scope_level')
                    ->default('Work Unit');

                $table->text('description')->nullable();
                $table->json('attributes')->nullable();

                $table->timestamps();

                $table->unique(
                    ['process_id', 'hierarchy_scope_id'],
                    'process_location_scope_unique',
                );

                $table->index('process_id');
                $table->index('hierarchy_scope_id');
                $table->index('scope_level');
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('process_hierarchy_scopes');
    }
};
