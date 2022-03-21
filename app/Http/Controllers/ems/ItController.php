<?php

namespace App\Http\Controllers\ems;

use Session;
use Storage;
use App\User;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Employee;
use App\Models\Software;
use App\Models\Equipment;
use App\Models\Quotation;
use App\Models\Department;
use App\Models\StockDetails;
use Illuminate\Http\Request;
use App\Models\TicketProblem;
use App\Models\EquipmentAssign;
use App\Models\QuotationDetails;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\StockRequest;
use App\Http\Controllers\Controller;
use App\http\Requests\TicketRequest;
use App\Http\Requests\SoftwareRequest;
use App\Http\Requests\QuotationRequest;
use App\http\Requests\StockDetailRequest;
use Illuminate\Support\Facades\Validator;

class ItController extends Controller
{

// Quotation Functions
    public function quotationList()
    {
        $this->authorize('quotationView',new Stock());   
        $data['quotations'] = Quotation::where('employee_id',auth()->user()->employee->id)->whereHas('quotationDetails')->orderBy('created_at')->get();
        
        return view('it.quotationList',$data);
    }

    public function quotationCreate()
    {
        $this->authorize('quotationCreate',new Stock()); 
        $data['quotation']          = new Quotation();
        $data['quotationDetail']    = null;
        $data['submitRoute']        = 'quotationSubmit';
        
        return view('it.quotationForm',$data);
    }

    public function quotationSubmit(QuotationRequest $request)
    {
        $quotation                  = new Quotation();
        $quotation->item            = $request->item;
        $quotation->quantity        = $request->quantity;
        $quotation->employee_id     = auth()->user()->employee->id;
        $quotation->status          = 'pending';
        $quotation->save();

        $data['quotation']                  = $quotation;
        $data['quotationDetail']            = new QuotationDetails();
        $data['quotationDetailSubmitRoute'] = 'quotationDetailSubmit';
        
        return view('it.quotationForm',$data);
    }

    public function quotationDetailSubmit(Request $request)
    {
        $quotation              = Quotation::find($request->id);
        $quotation->item        = $request->item;
        $quotation->quantity    = $request->quantity;
        $quotation->update();
        for($i=1; $i <=$request->total_quotation; $i++) 
        { 
            $quotationDetail = new QuotationDetails();

            if($i==1 && !empty($request->quotation_id))
            {
                $quotationDetail    = QuotationDetails::find($request->quotation_id);
            }
            $itemDetails    = null;
            if($request->has('feature_type_'.$i))
            {
            $itemDetails    =  serialize(array_combine($request['feature_type_'.$i],$request['feature_detail_'.$i]));
            }
            $quotationDetail->quotation_id      = $quotation->id;
            $quotationDetail->price_per_item    = $request['price_per_item_'.$i];
            $quotationDetail->total_price       = $request['total_price_'.$i];
            $quotationDetail->vendor_detail     = $request['vendor_detail_'.$i];
            $quotationDetail->item_detail       = $itemDetails;
            $quotationDetail->save();
        }
            Session::flash('success','Quotation Submitted!');
            return redirect()->route('quotationList');
    }

    public function quotationDetailList(Quotation $quotation)
    {
        $this->authorize('quotationView',new Stock()); 
        $data['quotationDetails']   = $quotation->quotationDetails->load('quotation');
        return view('it.quotationDetailList',$data);
    }

    public function quotationDetailEdit(QuotationDetails $quotationDetail)
    {   
        $this->authorize('quotationEdit',new Stock()); 
        $data['quotation']                  = $quotationDetail->load('quotation')->quotation;
        // $data['submitRoute']= 'quotationSubmit';
        $data['quotationDetail']            = $quotationDetail;
        $data['quotationDetailSubmitRoute'] = 'quotationDetailSubmit';

        return view('it.quotationForm',$data);
    }

    public function quotationDelete(Request $request)
    {   
        $this->authorize('quotationDelete',new Stock());
        $quotation  = Quotation::with('quotationDetails')->find($request->quotation_id);
        $quotation->quotationDetails()->delete();
        $quotation->delete();

        return 'quotation deleted';
    }

    public function quotationDetailDelete(Request $request)
    {
        $this->authorize('quotationDelete',new Stock());
        QuotationDetails::find($request->quotationDetail_id)->delete();
          
        return 'Quotation Detail Deleted';
    }
    
