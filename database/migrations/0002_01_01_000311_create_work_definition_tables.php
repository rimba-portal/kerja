<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_activity_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('work_business_objects', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('model_class')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('work_lifecycles', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('business_object_id')
                ->constrained('work_business_objects')
                ->restrictOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('work_lifecycle_phases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lifecycle_id')
                ->constrained('work_lifecycles')
                ->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('sequence')->default(1);
            $table->string('functional_role')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['lifecycle_id', 'sequence']);
        });

        Schema::create('work_packages', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // External Pihak/Jawat IDs. Do not create cross-package DB FKs.
            $table->unsignedBigInteger('org_team_id')->index();
            $table->unsignedBigInteger('actor_job_role_id')->index();

            $table->foreignId('activity_type_id')
                ->constrained('work_activity_types')
                ->restrictOnDelete();
            $table->foreignId('business_object_id')
                ->constrained('work_business_objects')
                ->restrictOnDelete();

            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });

        Schema::create('work_package_parties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_package_id')
                ->constrained('work_packages')
                ->cascadeOnDelete();
            $table->string('side')->index();
            $table->nullableMorphs('party');
            $table->string('name');
            $table->unsignedInteger('sequence')->default(1);
            $table->timestamps();
        });

        Schema::create('work_package_payloads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_package_id')
                ->constrained('work_packages')
                ->cascadeOnDelete();
            $table->string('direction')->index();
            $table->string('code');
            $table->string('name');
            $table->string('data_type')->default('mixed');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sequence')->default(1);
            $table->timestamps();

            $table->unique(['work_package_id', 'direction', 'code']);
        });

        Schema::create('work_flows', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('owning_org_team_id')->nullable()->index();

            $table->foreignId('business_object_id')
                ->nullable()
                ->constrained('work_business_objects')
                ->nullOnDelete();

            $table->foreignId('lifecycle_phase_id')
                ->nullable()
                ->constrained('work_lifecycle_phases')
                ->nullOnDelete();

            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('draft')->index();

            $table->string('lifecycle_transition_type')
                ->default('stay')
                ->index();

            $table->foreignId('next_lifecycle_phase_id')
                ->nullable()
                ->constrained('work_lifecycle_phases')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('work_flow_owners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')
                ->constrained('work_flows')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('job_role_id')->index();
            $table->timestamps();

            $table->unique(['workflow_id', 'job_role_id']);
        });

        Schema::create('work_flow_initiators', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')
                ->constrained('work_flows')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('org_team_id')->nullable()->index();
            $table->unsignedBigInteger('job_role_id')->index();
            $table->timestamps();

            $table->unique(
                ['workflow_id', 'org_team_id', 'job_role_id'],
                'work_flow_initiators_scope_unique'
            );
        });

        Schema::create('work_flow_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')
                ->constrained('work_flows')
                ->cascadeOnDelete();
            $table->foreignId('work_package_id')
                ->constrained('work_packages')
                ->restrictOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('step_type')->index();
            $table->unsignedInteger('sequence')->default(1);
            $table->boolean('is_start')->default(false);
            $table->timestamps();

            $table->unique(['workflow_id', 'code']);
        });

        Schema::create('work_flow_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_step_id')
                ->constrained('work_flow_steps')
                ->cascadeOnDelete();
            $table->string('action_type')->index();
            $table->foreignId('target_workflow_step_id')
                ->nullable()
                ->constrained('work_flow_steps')
                ->nullOnDelete();
            $table->string('terminal_status')->nullable()->index();
            $table->boolean('requires_confirmation')->default(false);
            $table->unsignedInteger('sequence')->default(1);
            $table->timestamps();

            $table->unique(
                ['workflow_step_id', 'action_type'],
                'work_flow_actions_step_type_unique'
            );
        });

        Schema::create('work_endpoints', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('base_url');
            $table->string('auth_profile')->nullable();
            $table->unsignedInteger('timeout_seconds')->default(30);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('work_hooks', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('operation')->index();
            $table->unsignedInteger('version')->default(1);
            $table->json('schema');
            $table->json('configuration');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('work_hook_bindings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hook_id')
                ->constrained('work_hooks')
                ->restrictOnDelete();
            $table->morphs('hookable');
            $table->string('hook_point')->index();
            $table->unsignedInteger('sequence')->default(1);
            $table->json('parameters')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();

            $table->unique(
                ['hookable_type', 'hookable_id', 'hook_point', 'sequence'],
                'work_hook_bindings_sequence_unique'
            );

            $table->unique(
                ['hook_id', 'hookable_type', 'hookable_id', 'hook_point'],
                'work_hook_bindings_hook_unique'
            );
        });
    }

    public function down(): void
    {
        foreach ([
            'work_hook_bindings',
            'work_hooks',
            'work_endpoints',
            'work_flow_actions',
            'work_flow_steps',
            'work_flow_initiators',
            'work_flow_owners',
            'work_flows',
            'work_package_payloads',
            'work_package_parties',
            'work_packages',
            'work_lifecycle_phases',
            'work_lifecycles',
            'work_business_objects',
            'work_activity_types',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
