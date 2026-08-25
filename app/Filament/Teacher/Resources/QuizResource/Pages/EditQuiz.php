<?php

namespace App\Filament\Teacher\Resources\QuizResource\Pages;

use App\Filament\Teacher\Resources\QuizResource;
use App\Models\Quiz;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuiz extends EditRecord
{
    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_ai')
                ->label('Generate with Agent')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generate quiz with Agent')
                ->modalDescription('This reads the lecture text and produces multiple-choice questions. Any existing questions on this quiz will be REPLACED.')
                ->modalSubmitActionLabel('Generate')
                ->action(function () {
                    /** @var Quiz $record */
                    $record = $this->getRecord();
                    QuizResource::runAiGeneration($record);
                    $this->refreshFormData(['ai_generation_status', 'ai_provider_used', 'ai_tokens_used', 'ai_generated_at']);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
