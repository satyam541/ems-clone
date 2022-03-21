@extends('layouts.master')
@section('headerLinks')
<style>
    .cross{
        float: right;
    margin: -3px 2px;
    color: red;
    cursor: pointer;
    }
</style>
@endsection
@section('content')   

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quotation Form</li>
                </ol>
            </nav>
        </div>
        @if(empty($quotationDetail))
        <div class="col-12 grid-margin">
    
            <div class="card">
    
                {{ Form::model($quotation, ['route' => $submitRoute, 'files' => 'true']) }}
                <div class="card-body">
                    <h4 class="card-title">Quotation Form</h4>
                    {{Form::hidden('id',null)}}
    
                    <div class="row">
    
                        
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('item', 'Item Name', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('item', null, ['class' => 'form-control','required', 'id' => 'name', 'placeholder' => 'laptop *']) }}
    
                                    @error('item')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('quantity', 'Enter quantity', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('quantity',null,
                                     ['class' => 'form-control', 'id' => 'name','required', 'placeholder' => '10 *']) }}
    
                                    @error('quantity')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        @if(!$quotation->id)
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Add Quotation</button>
                        </div>
                        @endif
    
                        {{Form::close()}}
                    </div>
                </div>
    
            </div>
    
    
    
        </div>
        @endif
        @if($quotation->id)
        <div class="col-12 grid-margin">
        {{ Form::model($quotationDetail, ['route' => $quotationDetailSubmitRoute, 'files' => 'true']) }}
        {{Form::hidden('total_quotation',1)}}
            <div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Quotation Form</h4>
                        {{Form::hidden('id',$quotation->id)}}
                        {{Form::hidden('quotation_id',$quotationDetail->id ?? null)}}
                        <div class="row">
        
                            
        
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('item', 'Item Name', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::text('item', $quotation->item, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'laptop *','required']) }}
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('quantity', 'Enter quantity', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::text('quantity',$quotation->quantity,
                                         ['class' => 'form-control', 'id' => 'name', 'placeholder' => '10 *','required']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                </div>
        
        
        
            </div>
            <div class="col-12 grid-margin">
            <div class="card quotation-details mt-3"> 
                <div class="card-body">
                    <h4 class="card-title detail-form">Quotation Detail Form</h4>
                    <h4 class="card-title text-right index" id="1">#1</h4>
                    <div class="row">
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('price_per_item_1', 'Price Per Item', ['class' => 'col-sm-3 col-form-label price_per_item']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('price_per_item_1', $quotationDetail->price_per_item ?? null,
                                     ['class' => 'form-control price-per-item', 'placeholder' => '200 *','required']) }}
    
                                    @error('price_per_item')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('total_price_1', 'Total Price', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('total_price_1', $quotationDetail->total_price ?? null,
                                     ['class' => 'form-control total-price', 'placeholder' => '2000 *','required']) }}
    
                                    @error('total_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('vendor_detail_1', 'Vendor Details', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('vendor_detail_1', $quotationDetail->vendor_detail ?? null,
                                     ['class' => 'vendor-detail form-control', 'placeholder' => 'Monica Tower,Jalandhar','required']) }}

                                    @error('vendor_detail')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                {{ Form::label('item_detail', 'Item Details:', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <button class="btn float-right add_detail" type="button"><i class="mdi mdi-plus-circle" style="font-size: x-large"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12 item_detail">
                                @if($quotationDetail->id && !empty($quotationDetail->item_detail))
                                @foreach($quotationDetail->item_detail as $key => $value)
                                <div class="row form-group">
                                    <span class="col-sm-1 text-center">#</span>
                                    <input type="text" placeholder="processor" required   style="border:1px solid grey" value="{{$key}}"  class="form-control-sm mr-3 col-md-4 feature-type" name="feature_type_1[]">
                                    <input  class="form-control-sm mr-3 col-md-4 feature-detail" required style="border:1px solid grey" value="{{$value}}" placeholder="i5" name="feature_detail_1[]" type="text">
                                    <button type="button" class="removeSpecification btn"><i class="fa fa-trash text-danger"></i></button>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>

                        
    
    
                        
                    </div>
                </div>
    
            </div>
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn float-right btn-success quotation">Add more Quotation</a>
            </div>   
            {{Form::close()}}
            
        </div>
        </div>
        @endif
    
    </div>
@endsection

@section('footerScripts')
<script>
    
    $('.add_detail').click(function(){
    var html = '<div class="row form-group">\
                    <span class="col-sm-1 text-center">#</span>\
                    <input type="text" placeholder="processor" required   style="border:1px solid grey"  class="form-control-sm mr-3 col-md-4 feature-type" name="feature_type_1[]">\
                    <input  class="form-control-sm mr-3 col-md-4 feature-detail" required style="border:1px solid grey" placeholder="i5" name="feature_detail_1[]" type="text">\
                    <button type="button" class="removeSpecification btn"><i class="fa fa-trash text-danger"></i></button>\
                </div>';
        
    $(this).closest('div.form-group').next('.item_detail').append(html).show();
});
$(".item_detail").on('click','.removeSpecification.btn',function(){
    $(this).closest('div.form-group.row').remove();
        
});
$('.quotation').on('click',function(){
var quotationClone=$('.quotation-details').last().clone(true).addClass('mt-3');
var index=$(quotationClone).find('.index').attr('id');
if(index==1)
{
$(quotationClone).find('.detail-form').append('<i class="fa fa-times fa-lg cross"></i>');
}
var inputs=$(quotationClone).find(':input');
$.each(inputs,function(i,value){
var oldInputName=$(this).attr('name');
if(typeof oldInputName!='undefined')
{   
    if((oldInputName=='price_per_item_'+index) || (oldInputName=='total_price_'+index) || (oldInputName=='vendor_detail_'+index))
    {
        var newInputName=oldInputName.slice(0, oldInputName.lastIndexOf('_'))+'_'+(parseInt(index)+parseInt(1));
        $(this).attr('name',newInputName).val('');
    }
    if((oldInputName=='feature_type_'+index+'[]') || (oldInputName=='feature_detail_'+index+'[]'))
    {
        
        var newInputName=oldInputName.slice(0, oldInputName.lastIndexOf('_'))+'_'+(parseInt(index)+parseInt(1))+'[]';
        $(this).attr('name',newInputName).val('');
    }
}
});
index++;
console.log(index);
$('input[name="total_quotation"]').val(index);
$(quotationClone).find('.index').attr('id',index).html('#'+index);
$(this).parent('div').before(quotationClone);
});

$('.quotation-details .card-body').on('click','.cross',function(){

var currentCount=$('input[name="total_quotation"]').val();

$('input[name="total_quotation"]').val(eval(currentCount-1));
$(this).closest('div.quotation-details').remove();
$.each($('.quotation-details'),function(i,value){
    var oldInputName='';
    var currentQuotationIndex=parseInt(i)+parseInt(1);
    var inputs=$(value).find(':input');
    $.each(inputs,function(i,value){
        var oldInput=this;
        console.log(oldInput);
           
            if(($(oldInput).hasClass('price-per-item')) || ($(oldInput).hasClass('total-price')) || ($(oldInput).hasClass('vendor-detail')))
            {
                oldInputName=$(oldInput).attr('name');
                var newInputName=oldInputName.slice(0, oldInputName.lastIndexOf('_'))+'_'+currentQuotationIndex;
                $(this).attr('name',newInputName);
            }
            if(($(oldInput).hasClass('feature-type')) || ($(oldInput).hasClass('feature-detail')))
            {
                oldInputName=$(oldInput).attr('name');   
                var newInputName=oldInputName.slice(0, oldInputName.lastIndexOf('_'))+'_'+currentQuotationIndex+'[]';
                $(this).attr('name',newInputName);
            }
        
    });
    $(value).find('.index').attr('id',currentQuotationIndex).html('#'+currentQuotationIndex);
    
});
});
</script>
@endsection

