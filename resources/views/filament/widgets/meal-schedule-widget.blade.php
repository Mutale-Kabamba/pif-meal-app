<x-filament-widgets::widget>
    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Active Program Allocation Limits</h3>
            <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-md dark:bg-emerald-950/30 dark:text-emerald-400">Ration Control</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($projects as $project)
                <div class="py-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->budget_code }} &bull; Status: {{ $project->is_active ? 'Active' : 'Disabled' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2.5 py-1 text-xs font-mono font-bold bg-gray-100 text-gray-800 rounded-lg dark:bg-gray-800 dark:text-gray-200">
                            {{ $project->daily_meal_limit_per_beneficiary }} meal/day max
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>