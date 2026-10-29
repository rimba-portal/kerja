<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_forms', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('org_unit_id')->index()->constrained('org_units');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('provider')->default('packstub')->index();
            $table->string('provider_key')->nullable()->index();
            $table->string('display_mode')->default('single');
            $table->json('schema')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['provider', 'provider_key']);
        });
        Schema::create('work_workflows', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('org_unit_id')->index()->constrained('org_units');
            $table->text('description')->nullable();
            $table->string('lifecycle_code')->nullable()->index();
            $table->string('lifecycle_phase_code')->nullable()->index();
            $table->string('provider')->default('packstub')->index();
            $table->string('provider_key')->index();
            $table->string('provider_reference')->nullable()->index();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_key']);
        });
        Schema::create('work_offerings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('org_unit_id')->index()->constrained('org_units');
            $table->string('form_code')->nullable();
            $table->string('workflow_code')->nullable();
            $table->string('subject_type')->nullable()->index();
            $table->string('subject_creation_mode')->default('create');
            $table->json('input_mapping')->nullable();
            $table->json('context_mapping')->nullable();
            $table->json('defaults')->nullable();
            $table->string('visibility')->default('team');
            $table->string('availability')->default('available');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->foreign('form_code')->references('code')->on('work_forms')->nullOnDelete();
            $table->foreign('workflow_code')->references('code')->on('work_workflows')->nullOnDelete();
        });
        Schema::create('work_offering_executions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('offering_code')->index();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->index()->constrained('staff')->nullOnDelete();
            $table->foreignId('org_unit_id')->nullable()->index()->constrained('org_units')->nullOnDelete();
            $table->foreignId('org_team_id')->nullable()->index()->constrained('org_teams')->nullOnDelete();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->string('workflow_instance_type')->nullable();
            $table->string('workflow_instance_id')->nullable();
            $table->string('form_submission_type')->nullable();
            $table->string('form_submission_id')->nullable();
            $table->string('status')->index();
            $table->json('input_snapshot')->nullable();
            $table->json('context_snapshot')->nullable();
            $table->json('mapped_snapshot')->nullable();
            $table->json('result_snapshot')->nullable();
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['workflow_instance_type', 'workflow_instance_id']);
            $table->foreign('offering_code')->references('code')->on('work_offerings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_offering_executions');
        Schema::dropIfExists('work_offerings');
        Schema::dropIfExists('work_workflows');
        Schema::dropIfExists('work_forms');
    }
};
