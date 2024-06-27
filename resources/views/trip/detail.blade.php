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
                <label class="control-label col-md-4">Start Time :<span class="required"></span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{ \Carbon\Carbon::parse($det->start_time)->format('d M Y h:i A')}}</label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">End Time :<span class="required"></span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{ \Carbon\Carbon::parse($det->end_time)->format('d M Y h:i A')}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Special Note :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{$det->note}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Vehicle :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{$vdet->number}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Driver :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{$edet->name}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Remark :<span class="required"> </span></label>
                <div class="col-md-7">
                    <label class="control-label">  {{$det->remark}}</label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Attachments<span class="required"> </span></label>
                <div class="col-md-7">
                    @foreach($attachments as $k=>$v)
                    <a href="{{$v}}" target="_blank">Attachment {{$k+1}}</a><br>
                    @endforeach
                </div>
            </div>


            
        </div>
    </form>
</div>
@endsection
