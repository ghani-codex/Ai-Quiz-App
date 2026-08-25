<?php

namespace App\Filament\Resources\AiUsageLogResource\Pages;

use App\Filament\Resources\AiUsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiUsageLog extends EditRecord
{
    protected static string $resource = AiUsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
