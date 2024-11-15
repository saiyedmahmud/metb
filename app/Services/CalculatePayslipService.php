<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\PublicHoliday;
use App\Models\Users;
use Carbon\Carbon;

class CalculatePayslipService
{
    protected function getHolidaysInMonth($year, $month, $startDay, $endDay): int
    {
        $daysInMonth = Carbon::createFromDate($year, $month)->endOfMonth()->day;
        $firstWeekDayOfMonth = Carbon::create($year, $month, 1)->dayOfWeek;

        $dayNameToIndex = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $startDayIndex = $dayNameToIndex[$startDay];
        $endDayIndex = $dayNameToIndex[$endDay];

        $holidays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayOfWeek = ($firstWeekDayOfMonth + $day - 1) % 7;
            if ($startDayIndex === 0) {
                if ($dayOfWeek < $startDayIndex || $dayOfWeek > $endDayIndex) {
                    $holidays++;
                }
            } else {
                if ($dayOfWeek < $startDayIndex && $dayOfWeek > $endDayIndex) {
                    $holidays++;
                }
            }
        }

        return $holidays;
    }

    public function calculatePayslip($salaryMonth, $salaryYear)
    {
        $startDate = Carbon::parse("$salaryYear-$salaryMonth-01");
        $endDate = Carbon::parse("$salaryYear-$salaryMonth-01")->addMonth();

        // echo $startDate;
        // echo $endDate;

        // get all employee salary and show in payroll
        $allEmployee = Users::with([
            'weeklyHoliday',
            'shift',
            'leaveApplication' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'ACCEPTED')
                    ->where('acceptLeaveFrom', '>=', $startDate)
                    ->where('acceptLeaveTo', '<=', $endDate);
            },
            'salaryHistory' => function ($query) {
                $query->orderBy('id', 'desc');
            },
        ])
            ->get();

        // remove password
        $allEmployee->each(function ($item) {
            unset($item->password);
        });

        // get working hours of each employee
        $allEmployeeWorkingHours = Attendance::whereBetween('inTime', [$startDate, $endDate])
            ->select('userId', 'totalHour')
            ->get();

        // calculate work days in a month based on publicHoliday table
        $publicHoliday = PublicHoliday::whereBetween('date', [$startDate, $endDate])
            ->count();

        // get only the first salary of each employee from salary history
        $allEmployeeSalary = collect($allEmployee)->map(function ($item) use ($salaryMonth, $salaryYear, $publicHoliday) {
            $dayInMonth = Carbon::createFromDate($salaryYear, $salaryMonth)->endOfMonth()->day;
            $shiftWiseWorkHour = (float) round($item->shift->workHour, 2);
            $salary = $item->salaryHistory[0]->salary ?? 0;

            $paidLeave = collect($item->leaveApplication)
                ->filter(function ($leave) {
                    return $leave->leaveType === "PAID";
                })
                ->reduce(function ($acc, $leave) {
                    return $acc + $leave->leaveDuration;
                }, 0);

            $unpaidLeave = collect($item->leaveApplication)
                ->filter(function ($leave) {
                    return $leave->leaveType === "UNPAID";
                })
                ->reduce(function ($acc, $leave) {
                    return $acc + $leave->leaveDuration;
                }, 0);

            $monthlyHoliday = $this->getHolidaysInMonth(
                $salaryYear,
                $salaryMonth,
                $item->weeklyHoliday->startDay,
                $item->weeklyHoliday->endDay,
            );

            $monthlyWorkHour = (float) round((($dayInMonth - $monthlyHoliday - $publicHoliday) * $shiftWiseWorkHour), 2);

            return [
                'id' => $item->id,
                'firstName' => $item->firstName,
                'lastName' => $item->lastName,
                'salaryMonth' => (int) $salaryMonth,
                'salaryYear' => (int) $salaryYear,
                'salary' => $salary,
                'paidLeave' => $paidLeave,
                'unpaidLeave' => $unpaidLeave,
                'monthlyHoliday' => $monthlyHoliday,
                'publicHoliday' => $publicHoliday,
                'workDay' => $dayInMonth - $monthlyHoliday - $publicHoliday,
                'shiftWiseWorkHour' => $shiftWiseWorkHour,
                'monthlyWorkHour' => $monthlyWorkHour,
                'hourlySalary' => $monthlyWorkHour ? (float) round(($salary / $monthlyWorkHour), 2) : 0,
                'bonus' => 0,
                'bonusComment' => "",
                'deduction' => 0,
                'deductionComment' => "",
                'totalPayable' => 0,
            ];
        });

        // sum up the total working hours of each employee
        $allEmployeeWorkingHoursSum = $allEmployeeWorkingHours->reduce(function ($acc, $item) {
            $userId = $item->userId;
            $totalHour = $item->totalHour;

            if (isset($acc[$userId])) {
                $acc[$userId] += $totalHour;
            } else {
                $acc[$userId] = $totalHour;
            }

            return $acc;
        }, []);

        // add working hours to the allEmployeeSalary array
        $finalResult = $allEmployeeSalary->map(function ($item) use ($allEmployeeWorkingHoursSum) {
            $workingHour = (float) round(($allEmployeeWorkingHoursSum[$item['id']] ?? 0), 2);
            $salaryPayable = (float) round(
                ($workingHour * $item['hourlySalary'] + $item['paidLeave'] * $item['shiftWiseWorkHour'] * $item['hourlySalary']),
                2
            );
            $totalPayable = (float) round(($salaryPayable + $item['bonus'] - $item['deduction']), 2);

            $item['workingHour'] = $workingHour;
            $item['salaryPayable'] = $salaryPayable;
            $item['totalPayable'] = $totalPayable;

            return $item;
        });

        $sortedResult = $finalResult->sortBy('id')->values()->all();

        return $sortedResult;
    }
}
