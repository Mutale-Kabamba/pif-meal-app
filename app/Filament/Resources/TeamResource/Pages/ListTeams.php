<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use App\Imports\TeamImport;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        $isAdmin = in_array(auth()->user()?->role, [
            User::ROLE_HEAD_OF_PROGRAMMES,
            User::ROLE_SYSTEM_MANAGER,
        ]);

        if (!$isAdmin) {
            return [];
        }

        return [
            Actions\Action::make('downloadTeamTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['name', 'project', 'coach', 'is_active']);
                        fputcsv($out, ['U12 Lions',    'Senior Boys Team',  'Coach Alpha', '1']);
                        fputcsv($out, ['U17 Cheetahs', 'Senior Girls Team', '',            '1']);
                        fclose($out);
                    }, 'teams-import-template.csv', ['Content-Type' => 'text/csv']);
                }),

            Actions\Action::make('importTeams')
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
                        ->directory('imports/teams'),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);

                    $import = new TeamImport();
                    Excel::import($import, $path);

                    Storage::disk('local')->delete($data['file']);

                    $failures  = $import->failures();
                    $failCount = count($failures);
                    $body      = $import->skippedCount > 0
                        ? $import->skippedCount . ' row(s) skipped (missing name, project, or unknown football project).'
                        : null;

                    if ($failCount > 0) {
                        $messages = collect($failures)
                            ->take(5)
                            ->map(fn ($f) => 'Row ' . $f->row() . ': ' . implode(', ', $f->errors()))
                            ->implode("\n");

                        Notification::make()
                            ->title($import->importedCount . ' team(s) imported, ' . $failCount . ' row(s) had validation errors')
                            ->body($messages)
                            ->warning()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title($import->importedCount . ' team(s) imported successfully')
                            ->body($body)
                            ->success()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }

}
