<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Models\Entity;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;




class EquipmentImport implements WithMultipleSheets, SkipsUnknownSheets 
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
   
       
    public function sheets(): array
    {
      
        
        return [
          
            'Mouse List'  => new MouseImport(),
           'Laptops'      => new LaptopImport(),
           'Headphones'   => new HeadphoneImport(),

        ];
    }
    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
    
}
