<?php

namespace Custom\Modules\Tutorial;

use Custom\Modules\Tutorial\Models\TutorialCheckpoint;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class TutorialServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Relation::morphMap(['custom_tutorial_checkpoint' => TutorialCheckpoint::class]);

        foreach ($this->recordsets() as $recordset) {
            Relation::morphMap([$recordset['alias'] => $recordset['model']]);
        }
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        foreach ($this->recordsets() as $recordset) {
            Gate::policy($recordset['model'], $recordset['policy']);
        }
    }

    // epesi:recordsets-json — consumed by make:epesi-recordset.
    private function recordsets(): array
    {
        $file = __DIR__.'/../recordsets.json';

        return is_file($file) ? json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR) : [];
    }
}
