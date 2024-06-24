@extends('layouts.admin')

@section('content')
<div class="row" id="insert">
    <form action="/trip/scheduleupdate" method="post" id="customerForm" class="form-horizontal">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">Vehicle type :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label"> {{$det->vehicle_type}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Date :<span class="required"></span></label>
                <div class="col-md-7">
                    <input type="text" name="date" readonly="" value="{{ \Carbon\Carbon::parse($det->date)->format('d M yyyy')}}" autocomplete="off" class="form-control form-control-inline date-picker" data-date-format="dd M yyyy">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Pickup Time :<span class="required"></span></label>
                <div class="col-md-7">
                    <div class="bootstrap-timepicker">
                        <div class="">
                            <div class="input-group">
                                <input type="text" required="" readonly="" value="{{ \Carbon\Carbon::parse($det->time)->format('h:i A')}}" name="pickup_time" class="form-control timepicker">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Total Passengers :<span class="required"> </span></label>
                <div class="col-md-7">

                    <input type="text" name="total_passengers" readonly value="{{$det->total_passengers}}" class="form-control">
                </div>
            </div>

            <div id="passengers_name">
                <div class="form-group">
                    <label class="control-label col-md-4">Passenger Name :<span class="required"> </span></label>
                    <div class="col-md-7">
                        <input type="text" name="passengers" readonly value="{{$det->passengers}}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Pickup Location :<span class="required"></span></label>
                <div class="col-md-7">
                <input type="text" name="pickup_location" value="{{$det->pickup_location}}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Drop Location :<span class="required"> </span></label>
                <div class="col-md-7">
                <input type="text" name="drop_location" value="{{$det->drop_location}}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Special Note :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label"> {{$det->note}}</label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Emails<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="text" name="emails" value="{{$det->emails}}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Whatsapp numbers<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="text" name="mobiles" value="{{$det->mobiles}}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Vehicle<span class="required">* </span></label>
                <div class="col-md-7">
                    <select name="vehicle_id" required class="form-control select2" data-placeholder="Select...">
                        <option value="">Select vehicle</option>
                        @foreach ($vehicle_list as $item)
                        <option @if($det->vehicle_id==$item->vehicle_id) selected @endif value="{{$item->vehicle_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Driver<span class="required">* </span></label>
                <div class="col-md-7">
                    <select name="employee_id" required class="form-control select2" data-placeholder="Select...">
                        <option value="">Select Driver</option>
                        @foreach ($employee_list as $item)
                        <option @if($det->employee_id==$item->employee_id) selected @endif value="{{$item->employee_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Vendor<span class="required">* </span></label>
                <div class="col-md-7">
                    <select name="vendor_id" required class="form-control select2" data-placeholder="Select...">
                        <option value="">Select vendor</option>
                        @foreach ($employee_list as $item)
                        <option @if($det->vendor_id==$item->employee_id) selected @endif value="{{$item->employee_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-4"></div>
                <div class="col-md-7">
                    <h4 id="status"></h4>
                    <p id="loaded_n_total"></p>
                    <input type="hidden" name="trip_id" value="{{$trip_id}}">
                    <button id="savebutton" type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-Close">Clear</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection