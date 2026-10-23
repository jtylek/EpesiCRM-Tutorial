<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A ticket's Impact is a CommonData pick (`Tutorial_TicketImpact`: Low, Medium,
 * High) instead of a bare number. The column stores the list key, so an
 * installation that already has the integer column gets it converted.
 *
 * The query builder, not the CommonData facade: this can run before
 * CommonData's classes are loaded.
 */
return new class extends Migration
{
    private const LIST = 'Tutorial_TicketImpact';

    private const ITEMS = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];

    public function up(): void
    {
        $impact = collect(Schema::getColumns('custom_tutorial_tutorial_tickets'))->firstWhere('name', 'impact');

        if ($impact && ! str_contains(strtolower($impact['type_name']), 'char')) {
            Schema::table('custom_tutorial_tutorial_tickets', function ($table): void {
                $table->string('impact', 32)->nullable()->change();
            });

            // The old numbers mean nothing as list keys.
            DB::table('custom_tutorial_tutorial_tickets')->whereNotIn('impact', array_keys(self::ITEMS))->update(['impact' => null]);
        }

        $table = DB::table('common_data');

        if ($table->clone()->where('path', self::LIST)->exists()) {
            return;
        }

        $now = now();
        $parentId = $table->clone()->insertGetId([
            'parent_id' => null,
            'key' => self::LIST,
            'value' => null,
            'path' => self::LIST,
            'readonly' => true,
            'position' => (int) $table->clone()->whereNull('parent_id')->max('position') + 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $position = 0;

        foreach (self::ITEMS as $key => $value) {
            $table->clone()->insert([
                'parent_id' => $parentId,
                'key' => $key,
                'value' => $value,
                'path' => self::LIST.'/'.$key,
                'readonly' => true,
                'position' => ++$position,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (class_exists(\Epesi\Modules\CommonData\CommonDataRepository::class)) {
            \Epesi\Modules\CommonData\CommonDataRepository::invalidate();
        }
    }

    /** Nothing to undo: tickets may already use the list. */
    public function down(): void {}
};
