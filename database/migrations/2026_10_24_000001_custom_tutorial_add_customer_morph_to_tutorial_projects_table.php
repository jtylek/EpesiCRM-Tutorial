<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_tutorial_tutorial_projects', function (Blueprint $table): void {
            $table->nullableMorphs('customer', 'custom_tutorial_projects_customer_index');
        });
    }

    public function down(): void
    {
        Schema::table('custom_tutorial_tutorial_projects', function (Blueprint $table): void {
            $table->dropMorphs('customer', 'custom_tutorial_projects_customer_index');
        });
    }
};
