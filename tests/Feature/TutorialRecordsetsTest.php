<?php

namespace Custom\Modules\Tutorial\Tests\Feature;

use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\TutorialProjectResource;
use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\Pages\ViewTutorialProject;
use Custom\Modules\Tutorial\Filament\Resources\TutorialProjects\TutorialTicketsRelationManager;
use Custom\Modules\Tutorial\Filament\Resources\TutorialTickets\TutorialTicketResource;
use Custom\Modules\Tutorial\Models\TutorialCheckpoint;
use Custom\Modules\Tutorial\Models\TutorialProject;
use Custom\Modules\Tutorial\Models\TutorialTicket;
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

    public function test_the_tickets_addon_renders_on_a_project(): void
    {
        $this->actingAs($this->userWithRole('super_admin'));
        $project = TutorialProject::create(['name' => 'Addon project']);

        Livewire::test(TutorialTicketsRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ViewTutorialProject::class,
        ])->assertSuccessful();
    }
}
