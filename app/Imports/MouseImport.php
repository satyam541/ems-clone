<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Equipment;
use App\Models\Entity;
use App\Models\Specification;
use App\Models\Repair;
use App\Models\EquipmentProblems;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Employee;


class MouseImport implements  ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
    
        $employee        = Employee::where("name", "like", "%".$row['holder_name']."%")->first()  ?? " ";
        $employee_id     = $employee->registration_id ?? "0";
        $entity          = Entity::where('name','mouse')->first();
        $entity_id       = $entity->id;
      
       
           return  new Equipment([
            
                'employee_id'              =>   $employee_id ?? "0",
                'entity_id'                =>   $entity_id,
                'alloted_no'               =>   $row['mouse_no']
            ]);
    }

    }

