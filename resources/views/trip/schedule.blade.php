@extends('layouts.admin')

@section('content')
<div class="row" id="insert">
    <form action="/trip/schedulesave" method="post" id="customerForm" class="form-horizontal">
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
                    <label class="control-label"> {{ \Carbon\Carbon::parse($det->date)->format('d M Y')}} </label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Pickup Time :<span class="required"></span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{ \Carbon\Carbon::parse($det->time)->format('h:i A')}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Total Passengers :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">   {{$det->total_passengers}}</label>
                </div>
            </div>

            <div id="passengers_name">
                <div class="form-group">
                    <label class="control-label col-md-4">Passenger Name :<span class="required"> </span></label>
                    <div class="col-md-7">
                        <label class="control-label">   {{$det->passengers}}</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Pickup Location :<span class="required"></span></label>
                <div class="col-md-7">
                    <label class="control-label"> {{$det->pickup_location}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Drop Location :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label"> {{$det->drop_location}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Special Note :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{$det->note}}</label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Emails<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="text" name="emails" class="form-control" >
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Whatsapp numbers<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="text" name="mobiles" class="form-control" >
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Vehicle<span class="required">* </span></label>
                <div class="col-md-7">
                    <select name="vehicle_id" required class="form-control select2" data-placeholder="Select...">
                        <option value="">Select vehicle</option>
                        @foreach ($vehicle_list as $item)
                        <option value="{{$item->vehicle_id}}">{{$item->name}}</option>
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
                        <option value="{{$item->employee_id}}">{{$item->name}}</option>
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
                        <option value="{{$item->employee_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Notification send?<span class="required">* </span></label>
                <div class="col-md-7">
                    <input type="checkbox" value="1" name="notification_sent">
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-4"></div>
                <div class="col-md-7">
                    <h4 id="status"></h4>
                    <p id="loaded_n_total"></p>
                    <input type="hidden" name="req_id" value="{{$req_id}}">
                    <button id="savebutton" type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-Close">Clear</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
