@extends('layouts.admin')

@section('content')
<div class="row" id="insert">
    <form action="/trip/packagesave" method="post" id="customerForm" class="form-horizontal">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">Company<span class="required"> </span></label>
                <div class="col-md-7">
                    <select name="company_id" class="form-control select2">
                        @foreach ($company_list as $item)
                        <option value="{{$item->company_id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Vehicle type<span class="required"> </span></label>
                <div class="col-md-7">
                    <select name="vehicle_type" class="form-control select2">
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Crysta">Crysta</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Fortuner">Fortuner</option>
                        <option value="Ambulance">Ambulance</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Package name<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="text" name="package_name" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Package amount<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="number" step="1" name="package_amount" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Extra KM<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="number" step="1" name="extra_km" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Extra Hour<span class="required"> </span></label>
                <div class="col-md-7">
                    <input type="number" step="1" name="extra_hour" value="" class="form-control">
                </div>
            </div>




            <div class="form-group">
                <div class="col-md-4"></div>
                <div class="col-md-7">
                    <h4 id="status"></h4>
                    <p id="loaded_n_total"></p>
                    <button id="savebutton" type="submit" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-Close">Clear</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection