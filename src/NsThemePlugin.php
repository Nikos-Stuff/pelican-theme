<?php

namespace NikosStuff\NsTheme;

use Filament\Contracts\Plugin;
use Filament\Panel;

class NsThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'ns-theme';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
