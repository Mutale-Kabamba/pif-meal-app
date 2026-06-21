<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Beneficiary;
use App\Models\GeneratedSheet;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CardGenerationPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Generate Card Sheets';
    protected static ?string $slug = 'generate-cards';
    protected static string $view = 'filament.pages.card-generation-page';

    public ?array $data = [];
    
    // Modal Interaction & Routing States
    public bool $showPostGenerationModal = false;
    public ?string $latestGeneratedFileUrl = null;
    public ?string $latestGeneratedFileDownloadUrl = null;
    public string $activeTab = 'generator'; 

    public static function canAccess(): bool
    {
        return auth()->user()?->can('generate_cards') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->label('Select Project Stream Allocation')
                    ->options(Project::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
            ])
            ->statePath('data');
    }

    public function generateCards()
    {
        $formData = $this->form->getState();
        $projectId = $formData['project_id'] ?? null;

        if (!$projectId) {
            Notification::make()->title('Please select a valid project.')->danger()->send();
            return;
        }

        $project = Project::find($projectId);
        $beneficiaries = Beneficiary::where('project_id', $projectId)->where('is_active', true)->get();

        if ($beneficiaries->isEmpty()) {
            Notification::make()->title('No active beneficiaries found for this project.')->warning()->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.card-sheet', [
            'project' => $project,
            'beneficiaries' => $beneficiaries,
        ]);

        $filename = 'cards-' . $project->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        $directory = 'public/generated_sheets';
        
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $relativeFilePath = 'generated_sheets/' . $filename;
        $absoluteStoragePath = $directory . '/' . $filename;

        Storage::put($absoluteStoragePath, $pdf->output());

        GeneratedSheet::create([
            'project_id' => $project->id,
            'filename' => $project->name . ' Sheet (' . now()->format('M d, Y H:i') . ')',
            'file_path' => $relativeFilePath,
            'total_cards' => $beneficiaries->count(),
            'generated_by' => auth()->user()->name,
        ]);

        $this->latestGeneratedFileUrl = route('pdf.direct-download', ['filename' => $filename]) . '?stream=true';
        $this->latestGeneratedFileDownloadUrl = route('pdf.direct-download', ['filename' => $filename]);
        $this->showPostGenerationModal = true;

        Notification::make()->title('Beneficiary identity card batch successfully processed.')->success()->send();
    }

    public function dismissModalAndGoToFolder()
    {
        $this->showPostGenerationModal = false;
        $this->activeTab = 'folder';
    }

    public function deleteSheet(int $id)
    {
        $sheet = GeneratedSheet::find($id);
        if ($sheet) {
            Storage::delete('public/' . $sheet->file_path);
            $sheet->delete();
            Notification::make()->title('PDF sheet purged from folder history storage area.')->success()->send();
        }
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    protected function getViewData(): array
    {
        return [
            'archivedSheets' => GeneratedSheet::with('project')->latest()->get()
        ];
    }
}