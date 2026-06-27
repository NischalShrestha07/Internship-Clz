<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\CalendarEvent;
use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;

class AttendanceCalendar extends Component
{

    // Basic calendar properties
    public $month;
    public $year;
    public $monthName;
    public $daysInMonth;
    public $startDay;
    public $view = 'month';
    public $selectedDate;
    public $pageTitle = 'Calendar';

    // Event management properties
    public $events = [];
    public $showAddEventModal = false;
    public $showEditEventModal = false;
    public $editingEventId;

    // Event form properties
    public $eventTitle;
    public $eventDate;
    public $eventTime;
    public $eventType = 'Meeting';
    public $eventPriority = 'Low';
    public $eventDescription;

    // Recurrence properties
    public $isRecurring = false;
    public $recurrencePattern = 'none';
    public $recurrenceInterval = 1;
    public $recurrenceWeekdays = [];
    public $endDate;
    public $currentTime;

    protected $rules = [
        'eventTitle' => 'required|string|max:255',
        'eventDate' => 'required|date',
        'eventTime' => 'nullable|date_format:H:i',
        'eventType' => 'required|in:Meeting,Leave,Notice,Holiday',
        'eventPriority' => 'required|in:Low,Medium,High',
        'eventDescription' => 'nullable|string|max:1000',
        'recurrencePattern' => 'required_if:isRecurring,true',
        'recurrenceInterval' => 'required_if:isRecurring,true|integer|min:0|max:365',
        'recurrenceWeekdays' => 'required_if:recurrencePattern,weekly|array|min:1',
        'recurrenceWeekdays.*' => 'string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        'endDate' => 'nullable|date|after_or_equal:eventDate',
    ];


    protected $messages = [
        'eventTitle.required' => 'Event title is required.',
        'eventDate.required' => 'Event date is required.',
        'eventDate.date' => 'Event date must be a valid date.',
        'eventTime.date_format' => 'Event time must be in HH:MM format.',
        'recurrencePattern.required_if' => 'Recurrence pattern is required for recurring events.',
        'recurrenceInterval.required_if' => 'Recurrence interval is required for recurring events.',
        'recurrenceWeekdays.required_if' => 'Please select at least one weekday for weekly recurrence.',
    ];

    protected $listeners = ['close' => 'closeModal'];

    public function mount()
    {
        $this->setMonth(Carbon::now('Asia/Kathmandu'));
    }

