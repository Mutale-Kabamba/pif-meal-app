<x-filament-panels::page>
    <div class="space-y-4">
        <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Project Stream Boundary</label>
                    <select wire:model.live="selectedProjectId" @disabled($lockProject) class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Target Month</label>
                    <select wire:model.live="filterMonth" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        @foreach($monthsList as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Target Calendar Year</label>
                    <select wire:model.live="filterYear" class="w-full text-xs rounded-lg border-gray-300 py-1.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        @foreach($yearsList as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
            @if($beneficiaries->isEmpty())
                <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-xs">
                    No active programmatic metadata register elements captured for boundaries.
                </div>
            @else
                <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
                    <table class="w-full text-left border-collapse min-w-max table-fixed">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-[10px] font-black tracking-widest border-b border-gray-200 dark:border-gray-700">
                                <th class="p-2 sticky left-0 bg-gray-100 dark:bg-gray-800 z-30 w-[180px] border-r border-gray-200 dark:border-gray-700"></th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    <th colspan="{{ count($days) }}" class="p-1 text-center border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 font-sans uppercase">
                                        Week {{ $weekNum }}
                                    </th>
                                @endforeach
                                <th class="p-2 text-center w-14 bg-gray-100 dark:bg-gray-800 sticky right-0 z-30 border-l border-gray-200 dark:border-gray-700"></th>
                            </tr>
                            
                            <tr class="bg-gray-50 text-gray-500 dark:bg-gray-800/40 dark:text-gray-400 text-[10px] font-bold border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20">
                                <th class="p-2 sticky left-0 bg-gray-50 dark:bg-gray-800 z-30 w-[180px] border-r border-gray-200 dark:border-gray-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Beneficiary Name</th>
                                @foreach($weeksStructure as $weekNum => $days)
                                    @foreach($days as $dayMeta)
                                        <th class="p-1 text-center w-8 border-r border-gray-200 dark:border-gray-700 font-mono text-[9px]" title="Day {{ sprintf('%02d', $dayMeta['day_number']) }}">
                                            {{ $dayMeta['day_label'] }}<span class="block text-[8px] font-normal text-gray-400">{{ sprintf('%02d', $dayMeta['day_number']) }}</span>
                                        </th>
                                    @endforeach
                                @endforeach
                                <th class="p-2 text-center w-14 bg-gray-100 dark:bg-gray-800 sticky right-0 z-30 border-l border-gray-200 dark:border-gray-700 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)] text-xs font-black">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                            @foreach($beneficiaries as $beneficiary)
                                @php $totalEaten = 0; @endphp
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="p-2 font-medium sticky left-0 bg-white dark:bg-gray-900 z-10 border-r border-gray-200 dark:border-gray-700 truncate max-w-[180px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-gray-900 dark:text-white" title="{{ $beneficiary->name }}">
                                        {{ $beneficiary->name }}
                                    </td>
                                    
                                    @foreach($weeksStructure as $weekNum => $days)
                                        @foreach($days as $dayMeta)
                                            @php 
                                                $dayNum = $dayMeta['day_number'];
                                                $hasEaten = $mealMatrix[$beneficiary->id][$dayNum] ?? false; 
                                                if($hasEaten) $totalEaten++;
                                            @endphp
                                            <td class="p-0.5 border-r border-gray-100 dark:border-gray-800 text-center select-none">
                                                @if($hasEaten)
                                                    <span class="inline-flex items-center justify-center w-6 h-5 text-[11px] font-black rounded" 
                                                          style="background-color: #22c55e !important; color: #ffffff !important; display: inline-flex !important;" 
                                                          title="Present (Meal Logged)">✓</span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-5 text-[10px] font-black rounded" 
                                                          style="background-color: #ef4444 !important; color: #ffffff !important; display: inline-flex !important;" 
                                                          title="Absent (Missed)">✕</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                    
                                    <td class="p-2 text-center font-mono font-black sticky right-0 z-10 border-l border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white shadow-[-2px_0_5px_rgba(0,0,0,0.1)]">
                                        {{ $totalEaten }}
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