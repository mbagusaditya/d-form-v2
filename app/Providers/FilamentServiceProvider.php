<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'primary' => Color::Blue,
            'secondary' => Color::Gray,
            'accent' => Color::Cyan,
            'success' => Color::Green,
            'warning' => Color::Yellow,
            'danger' => Color::Red,
        ]);
    }
}
