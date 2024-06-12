@extends('layouts.admin')

@section('content')
<div class="row" id="insert">
    <form action="/trip/completesave" method="post" id="customerForm" class="form-horizontal">
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
                    <label class="control-label"> {{ \Carbon\Carbon::parse($det->time)->format('h:i A')}}</label>
                </div>
            </div>
            <div id="passengers_name">
                <div class="form-group">
                    <label class="control-label col-md-4">Passenger Name :<span class="required"> </span></label>
                    <div class="col-md-7">
                        <label class="control-label"> {{$det->passengers}}</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Package<span class="required">* </span></label>
                <div class="col-md-7">
                    <select name="package_id" required class="form-control select2" data-placeholder="Select...">
                        <option value="">Select package</option>
                        @foreach ($package_list as $item)
                        <option value="{{$item->id}}">{{$item->package_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Extra KM<span class="required"></span></label>
                <div class="col-md-7">
                    <input type="number" step="0.01"  name="extra_km" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Extra hour<span class="required"></span></label>
                <div class="col-md-7">
                    <input type="number" step="0.01"  name="extra_hour" value="" class="form-control">
                </div>
            </div>
          
            <div class="form-group">
                <label class="control-label col-md-4">Toll / Parking<span class="required"></span></label>
                <div class="col-md-7">
                    <input type="number" step="0.01"  name="toll_parking" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Vendor amount<span class="required"></span></label>
                <div class="col-md-7">
                    <input type="number" step="0.01"  name="vendor_amount" value="" class="form-control">
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