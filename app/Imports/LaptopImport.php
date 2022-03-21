<?php

namespace App\Imports;

use Illuminate\Support\Collection;

use App\Models\Equipment;
use App\Models\Entity;
use App\Models\Specification;
use App\Models\Repair;
use App\Models\EquipmentProblem;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToArray;


class LaptopImport implements ToArray
{
    /**
    * @param Collection $collection
    */
    public function  array(array $rows)
    {

        $entity =  Entity::where('name','laptop')->first();

        foreach($rows as $index=>$row)
        {

            if($index==0)
            {
                continue;
            }

                $employee   =  Employee::where("name", "like", "%".$row[5]."%")->first()  ?? " ";
                $equipment               =    new Equipment();
                $equipment->entity_id    =    $entity->id;
                $equipment->alloted_no   =    $row[1];
                $equipment->manufacturer =    $row[2];
                $equipment->employee_id  =    $employee->id ?? "0";
                $equipment->save();
                if($row[4]!=null)
                {

                $problem                           = new  EquipmentProblem();
                $problem->name                     = $row[4] ;
                $problem->equipment_alloted_no     = $equipment->alloted_no;
                $problem->save();
                }
                if($row[3]!=null)
                {
                $specification                          = new Specification();
                $specification->name                    = $row[3];
                $specification->equipment_id            = $equipment->id;
                $specification->save();

                }
                if($row[13]!=null)
                {
                $repair                           = new Repair();
                $repair->part                     = $row[13];
                $repair->cost                     = $row[14];
                $repair->equipment_id             = $equipment->id;
                $repair->save();
                }
            }
        }
    }
