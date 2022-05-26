<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use App\Models\LeaveBalance;
use Illuminate\Console\Command;

class CalculateLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add leave balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', '-1');
        $users              =   User::whereHas('employee', function ($query) {
            $query->whereNotNull('contract_date');
        })->with('employee')->get();
        $currentMonth       =   Carbon::now()->startOfMonth();
        $lastMonth          =   Carbon::now()->startOfMonth()->subMonth();
        foreach ($users as $user) {
            $joinMonth      =   Carbon::createFromFormat('Y-m-d', $user->employee->contract_date);
            if ($joinMonth->format('d') > 14 || $joinMonth->format('m')==$currentMonth->format('m')) {
                $diff   =   $joinMonth->diffInDays($currentMonth);
                if ($diff < 20) {
                    continue;
                }
            }
            $leaveBalance               =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $currentMonth)->first();
            $lastMonthLeaveBalance      =   LeaveBalance::where('user_id', $user->id)->whereMonth('month', $lastMonth)->first();
            if (empty($leaveBalance)) {
                $leaveBalance               =   new LeaveBalance();
                $leaveBalance->balance      =   1.25;
                $leaveBalance->month        =   $currentMonth->format('Y-m-d');
                $leaveBalance->user_id      =   $user->id;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $leaveBalance->balance                  =   $lastMonthLeaveBalance->balance + $leaveBalance->balance;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
                    $lastMonthLeaveBalance->save();
                }
            } else {
                $lastMonthBalance                       =   0;
                if (!empty($lastMonthLeaveBalance) && $lastMonthLeaveBalance->is_forwarded != 1) {
                    $lastMonthBalance   =   $lastMonthLeaveBalance->balance;
                    $leaveBalance->prev_month_deduction     =   $lastMonthLeaveBalance->next_month_deduction;
                    $lastMonthLeaveBalance->is_forwarded    =   1;
                    $lastMonthLeaveBalance->save();
                }
                $deductibleBalance  =   $leaveBalance->balance + $lastMonthBalance + 1.25;
                $leftBalance        =   0;
                $whole              = intval($deductibleBalance);
                $decimal1           = $deductibleBalance - $whole;
                $decimal2           = round($decimal1, 2);
                $decimal            = substr($decimal2, 2);
                if ($decimal == '25' || $decimal == '75') {
                    $deductibleBalance    =   $deductibleBalance - 0.25;
                    $leftBalance          =   0.25;
                }


                $balance            =   $leaveBalance->deduction - $deductibleBalance;
                if ($balance <= 0) {
                    // if person has next month deduction then adjust here
                    if ($leaveBalance->next_month_deduction != 0) {
                        $balance            =   $leaveBalance->next_month_deduction - abs($balance);
                        if ($balance <= 0) {
                            $leaveBalance->next_month_deduction = 0;
                            $leaveBalance->balance      =    $leftBalance + abs($balance);
                        } else {
                            $leaveBalance->next_month_deduction     =   $balance;
                            $leaveBalance->balance                  =   $leftBalance;
                        }
                    } else {
                        $leaveBalance->balance      =    $leftBalance + abs($balance);
                        $leaveBalance->deduction    =   0;
                    }
                } else {
                    $leaveBalance->balance      =   $leftBalance;
                    $leaveBalance->deduction    =   $balance;
                }
            }
            $leaveBalance->save();
        }
    }
}
