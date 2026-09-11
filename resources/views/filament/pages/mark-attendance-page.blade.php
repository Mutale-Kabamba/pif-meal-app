<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- Top Navigation & Action Pills (Image 2 style) --}}
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; background: #ffffff; padding: 0.85rem 1.25rem; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div>
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #059669;">
                    Academics & Content &bull; Live Session Register &amp; Tracking
                </div>
                <h1 style="font-size: 1.35rem; font-weight: 800; color: #111827; margin: 0; padding: 0;" class="dark:!text-white">
                    Attendance Register
                </h1>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                {{-- Active Markable Register Button --}}
                <button type="button" 
                        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background-color: #0f766e; color: #ffffff; border: none; cursor: default;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Markable Register</span>
                </button>

                {{-- All Sessions / Monthly Register Link --}}
                <a href="{{ route('filament.admin.pages.project-registers') }}" 
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background-color: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; text-decoration: none;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>All Sessions</span>
                </a>

                {{-- Export Excel --}}
                <button type="button" 
                        onclick="window.print()" 
                        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background-color: #ffffff; color: #059669; border: 1px solid #059669; cursor: pointer;" class="dark:!bg-gray-900">
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export Excel</span>
                </button>

                {{-- Export Register PDF (matching Image 2 top right) --}}
                <button type="button" 
                        wire:click="exportPdf" 
                        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem; background-color: #ffffff; color: #e11d48; border: 1px solid #e11d48; cursor: pointer; transition: all 0.15s;" class="dark:!bg-gray-900">
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Export Register PDF</span>
                </button>
            </div>
        </div>

        {{-- Filters Strip (Image 2 style) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; background: #ffffff; padding: 1rem 1.25rem; border-radius: 1rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            {{-- Filter by Course / Project --}}
            <div>
                <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">
                    Filter By Course
                </label>
                <select wire:model.live="selectedProjectId" 
                        @disabled($lockProject) 
                        style="width: 100%; font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    @unless($lockProject)
                        <option value="">— All Courses / Projects —</option>
                    @endunless
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Class / Team Filter --}}
            <div>
                <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">
                    {{ $isFootballProject ? 'Football Team' : 'Literacy Class' }}
                </label>
                @if($showTeamFilter)
                    <select wire:model.live="selectedTeamId" 
                            style="width: 100%; font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white">
                        <option value="">— All {{ $isFootballProject ? 'Teams' : 'Classes' }} —</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div style="font-size: 0.8rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; background: #f9fafb; color: #374151;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-200">
                        {{ $groupBadge }}
                    </div>
                @endif
            </div>

            {{-- Active Session Date & Title --}}
            <div>
                <label style="display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; margin-bottom: 0.35rem;" class="dark:!text-gray-400">
                    Active Session Date
                </label>
                <input type="date" 
                       wire:model.live="sessionDate" 
                       style="width: 100%; font-size: 0.8rem; font-weight: 600; padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white" />
            </div>
        </div>

        {{-- Active Session Details Card (Image 2 style) --}}
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);" class="dark:!bg-gray-900 dark:!border-gray-800">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1; min-width: 280px;">
                    {{-- Badge pills --}}
                    <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 0.35rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;" class="dark:!bg-emerald-950 dark:!border-emerald-800 dark:!text-emerald-300">
                            {{ $courseCode }}
                        </span>
                        <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 0.35rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;" class="dark:!bg-blue-950 dark:!border-blue-800 dark:!text-blue-300">
                            {{ $groupBadge }}
                        </span>
                        <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: 0.35rem; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;" class="dark:!bg-emerald-900/60 dark:!text-emerald-200">
                            Active Session
                        </span>
                    </div>

                    {{-- Session Title --}}
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #111827; margin: 0 0 0.4rem 0;" class="dark:!text-white">
                        {{ $sessionTitle }}
                    </h2>

                    {{-- Metadata info strip --}}
                    <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; font-size: 0.75rem; color: #6b7280;" class="dark:!text-gray-400">
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg style="width: 0.9rem; height: 0.9rem; color: #059669;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <strong>{{ $selectedProject ? $selectedProject->name : 'All Programmes' }}</strong>
                        </span>

                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg style="width: 0.9rem; height: 0.9rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($sessionDate)->format('l, F j, Y') }}</span>
                        </span>

                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg style="width: 0.9rem; height: 0.9rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $sessionTime }}</span>
                        </span>

                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <svg style="width: 0.9rem; height: 0.9rem; color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ $instructorTitle }}: {{ $instructorName }}</span>
                        </span>
                    </div>
                </div>

                {{-- Sync Session Roster Button --}}
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                    <button type="button" 
                            wire:click="syncSessionRoster" 
                            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.5rem; background-color: #ffffff; color: #0f766e; border: 1px solid #0f766e; cursor: pointer;" class="dark:!bg-gray-800 dark:!text-teal-400">
                        <svg style="width: 0.9rem; height: 0.9rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Sync Session Roster</span>
                    </button>
                    <span style="font-size: 0.65rem; color: #9ca3af;">Auto-synced with active enrollments</span>
                </div>
            </div>
        </div>

        {{-- 6 Real-Time KPI Metric Cards (Image 2 style) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
            {{-- Registered Expected --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Registered
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #111827; margin: 0.2rem 0;" class="dark:!text-white">
                    {{ $registeredCount }}
                </div>
                <div style="font-size: 0.7rem; color: #6b7280;">Total Expected</div>
            </div>

            {{-- Present --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Present
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #059669; margin: 0.2rem 0;">
                    {{ $presentCount }}
                </div>
                <div style="font-size: 0.7rem; color: #059669; font-weight: 600;">
                    {{ $registeredCount > 0 ? round(($presentCount / $registeredCount) * 100) : 0 }}% {{ $percentOfLabel }}
                </div>
            </div>

            {{-- Absent --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Absent
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #e11d48; margin: 0.2rem 0;">
                    {{ $absentCount }}
                </div>
                <div style="font-size: 0.7rem; color: #e11d48; font-weight: 600;">
                    {{ $registeredCount > 0 ? round(($absentCount / $registeredCount) * 100) : 0 }}% {{ $percentOfLabel }}
                </div>
            </div>

            {{-- Late --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Late
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #d97706; margin: 0.2rem 0;">
                    {{ $lateCount }}
                </div>
                <div style="font-size: 0.7rem; color: #d97706; font-weight: 600;">
                    {{ $registeredCount > 0 ? round(($lateCount / $registeredCount) * 100) : 0 }}% {{ $percentOfLabel }}
                </div>
            </div>

            {{-- Apology / Excused --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Apology / Excused
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #2563eb; margin: 0.2rem 0;">
                    {{ $apologyCount }}
                </div>
                <div style="font-size: 0.7rem; color: #2563eb; font-weight: 600;">
                    Approved Leave
                </div>
            </div>

            {{-- Attendance Rate % with Progress Bar --}}
            <div style="background: #ffffff; padding: 1rem; border-radius: 0.85rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" class="dark:!bg-gray-900 dark:!border-gray-800">
                <div style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #6b7280;" class="dark:!text-gray-400">
                    Attendance Rate
                </div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #0f766e; margin: 0.2rem 0;">
                    {{ $attendanceRate }}%
                </div>
                <div style="width: 100%; height: 0.35rem; background: #e5e7eb; border-radius: 9999px; overflow: hidden; margin-top: 0.35rem;" class="dark:!bg-gray-700">
                    <div style="height: 100%; width: {{ $attendanceRate }}%; background: #059669; border-radius: 9999px;"></div>
                </div>
            </div>
        </div>

        {{-- Search, Status Filters & Bulk Mark Strip (Image 2 style) --}}
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; background: #ffffff; padding: 0.75rem 1.25rem; border-radius: 0.85rem; border: 1px solid #e5e7eb;" class="dark:!bg-gray-900 dark:!border-gray-800">
            {{-- Search student input --}}
            <div style="position: relative; flex: 1; min-width: 220px; max-width: 360px;">
                <svg style="width: 1rem; height: 1rem; position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       wire:model.live.debounce.250ms="searchQuery" 
                       placeholder="Search student by name or email..." 
                       style="width: 100%; padding: 0.45rem 0.75rem 0.45rem 2.25rem; font-size: 0.75rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background: #ffffff; color: #111827;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white" />
            </div>

            {{-- Status Filter Tabs --}}
            <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                <button type="button" 
                        wire:click="setStatusFilter('all')" 
                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; border: none; cursor: pointer; {{ $statusFilter === 'all' ? 'background: #0f766e; color: #ffffff;' : 'background: #f3f4f6; color: #4b5563;' }}" class="dark:!bg-gray-800 dark:!text-gray-300">
                    All
                </button>
                <button type="button" 
                        wire:click="setStatusFilter('present')" 
                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; border: none; cursor: pointer; {{ $statusFilter === 'present' ? 'background: #059669; color: #ffffff;' : 'background: #f3f4f6; color: #4b5563;' }}" class="dark:!bg-gray-800 dark:!text-gray-300">
                    Present
                </button>
                <button type="button" 
                        wire:click="setStatusFilter('absent')" 
                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; border: none; cursor: pointer; {{ $statusFilter === 'absent' ? 'background: #e11d48; color: #ffffff;' : 'background: #f3f4f6; color: #4b5563;' }}" class="dark:!bg-gray-800 dark:!text-gray-300">
                    Absent
                </button>
                <button type="button" 
                        wire:click="setStatusFilter('late')" 
                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; border: none; cursor: pointer; {{ $statusFilter === 'late' ? 'background: #d97706; color: #ffffff;' : 'background: #f3f4f6; color: #4b5563;' }}" class="dark:!bg-gray-800 dark:!text-gray-300">
                    Late
                </button>
                <button type="button" 
                        wire:click="setStatusFilter('apology')" 
                        style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; border: none; cursor: pointer; {{ $statusFilter === 'apology' ? 'background: #2563eb; color: #ffffff;' : 'background: #f3f4f6; color: #4b5563;' }}" class="dark:!bg-gray-800 dark:!text-gray-300">
                    Apology
                </button>
            </div>

            {{-- Bulk Mark Buttons (Image 2 style) --}}
            @if($canMark)
                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                    <span style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; color: #6b7280; text-transform: uppercase;">
                        Bulk Mark:
                    </span>
                    <button type="button" 
                            wire:click="markAll('present')" 
                            style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; background: #ffffff; color: #059669; border: 1.5px solid #059669; cursor: pointer;" class="dark:!bg-gray-900">
                        Mark All Present
                    </button>
                    <button type="button" 
                            wire:click="markAll('absent')" 
                            style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; background: #ffffff; color: #e11d48; border: 1.5px solid #e11d48; cursor: pointer;" class="dark:!bg-gray-900">
                        Mark All Absent
                    </button>
                    <button type="button" 
                            wire:click="markAll('late')" 
                            style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 0.45rem; background: #ffffff; color: #d97706; border: 1.5px solid #d97706; cursor: pointer;" class="dark:!bg-gray-900">
                        Mark All Late
                    </button>
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 0.4rem; background: #fef2f2; border: 1px solid #fecaca; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; color: #991b1b; font-weight: 600;" class="dark:!bg-red-950/50 dark:!border-red-900 dark:!text-red-300">
                    <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Monitoring Mode (Marking restricted to Coaches &amp; Project Officers)</span>
                </div>
            @endif
        </div>

        {{-- Student Attendance List Table (Image 1 style) --}}
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.03);" class="dark:!bg-gray-900 dark:!border-gray-800">
            @php
                $filteredBeneficiaries = $beneficiaries->filter(function($b) use ($statusFilter) {
                    if ($statusFilter === 'all') return true;
                    $status = $this->attendanceStatuses[$b->id] ?? 'present';
                    return $status === $statusFilter;
                });
            @endphp

            @if($filteredBeneficiaries->isEmpty())
                <div style="padding: 3rem; text-align: center; color: #6b7280; font-size: 0.85rem;">
                    No beneficiaries found matching current filters.
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                                <th style="padding: 0.85rem 1rem; width: 40px; text-align: center;">#</th>
                                <th style="padding: 0.85rem 1rem; width: 280px;">Student</th>
                                <th style="padding: 0.85rem 1rem; width: 360px; text-align: center;">Mark Attendance Status</th>
                                <th style="padding: 0.85rem 1rem;">Remarks / Notes</th>
                            </tr>
                        </thead>
                        <tbody style="divide-y divide-gray-200;">
                            @foreach($filteredBeneficiaries as $index => $beneficiary)
                                @php
                                    $currentStatus = $attendanceStatuses[$beneficiary->id] ?? 'present';
                                    $studentNote   = $attendanceNotes[$beneficiary->id] ?? '';
                                    $initials      = strtoupper(substr(trim($beneficiary->name), 0, 2));
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.15s;" class="hover:!bg-gray-50/80 dark:hover:!bg-gray-800/40 dark:!border-gray-800">
                                    {{-- Index --}}
                                    <td style="padding: 0.75rem 1rem; text-align: center; color: #94a3b8; font-weight: 700; font-size: 0.75rem;">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- Student Info with Avatar --}}
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            {{-- Pastel Teal Circle Avatar --}}
                                            <div style="width: 2.25rem; height: 2.25rem; border-radius: 9999px; background: #ccfbf1; color: #0f766e; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem; flex-shrink: 0;" class="dark:!bg-teal-950 dark:!text-teal-300">
                                                {{ $initials }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div style="font-weight: 800; color: #111827; font-size: 0.825rem;" class="dark:!text-white">
                                                    {{ $beneficiary->name }}
                                                </div>
                                                <div style="font-size: 0.7rem; color: #64748b;" class="dark:!text-gray-400">
                                                    @php
                                                        $rowTeam = $beneficiary->team ? $beneficiary->team->name : ($team ? $team->name : ($selectedProject ? $selectedProject->name : 'PIF'));
                                                        $rowCode = $beneficiary->shortcode ?: ('PIF-' . str_pad($beneficiary->id, 5, '0', STR_PAD_LEFT));
                                                    @endphp
                                                    {{ $isFootballProject ? 'Team' : 'Class' }}: {{ $rowTeam }} &bull; Code: {{ $rowCode }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 4 Segmented Status Buttons (Image 1 style) --}}
                                    <td style="padding: 0.75rem 1rem; text-align: center;">
                                        <div style="display: inline-flex; align-items: center; background: #f1f5f9; padding: 0.2rem; border-radius: 0.5rem; gap: 0.2rem; border: 1px solid #e2e8f0;" class="dark:!bg-gray-800 dark:!border-gray-700">
                                            {{-- Present Button --}}
                                            <button type="button" 
                                                    @if($canMark) wire:click="setStatus({{ $beneficiary->id }}, 'present')" @else disabled @endif
                                                    style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.65rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.4rem; border: none; transition: all 0.15s; {{ $currentStatus === 'present' ? 'background: #059669; color: #ffffff; box-shadow: 0 1px 2px rgba(5,150,105,0.3);' : 'background: transparent; color: #64748b; cursor: pointer;' }} {{ !$canMark ? 'cursor: default;' : '' }}">
                                                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Present</span>
                                            </button>

                                            {{-- Absent Button --}}
                                            <button type="button" 
                                                    @if($canMark) wire:click="setStatus({{ $beneficiary->id }}, 'absent')" @else disabled @endif
                                                    style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.65rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.4rem; border: none; transition: all 0.15s; {{ $currentStatus === 'absent' ? 'background: #e11d48; color: #ffffff; box-shadow: 0 1px 2px rgba(225,29,72,0.3);' : 'background: transparent; color: #64748b; cursor: pointer;' }} {{ !$canMark ? 'cursor: default;' : '' }}">
                                                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Absent</span>
                                            </button>

                                            {{-- Late Button --}}
                                            <button type="button" 
                                                    @if($canMark) wire:click="setStatus({{ $beneficiary->id }}, 'late')" @else disabled @endif
                                                    style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.65rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.4rem; border: none; transition: all 0.15s; {{ $currentStatus === 'late' ? 'background: #d97706; color: #ffffff; box-shadow: 0 1px 2px rgba(217,119,6,0.3);' : 'background: transparent; color: #64748b; cursor: pointer;' }} {{ !$canMark ? 'cursor: default;' : '' }}">
                                                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Late</span>
                                            </button>

                                            {{-- Apology Button --}}
                                            <button type="button" 
                                                    @if($canMark) wire:click="setStatus({{ $beneficiary->id }}, 'apology')" @else disabled @endif
                                                    style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.65rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.4rem; border: none; transition: all 0.15s; {{ $currentStatus === 'apology' ? 'background: #2563eb; color: #ffffff; box-shadow: 0 1px 2px rgba(37,99,235,0.3);' : 'background: transparent; color: #64748b; cursor: pointer;' }} {{ !$canMark ? 'cursor: default;' : '' }}">
                                                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                </svg>
                                                <span>Apology</span>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Remarks / Notes Input (Image 1 style) --}}
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="position: relative; display: flex; align-items: center;">
                                            <input type="text" 
                                                   wire:model.defer="attendanceNotes.{{ $beneficiary->id }}" 
                                                   @disabled(!$canMark)
                                                   placeholder="Optional note / reason..." 
                                                   style="width: 100%; font-size: 0.75rem; padding: 0.45rem 2rem 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; background: #ffffff; color: #1e293b;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-white disabled:opacity-60 disabled:cursor-not-allowed" />
                                            
                                            <div style="position: absolute; right: 0.5rem; display: flex; align-items: center; gap: 0.2rem; font-size: 0.65rem; font-weight: 700; color: #059669;">
                                                <svg style="width: 0.8rem; height: 0.8rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>XP</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Bottom Sticky Save Bar --}}
        @if($canMark)
            <div style="position: sticky; bottom: 1rem; z-index: 20; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding: 0.85rem 1.5rem; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); border-radius: 1rem; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);" class="dark:!bg-gray-900/95 dark:!border-gray-800">
                <div style="font-size: 0.8rem; color: #4b5563;" class="dark:!text-gray-300">
                    Ready to record attendance for <strong style="color: #111827;" class="dark:!text-white">{{ \Carbon\Carbon::parse($sessionDate)->format('l, F j, Y') }}</strong>
                </div>
                <button type="button" 
                        wire:click="saveAttendance" 
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 0.75rem; background: #059669; color: #ffffff; border: none; box-shadow: 0 4px 6px -1px rgba(5,150,105,0.25); cursor: pointer; transition: all 0.15s;">
                    <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Session Attendance</span>
                </button>
            </div>
        @else
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1.25rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid #e2e8f0; font-size: 0.75rem; color: #64748b;" class="dark:!bg-gray-800 dark:!border-gray-700 dark:!text-gray-300">
                <span><strong>Administrator Monitoring View:</strong> Attendance registers are marked by assigned coaches &amp; project officers. You can monitor sessions and export official sheets.</span>
                <button type="button" wire:click="exportPdf" style="font-weight: 700; color: #e11d48; text-decoration: underline; background: none; border: none; cursor: pointer;">
                    Download Official Register PDF &rarr;
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
