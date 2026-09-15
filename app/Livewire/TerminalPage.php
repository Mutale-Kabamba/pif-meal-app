<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\MealLog;
use App\Services\MealValidationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.terminal')]
class TerminalPage extends Component
{
    public string $search = '';
    public array $matches = [];
    public ?array $alert = null;
    public int $totalFedToday = 0;
    public string $cookName = '';
    public string $projectName = '';
    /** QR tokens already served today — pre-loaded so the client blocklist survives logout */
    public array $todayScannedTokens = [];

    protected MealValidationService $validationService;

    public function boot(MealValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function mount()
    {
        abort_unless(Auth::user()?->isCook(), 403, 'Access denied. The Kitchen Terminal is strictly accessible to cooks only.');

        $user = Auth::user();
        $this->cookName    = $user->name;
        $this->projectName = $user->assignedProject?->name ?? 'All Projects';
        $this->updateStats();
        $this->loadTodayScannedTokens();
    }

    public function updatedSearch()
    {
        $this->searchBeneficiaries();
    }

    public function searchBeneficiaries()
    {
        if (strlen($this->search) < 1) {
            $this->matches = [];
            return;
        }

        $query = $this->search;
        $user = Auth::user();

        $beneficiaries = Beneficiary::query()
            ->with('project:id,name')
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('shortcode', 'like', $query . '%')
                  ->orWhere('name', 'like', '%' . $query . '%');
            });

        $this->matches = $beneficiaries
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'shortcode' => $b->shortcode,
                'project' => $b->project->name,
            ])
            ->toArray();
    }

    public function markFed(int $beneficiaryId)
    {
        $beneficiary = Beneficiary::find($beneficiaryId);
        if (!$beneficiary) {
            $this->showAlert('error', 'Beneficiary not found.');
            $this->dispatch('play-error-sound');
            return;
        }

        $result = $this->validationService->validate($beneficiary, Auth::user());

        if ($result->success) {
            $this->showAlert('success', $result->message);
            $this->dispatch('play-success-sound');
        } elseif ($result->status === 'duplicate') {
            $this->showAlert('warning', $result->message . ' Proceed to scan the next person.');
            $this->dispatch('play-warning-sound');
        } else {
            $this->showAlert('error', $result->message);
            $this->dispatch('play-error-sound');
        }

        $this->search = '';
        $this->matches = [];
        $this->updateStats();
    }

    public function processQrToken(string $token)
    {
        $token = trim($token);
        $beneficiary = Beneficiary::where('qr_token', $token)->first();

        if (!$beneficiary) {
            $this->showAlert('error', 'Invalid QR code: No beneficiary found.');
            $this->dispatch('play-error-sound');
            return;
        }

        // Check if already received meal today
        if ($this->validationService->hasReceivedMealToday($beneficiary, 'standard_ration')) {
            $existingLog = MealLog::where('beneficiary_id', $beneficiary->id)
                ->where('meal_type', 'standard_ration')
                ->whereDate('served_at', today())
                ->first();

            $time = $existingLog ? $existingLog->served_at->format('h:i A') : 'earlier today';

            $this->showAlert('warning', "Already Scanned: {$beneficiary->name} ({$beneficiary->shortcode}) already received a meal today at {$time}. Proceed to scan next person.");
            $this->dispatch('play-warning-sound');
            return;
        }

        $result = $this->validationService->validate($beneficiary, Auth::user());

        if ($result->success) {
            $this->showAlert('success', $result->message);
            $this->dispatch('play-success-sound');
        } elseif ($result->status === 'duplicate') {
            $this->showAlert('warning', $result->message . ' Proceed to scan next person.');
            $this->dispatch('play-warning-sound');
        } else {
            $this->showAlert('error', $result->message);
            $this->dispatch('play-error-sound');
        }

        $this->updateStats();
    }

    private function showAlert(string $type, string $message)
    {
        $this->alert = [
            'type' => $type,
            'message' => $message,
        ];
        $this->dispatch('alert-shown');
    }

    private function loadTodayScannedTokens(): void
    {
        $this->todayScannedTokens = MealLog::whereDate('served_at', today())
            ->join('beneficiaries', 'meal_logs.beneficiary_id', '=', 'beneficiaries.id')
            ->pluck('beneficiaries.qr_token')
            ->toArray();
    }

    private function updateStats()
    {
        $user = Auth::user();
        $query = \App\Models\MealLog::query()
            ->whereDate('served_at', today())
            ->where('served_by_user_id', $user->id);

        $this->totalFedToday = $query->count();
    }

    public function render()
    {
        return view('livewire.terminal-page');
    }
}
