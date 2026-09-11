<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Register Mode Switcher (Attendance vs Meals) --}}
        <div class="flex items-center justify-between gap-4 flex-wrap bg-white dark:bg-gray-900 p-2.5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="inline-flex p-1 rounded-xl bg-gray-100 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60">
                <button type="button"
                        wire:click="$set('registerType', 'attendance')"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 flex items-center gap-2 {{ $registerType === 'attendance' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Attendance Register (Training / Classes)</span>
                </button>

                <button type="button"
                        wire:click="$set('registerType', 'meals')"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-150 flex items-center gap-2 {{ $registerType === 'meals' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    <span>Meal Distribution Register (Cooks)</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                @if($registerType === 'attendance')
                    <a href="{{ route('filament.admin.pages.mark-attendance') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all duration-150 transform active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Mark Session Attendance</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Filter Strip --}}
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">

                {{-- Programme Scope (admins only) --}}
                @unless($lockScope)
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Programme Scope</label>
                    <select wire:model.live="scope" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="all">All Programmes</option>
                        <option value="education">Education / Literacy Only</option>
                        <option value="football">Football Only</option>
                    </select>
                </div>
                @endunless

                {{-- Project --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Project Stream</label>
                    <select wire:model.live="selectedProjectId" @disabled($lockProject) class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                        @unless($lockProject)
                            <option value="">— All in Scope —</option>
                        @endunless
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Team (football projects only, not locked coaches) --}}
                @if($showTeamFilter)
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Football Team</label>
                    <select wire:model.live="selectedTeamId" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="">— All Teams —</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Month --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Month</label>
                    <select wire:model.live="filterMonth" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        @foreach($monthsList as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Year</label>
                    <select wire:model.live="filterYear" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        @foreach($yearsList as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Register scope, month, and activity strip --}}
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Viewing:</span>
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">{{ $registerLabel }}</span>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Activity:</span>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        {{ $registerType === 'attendance' ? $activityLabel : 'Cooks Meal Distribution' }}
                    </span>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Month:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                        {{ $selectedMonthName }}
                    </span>
                </div>

                @if($registerType === 'attendance')
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 italic hidden sm:inline">
                        (Click any day cell to quickly toggle attendance)
                    </span>
                @else
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 italic hidden sm:inline">
                        (Meals served by cooks at feeding terminal)
                    </span>
                @endif

                <span class="ml-auto text-[10px] text-gray-400">
                    {{ $beneficiaries->count() }} beneficiar{{ $beneficiaries->count() === 1 ? 'y' : 'ies' }}
                </span>
            </div>
        </div>

        {{-- Register Grid --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
            @if($beneficiaries->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                    No active beneficiaries found for {{ $selectedMonthName }}.
                </div>
            @else
                <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
                    <table class="w-full text-left border-collapse min-w-max table-fixed">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-[10px] font-black tracking-widest border-b border-gray-200 dark:border-gray-700">
                                <th class="p-2 sticky left-0 bg-gray-100 dark:bg-gray-800 z-30 w-[180px] border-r border-gray-200 dark:border-gray-700 text-center font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    {{ $selectedMonthShort }}
                                </th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    <th colspan="{{ count($days) }}" class="p-1 text-center border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 font-sans uppercase">
                                        Week {{ $weekNum }}
                                    </th>
                                @endforeach
                                <th class="p-2 text-center w-16 bg-gray-100 dark:bg-gray-800 sticky right-0 z-30 border-l border-gray-200 dark:border-gray-700 font-bold uppercase text-[9px]">
                                    {{ $registerType === 'attendance' ? 'Attended' : 'Meals' }}
                                </th>
                            </tr>
                            
                            <tr class="bg-gray-50 text-gray-500 dark:bg-gray-800/40 dark:text-gray-400 text-[10px] font-bold border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20">
                                <th class="p-2 sticky left-0 bg-gray-50 dark:bg-gray-800 z-30 w-[180px] border-r border-gray-200 dark:border-gray-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    Beneficiary Name
                                </th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    @foreach($days as $dayMeta)
                                        <th class="p-1 text-center w-8 border-r border-gray-200 dark:border-gray-700 font-mono text-[9px]" title="Day {{ sprintf('%02d', $dayMeta['day_number']) }} ({{ $selectedMonthShort }})">
                                            {{ $dayMeta['day_label'] }}<span class="block text-[8px] font-normal text-gray-400">{{ sprintf('%02d', $dayMeta['day_number']) }}</span>
                                        </th>
                                    @endforeach
                                @endforeach
                                <th class="p-2 text-center w-16 bg-gray-100 dark:bg-gray-800 sticky right-0 z-30 border-l border-gray-200 dark:border-gray-700 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)] text-xs font-black">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                            @foreach($beneficiaries as $beneficiary)
                                @php $totalCount = 0; @endphp
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="p-2 font-medium sticky left-0 bg-white dark:bg-gray-900 z-10 border-r border-gray-200 dark:border-gray-700 truncate max-w-[180px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-gray-900 dark:text-white" title="{{ $beneficiary->name }}">
                                        {{ $beneficiary->name }}
                                    </td>
                                    
                                    @foreach($weeksStructure as $weekNum => $days)
                                        @foreach($days as $dayMeta)
                                            @php 
                                                $dayNum = $dayMeta['day_number'];
                                                $isActive = $activeMatrix[$beneficiary->id][$dayNum] ?? false; 
                                                if($isActive) $totalCount++;
                                            @endphp
                                            <td class="p-0.5 border-r border-gray-100 dark:border-gray-800 text-center select-none {{ $registerType === 'attendance' ? 'cursor-pointer hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30' : '' }}"
                                                @if($registerType === 'attendance') wire:click="toggleAttendance({{ $beneficiary->id }}, {{ $dayNum }})" title="Click to toggle attendance" @endif>
                                                @if($isActive)
                                                    <span class="inline-flex items-center justify-center w-6 h-5 text-[11px] font-black rounded text-white bg-emerald-500 shadow-sm" 
                                                          title="{{ $registerType === 'attendance' ? 'Present (Training/Class Attended)' : 'Meal Served' }}">✓</span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-5 text-[10px] font-bold rounded text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-500" 
                                                          title="{{ $registerType === 'attendance' ? 'Absent (Missed Session)' : 'No Meal' }}">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                    
                                    <td class="p-2 text-center font-mono font-black sticky right-0 z-10 border-l border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white shadow-[-2px_0_5px_rgba(0,0,0,0.1)]">
                                        {{ $totalCount }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
