<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_tutorial_tutorial_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 160);
            $table->foreignId('project_id')->constrained('custom_tutorial_tutorial_projects')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('status', 32)->default('open');
            $table->unsignedTinyInteger('permission')->default(0);
            $table->date('due_date')->nullable();
            $table->unsignedTinyInteger('impact')->nullable();
            $table->foreignId('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_tutorial_tutorial_tickets');
    }
};
