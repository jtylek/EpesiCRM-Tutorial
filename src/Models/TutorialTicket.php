<?php

namespace Custom\Modules\Tutorial\Models;

use App\Enums\RecordPermission;
use Epesi\Modules\RecordBrowser\Models\Concerns\HasCustomFields;
use Epesi\Modules\RecordBrowser\Models\Concerns\HasOwnershipVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TutorialTicket extends Model
{
    use HasCustomFields, HasOwnershipVisibility, LogsActivity, SoftDeletes;

    protected $table = 'custom_tutorial_tutorial_tickets';

    protected $attributes = ['permission' => 0, 'status' => 'open'];

    protected $fillable = ['title', 'project_id', 'description', 'status', 'permission', 'due_date', 'impact'];

    protected function casts(): array
    {
        return ['permission' => RecordPermission::class, 'due_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('visible_project', fn (Builder $query) => $query->whereHas('project'));
        static::saving(function (TutorialTicket $ticket): void {
            if (($ticket->isDirty('project_id') || ! $ticket->exists)
                && ! TutorialProject::query()->whereKey($ticket->project_id)->exists()) {
                throw ValidationException::withMessages(['project_id' => __('Choose an available tutorial project.')]);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(TutorialProject::class, 'project_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontSubmitEmptyLogs()->useLogName('custom_tutorial_ticket');
    }
}
