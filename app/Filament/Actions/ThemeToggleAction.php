<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

class ThemeToggleAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'theme-toggle';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Toggle Theme')
            ->icon('heroicon-o-moon')
            ->color('gray')
            ->action(function () {
                // Toggle theme using JavaScript
                $this->dispatch('theme-toggle');
            });
    }
}
