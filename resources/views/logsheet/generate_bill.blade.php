@extends('layouts.admin')

@section('content')
<style>
    .table>tbody+tbody {
        border-top: 0px solid #ddd;
    }

    .danger {
        color: red;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        @isset($success_message)
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Success! </strong> {{$success_message}}
        </div>
        @endisset
        <div class="panel panel-primary">
            <div class="panel-body" style="overflow: auto;">
                <div class="row">
                    <form action="" method="post" class="form-horizontal">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <input type="text" name="date" readonly="" required="" value="{{$month}}" autocomplete="off" class="form-control form-control-inline month-picker" data-date-format="M yyyy">
                                <div class="help-block"></div>
                            </div>
                            <div class="col-md-3">
                                <select name="vehicle_id" required class="form-control select2" data-placeholder="Select...">
                                    <option value="">Select vehicle</option>
                                    @foreach ($vehicle_list as $item)
                                    @if($vehicle_id==$item->vehicle_id)
                                    <option selected value="{{$item->vehicle_id}}">{{$item->name}}</option>
                                    @else
                                    <option value="{{$item->vehicle_id}}">{{$item->name}}</option>
                                    @endif

                                    @endforeach
                                </select>
                                <div class="help-block"></div>
                            </div>


                            <div class="col-md-3">
                                <select name="company_id" required class="form-control select2" data-placeholder="Select...">
                                    <option value="">Select comapny</option>
                                    @foreach ($company_list as $item)
                                    @if($company_id==$item->company_id)
                                    <option selected="" value="{{$item->company_id}}">{{$item->name}}</option>
                                    @else
                                    <option value="{{$item->company_id}}">{{$item->name}}</option>
                                    @endif

                                    @endforeach
                                </select>
                                <div class="help-block"></div>
                            </div>

                            <div class="col-md-3">
                                <input type="hidden" name="bill_type" value="{{$bill_type}}">
                                <button type="submit" class="btn btn-primary">Generate </button>
                                <a href="/admin/logsheet" class="btn btn-default">Back </a>
                            </div>
                            <br>
                            <br>
                        </div>

                    </form>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        @yield('middle_content')
        @php($invoice_id=0)
        @php($bill_date='')
        @if(isset($logsheet_detail) || !empty($casual_list))
        @isset($invoice)
        @php($bill_date=Carbon\Carbon::parse($invoice->bill_date)->format('d-m-Y'))
        @php($invoice_id=$invoice->invoice_id)
        @elseif(isset($logsheet_detail) && $trip_ids=='')
        @php($bill_date='')
        @php($invoice_id=0)
        @php($logsheet_detail[3]['qty']=$extra_hour)
        @php($logsheet_detail[3]['amount']=number_format($extra_hour*$logsheet_detail[3]['rate'],2))
        @php($logsheet_detail[5]['amount']=number_format($toll,2))
        @endisset
        <form @if($bill_type=='casual') action="" @else action="/admin/logsheet/logsheetbillsave" @endif  method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="panel panel-primary">
                <div class="panel-body" style="overflow: auto;">
                    <div class="row">




                        <div class="col-md-12">
                            @if(!empty($logsheet_detail))

                            <table class="table table-bordered" id="particular_table" style="font-size: 12px !important;color: black !important;">

                                <tr>
                                    <th class="td-c">Particulars</th>
                                    <th class="td-c">Unit</th>
                                    <th class="td-c">Qty</th>
                                    <th class="td-c">Rate(R.s)</th>
                                    <th class="td-c">Amount</th>
                                    <th class="td-c"><a href="javascript:void(0)" class="btn btn-sm success" onclick="addRow()"><i class="fa fa-plus"> </i></a></th>
                                </tr>
                                @php($int=1)
                                @php($body_closed=0)
                                <tbody id="abc">
                                    @foreach($logsheet_detail as $det)
                                    @if($det['is_deduct']==1)
                                </tbody>
                                @php($body_closed=1)
                                @endif

                                @if($det['particular_name']=='Toll /Parking' && $body_closed==0)
                                </tbody>
                                @php($body_closed=1)

                                @endif
                                @if($invoice_id==0 && $int==5 && Session::get('admin_id')!=8)
                                <tr>
                                    <td class="td-c"><input type="text" class="form-control" value="{{$det['particular_name']}}" name="particular_name[]"> </td>
                                    <td class="td-c"><input type="text" class="form-control" name="unit[]" value="{{$det['unit']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" @if($det['unit']=='' ) readonly @endif class="form-control" onblur="calculateLogsheet();" id="qty{{$int}}" name="qty[]" value="{{$det['qty']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" @if($det['unit']=='' ) readonly @endif class="form-control" onblur="calculateLogsheet();" id="rate{{$int}}" name="rate[]" value="{{$det['rate']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" class="form-control" id="amt{{$int}}" name="amount[]" onblur="calculateLogsheet();" value="{{$det['amount']}}">
                                        <input type="hidden" class="form-control" name="is_deduct[]" id="is_deduct{{$int}}" value="{{$det['is_deduct']}}">
                                        <input type="hidden" class="form-control" name="int[]" value="{{$int}}">
                                        <input type="hidden" class="form-control" name="detail_id[]" value="{{$det['id']}}">
                                    </td>
                                    <td class="td-c">
                                        <a href="javascript:;" onclick="$(this).closest('tr').remove();calculateLogsheet();" data-confirm="Are you sure to delete this item?" class="btn btn-sm danger"> <i class="fa fa-times"> </i> </a>
                                    </td>
                                </tr>

                                @else
                                <tr>
                                    <td class="td-c"><input type="text" class="form-control" value="{{$det['particular_name']}}" name="particular_name[]"> </td>
                                    <td class="td-c"><input type="text" class="form-control" name="unit[]" value="{{$det['unit']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" @if($det['unit']=='' ) readonly @endif class="form-control" onblur="calculateLogsheet();" id="qty{{$int}}" name="qty[]" value="{{$det['qty']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" @if($det['unit']=='' ) readonly @endif class="form-control" onblur="calculateLogsheet();" id="rate{{$int}}" name="rate[]" value="{{$det['rate']}}"></td>
                                    <td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" class="form-control" id="amt{{$int}}" name="amount[]" onblur="calculateLogsheet();" value="{{$det['amount']}}">
                                        <input type="hidden" class="form-control" name="is_deduct[]" id="is_deduct{{$int}}" value="{{$det['is_deduct']}}">
                                        <input type="hidden" class="form-control" name="int[]" value="{{$int}}">
                                        <input type="hidden" class="form-control" name="detail_id[]" value="{{$det['id']}}">
                                    </td>
                                    <td class="td-c">
                                        <a href="javascript:;" onclick="$(this).closest('tr').remove();calculateLogsheet();" data-confirm="Are you sure to delete this item?" class="btn btn-sm danger"> <i class="fa fa-times"> </i> </a>
                                    </td>
                                </tr>

                                @endif
                                @php($int++)
                                @endforeach
                                @if($body_closed==0)
                                </tbody>
                                @endif

                                @if($int==7)

                                @endif



                                <tr>
                                    <td class="td-c"><input type="hidden" id="countRow" value="{{$int}}"></td>
                                    <td class="td-c"></td>
                                    <td class="td-c"></td>
                                    <th class="">Total Value /Taxable Value(Rs.)</th>
                                    <th><input type="number" class="form-control" readonly="" id="base_total" name="base_total"></th>
                                </tr>
                                <tr>
                                    <td colspan="2" rowspan="3" style="vertical-align: middle;"><span class="pull-right"><b>Goods and Services Tax @5%</b></span></td>
                                    <td><span class="pull-right">CGST@</span></td>
                                    <td><span class="pull-right"><input id="cgst" class="form-control" type="number" onblur="calculateLogsheet();" step="0.01" value="2.50"></span></td>
                                    <th><span class="pull-right"><input type="number" step="0.01" class="form-control" readonly="" id="cgst_amt" name="cgst"></span></th>
                                </tr>
                                <tr>
                                    <td><span class="pull-right">SGST@</span></td>
                                    <td><span class="pull-right"><input id="sgst" class="form-control" onblur="calculateLogsheet();" type="number" step="0.01" value="2.50"></span></td>
                                    <th><span class="pull-right"><input type="number" step="0.01" class="form-control" readonly="" id="sgst_amt" name="sgst"></span></th>
                                </tr>
                                <tr>
                                    <td><span class="pull-right">IGST@</span></td>
                                    <td><span class="pull-right"><input id="igst" class="form-control" onblur="calculateLogsheet();" type="number" step="0.01" value="0.0"></span></td>
                                    <th><span class="pull-right"><input type="number" step="0.01" class="form-control" readonly="" id="igst_amt" name="igst"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Total GST Value</b></span></td>
                                    <th><span class="pull-right"><input type="number" class="form-control" readonly="" id="total_gst" name="total_gst"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Grand Total (Inclusive of GST)</b></span></td>
                                    <th><span class="pull-right"><input type="number" class="form-control" readonly="" id="grand_total" name="grand_total"></span></th>
                                </tr>
                                @if($invoice_id==0)
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Select Bill Number Sequence</b></span></td>
                                    <th><span class="pull-right">
                                            <select name="invoice_seq" class="form-control" data-placeholder="Select...">
                                                <option value="">Select Bill Sequence</option>
                                                @foreach ($sequence as $item)
                                                <option selected value="{{$item->id}}">{{$item->prefix}}-{{$item->current_number}}</option>
                                                @endforeach
                                            </select>
                                        </span></th>
                                </tr>
                                @else
                                <input type="hidden" name="invoice_seq" value="0">
                                @endif
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Select Bill date</b></span></td>
                                    <th><span class="pull-right">
                                            <input type="text" name="bill_date" required value="{{$bill_date}}" autocomplete="off" class="form-control date-picker" data-date-format="d-M-yyyy"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Work Order</b></span></td>
                                    <th><span class="pull-right">
                                            <input type="text" name="work_order_no" value="{{$work_order_no}}" class="form-control"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>PO Number</b></span></td>
                                    <th><span class="pull-right">
                                            <input type="text" name="po_number" value="{{$po_number}}" class="form-control"></span></th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: middle;"><span class="pull-right"><b>Description</b></span></td>
                                    <th><span class="pull-right">
                                            <input type="text" name="narrative" value="{{$narrative}}" class="form-control"></span></th>
                                </tr>

                            </table>
                            @endif
                            @if(count($expense_list)>0)
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h4>Expense List</h4>
                                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th class="td-c">DATE</th>
                                                <th class="td-c">Category</th>
                                                <th class="td-c">Name</th>
                                                <th class="td-c">Note</th>
                                                <th class="td-c">Amount</th>
                                                <th class="td-c">Adjust Amt</th>
                                                <th class="td-c">Adjust?</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expense_list as $item)
                                            <tr>
                                                <td class="td-c">
                                                    {{ Carbon\Carbon::parse($item->date)->format('d/m/Y')}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->category}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->employee_name}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->note}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->pending_amount}}
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="req_{{$item->request_id}}" max="{{$item->pending_amount}}" onchange="invexpense();" id="req_{{$item->request_id}}" value="{{$item->pending_amount}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="checkbox" name="rcheck[]" onchange="invexpense();" value="{{$item->request_id}}">
                                                </td>

                                            </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5"></th>

                                                <th class="td-c" id="total_expense">0.00</th>
                                                <th class="td-c"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.panel-body -->
                            </div>
                            @endif

                            @if(!empty($casual_list))
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h4>Casual List</h4>
                                    <table id="example1" class="table table-bordered table-striped" style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th class="td-c">Select?</th>
                                                <th class="td-c">DATE</th>
                                                <th class="td-c">Cab</th>
                                                <th class="td-c">Passengers</th>
                                                <th class="td-c">Pickup</th>
                                                <th class="td-c">Package</th>
                                                <th class="td-c">Package amount</th>
                                                <th class="td-c">Extra km</th>
                                                <th class="td-c">Extra km Rate</th>
                                                <th class="td-c">Extra hour</th>
                                                <th class="td-c">Extra hour Rate</th>
                                                <th class="td-c">DA</th>
                                                <th class="td-c">Toll/Parking</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($casual_list as $item)
                                            <tr>
                                                <td class="td-c">
                                                    <input type="checkbox" name="trip_id[]" checked  value="{{$item->trip_id}}">
                                                </td>
                                                <td class="td-c">
                                                    {{ Carbon\Carbon::parse($item->date)->format('d/m/Y')}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->vehicle_type}}
                                                    <input type="hidden" name="vehicle_type{{$item->trip_id}}"  value="{{$item->vehicle_type}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    {{$item->passengers}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->pickup_location}}
                                                </td>
                                                <td class="td-c">
                                                    {{$item->package_name}}
                                                    <input type="hidden" name="package_name{{$item->trip_id}}"  value="{{$item->package_name}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="package_amount{{$item->trip_id}}"  value="{{$item->package_amount}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="extra_km{{$item->trip_id}}"  value="{{$item->extra_km}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="extra_km_rate{{$item->trip_id}}" @if($item->extra_km>0) value="{{$item->extra_km_amount/$item->extra_km}}" @endif class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="extra_hour{{$item->trip_id}}"  value="{{$item->extra_hour}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="extra_hour_rate{{$item->trip_id}}" @if($item->extra_hour>0) value="{{$item->extra_hour_amount/$item->extra_hour}}" @endif class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="driver_amount{{$item->trip_id}}"  value="{{$item->driver_amount}}" class="form-control input-sm">
                                                </td>
                                                <td class="td-c">
                                                    <input type="number" step="0.01" name="toll_parking{{$item->trip_id}}"  value="{{$item->toll_parking}}" class="form-control input-sm">
                                                </td>

                                            </tr>
                                            @endforeach

                                        </tbody>
                                        
                                    </table>
                                </div>
                                <!-- /.panel-body -->
                            </div>

                            @endif
                            <input type="hidden" name="invoice_id" value="{{$invoice_id}}">
                            <input type="hidden" name="vehicle_id" value="{{$vehicle_id}}">
                            <input type="hidden" name="company_id" value="{{$company_id}}">
                            <input type="hidden" name="bill_type" value="{{$bill_type}}">
                            <input type="hidden" name="date" value="{{$month}}">
                            <input type="hidden" name="type" value="{{$type}}">
                            <input type="hidden" name="trip_ids" value="{{$trip_ids}}">
                            <input type="hidden" id="expense_amount" name="expense_amount">
                            <input type="submit" class="btn btn-primary pull-right">
                        </div>

                    </div>
                </div>

            </div>
        </form>


        <script>
            function addRow() {
                count = Number(document.getElementById("countRow").value);
                count = count + 1;
                $('#abc').append('<tr><td class="td-c"><input type="text" class="form-control" value="" name="particular_name[]"> </td><td class="td-c"><input type="text" class="form-control" name="unit[]" value=""></td><td class="td-c"><input type="number" step="0.01" pattern="[0-9]*"  class="form-control" onblur="calculateLogsheet();" id="qty' + count + '" name="qty[]" value=""></td><td class="td-c"><input type="number" step="0.01" pattern="[0-9]*"  class="form-control" onblur="calculateLogsheet();" id="rate' + count + '" name="rate[]" value=""></td><td class="td-c"><input type="number" step="0.01" pattern="[0-9]*" class="form-control" id="amt' + count + '" name="amount[]" onblur="calculateLogsheet();" value=""><input type="hidden" class="form-control" name="is_deduct[]" id="is_deduct' + count + '" value="0"><input type="hidden" class="form-control" name="int[]" value="' + count + '"><input type="hidden" class="form-control" name="detail_id[]" value="0"></td><td class="td-c"><a data-cy="particular-remove2" href="javascript:;" onclick="$(this).closest(' + "'" + 'tr' + "'" + ').remove();calculateLogsheet();" class="btn btn-sm danger"> <i class="fa fa-times"> </i> </a></td></tr>');
                document.getElementById("countRow").value = count;
            }
        </script>
        @endif
        <!-- END PAYMENT TRANSACTION TABLE -->

    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
@endsection