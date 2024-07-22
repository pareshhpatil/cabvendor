<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{$title}}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <style>
            #load{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                background:url("{{ asset('dist/img/loader.gif') }}") no-repeat center center rgba(0,0,0,0.25)
            }
        </style>
        <script>
            document.onreadystatechange = function () {
                var state = document.readyState
                if (state == 'complete') {
                    document.getElementById('interactive');
                    document.getElementById('load').style.visibility = "hidden";
                }
            }
        </script>
        
        
     
    </head>
    <body class="hold-transition skin-blue sidebar-mini fixed">
        
        <div id="load"></div>
        <div class="wrapper">
            <header class="main-header">
                <a href="/{{$login_type}}/dashboard" class="logo" style="background-color: #ffffff;">
                    <span class="logo-mini"><img style="max-height:45px;" src="{{ asset('dist/img/'.$company_logo) }}"></span>
                    <span class="logo-lg"><img style="max-height:45px;" src="{{ asset('dist/img/'.$company_logo) }}"></span>
                </a>
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="navbar-custom-menu pull-left">
                        <h4 style="color: #ffffff;vertical-align: middle;margin-top: 15px;">{{$company_name}}</h4>
                    </div>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <?php $name = Session::get('user_name'); ?>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{ asset('dist/img/avatar.png') }}" class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{$name}}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header" style="height: 140px;">
                                        <img src="{{ asset('dist/img/avatar.png') }}" class="img-circle" alt="User Image">
                                        <p>
                                            {{$name}}
                                        </p>
                                    </li>
                                    <li class="user-footer" style="background: #222d32;">
                                        <div class="pull-left">
                                            <a href="/admin/profile" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="/logout" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu" data-widget="tree">
                        <li><a href="/{{$login_type}}/dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

                        @if($login_type!='client' && $login_type!='vendor')

                        <li><a href="/admin/logsheet"><i class="fa fa-rupee"></i> <span>Bills</span></a></li>

                        <li class="treeview"><a href="/admin/logsheet"><i class="fa fa-file-text"></i> <span>Log sheet</span><span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/logsheet/month">
                                        <i class="fa fa-circle-o"></i> Create</a>
                                </li>
                                <li>
                                    <a href="/admin/logsheet/getlogsheet">
                                        <i class="fa fa-circle-o"></i> List</a>
                                </li>
                            </ul>
                        </li>
                        @if(Session::get('admin_id')==1 || Session::get('admin_id')==10)
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-cogs"></i> <span>MIS</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/mis/createmis">
                                        <i class="fa fa-circle-o"></i> Create</a>
                                </li>
                                <li>
                                    <a href="/admin/mis/listmis">
                                        <i class="fa fa-circle-o"></i> List</a>
                                </li>
                                <li>
                                    <a href="/admin/mis/listmiscompany">
                                        <i class="fa fa-circle-o"></i> Company MIS</a>
                                </li>



                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-cogs"></i> <span>Casual</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/trip/add">
                                        <i class="fa fa-circle-o"></i> Create</a>
                                </li>
                                <li>
                                    <a href="/trip/list/all">
                                        <i class="fa fa-circle-o"></i> List</a>
                                </li>
                                <li>
                                    <a href="/trip/package/list">
                                        <i class="fa fa-circle-o"></i> Package</a>
                                </li>
                                <li>
                                    <a href="/admin/logsheet/generatebill/casual">
                                        <i class="fa fa-circle-o"></i> Create Invoice</a>
                                </li>
                                
                            </ul>
                        </li>
                        @endif
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-cogs"></i> <span>Masters</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                           
                                <li>
                                    <a href="/admin/company/list">
                                        <i class="fa fa-circle-o"></i> Company</a>
                                </li>
                                <li>
                                    <a href="/admin/vehicle/list">
                                        <i class="fa fa-circle-o"></i> Vehicle</a>
                                </li>
                                @if(Session::get('admin_id')==1 || Session::get('admin_id')==10)
                                <li>
                                    <a href="/admin/employee/list">
                                        <i class="fa fa-circle-o"></i> Employee</a>
                                </li>
                                <li>
                                    <a href="/admin/vendor/list">
                                        <i class="fa fa-circle-o"></i> Vendor</a>
                                </li>
                                
                                <li>
                                    <a href="/admin/zone/list">
                                        <i class="fa fa-circle-o"></i> Zone</a>
                                </li>
                                <li>
                                    <a href="/admin/location/list">
                                        <i class="fa fa-circle-o"></i> Location</a>
                                </li>
                                <li>
                                    <a href="/admin/paymentsource/list">
                                        <i class="fa fa-circle-o"></i> Payment Source</a>
                                </li>
                                @endif
                                


                            </ul>
                        </li>
                        @if(Session::get('admin_id')==1 || Session::get('admin_id')==10)
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-rupee"></i> <span>Bills & Payments</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/bill/new">
                                        <i class="fa fa-circle-o"></i> Create</a>
                                </li>
                                <li>
                                    <a href="/admin/bill">
                                        <i class="fa fa-circle-o"></i> Pending Bills</a>
                                </li>
                                <li>
                                    <a href="/admin/transaction">
                                        <i class="fa fa-circle-o"></i> Payment Transaction</a>
                                </li>
                                <li>
                                    <a href="/admin/request">
                                        <i class="fa fa-circle-o"></i> Payment Request</a>
                                </li>
                                <li>
                                    <a href="/admin/paymentsource/credit">
                                        <i class="fa fa-circle-o"></i> Credit</a>
                                </li>
                                <li>
                                    <a href="/admin/paymentsource/creditlist">
                                        <i class="fa fa-circle-o"></i> Credit list</a>
                                </li>
                                <li>
                                    <a href="/admin/bill/subscription">
                                        <i class="fa fa-circle-o"></i> Subscription</a>
                                </li>
                                <li>
                                    <a href="/admin/income/list">
                                        <i class="fa fa-circle-o"></i> Income</a>
                                </li>
                                <li>
                                    <a href="/admin/expense/pending">
                                        <i class="fa fa-circle-o"></i> Expense Adjust</a>
                                </li>

                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-users"></i> <span>Employee</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/employee/absent">
                                        <i class="fa fa-circle-o"></i> Absent</a>
                                </li>
                                <!--<li>
                                    <a href="/admin/employee/advance">
                                        <i class="fa fa-circle-o"></i> Advance</a>
                                </li>-->
                                <li>
                                    <a href="/admin/employee/overtime">
                                        <i class="fa fa-circle-o"></i> Over Time</a>
                                </li>
                                
                                <!--
                                <li>
                                    <a href="/admin/employee/salary">
                                        <i class="fa fa-circle-o"></i> Salary</a>
                                </li>-->

                            </ul>
                        </li>

                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-car"></i> <span>Vehicle</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/vehicle/fuel">
                                        <i class="fa fa-circle-o"></i> Fuel</a>
                                </li>
                                <li>
                                    <a href="/admin/vehicle/replacecab">
                                        <i class="fa fa-circle-o"></i> Replace Cab</a>
                                </li>

                            </ul>
                        </li>

                        
                        @endif
                        @endif

                        @if($login_type=='vendor')
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-cogs"></i> <span>Masters</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/admin/employee/list">
                                        <i class="fa fa-circle-o"></i> Employee</a>
                                </li>
                                <li>
                                    <a href="/admin/vehicle/list">
                                        <i class="fa fa-circle-o"></i> Vehicle</a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-car"></i> <span>Trips</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="/trip/add">
                                        <i class="fa fa-circle-o"></i> Add Trip</a>
                                </li>
                                <li>
                                    <a href="/trip/list/upcoming">
                                        <i class="fa fa-circle-o"></i> Upcoming Trips</a>
                                </li>
                                <li>
                                    <a href="/trip/list/past">
                                        <i class="fa fa-circle-o"></i> Past Trips</a>
                                </li>

                            </ul>
                        </li>
                        @endif
                    </ul>
                </section>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    @isset($title)
                    <h1>
                        {{$title}}
                        @isset($titledeccription)
                        <small>{{$titledeccription}}</small>
                        @endisset
                        @isset($addnewlink)
                        <a href="{{$addnewlink}}" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus"></i> &nbsp; Add new</a>
                        @endisset
                        @if($title=='Invoices')
                        @if($user_type==1)
                        <button  onclick="logsheetDiv();" style="margin-left: 10px;" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> &nbsp; Logsheet Entry</button>
                        <a href="/admin/logsheet/generatebill" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus"></i> &nbsp; Generate Bill</a>
                        <form id="frm" style="display: contents;" action="" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <select onchange="document.getElementById('frm').submit();" name="type" required class="form-control pull-right" style="width: 150px; margin-right: 10px;" data-placeholder="Select...">
                                <option @if($type==1) selected @endif value="1">Unpaid Bill</option>
                                <option @if($type==2) selected @endif value="2">Unpaid GST</option>
                                <option @if($type==3) selected @endif value="3">All Bills</option>
                            </select>
                            <select name="company_id" onchange="document.getElementById('frm').submit();" class="form-control pull-right" style="width: 150px; margin-right: 10px;" data-placeholder="Select...">
                                        <option value="0">Select comapny</option>
                                        @foreach ($company_list as $item)
                                        <option @if($company_id==$item->company_id) selected @endif value="{{$item->company_id}}">{{$item->name}}</option>
                                        @endforeach
                            </select>
                        </form>
                        @else
                        <a href="/admin/logsheet/getlogsheet" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus"></i> &nbsp; Get Logsheet</a>
                        @endif

                        @endif
                        @isset($addnew_button)
                        <button  onclick="logsheetDiv();" style="margin-left: 10px;" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> &nbsp; Add new</button>
                        @endisset
                        @if($title=='Bill Print')
                        <a  href="/admin/logsheet/downloadbill/{{$link}}" style="margin-left: 10px;" class="btn btn-success btn-sm pull-right"><i class="fa fa-download"></i> &nbsp; Download</a>
                        @endif
                        @if($title=='Logsheet Print')
                        <a  href="/admin/logsheet/downloadlogsheet/{{$link}}" style="margin-left: 10px;" class="btn btn-success btn-sm pull-right"><i class="fa fa-download"></i> &nbsp; Download</a>
                        @endif
                    </h1>
                    @endisset

                </section>
                <section class="content">
                    @yield('content')
					
					
                </section>
				
            </div>
            <footer class="main-footer hidden-print">
                <small>Copyright &copy; 2023 Ride Track All rights reserved.</small>
            </footer>
            <div class="control-sidebar-bg"></div>
        </div>
        <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
        <script src="{{ asset('dist/js/demo.js') }}"></script>
        <script src="{{ asset('dist/js/custom.js?version=34') }}"></script>
        <script>
                            $(function () {
                                $('.select2').select2({
								  tags: true
								})
                                //Date picker
                                $('.date-picker').datepicker({
                                    format: 'dd-mm-yyyy',
                                    todayHighlight: true,
                                    autoclose: true
                                })

                                $('#example1').DataTable(
                                        {
                                            "order": [[0, "desc"]]
                                        })
                                $('.timepicker').timepicker({
                                    showInputs: false
                                })

                                $('.month-picker').datepicker({
                                    autoclose: true,
                                    minViewMode: 1,
                                    format: 'M-yyyy'
                                });
                            })

                            @isset($logsheet_detail)
                                    calculateLogsheet();
                                    @endisset
        </script>
    </body>
</html>