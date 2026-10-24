<?php

namespace Custom\Modules\Tutorial\Models;

use App\Models\User;
use Epesi\Modules\CRM\Contacts\Models\Contact;
use Epesi\Modules\RecordBrowser\Files\StoredFileIds;
use Epesi\Modules\RecordBrowser\Models\Concerns\HasCustomFields;
use Epesi\Modules\RecordBrowser\Models\Concerns\HasRecordAuthors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TutorialProject extends Model
{
    use HasCustomFields, HasRecordAuthors, LogsActivity, SoftDeletes;

    protected $table = 'custom_tutorial_tutorial_projects';

    protected $fillable = [
        'name', 'description', 'quantity', 'estimated_cost', 'approved', 'start_date',
        'review_at', 'meeting_time', 'phase', 'channels', 'groups', 'owner_id',
        'file', 'files', 'budget', 'budget_currency', 'email', 'website', 'phone', 'customer_type', 'customer_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer', 'estimated_cost' => 'decimal:2', 'approved' => 'boolean',
            'start_date' => 'date', 'review_at' => 'datetime', 'budget' => 'decimal:2',
            'channels' => 'array', 'groups' => 'array',
            'file' => StoredFileIds::class,
            'files' => StoredFileIds::class,
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TutorialTicket::class, 'project_id');
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'custom_tutorial_project_contacts', 'project_id', 'contact_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('custom_tutorial_project');
    }
}
