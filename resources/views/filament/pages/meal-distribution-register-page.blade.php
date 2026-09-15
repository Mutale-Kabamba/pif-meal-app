<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        {{-- Top Header Banner & Actions --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; background: #ffffff; padding: 0.85rem 1.25rem; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.65rem; background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706;" class="dark:!bg-amber-950/40 dark:!text-amber-300">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;" class="dark:!text-white">
                        Meal Distribution Register (Cooks)
                    </h2>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0; margin-top: 2px;">
                        Feeding records sourced exclusively from kitchen cooks recording meal distributions at the terminal
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                {{-- Export Register PDF --}}
                <a href="{{ $this->getPdfExportUrl() }}" 
                   target="_blank"
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.85rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background: #ffffff; color: #e11d48; border: 1.5px solid #e11d48; text-decoration: none; cursor: pointer;" class="dark:!bg-gray-900">
                    <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Export Meal Register PDF</span>
                </a>
            </div>
        </div>

        {{-- Cook Terminal Information Banner --}}
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.75rem; font-size: 0.75rem; color: #92400e;" class="dark:!bg-amber-950/30 dark:!border-amber-900/60 dark:!text-amber-300">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><strong>Cook Terminal Data Feed:</strong> This register feeds automatically from the Cook Terminal. Only cooks have access to the terminal to mark and distribute meals.</span>
            </div>
            <span style="font-weight: 800; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; background: #fef3c7; color: #b45309; padding: 0.2rem 0.5rem; border-radius: 0.35rem;" class="dark:!bg-amber-900/60 dark:!text-amber-200">
                Cooks Only Terminal
            </span>
        </div>

        {{-- Summary Stats Row --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
            <div style="background: #ffffff; padding: 0.85rem 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div>
                    <div style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">Total Meals Distributed</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #047857; margin-top: 2px;">{{ number_format($totalMealsCount) }}</div>
                </div>
                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;" class="dark:!bg-emerald-950/50">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <div style="background: #ffffff; padding: 0.85rem 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div>
                    <div style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">Beneficiaries Fed</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #0f172a; margin-top: 2px;" class="dark:!text-white">{{ number_format($uniqueBeneficiariesFed) }} <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">/ {{ $beneficiaries->count() }}</span></div>
                </div>
                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;" class="dark:!bg-blue-950/50">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <div style="background: #ffffff; padding: 0.85rem 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div>
                    <div style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">Distribution Days</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #d97706; margin-top: 2px;">{{ count($dailyTotals) }} <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">days active</span></div>
                </div>
                <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706;" class="dark:!bg-amber-950/50">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filter Strip --}}
        <div style="padding: 1rem; background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">

                {{-- Programme Scope --}}
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

                {{-- Team (football projects only) --}}
                @if($showTeamFilter)
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Team</label>
                    <select wire:model.live="selectedTeamId" style="width: 100%; font-size: 0.75rem; padding: 0.45rem 0.65rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        <option value="">— All Teams —</option>
                        @foreach($teams as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Month --}}
                <div>
                    <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">Calendar Month</label>
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

            {{-- Operational Context Badge --}}
            <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;" class="dark:!border-gray-800">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem;">
                    <span style="font-weight: 700; color: #111827;" class="dark:!text-white">Feeding Stream:</span>
                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.55rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.4rem; color: #92400e; font-weight: 600;" class="dark:!bg-amber-950/30 dark:!border-amber-900/60 dark:!text-amber-300">
                        {{ $registerLabel }}
                    </span>
                    <span style="color: #6b7280;">&bull;</span>
                    <span style="color: #059669; font-weight: 600;">
                        {{ $selectedMonthName }}
                    </span>
                </div>

                <div style="font-size: 0.75rem; color: #6b7280;">
                    {{ $beneficiaries->count() }} registered beneficiar{{ $beneficiaries->count() === 1 ? 'y' : 'ies' }}
                </div>
            </div>
        </div>

        {{-- Meal Distribution Grid (Monthly Table) --}}
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
                                    Meals
                                </th>
                            </tr>
                            
                            <tr style="background: #ffffff; color: #64748b; font-size: 0.65rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 20;" class="dark:!bg-gray-800/60 dark:!border-gray-700 dark:!text-gray-400">
                                <th style="padding: 0.5rem 0.75rem; position: sticky; left: 0; background: #ffffff; z-index: 30; width: 260px; border-right: 1px solid #e2e8f0; text-align: left;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                    Beneficiary Details
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
                                @php $rowMealsCount = 0; @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" class="hover:!bg-gray-50/80 dark:hover:!bg-gray-800/40 dark:!border-gray-800">
                                    <td style="padding: 0.45rem 0.75rem; font-weight: 600; position: sticky; left: 0; background: #ffffff; z-index: 10; border-right: 1px solid #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 260px; max-width: 260px; color: #111827;" class="dark:!bg-gray-900 dark:!border-gray-700 dark:!text-white" title="{{ $beneficiary->name }}">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:!text-white">{{ $beneficiary->name }}</div>
                                        <div style="font-size: 0.65rem; color: #64748b; margin-top: 1px; display: flex; align-items: center; gap: 0.35rem; flex-wrap: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            @php
                                                $mParts = [];
                                                if ($beneficiary->team) {
                                                    $mParts[] = '<span style="font-weight: 700; color: #0f766e;">' . e($beneficiary->team->name) . '</span>';
                                                }
                                                if ($beneficiary->relationLoaded('projects') && $beneficiary->projects->isNotEmpty()) {
                                                    foreach ($beneficiary->projects as $p) {
                                                        if ($beneficiary->team && $p->programme_type === \App\Models\Project::PROGRAMME_FOOTBALL) {
                                                            continue;
                                                        }
                                                        $mParts[] = '<span style="font-weight: 600; color: #2563eb;">' . e($p->name) . '</span>';
                                                    }
                                                } elseif (!$beneficiary->team) {
                                                    $mParts[] = '<span style="font-weight: 600; color: #0f766e;">Beneficiary</span>';
                                                }
                                                $mParts[] = '<span style="font-family: monospace; color: #475569;">' . e($beneficiary->shortcode ?? ('PIF-' . $beneficiary->id)) . '</span>';
                                                if (!empty($beneficiary->phone_number)) {
                                                    $mParts[] = '<span style="color: #0369a1; font-weight: 600;">' . e($beneficiary->phone_number) . '</span>';
                                                }
                                            @endphp
                                            {!! implode(' <span style="color: #cbd5e1;">&bull;</span> ', $mParts) !!}
                                        </div>
                                    </td>
                                    
                                    @foreach($weeksStructure as $weekNum => $days)
                                        @foreach($days as $dayMeta)
                                            @php 
                                                 $dayNum = $dayMeta['day_number'];
                                                 $count = $mealMatrix[$beneficiary->id][$dayNum] ?? 0;
                                                 if ($count > 0) $rowMealsCount += $count;
                                            @endphp
                                            <td style="padding: 0.15rem 0.1rem; border-right: 1px solid #f1f5f9; text-align: center; user-select: none;" 
                                                class="dark:!border-gray-800"
                                                title="Day {{ $dayNum }}: {{ $count > 0 ? $count . ' meal(s) served at Cook Terminal' : 'No meal recorded' }}">
                                                @if($count > 0)
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 900; border-radius: 0.25rem; background: #059669; color: #ffffff;">
                                                        {{ $count > 1 ? $count : '✓' }}
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.35rem; height: 1.15rem; font-size: 0.7rem; font-weight: 700; border-radius: 0.25rem; color: #cbd5e1;" class="dark:!text-gray-600">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                    
                                    <td style="padding: 0.45rem; text-align: center; font-family: monospace; font-weight: 900; position: sticky; right: 0; z-index: 10; border-left: 1px solid #e2e8f0; background: #f8fafc; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                                        {{ $rowMealsCount }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- Daily Distribution Totals Row --}}
                        <tfoot>
                            <tr style="background: #f1f5f9; font-size: 0.65rem; font-weight: 800; border-top: 2px solid #cbd5e1; position: sticky; bottom: 0; z-index: 20;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                <td style="padding: 0.5rem 0.75rem; position: sticky; left: 0; background: #f1f5f9; z-index: 30; border-right: 1px solid #e2e8f0; font-weight: 800; text-transform: uppercase; color: #334155;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                                    Daily Kitchen Total
                                </td>
                                @foreach($weeksStructure as $weekNum => $days)
                                    @foreach($days as $dayMeta)
                                        @php $dayTotal = $dailyTotals[$dayMeta['day_number']] ?? 0; @endphp
                                        <td style="padding: 0.35rem 0.1rem; text-align: center; border-right: 1px solid #e2e8f0; font-family: monospace; font-weight: 800; color: {{ $dayTotal > 0 ? '#047857' : '#94a3b8' }};" class="dark:!border-gray-700">
                                            {{ $dayTotal > 0 ? $dayTotal : '—' }}
                                        </td>
                                    @endforeach
                                @endforeach
                                <td style="padding: 0.5rem; text-align: center; font-family: monospace; font-size: 0.75rem; font-weight: 900; background: #e2e8f0; color: #047857; position: sticky; right: 0; z-index: 30; border-left: 1px solid #cbd5e1;" class="dark:!bg-gray-700 dark:!border-gray-600 dark:!text-emerald-400">
                                    {{ number_format($totalMealsCount) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Official Bottom Legend --}}
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding: 0.65rem 1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 0.7rem; color: #475569;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                    <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                        <span style="font-weight: 800; color: #1e293b;" class="dark:!text-white">Meal Register Legend:</span>
                        <span style="color: #059669; font-weight: 700;">&check; = Meal Distributed at Cook Terminal</span>
                        <span style="color: #94a3b8; font-weight: 700;">— = No Meal Recorded for Day</span>
                        <span style="color: #475569; font-weight: 700;">Cook Terminal logs update in real-time</span>
                    </div>

                    <div>
                        <a href="{{ $this->getPdfExportUrl() }}" target="_blank" style="font-weight: 700; color: #e11d48; text-decoration: none; cursor: pointer;">
                            Export Official Meal PDF &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
