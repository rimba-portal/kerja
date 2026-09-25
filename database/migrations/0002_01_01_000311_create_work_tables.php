<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            config('bites.sipoc.tables.workflow_instances', 'sipoc_workflow_instances'),
            function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                $table->string('workflow_slug')->index();
                $table->unsignedInteger('workflow_version')->default(1);
                $table->json('definition_snapshot');

                $table->nullableMorphs('subject');
                $table->nullableMorphs('initiator');

                $table->string('current_workpackage_slug')->nullable();
                $table->string('status')->default('draft')->index();
                $table->json('context')->nullable();

                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->text('failure_reason')->nullable();

                $table->timestamps();

                $table->index(
                    ['workflow_slug', 'workflow_version'],
                    'sipoc_workflow_definition_index'
                );
            }
        );

        Schema::create(
            config('bites.sipoc.tables.tasks', 'sipoc_tasks'),
            function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                $table->foreignId('workflow_instance_id')
                    ->constrained(
                        config(
                            'sipoc.tables.workflow_instances',
                            'sipoc_workflow_instances'
                        )
                    )
                    ->cascadeOnDelete();

                $table->string('workpackage_slug')->index();
                $table->json('workpackage_snapshot');

                $table->string('name');
                $table->string('execution_type')->index();
                $table->string('activity_type')->index();
                $table->string('business_object');
                $table->string('actor');

                $table->nullableMorphs('assignee');

                $table->unsignedInteger('sequence')->default(1);
                $table->unsignedInteger('attempt')->default(1);
                $table->string('status')->default('pending')->index();

                $table->json('suppliers')->nullable();
                $table->json('inputs')->nullable();
                $table->json('outputs')->nullable();
                $table->json('customers')->nullable();

                $table->json('payload')->nullable();
                $table->json('result')->nullable();

                $table->string('handler')->nullable();
                $table->string('trigger_event')->nullable();

                $table->timestamp('ready_at')->nullable();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('waiting_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->timestamp('due_at')->nullable();

                $table->text('failure_reason')->nullable();
                $table->timestamps();

                $table->index(
                    ['workflow_instance_id', 'workpackage_slug', 'sequence'],
                    'sipoc_task_execution_index'
                );

                $table->index(
                    ['assignee_type', 'assignee_id', 'status'],
                    'sipoc_task_assignee_inbox'
                );

                $table->index(
                    ['execution_type', 'status'],
                    'sipoc_task_executor_queue'
                );
            }
        );

        Schema::create(
            config('bites.sipoc.tables.artifacts', 'sipoc_artifacts'),
            function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();

                $table->foreignId('workflow_instance_id')
                    ->constrained(
                        config(
                            'sipoc.tables.workflow_instances',
                            'sipoc_workflow_instances'
                        )
                    )
                    ->cascadeOnDelete();

                $table->foreignId('produced_by_task_id')
                    ->nullable()
                    ->constrained(
                        config('bites.sipoc.tables.tasks', 'sipoc_tasks')
                    )
                    ->nullOnDelete();

                $table->string('key')->index();
                $table->string('name');
                $table->string('type')->default('data')->index();
                $table->json('value')->nullable();

                $table->nullableMorphs('record');
                $table->json('metadata')->nullable();

                $table->timestamp('produced_at');
                $table->timestamps();

                $table->index(
                    ['workflow_instance_id', 'key'],
                    'sipoc_artifact_lookup'
                );
            }
        );

        Schema::create(
            config('bites.sipoc.tables.transitions', 'sipoc_transitions'),
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('workflow_instance_id')
                    ->constrained(
                        config(
                            'sipoc.tables.workflow_instances',
                            'sipoc_workflow_instances'
                        )
                    )
                    ->cascadeOnDelete();

                $table->foreignId('from_task_id')
                    ->nullable()
                    ->constrained(
                        config('bites.sipoc.tables.tasks', 'sipoc_tasks')
                    )
                    ->nullOnDelete();

                $table->foreignId('to_task_id')
                    ->nullable()
                    ->constrained(
                        config('bites.sipoc.tables.tasks', 'sipoc_tasks')
                    )
                    ->nullOnDelete();

                $table->string('from_workpackage_slug')->nullable();
                $table->string('to_workpackage_slug')->nullable();
                $table->string('event')->index();

                $table->nullableMorphs('actor');

                $table->json('payload')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('performed_at');

                $table->timestamps();

                $table->index(
                    ['workflow_instance_id', 'performed_at'],
                    'sipoc_transition_timeline'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            config('bites.sipoc.tables.transitions', 'sipoc_transitions')
        );

        Schema::dropIfExists(
            config('bites.sipoc.tables.artifacts', 'sipoc_artifacts')
        );

        Schema::dropIfExists(
            config('bites.sipoc.tables.tasks', 'sipoc_tasks')
        );

        Schema::dropIfExists(
            config(
                'sipoc.tables.workflow_instances',
                'sipoc_workflow_instances'
            )
        );
    }
};