    public function sendForApproval(Quotation $quotation)
    {
        $quotation->status  = 'sent';
        $quotation->save();
        $user       = User::where('email','<>','martha.folkes@theknowledgeacademy.com')->whereHas('roles',function($query){
                                $query->where('name','hr');
                            })->get();
        $email                  = $user->pluck('email','email')->toArray();
        $notificationReceivers  = $user->pluck('id','id')->toArray(); 
        $message                = auth()->user()->employee->name.' sent quotation';
        $subject                = $message;
        $link                   = route('hrQuotationList');
        $data['link']           = $link;
        send_notification($notificationReceivers,$message,$link);
        send_email("email.action", $data, $subject, $message,$email,null);
        return 'done';
    }
    // ended here
    // item crud functions
    public function itemList()
    {
        $this->authorize('view',new Stock());
        $data['items']      = Item::all();

        return view('it.itemList',$data);
    }

    public function itemCreate()
    {
        $this->authorize('create',new Stock());
        $data['item']           = new Item();;
        $data['submitRoute']    = 'itemSubmit';

        return view('it.itemForm',$data);
    }

    public function itemEdit(Item $item)
    {
        $this->authorize('update',new Stock());
        $data['item']           = $item;
        $data['submitRoute']    = 'itemSubmit';

        return view('it.itemForm',$data);
    }

    public function itemSubmit(ItemRequest $request)
    {
        $assignable     = isset($request->assignable) ? 1 :0;
        Item::updateOrCreate(['id'=>$request->id],['name'=>$request->name,'assignable'=>$assignable]);
        return redirect()->route('itemList')->with('success','Item submitted');
    }
    // ended here
    
    // stock function
    public function stockList(Request $request)
    {
        $this->authorize('view',new Stock());
        $data['purchased_source']   =   Stock::pluck('purchased_source','purchased_source')->toArray();
        $item_id                    =   Stock::pluck('item_id','item_id')->toArray();
        $data['item_name']          =   Item::whereIn('id',$item_id)->pluck('name','id');
        $stock                      =   Stock::withCount(['stockDetails','stockDetails as currently_assigned_count'=>function($query){
                                        return $query->whereDoesntHave('EquipmentAssign');
                                        }])->with(['item','purchasedByEmployee','stockDetails']);
      
        if(request()->has('purchased_source'))
        {
            $stock = $stock->where('purchased_source',$request->purchased_source);
        }
        if(request()->has('item_id'))
        {
            $stock = $stock->where('item_id',$request->item_id);
        }
        if(!empty($request->dateFrom) || ($request->dateTo))
        {    
            $stock->where(function($subQuery) use ($request){

                $subQuery->where(function($query1) use($request)
                {

                    $query1->orWhereDate('created_at',$request->dateFrom);

                })->orWhere(function($query2) use($request){

                    $query2->whereBetween('created_at',[$request->dateTo,$request->dateFrom]);
                });

            });
        } 
        
        $data['stocks'] = $stock->orderBy('created_at')->paginate(20);
        return view('it.stockList',$data);
    }

    public function stockCreate()
    {
        $stock      = new Stock();
        $this->authorize('create',$stock);

        $data['stock']          = $stock;
        $data['submitRoute']    = 'stockSubmit';
        $data['items']          = Item::pluck('name','id')->toArray();
        $data['purchaseSource'] = ['online'=>'online','market'=>'market'];

        return view('it.stockForm',$data);
    }

    public function stockEdit(Stock $stock)
    {
        $this->authorize('update',$stock);
        $data['stock']          = $stock;
        $data['submitRoute']    = 'stockSubmit';
        $data['items']          = Item::pluck('name','id')->toArray();
        $data['purchaseSource'] = ['online'=>'online','market'=>'market'];

        return view('it.stockForm',$data);
    }
    
    public function stockSubmit(StockRequest $request)
    {
        $bill       = null;
        $stock      = Stock::firstOrCreate(['id'=>$request->id]);
        $stock->item_id         = $request->item_id;
        $stock->quantity        = $request->quantity;
        $stock->price_per_item  = $request->price_per_item;
        $stock->total_price     = $request->total_price;

        if(!empty($stock->bill) && $request->has('bill'))
        {
            if(\Storage::exists("documents/stock/$stock->bill"))
            {
                \Storage::delete("documents/stock/$stock->bill"); 
            }
        }
        if($request->has('bill'))
        {    
            $bill = 'bill'.Carbon::now()->timestamp.'.'.$request->file('bill')->getClientOriginalExtension();
            $request->file('bill')->move(storage_path('app/documents/stock'), $bill);
        }
        $stock->purchase_date       = $request->purchase_date;
        $stock->purchased_source    = $request->purchase_source;
        if(!empty($bill))
        {
           $stock->bill = $bill;
        }
        $stock->purchased_by    = auth()->user()->employee->id;
        $stock->save();

        return redirect()->route('stockList')->with('success','Stock submitted successfully');
    }

