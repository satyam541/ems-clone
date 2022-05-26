<?php

namespace App\Console;

use App\Models\Employee;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\ems\TicketController;
use App\Http\Controllers\ems\EmployeeController;
use App\Http\Controllers\ems\NotificationController;
use App\Http\Controllers\ems\LiveAttendanceController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function(){
            $employee= new EmployeeController();
            $employee::removeDocument();
        }) ->weekly()->sundays()
            ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $attendance= new LiveAttendanceController();
            $attendance->storeAttendance();
        })->everyMinute()
        ->timezone('Asia/Kolkata');


        $schedule->call(function(){
            $attendance= new LiveAttendanceController();
            $attendance->removeAttendance();
        })->daily()->at('9:00')
        ->timezone('Asia/Kolkata');

        $schedule->command('add:balance')->monthlyOn('01','00:00');
        

        $schedule->call(function(){
            $employee= new EmployeeController();
            $employee->scheduleReminder();

        })->weekly()->mondays()->at('9:30')
        ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $notification = new NotificationController();
            $notification->deleteNotifications();
        })->monthly()
        ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $employee= new EmployeeController();
            $employee->resetBirthdayReadOn();
        })->daily()->at('6:00')->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $employee= new TicketController();
            $employee->sendReminder();
        })->daily()->at('10:00')->timezone('Asia/Kolkata');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
