<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

final class HtmlCodeEditor extends Field
{
    protected string $view = 'filament.forms.components.html-code-editor';

    private int $rows = 20;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }
}
