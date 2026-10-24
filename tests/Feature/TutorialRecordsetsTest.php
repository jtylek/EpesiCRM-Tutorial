<?php

namespace Custom\Modules\Tutorial\Tests\Feature;

use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\TutorialProjectResource;
use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\Pages\ViewTutorialProject;
use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\TutorialTicketsRelationManager;
use Custom\Modules\Tutorial\Filament\Resources\TutorialTickets\TutorialTicketResource;
use Custom\Modules\Tutorial\Models\TutorialCheckpoint;
use Custom\Modules\Tutorial\Models\TutorialProject;
use Custom\Modules\Tutorial\Models\TutorialTicket;
use Epesi\Modules\CommonData\Facades\CommonData;
use Epesi\Modules\CRM\Contacts\Models\Contact;
use Epesi\Modules\RecordBrowser\Filament\RelationManagers\HistoryRelationManager;
use Epesi\Modules\RecordBrowser\Models\Address;
use Epesi\Modules\RecordBrowser\Models\EmailAddress;
use Epesi\Modules\RecordBrowser\Models\OnlineAccount;
use Epesi\Modules\RecordBrowser\Models\PhoneNumber;
use Epesi\Modules\RecordBrowser\Recordset\FieldType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\Concerns\SignsInUsers;
use Tests\TestCase;

class TutorialRecordsetsTest extends TestCase
{
    use RefreshDatabase, SignsInUsers;

    public function test_project_examples_cover_the_recordbrowser_field_types_and_collections(): void
    {
        $fields = collect(TutorialProjectResource::fields())->keyBy('name');

        foreach (['text', 'long_text', 'integer', 'decimal', 'currency', 'boolean', 'date', 'datetime', 'time', 'select', 'multiselect', 'commondata', 'relation', 'relations', 'customer', 'customers_field', 'autonumber', 'file', 'related', 'collection', 'email', 'url', 'phone'] as $type) {
            $this->assertTrue($fields->contains(fn ($field) => $field->type->value === $type), "Missing {$type} example");
        }

        $this->assertSame([EmailAddress::class, PhoneNumber::class, OnlineAccount::class, Address::class, TutorialCheckpoint::class],
            $fields->filter(fn ($field) => $field->type === FieldType::Collection)->map(fn ($field) => $field->collectionType())->values()->all());
        $this->assertTrue($fields->get('score')->isCalculated());
    }

    public function test_tickets_are_a_child_recordset_and_projects_expose_the_addon(): void
    {
        $this->assertSame(TutorialProject::class, TutorialTicket::query()->getModel()->project()->getRelated()::class);
        $this->assertSame([TutorialTicketsRelationManager::class], TutorialProjectResource::addons());
        $this->assertContains('epesi/recordbrowser', json_decode(file_get_contents(dirname(__DIR__, 2).'/module.json'), true)['requires']);
        $this->assertNotEmpty(TutorialTicketResource::fields());
    }

    public function test_customer_field_has_its_morph_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('custom_tutorial_tutorial_projects', ['customer_type', 'customer_id']));
        $this->assertTrue(method_exists(TutorialProject::class, 'customer'));
    }

    public function test_ticket_impact_is_a_low_medium_high_common_data_pick(): void
    {
        $impact = collect(TutorialTicketResource::fields())->firstWhere('name', 'impact');

        $this->assertSame(FieldType::CommonData, $impact->type);
        $this->assertSame(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'], CommonData::array('Tutorial_TicketImpact', 'position'));
        $this->assertTrue(TutorialTicket::create(['title' => 'T', 'project_id' => TutorialProject::create(['name' => 'P'])->id, 'impact' => 'high'])->refresh()->impact === 'high');
    }

    public function test_the_project_group_and_checkpoint_kind_lists_have_options(): void
    {
        $this->assertSame(['internal' => 'Internal', 'customer' => 'Customer', 'training' => 'Training'], CommonData::array('Tutorial_ProjectGroups', 'position'));
        $this->assertSame(['milestone' => 'Milestone', 'review' => 'Review', 'deadline' => 'Deadline'], CommonData::array(TutorialCheckpoint::kinds(), 'position'));
    }

    public function test_history_renders_for_a_project_with_watchers(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $project = TutorialProject::create(['name' => 'History project']);
        $project->watchers()->sync([Contact::create(['first_name' => 'Ann', 'last_name' => 'Lee'])->id]);
        $project->update(['name' => 'Renamed project', 'groups' => ['customer']]);

        Livewire::test(HistoryRelationManager::class, ['ownerRecord' => $project->fresh(), 'pageClass' => ViewTutorialProject::class])
            ->assertSuccessful()
            ->assertSeeText('Renamed project');
    }

    public function test_the_tickets_addon_renders_on_a_project(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $project = TutorialProject::create(['name' => 'Addon project']);
        $ticket = TutorialTicket::create(['title' => 'Addon ticket', 'project_id' => $project->id]);

        Livewire::test(TutorialTicketsRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ViewTutorialProject::class,
        ])->assertSuccessful()
            ->assertTableActionVisible('view', $ticket)
            ->assertTableActionVisible('edit', $ticket)
            ->assertTableActionHasUrl('edit', TutorialTicketResource::getUrl('edit', ['record' => $ticket]), $ticket);
    }
}
