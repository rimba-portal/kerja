<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_activity_types', function (Blueprint $t): void {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('work_business_objects', function (Blueprint $t): void {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->string('model_class')->nullable();
            $t->text('description')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('work_packages', function (Blueprint $t): void {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->unsignedBigInteger('org_team_id')->index();
            $t->unsignedBigInteger('actor_job_role_id')->index();
            $t->foreignId('activity_type_id')->constrained('work_activity_types')->restrictOnDelete();
            $t->foreignId('business_object_id')->constrained('work_business_objects')->restrictOnDelete();
            $t->unsignedInteger('version')->default(1);
            $t->string('status')->default('draft')->index();
            $t->timestamps();
        });
        Schema::create('work_package_parties', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('work_package_id')->constrained('work_packages')->cascadeOnDelete();
            $t->string('side')->index();
            $t->nullableMorphs('party');
            $t->string('name');
            $t->unsignedInteger('sequence')->default(1);
            $t->timestamps();
        });
        Schema::create('work_package_payloads', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('work_package_id')->constrained('work_packages')->cascadeOnDelete();
            $t->string('direction')->index();
            $t->string('code');
            $t->string('name');
            $t->string('data_type')->default('mixed');
            $t->boolean('is_required')->default(true);
            $t->unsignedInteger('sequence')->default(1);
            $t->timestamps();
            $t->unique(['work_package_id', 'direction', 'code']);
        });
        Schema::create('work_flows', function (Blueprint $t): void {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->unsignedBigInteger('owning_org_team_id')->nullable()->index();
            $t->foreignId('business_object_id')->nullable()->constrained('work_business_objects')->nullOnDelete();
            $t->unsignedInteger('version')->default(1);
            $t->string('status')->default('draft')->index();
            $t->timestamps();
        });
        Schema::create('work_flow_initiators', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_id')->constrained('work_flows')->cascadeOnDelete();
            $t->unsignedBigInteger('org_team_id')->nullable();
            $t->unsignedBigInteger('job_role_id')->index();
            $t->timestamps();
        });
        Schema::create('work_flow_form_fields', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_id')->constrained('work_flows')->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->string('field_type');
            $t->text('help_text')->nullable();
            $t->text('default_value')->nullable();
            $t->boolean('is_required')->default(false);
            $t->unsignedInteger('sequence')->default(1);
            $t->timestamps();
            $t->unique(['workflow_id', 'code']);
        });
        Schema::create('work_flow_steps', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_id')->constrained('work_flows')->cascadeOnDelete();
            $t->foreignId('work_package_id')->constrained('work_packages')->restrictOnDelete();
            $t->string('code');
            $t->string('name');
            $t->unsignedInteger('sequence')->default(1);
            $t->boolean('is_start')->default(false);
            $t->timestamps();
            $t->unique(['workflow_id', 'code']);
        });
        Schema::create('work_flow_actions', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('workflow_step_id')->constrained('work_flow_steps')->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->string('style')->default('primary');
            $t->foreignId('target_workflow_step_id')->nullable()->constrained('work_flow_steps')->nullOnDelete();
            $t->string('completion_effect')->default('continue');
            $t->boolean('requires_confirmation')->default(false);
            $t->unsignedInteger('sequence')->default(1);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['work_flow_actions', 'work_flow_steps', 'work_flow_form_fields', 'work_flow_initiators', 'work_flows', 'work_package_payloads', 'work_package_parties', 'work_packages', 'work_business_objects', 'work_activity_types'] as $x) {
            Schema::dropIfExists($x);
        }
    }
};
