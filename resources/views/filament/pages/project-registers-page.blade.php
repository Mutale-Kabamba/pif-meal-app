<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        {{-- Register Mode Switcher (Attendance vs Meals) & Top Actions --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; background: #ffffff; padding: 0.75rem 1rem; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div style="display: inline-flex; padding: 0.25rem; border-radius: 0.75rem; background: #f3f4f6; border: 1px solid #e5e7eb;" class="dark:!bg-gray-800 dark:!border-gray-700">
                <button type="button"
                        wire:click="$set('registerType', 'attendance')"
                        style="padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; display: inline-flex; align-items: center; gap: 0.4rem; border: none; cursor: pointer; transition: all 0.15s; {{ $registerType === 'attendance' ? 'background: #0f766e; color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'background: transparent; color: #4b5563;' }}" class="dark:!text-gray-300">
                    <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Attendance Register (Training / Classes)</span>
                </button>

                <button type="button"
                        wire:click="$set('registerType', 'meals')"
                        style="padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; display: inline-flex; align-items: center; gap: 0.4rem; border: none; cursor: pointer; transition: all 0.15s; {{ $registerType === 'meals' ? 'background: #0f766e; color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'background: transparent; color: #4b5563;' }}" class="dark:!text-gray-300">
                    <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    <span>Meal Distribution Register (Cooks)</span>
                </button>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @if($registerType === 'attendance')
                    @if($canMark)
                        <a href="{{ route('filament.admin.pages.mark-attendance') }}" 
                           style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; font-size: 0.75rem; font-weight: 700; color: #ffffff; background: #059669; border-radius: 0.65rem; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Mark Live Session</span>
                        </a>
                    @endif

                    {{-- Export Register PDF (Image 2 style) --}}
                    <button type="button" 
                            wire:click="exportPdf" 
                            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background: #ffffff; color: #e11d48; border: 1.5px solid #e11d48; cursor: pointer;" class="dark:!bg-gray-900">
                        <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Export Register PDF</span>
                    </button>
                @else
                    <a href="{{ route('terminal') }}" target="_blank"
                       style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; font-size: 0.75rem; font-weight: 700; color: #ffffff; background: #059669; border-radius: 0.65rem; text-decoration: none;">
                        <span>Open Kitchen Terminal &rarr;</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Role / Operational Notice --}}
        @if(!$canMark && $registerType === 'attendance')
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; font-size: 0.75rem; color: #166534;" class="dark:!bg-emerald-950/40 dark:!border-emerald-900 dark:!text-emerald-300">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span><strong>Administrator Monitoring View:</strong> Register marking is reserved for coaches &amp; project officers. Admins have viewing, audit, and PDF export capabilities.</span>
                </div>
                <span style="font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #047857;">Read-Only Mode</span>
            </div>
        @endif

        {{-- Filter Strip --}}
        <div style="padding: 1rem; background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">

                {{-- Programme Scope (admins only) --}}
                @unless($lockScope)
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Programme Scope</label>
                    <select wire:model.live="scope" style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        <option value="all">All Programmes</option>
                        <option value="education">Education / Literacy Only</option>
                        <option value="football">Football Only</option>
                    </select>
                </div>
                @endunless

                {{-- Project --}}
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Project Stream</label>
                    <select wire:model.live="selectedProjectId" @disabled($lockProject) style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white disabled:opacity-60 disabled:cursor-not-allowed">
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
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Football Team</label>
                    <select wire:model.live="selectedTeamId" style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        <option value="">— All Teams —</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Month --}}
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Month</label>
                    <select wire:model.live="filterMonth" style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        @foreach($monthsList as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Year --}}
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Year</label>
                    <select wire:model.live="filterYear" style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        @foreach($yearsList as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Register Scope Summary Bar --}}
            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;" class="dark:!border-gray-800">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; font-size: 0.75rem;">
                    <div>
                        <span style="color: #9ca3af; font-size: 0.7rem;">Viewing:</span>
                        <strong style="color: #0f766e;" class="dark:!text-teal-400">{{ $registerLabel }}</strong>
                    </div>

                    <div>
                        <span style="color: #9ca3af; font-size: 0.7rem;">Activity:</span>
                        <span style="color: #374151; font-weight: 600;" class="dark:!text-gray-300">
                            {{ $registerType === 'attendance' ? $activityLabel : 'Cooks Meal Distribution' }}
                        </span>
                    </div>

                    <div>
                        <span style="color: #9ca3af; font-size: 0.7rem;">Month:</span>
                        <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 0.35rem; background: #ecfdf5; color: #047857; font-weight: 700; font-size: 0.75rem;" class="dark:!bg-emerald-950 dark:!text-emerald-300">
                            {{ $selectedMonthName }}
                        </span>
                    </div>
                </div>

                <div style="font-size: 0.75rem; color: #6b7280;">
                    {{ $beneficiaries->count() }} enrolled beneficiar{{ $beneficiaries->count() === 1 ? 'y' : 'ies' }}
                </div>
            </div>
        </div>

        {{-- Register Grid (Monthly Table) --}}
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
            @if($beneficiaries->isEmpty())
                <div style="padding: 3rem; text-align: center; color: #6b7280; font-size: 0.85rem;">
                    No active beneficiaries found for {{ $selectedMonthName }}.
                </div>
            @else
                <div style="overflow-x: auto; max-height: 650px; overflow-y: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: max-content; table-layout: fixed;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                                <th style="padding: 0.5rem; position: sticky; left: 0; background: #f8fafc; z-index: 30; width: 260px; border-right: 1px solid #e2e8f0; text-align: left; padding-left: 0.75rem; font-weight: 800;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                    {{ $selectedMonthShort }}
                                </th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    <th colspan="{{ count($days) }}" style="padding: 0.35rem; text-align: center; border-right: 1px solid #e2e8f0; background: #f1f5f9; font-weight: 800;" class="dark:!bg-gray-800/80 dark:!border-gray-700">
                                        Week {{ $weekNum }}
                                    </th>
                                @endforeach
                                <th style="padding: 0.5rem; text-align: center; width: 65px; background: #f8fafc; position: sticky; right: 0; z-index: 30; border-left: 1px solid #e2e8f0; font-weight: 800;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                    {{ $registerType === 'attendance' ? 'Attended' : 'Meals' }}
                                </th>
                            </tr>
                            
                            <tr style="background: #ffffff; color: #64748b; font-size: 0.65rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 20;" class="dark:!bg-gray-800/60 dark:!border-gray-700 dark:!text-gray-400">
                                <th style="padding: 0.5rem 0.75rem; position: sticky; left: 0; background: #ffffff; z-index: 30; width: 260px; border-right: 1px solid #e2e8f0; text-align: left;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                    Beneficiary Name
                                </th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    @foreach($days as $dayMeta)
                                        <th style="padding: 0.35rem 0.1rem; text-align: center; width: 28px; border-right: 1px solid #f1f5f9; font-family: monospace; font-size: 0.65rem;" class="dark:!border-gray-800" title="Day {{ sprintf('%02d', $dayMeta['day_number']) }}">
                                            {{ $dayMeta['day_label'] }}<span style="display: block; font-size: 0.55rem; font-weight: 400; color: #94a3b8;">{{ sprintf('%02d', $dayMeta['day_number']) }}</span>
                                        </th>
                                    @endforeach
                                @endforeach
                                <th style="padding: 0.5rem; text-align: center; width: 65px; background: #ffffff; position: sticky; right: 0; z-index: 30; border-left: 1px solid #e2e8f0; font-size: 0.75rem; font-weight: 900;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.75rem;">
                            @foreach($beneficiaries as $beneficiary)
                                @php $totalCount = 0; @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" class="hover:!bg-gray-50/80 dark:hover:!bg-gray-800/40 dark:!border-gray-800">
                                    <td style="padding: 0.45rem 0.75rem; font-weight: 600; position: sticky; left: 0; background: #ffffff; z-index: 10; border-right: 1px solid #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 260px; max-width: 260px; color: #111827;" class="dark:!bg-gray-900 dark:!border-gray-700 dark:!text-white" title="{{ $beneficiary->name }}">
                                        {{ $beneficiary->name }}
                                    </td>
                                    
                                    @foreach($weeksStructure as $weekNum => $days)
                                        @foreach($days as $dayMeta)
                                            @php 
                                                 $dayNum = $dayMeta['day_number'];
                                                 $status = $activeMatrix[$beneficiary->id][$dayNum] ?? null;
                                                 $isMarked = !empty($status);
                                                 if ($registerType === 'attendance') {
                                                     if ($status === 'present' || $status === 'late') $totalCount++;
                                                 } else {
                                                     if ($status) $totalCount++;
                                                 }
                                            @endphp
                                            <td style="padding: 0.15rem 0.1rem; border-right: 1px solid #f1f5f9; text-align: center; user-select: none; {{ $canMark && $registerType === 'attendance' ? 'cursor: pointer;' : 'cursor: default;' }}" 
                                                class="dark:!border-gray-800"
                                                @if($canMark && $registerType === 'attendance') wire:click="toggleAttendance({{ $beneficiary->id }}, {{ $dayNum }})" title="Click to toggle attendance" @endif>
                                                @if($registerType === 'attendance')
                                                    @if($status === 'present')
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #059669; color: #ffffff;" title="Present">✓</span>
                                                    @elseif($status === 'absent')
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #e11d48; color: #ffffff;" title="Absent">✗</span>
                                                    @elseif($status === 'late')
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #d97706; color: #ffffff;" title="Late Arrival">L</span>
                                                    @elseif($status === 'apology')
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #2563eb; color: #ffffff;" title="Apology / Excused">E</span>
                                                    @else
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 400; border-radius: 0.25rem; color: #cbd5e1;" class="dark:!text-gray-600" title="Unmarked">&middot;</span>
                                                    @endif
                                                @else
                                                    @if($status)
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #059669; color: #ffffff;" title="Meal Served">✓</span>
                                                    @else
                                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 700; border-radius: 0.25rem; color: #cbd5e1;" class="dark:!text-gray-600" title="No Meal">—</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                    
                                    <td style="padding: 0.45rem; text-align: center; font-family: monospace; font-weight: 900; position: sticky; right: 0; z-index: 10; border-left: 1px solid #e2e8f0; background: #f8fafc; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                                        {{ $totalCount }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Official Bottom Legend matching Image 3 --}}
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding: 0.65rem 1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 0.7rem; color: #475569;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                    <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                        <span style="font-weight: 800; color: #1e293b;" class="dark:!text-white">Register Legend:</span>
                        <span style="color: #059669; font-weight: 700;">&check; = Present</span>
                        <span style="color: #e11d48; font-weight: 700;">&cross; = Absent</span>
                        <span style="color: #d97706; font-weight: 700;">L = Late Arrival</span>
                        <span style="color: #2563eb; font-weight: 700;">E = Apology / Excused</span>
                        <span style="color: #94a3b8; font-weight: 700;">&middot; = Unmarked</span>
                        <span style="color: #94a3b8;">— = No Session Scheduled</span>
                    </div>

                    <div>
                        <button type="button" wire:click="exportPdf" style="font-weight: 700; color: #e11d48; background: none; border: none; cursor: pointer;">
                            Export Official PDF &rarr;
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
