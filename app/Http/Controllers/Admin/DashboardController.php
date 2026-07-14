<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // TODO: ganti dummy data di bawah ini dengan query asli
        // dari model Intern, TaskSubmission, Attendance, Payment, dll.

        $activeInterns      = 42;
        $newInternsThisMonth = 5;
        $pendingRequests    = 12;
        $attendanceRate     = 94;
        $presentToday       = 38;
        $pendingTaskReviews = 8;
        $totalCollected     = 15000000;
        $pendingPayments    = 2;
        $absentCount        = 3;
        $leaveCount         = 1;
        $notYetCount        = 0;

        $weeklyAttendance = [
            'Mon' => 95, 'Tue' => 92, 'Wed' => 98, 'Thu' => 94, 'Fri' => 88,
        ];

        $attendanceAlerts = [
            [
                'type' => 'warning',
                'title' => '3 Interns Absent',
                'message' => 'Unexcused attendance detected for Morning session.',
            ],
            [
                'type' => 'info',
                'title' => 'Holiday Reminder',
                'message' => 'National Holiday this Friday. All interns informed.',
            ],
        ];

        // $recentReports dan $taskReviews sebaiknya diambil dari Eloquent, contoh:
        // $recentReports = Report::latest('submitted_at')->take(3)->get();
        // $taskReviews = TaskSubmission::with('intern')->latest()->take(10)->get();
        $recentReports = collect();
        $taskReviews = collect();

        return view('admin.dashboard', compact(
            'activeInterns',
            'newInternsThisMonth',
            'pendingRequests',
            'attendanceRate',
            'presentToday',
            'pendingTaskReviews',
            'totalCollected',
            'pendingPayments',
            'absentCount',
            'leaveCount',
            'notYetCount',
            'weeklyAttendance',
            'attendanceAlerts',
            'recentReports',
            'taskReviews',
        ));
    }
}