<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DashboardController
 *
 * @author Paresh
 */

namespace App\Http\Controllers;

use App\Model\Mis;
use App\Model\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use PDF;
use Excel;
use App\Exports\ExcelExport;

class MisController extends Controller
{

    private $mis_model;
    private $master_model;

    function __construct()
    {
        parent::__construct();
        $this->mis_model = new Mis();
        $this->master_model = new Master();
    }

    public function create()
    {
        $this->validateSession(array(1, 2));
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $mis_employee = $this->master_model->getMaster('mis_employee', 0);
        $location_km = $this->master_model->getMaster('location_km', 0);

        $data['title'] = 'MIS';
        $data['current_date'] = date('d-m-Y');
        $data['vehicle_list'] = $vehicle_list;
        $data['mis_employee'] = $mis_employee;
        $data['location_km'] = $location_km;
        $data['user_type'] = $this->user_type;
        return view('mis.create', $data);
    }

    public function createcompanymis($link = null)
    {
        $this->validateSession(array(1, 2));
        $det = [];
        $data['current_date'] = date('d-m-Y');
        $data['pickup_drop'] = 'Drop';
        $data['car_no'] = '0';
        $data['cab_zone'] = '';
        $data['escort'] = '0';
        if ($link != null) {
            $id = $this->encrypt->decode($link);
            $det = $this->master_model->getMasterDetail('company_mis', 'id', $id);
            $_POST['company_id'] = $det->company_id;
            $data['current_date'] = date('d-m-Y', strtotime($det->date));
            $data['pickup_time'] = date('h:i A', strtotime($det->pickup_time));
            $data['drop_time'] = date('h:i A', strtotime($det->drop_time));
            $data['pickup_drop'] = $det->pickup_drop;
            $data['car_no'] = $det->car_no;
            $data['cab_zone'] = $det->zone;
            $data['escort'] = $det->escort;
            $data['det'] = $det;
        }
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $mis_employee = $this->master_model->getMaster('mis_employee', 0);
        $location = $this->master_model->getMaster('location', 0);
        $data['company_list'] = $this->master_model->getMaster('company', $this->admin_id);


        $data['company_id'] = 0;
        if (isset($_POST['company_id'])) {
            $data['company_id'] = $_POST['company_id'];
            $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
            $data['zone'] = $this->master_model->getMaster('zone', $data['company_id'], 'company_id');
            $data['location'] = $this->master_model->getMaster('location', $data['company_id'], 'company_id');
            $data['company_type'] = $company->type;
        }

        $data['title'] = 'MIS';
        $data['vehicle_list'] = $vehicle_list;
        $data['mis_employee'] = $mis_employee;
        $data['user_type'] = $this->user_type;
        return view('mis.createcompanymis', $data);
    }

