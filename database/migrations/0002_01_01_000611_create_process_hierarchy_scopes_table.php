<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_hierarchy_scopes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('process_id')
                ->constrained('processes')
                ->cascadeOnDelete();
            $table->foreignId('hierarchy_scope_id')
                ->constrained('locations')
                ->cascadeOnDelete();
            $table->string('scope_level')->default('Work Unit');
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->unique(
                ['process_id', 'hierarchy_scope_id'],
                'process_hierarchy_scope_unique'
            );
            $table->index(
                ['hierarchy_scope_id', 'scope_level'],
                'process_scope_level_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_hierarchy_scopes');
    }
};
