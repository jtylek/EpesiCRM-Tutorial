<?php

namespace Custom\Modules\Tutorial;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TutorialPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'custom-tutorial';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__.'/Filament/Resources',
            for: 'Custom\\Modules\\Tutorial\\Filament\\Resources',
        );
    }

    public function boot(Panel $panel): void {}
}
