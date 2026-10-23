<?php

namespace Custom\Modules\Tutorial\Filament\Resources\TutorialProjects;

use Custom\Modules\Tutorial\Filament\Resources\TutorialTickets\TutorialTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/** Relationship-based addon: Filament scopes all rows and creates by project. */
class TutorialTicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Tickets';

    public function form(Schema $schema): Schema
    {
        return TutorialTicketResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TutorialTicketResource::table($table)
            ->headerActions([CreateAction::make()]);
    }
}
