<x-filament-panels::page>
    <div class="space-y-5">
        {{-- Activity Banner --}}
        <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50/50 dark:from-emerald-950/30 dark:to-gray-900 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/60 shadow-sm">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                        @if($activityIcon === 'football')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                <path d="M2 12h20" />
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                                {{ $activityTitle }}
                            </h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                {{ $activityTag }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                            {{ $activityDescription }}
                        </p>
                    </div>
                </div>

                {{-- Operational Role Note --}}
                <div class="text-[11px] text-gray-500 dark:text-gray-400 bg-white/80 dark:bg-gray-800/80 px-3 py-1.5 rounded-xl border border-gray-200/80 dark:border-gray-700/60 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span><strong>Attendance:</strong> Coaches & Officers • <strong>Meals:</strong> Cooks at Terminal</span>
                </div>
            </div>
        </div>

        {{-- Filters & Session Parameters --}}
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Session Date --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Session Date
                    </label>
                    <input type="date" 
                           wire:model.live="sessionDate" 
                           class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white font-medium" />
                </div>

                {{-- Programme Scope (Admins only) --}}
                @unless($lockScope)
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Programme Scope
                    </label>
                    <select wire:model.live="scope" 
                            class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="all">All Programmes</option>
                        <option value="education">Education / Literacy Only</option>
                        <option value="football">Football Only</option>
                    </select>
                </div>
                @endunless

                {{-- Project Allocation --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Project Stream
                    </label>
                    <select wire:model.live="selectedProjectId" 
                            @disabled($lockProject) 
                            class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                        @unless($lockProject)
                            <option value="">— All in Scope —</option>
                        @endunless
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Team (Football projects only) --}}
                @if($showTeamFilter)
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Football Team
                    </label>
                    <select wire:model.live="selectedTeamId" 
                            class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="">— All Teams —</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        {{-- Roster Toolbar & Stats --}}
        @php
            $totalCount = count($attendanceStates);
            $presentCount = collect($attendanceStates)->filter()->count();
            $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;
        @endphp

        <div class="flex items-center justify-between gap-3 flex-wrap bg-white dark:bg-gray-900 p-3.5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex items-center gap-3 flex-1 min-w-[240px]">
                {{-- Live Search --}}
                <div class="relative w-full max-w-xs">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           wire:model.live.debounce.250ms="searchQuery" 
                           placeholder="Filter beneficiaries by name..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white" />
                </div>

                {{-- Status Stats Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shrink-0">
                    <span class="text-xs font-bold text-gray-900 dark:text-white">
                        {{ $presentCount }} / {{ $totalCount }} Present
                    </span>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                        ({{ $percentage }}%)
                    </span>
                </div>
            </div>

            {{-- Quick Bulk Actions & Save Button --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" 
                        wire:click="markAllPresent" 
                        class="px-2.5 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg transition-colors cursor-pointer">
                    ✓ Mark All Present
                </button>
                <button type="button" 
                        wire:click="markAllAbsent" 
                        class="px-2.5 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 rounded-lg transition-colors cursor-pointer">
                    ✕ Mark All Absent
                </button>
                <button type="button" 
                        wire:click="saveAttendance" 
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-all duration-150 transform active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Attendance</span>
                </button>
            </div>
        </div>

        {{-- Beneficiary Attendance Roster Grid --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            @if($beneficiaries->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                    No active beneficiaries found for the selected scope.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4">
                    @foreach($beneficiaries as $beneficiary)
                        @php
                            $isPresent = $attendanceStates[$beneficiary->id] ?? false;
                        @endphp
                        <div wire:click="toggleBeneficiary({{ $beneficiary->id }})" 
                             class="p-3 rounded-xl border transition-all duration-150 cursor-pointer select-none flex items-center justify-between gap-3 {{ $isPresent ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-800' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100 dark:bg-gray-800/40 dark:border-gray-700 dark:hover:bg-gray-800' }}">
                            
                            <div class="flex items-center gap-2.5 min-w-0">
                                {{-- Avatar Initials --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isPresent ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ strtoupper(substr($beneficiary->name, 0, 2)) }}
                                </div>
                                <div class="truncate">
                                    <div class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                        {{ $beneficiary->name }}
                                    </div>
                                    @if($beneficiary->team)
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                            Team: {{ $beneficiary->team->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Badge / Button --}}
                            <div class="shrink-0">
                                @if($isPresent)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-600 text-white shadow-sm">
                                        ✓ Present
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium text-gray-500 bg-gray-200/80 dark:bg-gray-700 dark:text-gray-400">
                                        — Absent
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Bottom Sticky Save Bar for Convenience --}}
        @if($beneficiaries->isNotEmpty())
        <div class="sticky bottom-4 z-20 flex items-center justify-between p-3.5 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl">
            <div class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                Ready to record attendance for <strong class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($sessionDate)->format('l, F j, Y') }}</strong>
            </div>
            <button type="button" 
                    wire:click="saveAttendance" 
                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition-all duration-150 transform active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Session Attendance</span>
            </button>
        </div>
        @endif
    </div>
</x-filament-panels::page>
