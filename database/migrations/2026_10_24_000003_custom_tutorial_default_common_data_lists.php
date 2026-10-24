<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The two lists the Tutorial project fields read: `Tutorial_ProjectGroups` (a
 * project's Groups) and `Tutorial_ProjectCheckpointKinds` (a checkpoint's
 * Kind). Without them both pickers are empty.
 *
 * Only a list that is missing is created: one an administrator already built
 * is left as it is. The query builder, not the CommonData facade, because this
 * can run before CommonData's classes are loaded.
 */
return new class extends Migration
{
    private const LISTS = [
        'Tutorial_ProjectGroups' => ['internal' => 'Internal', 'customer' => 'Customer', 'training' => 'Training'],
        'Tutorial_ProjectCheckpointKinds' => ['milestone' => 'Milestone', 'review' => 'Review', 'deadline' => 'Deadline'],
    ];

    public function up(): void
    {
        $table = DB::table('common_data');
        $created = false;

        foreach (self::LISTS as $list => $items) {
            if ($table->clone()->where('path', $list)->exists()) {
                continue;
            }

            $now = now();
            $parentId = $table->clone()->insertGetId([
                'parent_id' => null,
                'key' => $list,
                'value' => null,
                'path' => $list,
                'readonly' => true,
                'position' => (int) $table->clone()->whereNull('parent_id')->max('position') + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $position = 0;

            foreach ($items as $key => $value) {
                $table->clone()->insert([
                    'parent_id' => $parentId,
                    'key' => $key,
                    'value' => $value,
                    'path' => $list.'/'.$key,
                    'readonly' => true,
                    'position' => ++$position,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $created = true;
        }

        if ($created && class_exists(\Epesi\Modules\CommonData\CommonDataRepository::class)) {
            \Epesi\Modules\CommonData\CommonDataRepository::invalidate();
        }
    }

    /** Nothing to undo: records may already use the lists. */
    public function down(): void {}
};
