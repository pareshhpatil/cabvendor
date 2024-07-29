@extends('layouts.admin')

@section('content')
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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Trip #</th>
                            <th>Company</th>
                            <th>Vehicle Type </th>
                            <th>Date Time </th>
                            <th>Pickup location </th>
                            <th>Drop location </th>
                            <th>Passengers </th>
                            <th>Status </th>
                            <th style="width: 70px;">Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $item)
                        <tr class="odd gradeX">
                            <td>{{$item->req_id}}</td>
                            <td>{{$item->company_name}}</td>
                            <td>{{$item->vehicle_type}}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y')}} {{ \Carbon\Carbon::parse($item->time)->format('h:i A')}}</td>
                            <td>{{$item->pickup_location}}</td>
                            <td>{{$item->drop_location}}</td>
                            <td>{{$item->passengers}}</td>
                            <td>
                                {{$item->status}}

                            </td>
                            <td>
                                @if($item->status=='Assigned')
                                <a href="/trip/complete/{{$item->link}}" target="_BLANK" class="btn btn-xs btn-primary">Complete</a>
                                <a href="/trip/update/{{$item->link}}" target="_BLANK" class="btn btn-xs btn-warning">Update</a>
                                <!-- <a href="https://app.siddhivinayaktravelshouse.in/driver/app-ride/{{$item->ride_id}}" target="_BLANK" class="btn btn-xs btn-primary">Status</a> -->
                                <a href="#" onclick="document.getElementById('deleteanchor').href = '/admin/trip/delete/{{$item->link}}'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-danger"><i class="fa fa-remove"></i></a>
                                @endif
                                @if($item->status=='Completed')
                                <a href="/trip/complete/detail/{{$item->link}}" target="_BLANK" class="btn btn-xs btn-primary">Detail</a>
                                <a href="/trip/update/{{$item->link}}" target="_BLANK" class="btn btn-xs btn-warning">Update</a>
                                @endif
                                @if($login_type!='client' && $item->status=='Requested')
                                <a href="/trip/schedule/{{$item->req_link}}" target="_BLANK" class="btn btn-xs btn-warning">Schedule</a>
                                <a href="#" onclick="document.getElementById('deleteanchor').href = '/admin/trip_request/delete/{{$item->req_link}}'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-danger"><i class="fa fa-remove"></i></a>

                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="modal modal-danger fade" id="modal-danger">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete Record</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you would not like to use this Record in the future?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <a id="deleteanchor" href="" class="btn btn-outline">Delete</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection