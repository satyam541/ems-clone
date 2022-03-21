<?php

namespace App\Exports;
use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EquipmentExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $entity;

    function __construct($entity) {
            $this->entity = $entity;
    }
    public function collection()
    {
        return Equipment::where('entity_id',$this->entity)->with('employee','entity','specifications','repairs')->get();
    }
    public function map($equipment): array
    {
        if($equipment->isWorking=="1")
            $working_status="Yes";
        else
            $working_status="No";
        $specifications             =   $equipment->specifications;
        $repairs                    =   $equipment->repairs;
        $equipmentRepair            =   array();
        $equipmentSpecification     =   array();

        foreach($specifications as $specification)
        {
            $equipmentSpecification[]   =   $specification->name.":".$specification->description;   
        }
        foreach($repairs as $repair)
        {
            $equipmentRepair[]          =   $repair->date." : ".$repair->part." : ".$specification->cost;
        }
        $equipmentRepair            =   implode(',',$equipmentRepair);
        $equipmentSpecification     =   implode(',',$equipmentSpecification);

       return [
                $equipment->alloted_no,
                $equipment->entity->name,
                $equipment->manufacturer,
                $equipment->buy_date,
                $equipmentSpecification,
                $equipmentRepair,
                optional($equipment->employee)->name,
                $working_status
        ]   ;
    }
    public function headings() : array{
        return [
            'Alloted No',
            'Entity',
            'Manufacturer',
            'Buy Date',
            'Specifications',
            'Repair',
            'Allotted To',
            'Working Status',
        ];
    }
}