    public function confirmmis(Request $request)
    {
        $this->validateSession(array(1, 2));
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $request->vehicle_id);
        $km_det = $this->mis_model->getMaxKm($_POST['location']);
        $month = $date = date('m', strtotime($_POST['date']));
        $year = $date = date('Y', strtotime($_POST['date']));
        $startkm_det = $this->mis_model->getStartKm($month, $year, $request->vehicle_id);
        if ($startkm_det->km > 0) {
            $start_km = $startkm_det->km;
        } else {
            $start_km = 0;
        }
        $max_km = $km_det->km;
        $end_km = $max_km + $start_km;
        $toll = ($_POST['toll_amount'] > 0) ? $_POST['toll_amount'] : 0;
        echo '<table class="table table-bordered table-condensed" style="margin: 0px 0 0px 0 !important;">';
        echo ' <tr><td><b>Date:</b></td><td>' . $_POST['date'] . '</td></tr>';
        echo ' <tr><td><b>Vehicle number:</b></td><td>' . $vehicle->name . '</td></tr>';
        echo ' <tr><td><b>Logsheet No:</b></td><td>' . $_POST['logsheet_no'] . '</td></tr>';
        echo ' <tr><td><b>Employee:</b></td><td>' . implode(',', $request->employee) . '</td></tr>';
        echo ' <tr><td><b>Location:</b></td><td>' . implode(',', $request->location) . '</td></tr>';
        echo ' <tr><td><b>Start KM:</b></td><td>' . $start_km . '</td></tr>';
        echo ' <tr><td><b>End KM:</b></td><td>' . $end_km . '</td></tr>';
        echo ' <tr><td><b>Total KM:</b></td><td>' . $max_km . '</td></tr>';
        echo ' <tr><td><b>Shift Time:</b></td><td>' . date('h:i A', strtotime($_POST['shift_time'])) . '</td></tr>';
        echo ' <tr><td><b>Toll:</b></td><td>' . $toll . '</td></tr>';
        echo ' <tr><td><b>Remark:</b></td><td>' . $_POST['remark'] . '</td></tr>';
        echo '</tbody></table>';
    }

    public function confirmcompanymis(Request $request)
    {
        $this->validateSession(array(1, 2));
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $request->vehicle_id);
        $km_det = $this->mis_model->getMaxKm($_POST['location']);
        $month = $date = date('m', strtotime($_POST['date']));
        $year = $date = date('Y', strtotime($_POST['date']));
        $startkm_det = $this->mis_model->getStartKm($month, $year, $request->vehicle_id);
        if ($startkm_det->km > 0) {
            $start_km = $startkm_det->km;
        } else {
            $start_km = 0;
        }
        $max_km = $km_det->km;
        $end_km = $max_km + $start_km;
        $toll = ($_POST['toll_amount'] > 0) ? $_POST['toll_amount'] : 0;
        echo '<table class="table table-bordered table-condensed" style="margin: 0px 0 0px 0 !important;">';
        echo ' <tr><td><b>Date:</b></td><td>' . $_POST['date'] . '</td></tr>';
        echo ' <tr><td><b>Vehicle number:</b></td><td>' . $vehicle->name . '</td></tr>';
        echo ' <tr><td><b>Logsheet No:</b></td><td>' . $_POST['logsheet_no'] . '</td></tr>';
        echo ' <tr><td><b>Employee:</b></td><td>' . implode(',', $request->employee) . '</td></tr>';
        echo ' <tr><td><b>Location:</b></td><td>' . implode(',', $request->location) . '</td></tr>';
        echo ' <tr><td><b>Start KM:</b></td><td>' . $start_km . '</td></tr>';
        echo ' <tr><td><b>End KM:</b></td><td>' . $end_km . '</td></tr>';
        echo ' <tr><td><b>Total KM:</b></td><td>' . $max_km . '</td></tr>';
        echo ' <tr><td><b>Shift Time:</b></td><td>' . date('h:i A', strtotime($_POST['shift_time'])) . '</td></tr>';
        echo ' <tr><td><b>Toll:</b></td><td>' . $toll . '</td></tr>';
        echo ' <tr><td><b>Remark:</b></td><td>' . $_POST['remark'] . '</td></tr>';
        echo '</tbody></table>';
    }

    public function savemis(Request $request)
    {
        $this->validateSession(array(1, 2));
        $km_det = $this->mis_model->getMaxKm($_POST['location']);
        $month = $date = date('m', strtotime($_POST['date']));
        $year = $date = date('Y', strtotime($_POST['date']));
        $startkm_det = $this->mis_model->getStartKm($month, $year, $request->vehicle_id);
        if ($startkm_det->km > 0) {
            $start_km = $startkm_det->km;
        } else {
            $start_km = 0;
        }
        $max_km = $km_det->km;
        $end_km = $max_km + $start_km;
        $toll = ($_POST['toll_amount'] > 0) ? $_POST['toll_amount'] : 0;
        $start_km = ($start_km > 0) ? $start_km : 0;
        $shift_time = date('H:i:s', strtotime($request->shift_time));
        $date = date('Y-m-d', strtotime($request->date));
        $result = $this->mis_model->saveLogsheetbill($request->vehicle_id, $date, implode(',', $request->employee), implode(',', $request->location), $start_km, $end_km, $max_km, $request->logsheet_no, $shift_time, $request->pickup_drop, $_POST['remark'], $toll, $this->user_id);
        echo 'Logsheet has been saved successfully';
    }

    public function savecompanymis(Request $request)
    {
        $this->validateSession(array(1, 2));
        $pickup_time = date('H:i:s', strtotime($request->pickup_time));
        $drop_time = date('H:i:s', strtotime($request->drop_time));
        $date = date('Y-m-d', strtotime($request->date));
        $det = $this->master_model->getMasterDetail('zone', 'zone_id', $request->zone_id);
        $result = $this->mis_model->saveCompanyMIS($request, $det, $date, $pickup_time, $drop_time, $this->user_id);
        echo 'MIS has been saved successfully';
    }

    public function saveemployee($name)
    {
        $this->validateSession(array(1, 2));
        $exist_detail = $this->master_model->getMasterDetail('mis_employee', 'employee_name', $name);
        if (empty($exist_detail)) {
            $this->mis_model->saveMISEmployee($name, $this->user_id);
            echo $name;
        } else {
            echo 'exist';
        }
    }

    public function savemislocation($name, $km)
    {
        $this->validateSession(array(1, 2));
        $exist_detail = $this->master_model->getMasterDetail('location_km', 'location', $name);
        if (empty($exist_detail)) {
            $this->mis_model->saveMISLocation($name, $km, $this->user_id);
            echo $name;
        } else {
            echo 'exist';
        }
    }

    public function mislist()
    {
        $this->validateSession(array(1, 2));
        $vehicle_list = $this->mis_model->getMisVehicle($this->admin_id);
        $vehicle_id = 0;
        $from_date_ = date('d M Y');
        $to_date_ = date('d M Y');
        if (empty($_POST)) {
            $invoice_list = $this->mis_model->getMISList($this->admin_id);
        } else {
            $from_date_ = $_POST['from_date'];
            $to_date_ = $_POST['to_date'];
            $from_date = date('Y-m-d', strtotime($_POST['from_date']));
            $to_date = date('Y-m-d', strtotime($_POST['to_date']));
            $vehicle_id = $_POST['vehicle_id'];
            $invoice_list = $this->mis_model->getMISList($this->admin_id, $from_date, $to_date, $vehicle_id);
        }
        if (isset($_POST['export'])) {
            $array = json_decode(json_encode($invoice_list), True);
            foreach ($array as $key => $row) {
                $export[$key]['DATE'] = date('d/m/Y', strtotime($row['date']));
                $export[$key]['COMPLETE CAB NO'] = $row['vehicle_name'];
                $export[$key]['LOG SHEET NUMBER'] = $row['logsheet_no'];
                $export[$key]['NAME'] = $row['employee'];
                $export[$key]['LOCATION'] = $row['location'];
                $export[$key]['Details'] = $row['pickdrop'];
                $export[$key]['Start KMS'] = $row['start_km'];
                $export[$key]['End KMS'] = $row['end_km'];
                $export[$key]['Total KMS'] = $row['total_km'];
                $export[$key]['Shift Time'] = date('h:i', strtotime($row['shift_time']));
                $export[$key]['Toll'] = $row['toll'];
            }
			return Excel::download(new ExcelExport($export), 'MIS.xlsx');
           return $this->exportExcel($export, 'MIS');
        }

        $int = 0;
        foreach ($invoice_list as $item) {
            $link = $this->encrypt->encode($item->id);
            $invoice_list[$int]->link = $link;
            $int++;
        }

        $data['title'] = 'MIS List';
        $data['from_date'] = $from_date_;
        $data['to_date'] = $to_date_;
        $data['list'] = $invoice_list;
        $data['user_type'] = $this->user_type;
        $data['vehicle_list'] = $vehicle_list;
        $data['vehicle_id'] = $vehicle_id;
        return view('mis.list', $data);
    }

    public function listmiscompany()
    {
        $this->validateSession(array(1, 2));
        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $company_id = 0;
        $from_date_ = date('d M Y');
        $to_date_ = date('d M Y');
        if (empty($_POST)) {
            $invoice_list = $this->mis_model->getMISListCompany($this->admin_id);
        } else {
            $from_date_ = $_POST['from_date'];
            $to_date_ = $_POST['to_date'];
            $from_date = date('Y-m-d', strtotime($_POST['from_date']));
            $to_date = date('Y-m-d', strtotime($_POST['to_date']));
            $company_id = $_POST['company_id'];
            $invoice_list = $this->mis_model->getMISListCompany($this->admin_id,$from_date, $to_date, $company_id);
        }
		$export=[];
        if (isset($_POST['export'])) {
            $array = json_decode(json_encode($invoice_list), True);
            foreach ($array as $key => $row) {
                $export[$key]['Date'] = date('d/m/Y', strtotime($row['date']));
                $export[$key]['Package'] = $row['zone'];
                if ($row['escort'] == 1) {
                    $export[$key]['Company_slab'] = $row['company_slab'] . ' with Escort';
                } else {
                    $export[$key]['Company_slab'] = $row['company_slab'];
                }

                $export[$key]['Driver_slab'] = $row['driver_slab'];
                $export[$key]['Escort'] = ($row['escort'] == 1) ? 'Yes' : 'No';
                $export[$key]['Logsheet number'] = $row['logsheet_no'];
                $export[$key]['Company name'] = $row['company_name'];
                $export[$key]['Car Type'] = $row['car_type'];
                $export[$key]['Car Number'] = $row['car_no'];
                $export[$key]['Pickup/Drop'] = $row['pickup_drop'];
                $export[$key]['Pickup location'] = $row['pickup_location'];
                $export[$key]['Drop location'] = $row['drop_location'];
                $export[$key]['Pickup time'] = date('h:i', strtotime($row['pickup_time']));
                $export[$key]['Drop time'] = date('h:i', strtotime($row['drop_time']));
                $export[$key]['Toll'] = $row['toll'];
				if($this->user_id==36 || $this->user_id==8)
				{
					$export[$key]['Vendor Amount'] = $row['vendor_amount'];
				}
                if ($this->user_id == 8) {
                    $export[$key]['SVK Amount'] = $row['svk_amount'];
                    $export[$key]['Admin Amount'] = $row['admin_amount'];
                    $export[$key]['Company Amount'] = $row['company_amount'];
                }

                $export[$key]['Employee name'] = $row['employee_name'];
                $export[$key]['Employee count'] = $row['user_count'];
                $export[$key]['Remark'] = $row['remark'];
            }
			if(!empty($export))
			{
			return Excel::download(new ExcelExport($export), 'MIS.xlsx');
            $this->exportExcel($export, 'MIS');
			}
        }

        $int = 0;
        foreach ($invoice_list as $item) {
            $link = $this->encrypt->encode($item->id);
            $invoice_list[$int]->link = $link;
            $int++;
        }

        $data['title'] = 'MIS List';
        $data['from_date'] = $from_date_;
        $data['to_date'] = $to_date_;
        $data['list'] = $invoice_list;
        $data['user_type'] = $this->user_type;
        $data['company_list'] = $company_list;
        $data['company_id'] = $company_id;
        $data['addnewlink'] = '/admin/mis/createcompanymis';
        return view('mis.company_list', $data);
    }

    public function deletemis($link)
    {
        $this->validateSession(array(1, 2));
        $id = $this->encrypt->decode($link);
        $this->master_model->deleteReccord('mis', 'id', $id, $this->user_id);
        $this->setSuccess('MIS has been deleted successfully');
        header('Location: /admin/mis/listmis');
        exit;
    }
    public function deletecompanymis($link)
    {
        $this->validateSession(array(1, 2));
        $id = $this->encrypt->decode($link);
        $this->master_model->deleteReccord('company_mis', 'id', $id, $this->user_id);
        $this->setSuccess('MIS has been deleted successfully');
        header('Location: /admin/mis/listmiscompany');
        exit;
    }

    public function exportExcel($column, $name)
    {
        try {
			
			
			
			
			return Excel::download(new ExcelExport($column), $name.'.xlsx');
			
            Excel::create($name, function ($excel) use ($column) {
                $excel->sheet('Sheet 1', function ($sheet) use ($column) {
                    $sheet->fromArray($column);
                    if (!empty($column)) {
                        $sheet->row(1, function ($row) {
                            // call cell manipulation methods
                            $row->setBackground('#2874A6');
                            $row->setFontColor('#ffffff');
                        });
                    }
                    $sheet->freezeFirstRow();
                    $sheet->setAutoSize(true);
                });
            })->export('xls');
        } catch (Exception $e) {
        }
    }

    function updatekm($from_date, $to_date)
    {
        $this->mis_model->refreshMISKM($from_date, $to_date);
        $mislist = $this->mis_model->getALLMISLIST($from_date, $to_date);
        foreach ($mislist as $mis) {
            $km_det = $this->mis_model->getMaxKm(explode(',', $mis->location));
            $month = $date = date('m', strtotime($mis->date));
            $year = $date = date('Y', strtotime($mis->date));
            $startkm_det = $this->mis_model->getStartKm($month, $year, $mis->vehicle_id);
            if ($startkm_det->km > 0) {
                $start_km = $startkm_det->km;
            } else {
                $start_km = 0;
            }
            $max_km = $km_det->km;
            $end_km = $max_km + $start_km;
            $start_km = ($start_km > 0) ? $start_km : 0;
            $this->mis_model->updateMISKM($mis->id, $start_km, $end_km, $max_km);
        }
    }
}
