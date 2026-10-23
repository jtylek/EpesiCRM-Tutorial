<?php

namespace Custom\Modules\Tutorial\Models;

use Epesi\Modules\RecordBrowser\Models\CollectionItem;
use Epesi\Modules\RecordBrowser\Recordset\Field;

/** A project-owned collection row, with no independent resource or policy. */
class TutorialCheckpoint extends CollectionItem
{
    protected $table = 'custom_tutorial_checkpoints';

    public static function fields(): array
    {
        return [
            Field::text('title')->required()->maxLength(120),
            Field::date('due_date'),
            Field::boolean('completed'),
        ];
    }

    public static function kinds(): string
    {
        return 'Tutorial_ProjectCheckpointKinds';
    }

    public function summary(): string
    {
        return (string) $this->title;
    }

    public static function label(): string
    {
        return __('Tutorial checkpoint');
    }
}
