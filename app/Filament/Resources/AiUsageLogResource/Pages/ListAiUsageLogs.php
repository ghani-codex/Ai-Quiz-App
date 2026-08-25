<?php

namespace App\Filament\Resources\AiUsageLogResource\Pages;

use App\Filament\Resources\AiUsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiUsageLogs extends ListRecords
{
    protected static string $resource = AiUsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
