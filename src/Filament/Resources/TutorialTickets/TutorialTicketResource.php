<?php

namespace Custom\Modules\Tutorial\Filament\Resources\TutorialTickets;

use App\Enums\RecordPermission;
use BackedEnum;
use Custom\Modules\Tutorial\Models\TutorialProject;
use Custom\Modules\Tutorial\Models\TutorialTicket;
use Epesi\Modules\RecordBrowser\Recordset\Field;
use Epesi\Modules\RecordBrowser\Recordset\RecordsetResource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TutorialTicketResource extends RecordsetResource
{
    protected static ?string $model = TutorialTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Tutorial';

    protected static ?string $recordTitleAttribute = 'title';

    public static function fields(): array
    {
        return [
            Field::text('title')->required()->maxLength(160)->inTable(),
            Field::relation('project_id', TutorialProject::class)->titleAttribute('name')->required()->inTable()->listedOnTarget(false),
            Field::longText('description'),
            Field::select('status', ['open' => 'Open', 'in_progress' => 'In progress', 'closed' => 'Closed'])->required()->inTable(),
            Field::select('permission', RecordPermission::class)->required()->default(RecordPermission::Public),
            Field::date('due_date')->inTable()->filterable(),
            Field::commonData('impact', 'Tutorial_TicketImpact')->param('order', 'position')->inTable(),
        ];
    }
}
