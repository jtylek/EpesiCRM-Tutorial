<?php

namespace Custom\Modules\Tutorial\Filament\Resources\TutorialProjects;

use Custom\Modules\Tutorial\Filament\Resources\TutorialTickets\TutorialTicketResource;
use Custom\Modules\Tutorial\Models\TutorialTicket;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
        // View and Edit open the ticket's own pages (View first, as everywhere in Epesi),
        // not a modal that can only be closed.
        return TutorialTicketResource::table($table)
            ->headerActions([CreateAction::make()])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (TutorialTicket $record): string => TutorialTicketResource::getUrl('view', ['record' => $record]))
                    ->iconButton()
                    ->tooltip(__('View')),
                // A plain link: Filament hides its own EditAction while the addon sits on a View page.
                Action::make('edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (TutorialTicket $record): string => TutorialTicketResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (TutorialTicket $record): bool => TutorialTicketResource::canEdit($record))
                    ->iconButton()
                    ->tooltip(__('Edit')),
            ])
            ->recordUrl(fn (TutorialTicket $record): string => TutorialTicketResource::getUrl('view', ['record' => $record]));
    }
}
