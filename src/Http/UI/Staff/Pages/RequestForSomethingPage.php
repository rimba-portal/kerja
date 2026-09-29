<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class RequestForSomethingPage extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static string|BackedEnum|null $navigationIcon = 'bites-w-request';

    protected static ?string $navigationLabel = 'Request for something';

    protected static ?int $navigationSort = 22;

    protected static ?string $title = 'Request for something';

    protected ?string $subheading = 'Request for support, service, item, asset, equipment, etc. through workflow system.';

    protected string $view = 'bites::pages.simple';

    protected function getHeaderWidgets(): array
    {
        return [
            // RequestWidget::class,
        ];
    }
}
