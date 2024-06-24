@extends('layouts.admin')

@section('content')
<div class="row" id="insert">
    <form action="/trip/save" method="post" id="customerForm" class="form-horizontal">
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
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Date <span class="required">*</span></label>
                <div class="col-md-7">
                    <input type="text" name="date" readonly="" value="{{$current_date}}" autocomplete="off" class="form-control form-control-inline date-picker" data-date-format="dd M yyyy">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Pickup Time <span class="required">*</span></label>
                <div class="col-md-7">
                    <div class="bootstrap-timepicker">
                        <div class="">
                            <div class="input-group">
                                <input type="text" required="" readonly="" value="08:00 AM" name="pickup_time" class="form-control timepicker">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Total Passengers<span class="required"> </span></label>
                <div class="col-md-7">
                    <select onchange="addPassengers(this.value);" name="total_passengers" class="form-control">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>

            <div id="passengers_name">
                <div class="form-group">
                    <label class="control-label col-md-4">Passenger Name<span class="required"> </span></label>
                    <div class="col-md-7">
                        <input type="text" name="passengers_name[]" value="" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Pickup Location<span class="required">*</span></label>
                <div class="col-md-7">
                    <input type="text" required="" name="pickup_location" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Drop Location<span class="required"> *</span></label>
                <div class="col-md-7">
                    <input type="text" required="" name="drop_location" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Note<span class="required"> </span></label>
                <div class="col-md-7">
                    <textarea name="note" value="" class="form-control"></textarea>
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