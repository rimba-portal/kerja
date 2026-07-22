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
        Schema::disableForeignKeyConstraints();

        Schema::create('work_packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('checklists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_package_id')->constrained('workpackages');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('description');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('work_package_instances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_package_id')->constrained('work_packages');
            $table->string('status')->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('checklist_instances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_package_instance_id')->constrained('workpackageinstances');
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->string('status')->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('task_instances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('checklist_instance_id')->constrained('checklist_instances');
            $table->foreignId('task_id')->constrained('tasks');
            $table->foreignId('assigned_to_id')->nullable()->constrained('staff');
            $table->foreignId('completed_by_id')->nullable()->constrained('staff');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
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
