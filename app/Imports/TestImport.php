<?php

namespace App\Imports;

use App\User;
use Carbon\Carbon;
use App\Models\LeaveBalance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        ini_set('max_execution_time','-1');
        $notFoundUsers = [];
        foreach ($collection as $index => $row) {
            // dd($row);
            if ($index == 0) {
                continue;
            }
            $user   =   User::where('email', $row[2])->first();
            if (empty($user) && ($row[2] != '' || $row[2] != ' ')) {
                $notFoundUsers[$row[2]] =   $row[2];
                continue;
            }
            if ($row['1'] != '' && $row['1'] != ' ') {
                if (str_contains($row['1'], '/')) {
                    $date   =   Carbon::createFromFormat('d/m/Y',$row['1'])->format('Y-m-d');
                } else {
                    $date   =   $this->transformDate($row, $row['1']);
                }
                // if($user->id!=4)
                // {
                //     continue;
                // }
                // dd($user,$date);
                if (!empty($user->employee)) {

                    $employee                   = $user->employee;
                    $employee->contract_date    = $date;
                    $employee->save();


                }
            }
            $leaveBalance           =   LeaveBalance::updateOrCreate(['user_id' => $user->id, 'month' => Carbon::today()->format('Y-m-d')]);
            $leaveBalance->balance  =   $row[3];
            $leaveBalance->save();

        }
    }

    public function transformDate($row, $value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-d-m');
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    // public function model(array $row)
    // {

    //     dd($row);

    // }
}