    public function viewBill(Request $request)
    {
        $file= storage_path("app/documents/stock/$request->bill");
        
        return response()->file($file, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function stockDetails(Request $request)
    {
        $this->authorize('detailView',new Stock());
        $data['stockDetails']   = StockDetails::with(['stock.item','EquipmentAssign.assignedToEmployee.department'])
                                        ->where('stock_id',$request->stock_id)->get();
        $data['stock']          = Stock::select('id','quantity')->find($request->stock_id);
        
        return view('it.stockDetailList',$data);
    }

    public function stockItemForm(Request $request)
    {
        $this->authorize('create',new Stock());
        
        $data['stock']          = Stock::with('item')->find($request->stock_id);
        $data['stockDetail']    = new StockDetails();

        if(!$request->stock_id)
        {
        // $validator = Validator::make([],[]);
        // $validator->errors()->add("limit",'Stock detail limit exceeded');
        // throw new \Illuminate\Validation\ValidationException($validator);
        return back()->with('failure','Stock detail limit exceeded');
        }

        if($request->stock_detail_id)
        {
        $data['stockDetail']    = StockDetails::find($request->stock_detail_id);
        }
        $data['status']         = ['Active'=>'Active','Damaged'=>'Damaged'];

        if(!$data['stock']->item->assignable)
        {
            $data['status']+=['In Use'=>'In Use'];
        }
        $data['submitRoute']    = 'stockDetailSubmit';
        
        return view('it.stockDetailForm',$data);
    }

    public function stockDetailSubmit(StockDetailRequest $request)
    {
        $equipmentAssignable    = Stock::find($request->stock_id)->item->assignable;

        if($equipmentAssignable && $request->status=='In Use')
        {
        // $validator = Validator::make([],[]);
        // $validator->errors()->add("status",'Cannot put status `In Use` if equipment is assignable');
        // throw new \Illuminate\Validation\ValidationException($validator);
        return back()->with('failure','Cannot put status `In Use` if equipment is assignable');
        }

        $stockDetail    = StockDetails::firstOrNew(['id'=>$request->id]);
        $stockDetail->stock_id          = $request->stock_id;
        $stockDetail->manufacturer      = $request->manufacturer;
        $stockDetail->model_no          = $request->model_no;
        $stockDetail->label             = $request->label;
        $stockDetail->warranty_from     = $request->warranty_from;
        $stockDetail->warranty_till     = $request->warranty_till;
        $stockDetail->status            = $request->status;
        $stockDetail->save();

        return redirect()->route('stockDetailList',['stock_id'=>$request->stock_id])->with('success','stock detail submitted');
    }

    public function checkLabelExists(Request $request)
    {
        $duplicateLabel = StockDetails::when($request->id,function($query,$request){
                                return $query->where('id','<>',$request);
                            })->whereNotNull('label')->where('label',$request->label)->exists();
        return (string)$duplicateLabel;
    }
    // ended here
    
    // Employee Equipment functions
    public function employeeEquipmentList()
    {
        $this->authorize('equipmentAssign',new Stock());
        $data['employees']  = Employee::withoutGlobalScope('is_active')->with('department')->whereDoesntHave('employeeExitDetail')
                                    ->orWhereHas('employeeExitDetail',function($query){
                                        $query->whereNull('it_no_due');
                                    })->get();
        return view('it.employeeEquipments',$data);
    }

    public function employeeEquipmentDetailList($employee_id)
    {
        $this->authorize('equipmentAssign',new Stock());
        $data['employee']       = Employee::withoutGlobalScope('is_active')->find($employee_id);
        $data['equipmentTypes'] = Stock::whereHas('item',function($query1){
            $query1->where('assignable','1');
        
                })->with(['stockDetails'=>function($query2) use($employee_id){
            
                    $query2->whereNotNull('label')->where('status','Active')->where(function($query3) use($employee_id){
                        $query3->whereDoesntHave('EquipmentAssign')->orWhereHas('EquipmentAssign',function($query4) use($employee_id){
                                $query4->where('assigned_to',$employee_id);
                });
            });

        }])->get()->groupBy('item.name');

        return view('it.employeeEquipmentsDetail',$data);
    }

    public function employeeEquipmentUpdate(Request $request)
    {
        $employee           = Employee::withoutGlobalScope('is_active')->find($request->id);
        $employeeEquipments = [];
        EquipmentAssign::where('assigned_to',$employee->id)->delete();
        
        foreach($request->available_equipment_ids as $available_equipment_id)
        {
            if(!empty($available_equipment_id))
            {
                $equipmentAssign = EquipmentAssign::create(['assigned_to'=>$request->id,'stock_item_id'=>$available_equipment_id,
                'assigned_by'=>auth()->user()->employee->id]);
                $employeeEquipments+=[$equipmentAssign->stockItemDetail->equipment_type=>$equipmentAssign->stockItemDetail->equipment_label];
            }
        }

        if(!empty($employeeEquipments))
        {
            $notificationReceivers      = [$employee->user_id];
            $link                       = route('employeeDetail',['employee'=>$employee->id]);
            $subject                    = 'Equipment Alloted';
            $message                    = $subject;
            $email                      = [$employee->office_email];
            $data['allotedEquipments']  = $employeeEquipments;
            $data['employee']           = $employee;
            $data['link']               = $link;
            send_notification($notificationReceivers,'Equipments alloted by '.auth()->user()->employee->name,$link);
            send_email("email.equipmentAlot", $data, $subject, $message,$email,null);
        }
        return redirect()->route('employeeEquipmentList')->with('success','equipment updated');
    }

    public function employeeEquipmentRemove(Request $request)
    {
        $itemRequest    = EquipmentAssign::find($request->id);
        $itemRequest->itemRequest()->delete();
        $itemRequest->delete();

        return redirect()->route('employeeEquipmentList')->with('success','equipment removed');
    }

    // Ended Here
// ended here


// HR functions

    public function hrQuotationList()
    {
        $this->authorize('quotationAction',new Stock());
        $data['quotations'] = Quotation::with('employee')->where('status','sent')->whereHas('quotationDetails',function($query){
                                    $query->whereNull('is_approved');
                                })->orderBy('created_at')->get();

        return view('hr.quotationList',$data);
    }

    public function quotationAction(Request $request)
    {
        $action             = ($request->action=='true') ? 1 :0;
        $quotationDetail    = QuotationDetails::find($request->quotation_id);
        $user               = $quotationDetail->quotation->employee->user;
        $quotationDetail->update(['is_approved'=>$action,'action_by'=>auth()->user()->employee->id]);
        $user_ids   = $user->id;
        $link       = route('quotationList');

        if($action)
        {
            $message    = 'Your quotation is approved';
            $subject    = 'Quotation Approved';
            $data['remarks']=!empty($request->comment) ? $request->comment :null;
        }
        else
        {
            $message    = 'Your quotation is rejected';
            $subject    = 'Quotation Rejected';
            $data['remarks']=!empty($request->comment) ? $request->comment :null;
        }
        
        send_notification($user_ids,$message,$link);
        send_email("email.quotationAction", $data, $subject,$message, array($user->email),null);
        return $action;
    }

    public function hrStockList()
    {
        $this->authorize('hr',auth()->user());
        $data['items']  = Item::whereHas('stocks')->with('stocks')->get();
        return view('hr.stockList',$data);
    }
    
    public function hrStockDetailList($item_id)
    {   
        $this->authorize('hr',auth()->user());
        $data['stocks'] = Stock::withCount(['stockDetails','stockDetails as currently_assigned_count'=>function($query){
                                return $query->whereDoesntHave('EquipmentAssign');
                            }])->with(['item','purchasedByEmployee'])->where('item_id',$item_id)->orderBy('created_at')->get();

        return view('hr.stockDetailList',$data);
    }
    // ended here

    //software crud 
    public function list()
    {
        return view('it.softwareList');
    }

    public function softwareList(Request $request)
    {
        $softwares  = Software::query();
        if (!empty($request->get('name'))) {
            $softwares = $softwares->where('name', 'like', '%' . $request->get('name') . '%');
        }
        $data['itemsCount'] = $softwares->count();
        $data['data']       = $softwares->get();
        return json_encode($data);
        
    }

    public function softwareSubmit(SoftwareRequest $request)
    {
        $data   =  software::updateOrCreate(['id'=>$request->id],['name'=>$request->name]);
        return json_encode($data);
        return redirect()->route('softwareList')->with('success','software submitted');
    }

    public function softwaredelete(Request $request)
    {
        $department 	= Software::find($request->id);
        $department->delete();
        return json_encode("done");
    }
    //end here
    
    public function editEmail()
    {
        $this->authorize('it',new Equipment());
        $data['departments']    =    Department::pluck('name','id')->toArray();
        $data['employees']      =    Employee::pluck('name','id')->toArray();

        return view('it.updateEmailForm',$data);
    }

    public function updateEmail(Request $request){
        
        $employee               = Employee::find($request->employee);
        $employee->office_email = $request->email;
        $employee->update();

        $user           = $employee->user;
        $user->email    = $request->email;
        $user->save();

        return back()->with('success','Email Updated!');
    }
}
