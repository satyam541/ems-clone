<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\Equipment;
use App\Models\Entity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Employee;

class HeadphoneImport implements  ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        
        $employee        = Employee::where("name", "like", "%".$row['employee_name']."%")->first() ?? "" ;
        $employee_id     = $employee->registration_id ?? "0";
        $entity          = Entity::where('name','laptop')->first();
        $entity_id       = $entity->id;
      
            return new Equipment([
            
                'employee_id'              =>   $employee_id ,
                'entity_id'                =>   $entity_id,
                'alloted_no'               =>   $row['headphone_no'],

            ]);
    }
}
