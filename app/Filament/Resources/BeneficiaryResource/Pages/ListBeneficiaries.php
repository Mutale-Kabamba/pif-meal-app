<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use App\Imports\BeneficiaryImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadBeneficiaryTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return response()->streamDownload(function () {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['name', 'project_budget_code', 'is_active']);
                        fputcsv($out, ['Amina Hussein', 'MHI-2024-001', '1']);
                        fputcsv($out, ['Omar Diallo', 'EDR-2024-001', '1']);
                        fclose($out);
                    }, 'beneficiaries-import-template.csv', ['Content-Type' => 'text/csv']);
                }),

            Actions\Action::make('importBeneficiaries')
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
                        ->directory('imports/beneficiaries'),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);

                    $import = new BeneficiaryImport();
                    Excel::import($import, $path);

                    Storage::disk('local')->delete($data['file']);

                    $failures  = $import->failures();
                    $failCount = count($failures);
                    $body      = $import->skippedCount > 0
                        ? $import->skippedCount . ' row(s) skipped (duplicate name or unknown project budget code).'
                        : null;

                    if ($failCount > 0) {
                        $messages = collect($failures)
                            ->take(5)
                            ->map(fn ($f) => 'Row ' . $f->row() . ': ' . implode(', ', $f->errors()))
                            ->implode("\n");

                        Notification::make()
                            ->title($import->importedCount . ' beneficiar(ies) imported, ' . $failCount . ' row(s) had validation errors')
                            ->body($messages)
                            ->warning()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title($import->importedCount . ' beneficiar(ies) imported successfully')
                            ->body($body)
                            ->success()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->label('Register New Beneficiary'),
        ];
    }
}