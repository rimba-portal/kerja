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

        Schema::create('work_packages', function (Blueprint $table): void {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

        Schema::create('checklists', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('work_package_id')->constrained();

            $table->string('name');

            $table->integer('sort')->default(0);

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('checklist_id')->constrained();
            $table->foreignId('role_id')->constrained();

            $table->string('description');

            $table->boolean('is_active')->default(true);

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

        Schema::create('work_package_instances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('work_package_id')->constrained();

            $table->string('status')->default('active');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

        Schema::create('checklist_instances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('work_package_instance_id')->constrained();
            $table->foreignId('checklist_id')->constrained();

            $table->string('status')->default('pending');

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

        Schema::create('task_instances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('checklist_instance_id')->constrained();
            $table->foreignId('task_id')->constrained();

            $table->foreignId('assigned_to_id')->nullable()->constrained('staff');
            $table->foreignId('completed_by_id')->nullable()->constrained('staff');

            $table->boolean('is_completed')->default(false);

            $table->timestamp('completed_at')->nullable();

            $table->json('attributes')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_instances');
        Schema::dropIfExists('checklist_instances');
        Schema::dropIfExists('work_package_instances');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('checklists');
        Schema::dropIfExists('work_packages');
    }
};
