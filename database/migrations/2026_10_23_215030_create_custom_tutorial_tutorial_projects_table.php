<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_tutorial_tutorial_projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->boolean('approved')->default(false);
            $table->date('start_date')->nullable();
            $table->dateTime('review_at')->nullable();
            $table->time('meeting_time')->nullable();
            $table->string('phase', 32)->nullable();
            $table->json('channels')->nullable();
            $table->json('groups')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('file')->nullable();
            $table->json('files')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('phone', 64)->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->char('budget_currency', 3)->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('custom_tutorial_project_contacts', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained('custom_tutorial_tutorial_projects')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'contact_id']);
        });

        Schema::create('custom_tutorial_checkpoints', function (Blueprint $table): void {
            $table->id();
            $table->string('owner_type', 64);
            $table->unsignedBigInteger('owner_id');
            $table->string('field', 64);
            $table->string('kind', 64)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('title', 120);
            $table->date('due_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->index(['owner_type', 'owner_id', 'field'], 'custom_tutorial_checkpoints_owner_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_tutorial_checkpoints');
        Schema::dropIfExists('custom_tutorial_project_contacts');
        Schema::dropIfExists('custom_tutorial_tutorial_projects');
    }
};
