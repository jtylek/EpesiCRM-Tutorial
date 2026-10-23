<?php

namespace Custom\Modules\Tutorial\Filament\Resources\TutorialProjects;

use App\Models\User;
use BackedEnum;
use Custom\Modules\Tutorial\Models\TutorialCheckpoint;
use Custom\Modules\Tutorial\Models\TutorialProject;
use Epesi\Modules\CRM\Companies\Models\Company;
use Epesi\Modules\CRM\Contacts\Models\Contact;
use Epesi\Modules\RecordBrowser\Models\Address;
use Epesi\Modules\RecordBrowser\Models\EmailAddress;
use Epesi\Modules\RecordBrowser\Models\OnlineAccount;
use Epesi\Modules\RecordBrowser\Models\PhoneNumber;
use Epesi\Modules\RecordBrowser\Recordset\Field;
use Epesi\Modules\RecordBrowser\Recordset\RecordsetResource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TutorialProjectResource extends RecordsetResource
{
    protected static ?string $model = TutorialProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tutorial';

    protected static ?string $recordTitleAttribute = 'name';

    public static function fields(): array
    {
        return [
            Field::text('name')->required()->maxLength(120)->inTable(),
            Field::longText('description')->section('Details'),
            Field::integer('quantity')->required(),
            Field::decimal('estimated_cost', 2),
            Field::boolean('approved'),
            Field::date('start_date'),
            Field::dateTime('review_at'),
            Field::time('meeting_time'),
            Field::select('phase', ['discovery' => 'Discovery', 'delivery' => 'Delivery']),
            Field::multiselect('channels', ['email' => 'E-mail', 'chat' => 'Chat', 'phone' => 'Phone']),
            Field::commonData('groups', 'Tutorial_ProjectGroups', multiple: true),
            Field::relation('owner_id', User::class)->label('Owner')->titleAttribute('name'),
            Field::relations('watchers', Contact::class)->label('Watchers'),
            Field::file('file', multiple: false),
            Field::file('files'),
            Field::autonumber('project_number', 'PRJ-', 5)->inForm(false)->inTable(),
            Field::currency('budget')->label('Budget'),
            Field::decimal('score')->calculated(
                fn (?TutorialProject $record, \Closure $get): ?float => $record?->tickets()->count() + (int) $get('quantity'),
                ['quantity'],
            )->inForm(false)->inTable(),
            Field::customer('customer', [Contact::class, Company::class])->label('Primary customer'),
            Field::customers('customers', [Contact::class, Company::class])->label('Additional customers'),
            Field::linkedTo('related_records', [Contact::class, Company::class])->label('Related records'),
            Field::collection('emails', EmailAddress::class)->label('E-mail addresses'),
            Field::collection('phones', PhoneNumber::class)->label('Phone numbers'),
            Field::collection('online_accounts', OnlineAccount::class)->label('Online accounts'),
            Field::collection('addresses', Address::class)->label('Addresses'),
            Field::collection('checkpoints', TutorialCheckpoint::class)
                ->label('Checkpoints')->inTable(),
            Field::email('email'),
            Field::url('website'),
            Field::phone('phone'),
        ];
    }

    public static function addons(): array
    {
        return [TutorialTicketsRelationManager::class];
    }
}
