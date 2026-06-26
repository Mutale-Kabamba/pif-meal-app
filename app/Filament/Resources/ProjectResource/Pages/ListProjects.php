<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Imports\ProjectImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadProjectTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['name', 'budget_code', 'daily_meal_limit', 'is_active', 'description']);
                        fputcsv($out, ['Maternal Health Initiative', 'MHI-2024-001', '1', '1', 'Optional description']);
                        fputcsv($out, ['Emergency Drought Response', 'EDR-2024-001', '2', '1', '']);
                        fclose($out);
                    }, 'projects-import-template.csv', ['Content-Type' => 'text/csv']);
                }),

            Actions\Action::make('importProjects')
                ->label('Import Excel / CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    FileUpload::make('file')
                        ->label('Select file (.xlsx or .csv)')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'application/csv',
                            'text/plain',
                        ])
                        ->disk('local')
                        ->directory('imports/projects'),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);

                    $import = new ProjectImport();
                    Excel::import($import, $path);

                    Storage::disk('local')->delete($data['file']);

                    $failures = $import->failures();
                    $failCount = count($failures);

                    if ($failCount > 0) {
                        $messages = collect($failures)
                            ->take(5)
                            ->map(fn ($f) => 'Row ' . $f->row() . ': ' . implode(', ', $f->errors()))
                            ->implode("\n");

                        Notification::make()
                            ->title($import->importedCount . ' project(s) imported, ' . $failCount . ' row(s) skipped')
                            ->body($messages)
                            ->warning()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title($import->importedCount . ' project(s) imported successfully')
                            ->success()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}
