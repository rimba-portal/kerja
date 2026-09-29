<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_flow_instances', function (Blueprint $t): void {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('workflow_id')->constrained('work_flows')->restrictOnDelete();
            $t->unsignedInteger('workflow_version');
            $t->nullableMorphs('initiator');
            $t->nullableMorphs('subject');
            $t->foreignId('current_workflow_step_id')->nullable()->constrained('work_flow_steps')->nullOnDelete();
            $t->string('status')->default('active')->index();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->timestamp('failed_at')->nullable();
        });
        Schema::create('work_tasks', function (Blueprint $t): void {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('workflow_instance_id')->constrained('work_flow_instances')->cascadeOnDelete();
            $t->foreignId('workflow_step_id')->constrained('work_flow_steps')->restrictOnDelete();
            $t->foreignId('work_package_id')->constrained('work_packages')->restrictOnDelete();
            $t->unsignedInteger('work_package_version');
            $t->nullableMorphs('assignee');
            $t->string('status')->default('ready')->index();
            $t->json('payload')->nullable();
            $t->json('result')->nullable();
            $t->timestamps();
            $t->timestamp('assigned_at')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamp('due_at')->nullable();
        });
        Schema::create('work_artifacts', function (Blueprint $t): void {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('workflow_instance_id')->constrained('work_flow_instances')->cascadeOnDelete();
            $t->foreignId('task_id')->nullable()->constrained('work_tasks')->nullOnDelete();
            $t->string('code');
            $t->string('name');
            $t->string('type')->default('data');
            $t->json('value')->nullable();
            $t->nullableMorphs('record');
            $t->timestamp('produced_at');
            $t->timestamps();
        });
        Schema::create('work_task_events', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_instance_id')->constrained('work_flow_instances')->cascadeOnDelete();
            $t->foreignId('task_id')->nullable()->constrained('work_tasks')->nullOnDelete();
            $t->foreignId('workflow_action_id')->nullable()->constrained('work_flow_actions')->nullOnDelete();
            $t->nullableMorphs('actor');
            $t->string('event')->index();
            $t->json('payload')->nullable();
            $t->timestamp('occurred_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['work_task_events', 'work_artifacts', 'work_tasks', 'work_flow_instances'] as $x) {
            Schema::dropIfExists($x);
        }
    }
};
