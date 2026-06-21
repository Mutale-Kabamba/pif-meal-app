<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Beneficiary;
use App\Models\MealLog;
use Carbon\Carbon;
use Filament\Pages\Page;

class ProjectRegistersPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $title = 'Monthly Attendance Registers';
    protected static ?string $slug = 'project-registers';
    protected static string $view = 'filament.pages.project-registers-page';

    public ?string $selectedProjectId = '';
    public int $filterMonth;
    public int $filterYear;
    public array $weeksStructure = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'head_of_programmes';
    }

    public function mount()
    {
        $firstProject = Project::where('is_active', true)->first();
        $this->selectedProjectId = $firstProject ? (string) $firstProject->id : '';
        
        $this->filterMonth = (int) now()->month;
        $this->filterYear = (int) now()->year;
        
        $this->buildSmartCalendarStructure();
    }

    public function updatedSelectedProjectId() { $this->buildSmartCalendarStructure(); }
    public function updatedFilterMonth() { $this->buildSmartCalendarStructure(); }
    public function updatedFilterYear() { $this->buildSmartCalendarStructure(); }

    protected function buildSmartCalendarStructure()
    {
        $startOfMonth = Carbon::createFromDate($this->filterYear, $this->filterMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        $this->weeksStructure = [];
        $currentDate = $startOfMonth->copy();
        
        $currentWeekNumber = 1;
        $weekDaysCollection = [];

        while ($currentDate->lte($endOfMonth)) {
            if ($currentDate->isWeekday()) {
                $weekDaysCollection[] = [
                    'day_number' => $currentDate->day,
                    'day_label' => match($currentDate->dayOfWeek) {
                        Carbon::MONDAY => 'M',
                        Carbon::TUESDAY => 'T',
                        Carbon::WEDNESDAY => 'W',
                        Carbon::THURSDAY => 'T',
                        Carbon::FRIDAY => 'F',
                    },
                    'full_date' => $currentDate->toDateString()
                ];
            }

            if ($currentDate->dayOfWeek === Carbon::FRIDAY || $currentDate->copy()->addDay()->month !== $this->filterMonth) {
                if (!empty($weekDaysCollection)) {
                    $this->weeksStructure[$currentWeekNumber] = $weekDaysCollection;
                    $currentWeekNumber++;
                    $weekDaysCollection = [];
                }
            }

            $currentDate->addDay();
        }
    }

    protected function getViewData(): array
    {
        $projects = Project::where('is_active', true)->get();
        $beneficiaries = collect();
        $mealMatrix = [];

        if (!empty($this->selectedProjectId)) {
            // Highly optimized queries select only required memory columns
            $beneficiaries = Beneficiary::where('project_id', $this->selectedProjectId)
                ->where('is_active', true)
                ->select(['id', 'name'])
                ->orderBy('name', 'asc')
                ->get();

            // Using toBase() skips expensive hydration of Eloquent models, accelerating rendering
            $logs = MealLog::where('project_id', $this->selectedProjectId)
                ->whereMonth('served_at', $this->filterMonth)
                ->whereYear('served_at', $this->filterYear)
                ->select(['beneficiary_id', 'served_at'])
                ->toBase()
                ->get();

            foreach ($logs as $log) {
                $day = Carbon::parse($log->served_at)->day;
                $mealMatrix[$log->beneficiary_id][$day] = true;
            }
        }

        return [
            'projects' => $projects,
            'beneficiaries' => $beneficiaries,
            'mealMatrix' => $mealMatrix,
            'monthsList' => [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ],
            'yearsList' => range(now()->year - 1, now()->year + 2),
        ];
    }
}