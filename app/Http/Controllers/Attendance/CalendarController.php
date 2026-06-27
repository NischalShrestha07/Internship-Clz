<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Kathmandu');

        $year = $request->year ?? $now->year;
        $month = $request->month ?? $now->month;

        $currentDate = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Kathmandu');

        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $events = [];

        $userEvents = CalendarEvent::where('user_id', Auth::id())
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
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        foreach ($userEvents as $event) {
            $this->processEvent($events, $event);
        }

        $holidays = Holiday::where('is_active', true)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfMonth);
                    })
                    ->orWhere(function ($q) use ($endOfMonth) {
                        $q->where('is_recurring', true)
                            ->where('start_date', '<=', $endOfMonth);
                    });
            })
            ->get();

        foreach ($holidays as $holiday) {
            $this->processHoliday($events, $holiday, $startOfMonth, $endOfMonth);
        }

        ksort($events);

        return view('attendance.attendance-calendar', [
            'month' => $month,
            'year' => $year,
            'monthName' => $currentDate->format('F'),
            'daysInMonth' => $currentDate->daysInMonth,
            'startDay' => Carbon::create($year, $month, 1)->dayOfWeek,
            'events' => $events,
            'today' => $now,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'eventTitle' => 'required|string|max:255',
            'eventDate' => 'required|date',
            'eventTime' => 'nullable',
            'eventType' => 'required|in:Meeting,Leave,Notice,Holiday',
            'eventPriority' => 'required|in:Low,Medium,High',
            'eventDescription' => 'nullable|string|max:1000',
            'recurrencePattern' => 'nullable|in:none,daily,weekly,monthly,yearly',
            'recurrenceInterval' => 'nullable|integer|min:1|max:365',
            'endDate' => 'nullable|date|after_or_equal:eventDate',
        ]);

        $recurrencePattern = $request->input('recurrencePattern', 'none');
        $recurrenceInterval = $request->input('recurrenceInterval', 1);

        CalendarEvent::create([
            'user_id' => Auth::id(),
            'title' => $validated['eventTitle'],
            'date' => $validated['eventDate'],
            'time' => $validated['eventTime'],
            'type' => $validated['eventType'],
            'priority' => $validated['eventPriority'],
            'description' => $validated['eventDescription'],
            'is_recurring' => $recurrencePattern !== 'none',
            'recurrence_pattern' => $recurrencePattern,
            'recurrence_interval' => $recurrenceInterval,
            'end_date' => $request->input('endDate'),
        ]);

        return redirect()->route('attendance.calendar', [
            'month' => request('month'),
            'year' => request('year'),
        ])->with('status', 'Event added successfully!');
    }

    public function update(Request $request, CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'eventTitle' => 'required|string|max:255',
            'eventDate' => 'required|date',
            'eventTime' => 'nullable',
            'eventType' => 'required|in:Meeting,Leave,Notice,Holiday',
            'eventPriority' => 'required|in:Low,Medium,High',
            'eventDescription' => 'nullable|string|max:1000',
            'recurrencePattern' => 'nullable|in:none,daily,weekly,monthly,yearly',
            'recurrenceInterval' => 'nullable|integer|min:1|max:365',
            'endDate' => 'nullable|date|after_or_equal:eventDate',
        ]);

        $recurrencePattern = $request->input('recurrencePattern', 'none');
        $recurrenceInterval = $request->input('recurrenceInterval', 1);

        $event->update([
            'title' => $validated['eventTitle'],
            'date' => $validated['eventDate'],
            'time' => $validated['eventTime'],
            'type' => $validated['eventType'],
            'priority' => $validated['eventPriority'],
            'description' => $validated['eventDescription'],
            'is_recurring' => $recurrencePattern !== 'none',
            'recurrence_pattern' => $recurrencePattern,
            'recurrence_interval' => $recurrenceInterval,
            'end_date' => $request->input('endDate'),
        ]);

        return redirect()->route('attendance.calendar', [
            'month' => request('month'),
            'year' => request('year'),
        ])->with('status', 'Event updated successfully!');
    }

    public function edit(CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        return view('attendance.calendar-event-edit', [
            'event' => $event,
            'month' => request('month', Carbon::now()->month),
            'year' => request('year', Carbon::now()->year),
        ]);
    }

    public function destroy(CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();

        return redirect()->route('attendance.calendar', [
            'month' => request('month'),
            'year' => request('year'),
        ])->with('status', 'Event deleted successfully!');
    }

    protected function processEvent(&$events, $event)
    {
        $eventDate = Carbon::parse($event->date);

        if ($event->is_recurring) {
            $dates = $this->generateRecurringDates($event);
            foreach ($dates as $date) {
                $dateStr = $date->format('Y-m-d');
                $events[$dateStr][] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'time' => $event->time ? Carbon::parse($event->time)->format('h:i A') : null,
                    'type' => $event->type,
                    'priority' => $event->priority,
                    'description' => $event->description,
                    'is_recurring' => true,
                    'recurrence_description' => $this->getRecurrenceDescription($event),
                    'editable' => true,
                ];
            }
        } else {
            $dateStr = $eventDate->format('Y-m-d');
            $events[$dateStr][] = [
                'id' => $event->id,
                'title' => $event->title,
                'time' => $event->time ? Carbon::parse($event->time)->format('h:i A') : null,
                'type' => $event->type,
                'priority' => $event->priority,
                'description' => $event->description,
                'is_recurring' => false,
                'recurrence_description' => null,
                'editable' => true,
            ];
        }
    }

    protected function processHoliday(&$events, $holiday, $startOfMonth, $endOfMonth)
    {
        $start = Carbon::parse($holiday->start_date);
        $end = $holiday->end_date ? Carbon::parse($holiday->end_date) : $start->copy();

        if ($holiday->is_recurring) {
            $recurStart = $start->copy()->year($startOfMonth->year);
            $recurEnd = $end->copy()->year($startOfMonth->year);
            $current = $recurStart->copy();
            while ($current <= $recurEnd) {
                if ($current->between($startOfMonth, $endOfMonth)) {
                    $events[$current->format('Y-m-d')][] = $this->makeHolidayEvent($holiday, true, 'Annual');
                }
                $current->addDay();
            }
        } else {
            $current = $start->copy();
            while ($current <= $end) {
                if ($current->between($startOfMonth, $endOfMonth)) {
                    $events[$current->format('Y-m-d')][] = $this->makeHolidayEvent($holiday, false, null);
                }
                $current->addDay();
            }
        }
    }

    private function makeHolidayEvent($holiday, $isRecurring, $recurrenceDesc)
    {
        return [
            'id' => 'holiday-' . $holiday->id,
            'title' => $holiday->title,
            'time' => null,
            'type' => 'Holiday',
            'priority' => 'Low',
            'description' => $holiday->description,
            'is_recurring' => $isRecurring,
            'recurrence_description' => $recurrenceDesc,
            'editable' => false,
        ];
    }

    protected function generateRecurringDates($event)
    {
        $dates = [];
        $startDate = Carbon::parse($event->date);
        $endDate = $event->end_date ? Carbon::parse($event->end_date) : Carbon::parse($event->date)->addYear();
        $current = $startDate->copy();

        $monthStart = Carbon::create(request('year', Carbon::now()->year), request('month', Carbon::now()->month), 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        while ($current <= $endDate && $current->year <= $monthEnd->year + 1) {
            if ($current->between($monthStart, $monthEnd)) {
                $dates[] = $current->copy();
            }

            switch ($event->recurrence_pattern) {
                case 'daily':
                    $current->addDays($event->recurrence_interval ?? 1);
                    break;
                case 'weekly':
                    $current->addWeeks($event->recurrence_interval ?? 1);
                    break;
                case 'monthly':
                    $current->addMonths($event->recurrence_interval ?? 1);
                    break;
                case 'yearly':
                    $current->addYears($event->recurrence_interval ?? 1);
                    break;
                default:
                    $current = $endDate->copy()->addDay();
            }
        }

        return $dates;
    }

    protected function getRecurrenceDescription($event)
    {
        if (!$event->is_recurring) return null;

        $interval = $event->recurrence_interval ?? 1;
        $pattern = $event->recurrence_pattern;

        $label = match ($pattern) {
            'daily' => $interval === 1 ? 'Daily' : "Every {$interval} days",
            'weekly' => $interval === 1 ? 'Weekly' : "Every {$interval} weeks",
            'monthly' => $interval === 1 ? 'Monthly' : "Every {$interval} months",
            'yearly' => $interval === 1 ? 'Yearly' : "Every {$interval} years",
            default => 'Repeats',
        };

        if ($event->end_date) {
            $label .= ' until ' . Carbon::parse($event->end_date)->format('M d, Y');
        }

        return $label;
    }
}