    public function setMonth($date)
    {
        $this->month = $date->month;
        $this->year = $date->year;
        $this->monthName = $date->format('F');
        $this->daysInMonth = $date->daysInMonth;
        $this->startDay = Carbon::create($this->year, $this->month, 1)->dayOfWeek;
        $this->selectedDate = $date->format('Y-m-d');
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth();

        // Initialize events array
        $this->events = [];

        // Load user events with optimized query
        $userEvents = CalendarEvent::query()
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($endOfMonth) {
                        $q->where('is_recurring', true)
                            ->where('date', '<=', $endOfMonth)
                            ->where(function ($q2) {
                                $q2->whereNull('end_date')
                                    ->orWhere('end_date', '>=', Carbon::today());
                            });
                    });
            })
            ->get();

        foreach ($userEvents as $event) {
            $this->processEvent($event);
        }

        // Load system holidays with optimized query
        $holidays = Holiday::query()
            ->where('is_active', true)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth) {
                        $q->where('is_recurring', true)
                            ->where(function ($recurring) use ($startOfMonth) {
                                $recurring->where('recurrence_pattern', 'yearly')
                                    ->orWhere('recurrence_pattern', 'monthly')
                                    ->orWhere('recurrence_pattern', 'weekly');
                            });
                    });
            })
            ->get();

        foreach ($holidays as $holiday) {
            $this->processHoliday($holiday);
        }

        // Sort events by time for each date
        foreach ($this->events as $date => &$dateEvents) {
            usort($dateEvents, function ($a, $b) {
                $timeA = $a['time'] === 'All Day' ? '23:59' : $a['time'];
                $timeB = $b['time'] === 'All Day' ? '23:59' : $b['time'];
                return strcmp($timeA, $timeB);
            });
        }
    }

    protected function processEvent($event)
    {
        if (!$event->is_recurring) {
            $dateKey = $event->date->format('Y-m-d');
            if (!isset($this->events[$dateKey])) {
                $this->events[$dateKey] = [];
            }
            $this->events[$dateKey][] = $this->formatEventForDisplay($event);
            return;
        }

        // Safely decode recurrence_weekdays if it's a string
        if (is_string($event->recurrence_weekdays)) {
            $event->recurrence_weekdays = json_decode($event->recurrence_weekdays, true) ?? [];
        }

        $current = Carbon::parse($event->date);
        $end = $event->end_date ? Carbon::parse($event->end_date) : $current->copy()->addYears(1);

        // For weekly recurrence with multiple weekdays, we need to generate all occurrences
        if ($event->recurrence_pattern === 'weekly' && !empty($event->recurrence_weekdays)) {
            $this->processWeeklyRecurrence($event, $current, $end);
        } else {
            // For other recurrence patterns, use the original logic
            $maxIterations = 500;
            $iteration = 0;

            while ($current <= $end && $iteration < $maxIterations) {
                if ($current->month == $this->month && $current->year == $this->year) {
                    $dateKey = $current->format('Y-m-d');
                    if (!isset($this->events[$dateKey])) {
                        $this->events[$dateKey] = [];
                    }
                    $this->events[$dateKey][] = $this->formatEventForDisplay($event);
                }

                $current = $this->getNextOccurrenceDate($current, $event);
                $iteration++;
            }
        }
    }

    // New helper method for weekly recurrence with multiple weekdays
    protected function processWeeklyRecurrence($event, Carbon $start, Carbon $end)
    {
        $weekdays = $event->recurrence_weekdays;
        if (is_string($weekdays)) {
            $weekdays = json_decode($weekdays, true) ?? [];
        }

        $weekdayOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $sortedWeekdays = array_intersect($weekdayOrder, $weekdays);
        $sortedWeekdays = array_values($sortedWeekdays);

        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->year, $this->month, 1)->endOfMonth();

        $currentWeek = $start->copy()->startOfWeek();
        $maxWeeks = 52; // Safety limit

        for ($week = 0; $week < $maxWeeks; $week++) {
            foreach ($sortedWeekdays as $weekday) {
                $occurrenceDate = $currentWeek->copy()->next(Carbon::parse($weekday)->dayOfWeek);

                // Skip if before start date
                if ($occurrenceDate->lt($start)) {
                    continue;
                }

                // Stop if after end date
                if ($end && $occurrenceDate->gt($end)) {
                    break 2;
                }

                // Add if within current month
                if ($occurrenceDate->month == $this->month && $occurrenceDate->year == $this->year) {
                    $dateKey = $occurrenceDate->format('Y-m-d');
                    if (!isset($this->events[$dateKey])) {
                        $this->events[$dateKey] = [];
                    }
                    $this->events[$dateKey][] = $this->formatEventForDisplay($event);
                }
            }

            // Move to next week based on interval
            $intervalWeeks = $event->recurrence_interval === 0 ? 1 : $event->recurrence_interval;
            $currentWeek->addWeeks($intervalWeeks);

            // Stop if we've gone beyond the month we're processing
            if ($currentWeek->gt($endOfMonth)) {
                break;
            }
        }
    }

    protected function processHoliday($holiday)
    {

        if (!$holiday->is_recurring) {
            $current = Carbon::parse($holiday->start_date);
            $end = Carbon::parse($holiday->end_date);

            while ($current <= $end) {
                if ($current->month == $this->month && $current->year == $this->year) {
                    $dateKey = $current->format('Y-m-d');
                    if (!isset($this->events[$dateKey])) {
                        $this->events[$dateKey] = [];
                    }
                    $this->events[$dateKey][] = $this->formatHolidayForDisplay($holiday);
                }
                $current->addDay();
            }

            return;
        }

        // Handle recurring holidays
        $currentYear = $this->year;
        $currentMonth = $this->month;

        // Safely decode recurrence_weekdays if it's a string
        $recurrenceWeekdays = $holiday->recurrence_weekdays;
        if (is_string($recurrenceWeekdays)) {
            $recurrenceWeekdays = json_decode($recurrenceWeekdays, true) ?? [];
        }

        if ($holiday->recurrence_pattern === 'yearly') {
            if ($holiday->start_date->month == $currentMonth) {
                $date = Carbon::create($currentYear, $currentMonth, $holiday->start_date->day);
                $dateKey = $date->format('Y-m-d');
                if (!isset($this->events[$dateKey])) {
                    $this->events[$dateKey] = [];
                }
                $this->events[$dateKey][] = $this->formatHolidayForDisplay($holiday);
            }
        } elseif ($holiday->recurrence_pattern === 'monthly') {
            $day = min($holiday->start_date->day, Carbon::create($currentYear, $currentMonth, 1)->daysInMonth);
            $date = Carbon::create($currentYear, $currentMonth, $day);
            $dateKey = $date->format('Y-m-d');
            if (!isset($this->events[$dateKey])) {
                $this->events[$dateKey] = [];
            }
            $this->events[$dateKey][] = $this->formatHolidayForDisplay($holiday);
        } elseif ($holiday->recurrence_pattern === 'weekly') {
            $startOfMonth = Carbon::create($currentYear, $currentMonth, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $weekdays = $recurrenceWeekdays;

            if (empty($weekdays)) {
                // If no specific weekdays, use the original holiday's weekday
                $weekdays = [strtolower($holiday->start_date->format('l'))];
            }

            $currentDate = $startOfMonth->copy();
            while ($currentDate <= $endOfMonth) {
                $currentWeekday = strtolower($currentDate->format('l'));
                if (in_array($currentWeekday, $weekdays)) {
                    $dateKey = $currentDate->format('Y-m-d');
                    if (!isset($this->events[$dateKey])) {
                        $this->events[$dateKey] = [];
                    }
                    $this->events[$dateKey][] = $this->formatHolidayForDisplay($holiday);
                }
                $currentDate->addDay();
            }
        }
    }

    protected function formatEventForDisplay($event)
    {
        $recurrenceWeekdays = $event->recurrence_weekdays;
        if (is_string($recurrenceWeekdays)) {
            $recurrenceWeekdays = json_decode($recurrenceWeekdays, true) ?? [];
        }

        return [
            'id' => $event->id,
            'title' => $event->title,
            'time' => $event->time ? Carbon::parse($event->time)->format('H:i') : 'All Day',
            'type' => $event->type,
            'priority' => $event->priority,
            'description' => $event->description,
            'is_holiday' => $event->is_holiday,
            'is_recurring' => $event->is_recurring,
            'recurrence_pattern' => $event->recurrence_pattern,
            'recurrence_interval' => $event->recurrence_interval,
            'recurrence_weekdays' => $recurrenceWeekdays,
            'end_date' => $event->end_date?->format('Y-m-d'),
            'recurrence_description' => $this->getRecurrenceDescription($event),
        ];
    }

    protected function formatHolidayForDisplay($holiday)
    {
        // Safely decode recurrence_weekdays
        $recurrenceWeekdays = $holiday->recurrence_weekdays;
        if (is_string($recurrenceWeekdays)) {
            $recurrenceWeekdays = json_decode($recurrenceWeekdays, true) ?? [];
        }
        return [
            'id' => 'holiday_' . $holiday->id,
            'title' => $holiday->title,
            'time' => 'All Day',
            'type' => 'Holiday',
            'priority' => 'High',
            'description' => $holiday->description,
            'is_holiday' => true,
            'is_recurring' => $holiday->is_recurring,
            'recurrence_pattern' => $holiday->recurrence_pattern,
            'recurrence_interval' => $holiday->recurrence_interval,
            'recurrence_weekdays' => $recurrenceWeekdays,
            'end_date' => $holiday->end_date?->format('Y-m-d'),
            'recurrence_description' => $this->getRecurrenceDescription($holiday),
        ];
    }

    // protected function getRecurrenceDescription($event)
    // {
    //     if (!$event->is_recurring) {
    //         return null;
    //     }

    //     $patterns = [
    //         'daily' => 'Daily',
    //         'weekly' => 'Weekly',
    //         'monthly' => 'Monthly',
    //         'yearly' => 'Yearly',
    //         'weekdays' => 'Weekdays',
    //     ];

    //     $description = $patterns[$event->recurrence_pattern] ?? $event->recurrence_pattern;

    //     // Handle interval display
    //     if ($event->recurrence_interval > 1) {
    //         $description = "Every {$event->recurrence_interval} " .
    //             str_plural(str_replace('ly', '', $event->recurrence_pattern), $event->recurrence_interval);
    //     } elseif ($event->recurrence_interval === 0 && $event->recurrence_pattern === 'weekly') {
    //         $description = "Every week"; // Special case for weekly with 0 interval
    //     }

    //     // Add weekday information for weekly recurrence
    //     if ($event->recurrence_pattern === 'weekly' && !empty($event->recurrence_weekdays)) {
    //         $weekdays = $event->recurrence_weekdays;
    //         if (is_string($weekdays)) {
    //             $weekdays = json_decode($weekdays, true) ?? [];
    //         }

    //         if (is_array($weekdays) && !empty($weekdays)) {
    //             $weekdays = array_map('ucfirst', $weekdays);
    //             $description .= ' on ' . implode(', ', $weekdays);
    //         }
    //     }

    //     if ($event->end_date) {
    //         $description .= ' until ' . $event->end_date->format('M j, Y');
    //     }

    //     return $description;
    // }
    protected function getRecurrenceDescription($event)
    {
        if (!$event->is_recurring) {
            return null;
        }

        $patterns = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'weekdays' => 'Weekdays',
        ];

        $pluralMap = [
            'day' => 'days',
            'week' => 'weeks',
            'month' => 'months',
            'year' => 'years',
            'weekday' => 'weekdays'
        ];

        $description = $patterns[$event->recurrence_pattern] ?? $event->recurrence_pattern;

        // Handle interval display
        if ($event->recurrence_interval > 1) {
            $basePattern = str_replace('ly', '', $event->recurrence_pattern);
            $plural = $pluralMap[$basePattern] ?? $basePattern . 's';
            $description = "Every {$event->recurrence_interval} {$plural}";
        } elseif ($event->recurrence_interval === 0 && $event->recurrence_pattern === 'weekly') {
            $description = "Every week"; // Special case for weekly with 0 interval
        } elseif ($event->recurrence_interval === 1) {
            // Add "Every" for single intervals
            $description = "Every " . str_replace('ly', '', $event->recurrence_pattern);
        }

        // Add weekday information for weekly recurrence
        if ($event->recurrence_pattern === 'weekly' && !empty($event->recurrence_weekdays)) {
            $weekdays = $event->recurrence_weekdays;
            if (is_string($weekdays)) {
                $weekdays = json_decode($weekdays, true) ?? [];
            }

            if (is_array($weekdays) && !empty($weekdays)) {
                $weekdays = array_map('ucfirst', $weekdays);
                $description .= ' on ' . implode(', ', $weekdays);
            }
        }

        if ($event->end_date) {
            $description .= ' until ' . $event->end_date->format('M j, Y');
        }

        return $description;
    }

    // Navigation methods
    public function previousMonth()
    {
        $this->setMonth(Carbon::create($this->year, $this->month, 1)->subMonth());
    }

    public function nextMonth()
    {
        $this->setMonth(Carbon::create($this->year, $this->month, 1)->addMonth());
    }

    public function today()
    {
        $this->setMonth(Carbon::now('Asia/Kathmandu'));
    }

    public function setView($view)
    {
        $this->view = $view;
        if ($view === 'day' && !$this->selectedDate) {
            $this->selectedDate = Carbon::today('Asia/Kathmandu')->format('Y-m-d');
        }
        $this->loadEvents();
    }

    // Event management methods
    public function openAddEventModal($day = null)
    {
        $this->resetInputFields();
        $this->showAddEventModal = true;

        if ($day) {
            $this->eventDate = Carbon::create($this->year, $this->month, $day)->format('Y-m-d');
        } else {
            $this->eventDate = Carbon::today('Asia/Kathmandu')->format('Y-m-d');
        }

        // Set default weekdays for weekly recurrence based on selected date
        if (empty($this->recurrenceWeekdays)) {
            $selectedDay = strtolower(Carbon::parse($this->eventDate)->format('l'));
            $this->recurrenceWeekdays = [$selectedDay];
        }
    }

    public function openEditEventModal($date, $index)
    {
        $this->resetInputFields();
        $this->showAddEventModal = false;
        $this->showEditEventModal = true;

        // Check if event exists at the given date and index
        if (!isset($this->events[$date][$index])) {
            $this->dispatch('alert', type: 'error', message: 'Event not found!');
            $this->closeModal();
            return;
        }

        $eventData = $this->events[$date][$index];

        // Check if it's a holiday (which can't be edited)
        if (str_starts_with($eventData['id'], 'holiday_')) {
            $this->dispatch('alert', type: 'error', message: 'Holidays cannot be edited from here!');
            $this->closeModal();
            return;
        }

        $event = CalendarEvent::find($eventData['id']);

        if ($event) {
            $this->editingEventId = $event->id;
            $this->eventTitle = $event->title;
            $this->eventDate = $event->date->format('Y-m-d');
            $this->eventTime = $event->time ? Carbon::parse($event->time)->format('H:i') : '';
            $this->eventType = $event->type;
            $this->eventPriority = $event->priority;
            $this->eventDescription = $event->description;
            $this->isRecurring = $event->is_recurring;
            $this->recurrencePattern = $event->recurrence_pattern ?? 'none';
            $this->recurrenceInterval = $event->recurrence_interval ?? 1;
            $this->recurrenceWeekdays = $event->recurrence_weekdays ? json_decode($event->recurrence_weekdays, true) : [];
            $this->endDate = $event->end_date?->format('Y-m-d');
        } else {
            $this->dispatch('alert', type: 'error', message: 'Event not found!');
            $this->closeModal();
        }
    }

    public function addEvent()
    {
        try {
            $eventData = [
                'title' => $this->eventTitle,
                'date' => $this->eventDate,
                'time' => $this->eventTime ?: null,
                'type' => $this->eventType,
                'priority' => $this->eventPriority,
                'description' => $this->eventDescription,
                'is_recurring' => $this->isRecurring && $this->recurrencePattern !== 'none',
                'recurrence_pattern' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrencePattern : null,
                'recurrence_interval' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrenceInterval : 0,
                'recurrence_weekdays' => $this->recurrencePattern === 'weekly' && !empty($this->recurrenceWeekdays)
                    ? json_encode($this->recurrenceWeekdays)
                    : null,
                'end_date' => $this->endDate,
                'user_id' => Auth::id(),
                'is_holiday' => $this->eventType === 'Holiday',
            ];

            $event = CalendarEvent::create($eventData);

            //holidays table
            // if ($this->eventType === 'Holiday' && Auth::user()->hasRole('HR Manager')) {
            //     Holiday::create([
            //         'title' => $this->eventTitle,
            //         'description' => $this->eventDescription,
            //         'start_date' => $this->eventDate,
            //         'end_date' => $this->endDate ?? $this->eventDate,
            //         'is_recurring' => $this->isRecurring && $this->recurrencePattern !== 'none',
            //         'recurrence_pattern' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrencePattern : null,
            //         'recurrence_interval' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrenceInterval : 1,
            //         'recurrence_weekdays' => $this->recurrencePattern === 'weekly' && !empty($this->recurrenceWeekdays)
            //             ? json_encode($this->recurrenceWeekdays)
            //             : null,
            //         'is_active' => true,
            //         'created_by' => Auth::id(),
            //     ]);
            // }

            $this->dispatch('close-modal', 'openAddEventModal');
            $this->dispatch('show-notification', 'Event added successfully!');
            $this->loadEvents();
        } catch (\Exception $e) {
            \Log::error('Failed to add event: ' . $e->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Failed to add event: ' . $e->getMessage());
        }
    }

    public function updateEvent()
    {
        try {
            $event = CalendarEvent::find($this->editingEventId);

            if (!$event) {
                $this->dispatch('alert', type: 'error', message: 'Event not found!');
                return;
            }

            $event->update([
                'title' => $this->eventTitle,
                'date' => $this->eventDate,
                'time' => $this->eventTime,
                'type' => $this->eventType,
                'priority' => $this->eventPriority,
                'description' => $this->eventDescription,
                'is_recurring' => $this->isRecurring && $this->recurrencePattern !== 'none',
                'recurrence_pattern' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrencePattern : null,
                'recurrence_interval' => $this->isRecurring && $this->recurrencePattern !== 'none' ? $this->recurrenceInterval : 0,
                'recurrence_weekdays' => $this->recurrencePattern === 'weekly' && !empty($this->recurrenceWeekdays)
                    ? json_encode($this->recurrenceWeekdays)
                    : null,
                'end_date' => $this->endDate,
            ]);

            $this->dispatch('close-modal', 'openEditEventModal');
            $this->dispatch('show-notification', 'Event updated successfully!');
            $this->loadEvents();
        } catch (\Exception $e) {
            \Log::error('Failed to update event: ' . $e->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Failed to update event: ' . $e->getMessage());
        }
    }

    public function deleteEvent($date, $index)
    {
        try {
            // Check if event exists at the given date and index
            if (!isset($this->events[$date][$index])) {
                $this->dispatch('alert', type: 'error', message: 'Event not found!');
                return;
            }

            $eventData = $this->events[$date][$index];

            // Check if it's a holiday
            if (str_starts_with($eventData['id'], 'holiday_')) {
                $this->dispatch('alert', type: 'error', message: 'Holidays cannot be deleted from here!');
                return;
            }

            CalendarEvent::destroy($eventData['id']);

            $this->dispatch('alert', type: 'success', message: 'Event deleted successfully!');
            $this->dispatch('close');
            $this->loadEvents();
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Failed to delete event: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showAddEventModal = false;
        $this->showEditEventModal = false;
        $this->resetInputFields();
        $this->resetErrorBag();
    }

    public function resetInputFields()
    {
        $this->reset([
            'eventTitle',
            'eventDate',
            'eventTime',
            'eventType',
            'eventPriority',
            'eventDescription',
            'editingEventId',
            'isRecurring',
            'recurrencePattern',
            'recurrenceInterval',
            'recurrenceWeekdays',
            'endDate'
        ]);

        $this->eventType = 'Meeting';
        $this->eventPriority = 'Low';
        $this->recurrencePattern = 'none';
        $this->recurrenceInterval = 1;
        $this->recurrenceWeekdays = [];
        $this->isRecurring = false;
        $this->endDate = null;
    }

    protected function getNextOccurrenceDate(Carbon $current, $event): Carbon
    {
        $nextDate = $current->copy();

        // Safely decode recurrence_weekdays if it's a string
        $recurrenceWeekdays = $event->recurrence_weekdays;
        if (is_string($recurrenceWeekdays)) {
            $recurrenceWeekdays = json_decode($recurrenceWeekdays, true) ?? [];
        }

        switch ($event->recurrence_pattern) {
            case 'daily':
                $nextDate->addDays($event->recurrence_interval);
                break;

            case 'weekly':
                if (!empty($recurrenceWeekdays)) {
                    $weekdays = $recurrenceWeekdays;
                    $currentDay = strtolower($nextDate->format('l'));

                    // Sort weekdays in chronological order (Sunday to Saturday)
                    $weekdayOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $sortedWeekdays = array_intersect($weekdayOrder, $weekdays);
                    $sortedWeekdays = array_values($sortedWeekdays);

                    // Find current day in the sorted weekdays
                    $currentIndex = array_search($currentDay, $sortedWeekdays);

                    if ($currentIndex !== false) {
                        // Try to find next weekday in the same week
                        if (isset($sortedWeekdays[$currentIndex + 1])) {
                            $nextDay = $sortedWeekdays[$currentIndex + 1];
                            $nextDate->next(Carbon::parse($nextDay)->dayOfWeek);
                        } else {
                            // Move to first weekday in the next week
                            $nextDay = $sortedWeekdays[0];
                            $nextDate->addWeek()->startOfWeek();
                            $nextDate->next(Carbon::parse($nextDay)->dayOfWeek);
                        }
                    } else {
                        // Current day is not in the selected weekdays, find the next available weekday
                        $found = false;
                        foreach ($sortedWeekdays as $weekday) {
                            $weekdayDate = $nextDate->copy()->next(Carbon::parse($weekday)->dayOfWeek);
                            if ($weekdayDate->gt($nextDate)) {
                                $nextDate = $weekdayDate;
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            // If no weekday found in current week, go to first weekday of next week
                            $nextDay = $sortedWeekdays[0];
                            $nextDate->addWeek()->startOfWeek();
                            $nextDate->next(Carbon::parse($nextDay)->dayOfWeek);
                        }
                    }
                } else {
                    // Standard weekly recurrence (every X weeks on same weekday)
                    $intervalWeeks = $event->recurrence_interval === 0 ? 1 : $event->recurrence_interval;
                    $nextDate->addWeeks($intervalWeeks);
                }
                break;

            case 'monthly':
                $day = $nextDate->day;
                $nextDate->addMonths($event->recurrence_interval);
                $nextDate->day = min($day, $nextDate->daysInMonth);
                break;

            case 'yearly':
                $nextDate->addYears($event->recurrence_interval);
                break;

            case 'weekdays':
                // For weekdays pattern (Monday to Friday) with interval
                if ($event->recurrence_interval > 1) {
                    $daysToAdd = $event->recurrence_interval * 5;
                    $nextDate->addDays($daysToAdd);
                    while ($nextDate->isWeekend()) {
                        $nextDate->addDay();
                    }
                } else {
                    do {
                        $nextDate->addDay();
                    } while ($nextDate->isWeekend());
                }
                break;

            default:
                $nextDate->addDay();
                break;
        }

        return $nextDate;
    }

    // Updated method to handle recurrence pattern changes
    public function updatedRecurrencePattern($value)
    {
        $this->isRecurring = $value !== 'none';

        // Set default weekdays for weekly recurrence based on selected date
        if ($value === 'weekly' && empty($this->recurrenceWeekdays) && $this->eventDate) {
            $selectedDay = strtolower(Carbon::parse($this->eventDate)->format('l'));
            $this->recurrenceWeekdays = [$selectedDay];
        }

        // Set default interval for weekdays
        if ($value === 'weekdays') {
            $this->recurrenceInterval = 1;
        }
    }

    // New method to handle when isRecurring changes
    public function updatedIsRecurring($value)
    {
        if (!$value) {
            $this->recurrencePattern = 'none';
            $this->recurrenceInterval = 1;
            $this->recurrenceWeekdays = [];
            $this->endDate = null;
        }
    }

    public function render()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $this->currentTime = $today->format('h:i A');

        $isAdminOrHr = auth()->check() && auth()->user()->hasRole(['Admin', 'HR Manager']);

        return view('livewire.attendance.attendance-calendar', [
            'today' => $today,
            'isSaturday' => $today->isSaturday(),
            'isAdminOrHr' => $isAdminOrHr,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
