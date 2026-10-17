<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_flow_instances', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workflow_id')
                ->constrained('work_flows')
                ->restrictOnDelete();
            $table->unsignedInteger('workflow_version');
            $table->nullableMorphs('initiator');
            $table->nullableMorphs('subject');
            $table->foreignId('current_workflow_step_id')
                ->nullable()
                ->constrained('work_flow_steps')
                ->nullOnDelete();
            $table->string('status')->default('active')->index();
            $table->json('form_data')->nullable();
            $table->json('traveller')->nullable();
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('failed_at')->nullable();
        });

        Schema::create('work_lifecycle_trackings', function (Blueprint $table): void {
            $table->id();
            $table->morphs('subject');
            $table->foreignId('business_object_id')
                ->constrained('work_business_objects')
                ->restrictOnDelete();
            $table->foreignId('lifecycle_id')
                ->constrained('work_lifecycles')
                ->restrictOnDelete();
            $table->foreignId('lifecycle_phase_id')
                ->constrained('work_lifecycle_phases')
                ->restrictOnDelete();
            $table->foreignId('last_workflow_instance_id')
                ->nullable()
                ->constrained('work_flow_instances')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['subject_type', 'subject_id', 'lifecycle_id'],
                'work_lifecycle_trackings_subject_unique'
            );
        });

        Schema::create('work_tasks', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workflow_instance_id')
                ->constrained('work_flow_instances')
                ->cascadeOnDelete();
            $table->foreignId('workflow_step_id')
                ->constrained('work_flow_steps')
                ->restrictOnDelete();
            $table->foreignId('work_package_id')
                ->constrained('work_packages')
                ->restrictOnDelete();
            $table->unsignedInteger('work_package_version');
            $table->nullableMorphs('assignee');
            $table->string('status')->default('ready')->index();
            $table->json('payload')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_at')->nullable();
        });

        Schema::create('work_artifacts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workflow_instance_id')
                ->constrained('work_flow_instances')
                ->cascadeOnDelete();
            $table->foreignId('task_id')
                ->nullable()
                ->constrained('work_tasks')
                ->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('type')->default('data');
            $table->json('value')->nullable();
            $table->nullableMorphs('record');
            $table->timestamp('produced_at');
            $table->timestamps();
        });

        Schema::create('work_task_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_instance_id')
                ->constrained('work_flow_instances')
                ->cascadeOnDelete();
            $table->foreignId('task_id')
                ->nullable()
                ->constrained('work_tasks')
                ->nullOnDelete();
            $table->foreignId('workflow_action_id')
                ->nullable()
                ->constrained('work_flow_actions')
                ->nullOnDelete();
            $table->nullableMorphs('actor');
            $table->string('event')->index();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'work_task_events',
            'work_artifacts',
            'work_tasks',
            'work_lifecycle_trackings',
            'work_flow_instances',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
