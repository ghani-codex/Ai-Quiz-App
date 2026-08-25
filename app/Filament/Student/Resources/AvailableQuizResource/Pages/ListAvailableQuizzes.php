<?php

namespace App\Filament\Student\Resources\AvailableQuizResource\Pages;

use App\Filament\Student\Resources\AvailableQuizResource;
use Filament\Resources\Pages\ListRecords;

class ListAvailableQuizzes extends ListRecords
{
    protected static string $resource = AvailableQuizResource::class;
}
