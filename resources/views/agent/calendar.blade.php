@extends('layouts.agent')

@section('page_title', 'Agent Availability Calendar')

@section('content')
<div class="space-y-6">

    <!-- Overview Header -->
    <div class="glass-panel p-6 rounded-3xl">
        <h3 class="font-outfit font-bold text-base text-slate-200">Manage Availability Calendar</h3>
        <p class="text-xs text-slate-400 mt-1">Configure your personal calendar blocks. Booking requests and site visits from buyers will automatically block dates defined here.</p>
    </div>

    <!-- Calendar Management Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add Block Form -->
        <div class="glass-panel p-6 rounded-3xl h-fit">
            <h3 class="font-outfit font-bold text-sm text-slate-200 mb-4">Mark Date as Unavailable</h3>
            
            <form action="{{ route('agent.calendar.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Date Picker -->
                <div>
                    <label for="unavailable_date" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Date</label>
                    <input type="date" id="unavailable_date" name="unavailable_date" required min="{{ date('Y-m-d') }}"
                           class="w-full bg-slate-950 border border-slate-900 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Reason / Label</label>
                    <input type="text" id="reason" name="reason" placeholder="e.g. Vacation, Doctor Appointment" 
                           class="w-full bg-slate-950 border border-slate-900 rounded-xl py-2.5 px-3 text-xs text-slate-200 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition duration-200">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="water-wave-btn w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold tracking-wide rounded-xl shadow-lg shadow-purple-500/10 hover:shadow-purple-500/20 active:scale-[0.98] transition duration-200">
                    <span class="relative z-10">Add Calendar Block</span>
                </button>

            </form>
        </div>

        <!-- Right Side: Interactive Month Calendar and Blocks Table -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Month Calendar Widget -->
            <div class="glass-panel p-6 rounded-3xl space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="font-outfit font-bold text-sm text-slate-200">Interactive Calendar Grid</h3>
                    <span class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded bg-red-500/20 border border-red-500/40 inline-block"></span>
                        Unavailable Blocks
                        <span class="w-2.5 h-2.5 rounded bg-purple-500/20 border border-purple-500/40 inline-block ml-2"></span>
                        Today
                    </span>
                </div>

                <!-- Calendar Header controls -->
                <div class="flex justify-between items-center bg-slate-950/40 p-3 rounded-2xl border border-slate-900/60">
                    <button type="button" id="prev-month" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 flex items-center justify-center transition duration-200">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <h3 id="calendar-month-year" class="font-outfit font-extrabold text-xs md:text-sm text-slate-200 tracking-wide"></h3>
                    <button type="button" id="next-month" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 flex items-center justify-center transition duration-200">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>

                <!-- Days of the week row -->
                <div class="grid grid-cols-7 gap-2 text-center text-[10px] font-bold text-purple-400 uppercase tracking-widest px-2">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>

                <!-- Calendar Days Grid -->
                <div id="calendar-days" class="grid grid-cols-7 gap-2 px-2">
                    <!-- Days will be generated dynamically by JS -->
                </div>
            </div>

            <!-- Blocked Dates List -->
            <div class="glass-panel p-6 rounded-3xl">
                <h3 class="font-outfit font-bold text-sm text-slate-200 mb-4">Your Registered Blocks</h3>
                
                <div class="overflow-hidden rounded-2xl border border-slate-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-900 bg-slate-950/40 text-slate-400 text-[10px] font-semibold uppercase tracking-wider">
                                    <th class="py-3 px-4">Blocked Date</th>
                                    <th class="py-3 px-4">Reason / Label</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 text-xs text-slate-200">
                                @forelse($unavailableDates as $dateBlock)
                                    <tr class="hover:bg-slate-950/20 transition duration-150">
                                        <td class="py-3.5 px-4 font-bold text-purple-400">
                                            {{ date('Y-m-d (l)', strtotime($dateBlock->unavailabledate)) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-300">
                                            {{ $dateBlock->reason }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <form action="{{ route('agent.calendar.delete', $dateBlock->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure you want to remove this block? This date will become open for site visits.')"
                                                        class="text-red-400 hover:text-red-300 text-xs font-bold transition duration-200 hover:underline">
                                                    <i class="fa-solid fa-trash-can mr-1"></i>
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i class="fa-solid fa-calendar-check text-2xl text-slate-600"></i>
                                                <span>No blocked dates registered. You are fully available.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unavailableDates = @json($unavailableDates);
        
        // Map blocked dates to rapid lookup: 'YYYY-MM-DD'
        const blockedDatesMap = {};
        unavailableDates.forEach(item => {
            if (item.unavailabledate) {
                const dateStr = item.unavailabledate.split(' ')[0];
                blockedDatesMap[dateStr] = {
                    id: item.id,
                    reason: item.reason
                };
            }
        });

        let currentDate = new Date();

        function renderCalendar() {
            const calendarDays = document.getElementById('calendar-days');
            const calendarMonthYear = document.getElementById('calendar-month-year');
            
            if (!calendarDays || !calendarMonthYear) return;
            
            calendarDays.innerHTML = '';
            
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            calendarMonthYear.textContent = `${monthNames[month]} ${year}`;
            
            // First day index
            const firstDayIndex = new Date(year, month, 1).getDay();
            
            // Last day of month
            const lastDay = new Date(year, month + 1, 0).getDate();
            
            // Last day of previous month
            const prevLastDay = new Date(year, month, 0).getDate();
            
            // Render previous month blank padding days
            for (let i = firstDayIndex; i > 0; i--) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'text-slate-800 text-center py-3 text-xs font-semibold select-none';
                dayDiv.textContent = prevLastDay - i + 1;
                calendarDays.appendChild(dayDiv);
            }
            
            // Render current month days
            for (let i = 1; i <= lastDay; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'text-center py-3 text-xs font-bold rounded-xl cursor-pointer transition duration-200 relative group flex flex-col items-center justify-center h-10';
                
                const dayStr = String(i).padStart(2, '0');
                const monthStr = String(month + 1).padStart(2, '0');
                const dateKey = `${year}-${monthStr}-${dayStr}`;
                
                dayDiv.textContent = i;
                
                // If blocked
                if (blockedDatesMap[dateKey]) {
                    dayDiv.classList.add('bg-red-500/20', 'border', 'border-red-500/40', 'text-red-400');
                    
                    const dot = document.createElement('span');
                    dot.className = 'w-1 h-1 bg-red-400 rounded-full mt-0.5';
                    dayDiv.appendChild(dot);
                    
                    const tooltip = document.createElement('span');
                    tooltip.className = 'absolute bottom-full mb-2 hidden group-hover:block bg-slate-950 border border-slate-850 text-[10px] text-slate-300 py-1 px-2.5 rounded-lg shadow-2xl whitespace-nowrap z-50';
                    tooltip.textContent = blockedDatesMap[dateKey].reason || 'Unavailable';
                    dayDiv.appendChild(tooltip);
                } else {
                    const today = new Date();
                    const isToday = today.getDate() === i && today.getMonth() === month && today.getFullYear() === year;
                    
                    if (isToday) {
                        dayDiv.classList.add('bg-purple-500/20', 'border', 'border-purple-500/40', 'text-purple-300');
                    } else {
                        dayDiv.classList.add('bg-slate-900/30', 'hover:bg-purple-500/10', 'text-slate-300');
                    }
                    
                    // Click to set date picker input value
                    dayDiv.addEventListener('click', function() {
                        const dateInput = document.getElementById('unavailable_date');
                        if (dateInput) {
                            dateInput.value = dateKey;
                            
                            // Bounce target animation effect to notify user
                            dateInput.classList.add('border-purple-500', 'scale-[1.02]');
                            setTimeout(() => {
                                dateInput.classList.remove('scale-[1.02]');
                            }, 200);
                        }
                    });
                }
                
                calendarDays.appendChild(dayDiv);
            }
        }

        // Controls
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });
        }

        renderCalendar();
    });
</script>
@endsection
