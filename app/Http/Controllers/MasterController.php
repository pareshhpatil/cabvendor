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

use App\Model\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class MasterController extends Controller
{

    private $master_model;

    function __construct()
    {
        parent::__construct();
        $this->validateSession(array(1, 4, 2));
        $this->master_model = new Master();
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function masterlist($master)
    {
        if ($master == 'zone') {
            $list = $this->master_model->getZoneList($this->admin_id);
        } else {
            if ($this->user_type == 4) {
                $list = $this->master_model->getMaster($master, $this->user_id, 'created_by');
            } else {
                $list = $this->master_model->getMaster($master, $this->admin_id);
            }
        }

        $int = 0;
        foreach ($list as $item) {
            $link = $this->encrypt->encode($item->{$master . '_id'});
            $list[$int]->link = $link;
            $int++;
        }
        $data['title'] = 'List ' . ucfirst($master);
        $data['list'] = $list;
        $data['addnewlink'] = '/admin/' . $master . '/create';
        return view('master.' . $master . '.list', $data);
    }

    public function masterview($master, $link)
    {
        $id = $this->encrypt->decode($link);
        $detail = $this->master_model->getMasterDetail($master, $master . '_id', $id);
        if ($master == 'employee') {
            $transaction_list = $this->master_model->getMaster('transaction', $id, 'employee_id');
            $data['transaction_list'] = $transaction_list;
            $request_list = $this->master_model->getMaster('request', $id, 'employee_id');
            $data['request_list'] = $request_list;
        }
        $data['title'] = ucfirst($master) . ' detail';
        $data['det'] = $detail;

        // $mpdf = new \Mpdf\Mpdf([
        //     'mode' => '',
        //     'format' => 'A4',
        //     'default_font_size' => 0,
        //     'default_font' => '',
        //     'margin_left' => 4,
        //     'margin_right' => 4,
        //      'margin_bottom' => 4,
        //     'margin_header' => 9,
        //      'margin_footer' => 9,
        //  ]);
        //  $mpdf->WriteHTML(\View::make('master.' . $master . '.view')->with($data)->render());
        //  $mpdf->Output();
        // $mpdf->Output('quotation.pdf', 'D');
        // die();

        return view('master.' . $master . '.view', $data);
    }

    public function mastercreate($master)
    {
        $data['title'] = 'Create ' . ucfirst($master);
        if ($master == 'subscription') {
            $employee_list = $this->master_model->getMaster('employee', $this->admin_id);
            $data['employee_list'] = $employee_list;
        }
        if ($master == 'location' || $master == 'zone') {
            $employee_list = $this->master_model->getMaster('company', $this->admin_id);
            $data['company_list'] = $employee_list;
        }

        return view('master.' . $master . '.create', $data);
    }

    public function masterdelete($master, $link)
    {
        $id = $this->encrypt->decode($link);
        if ($master == 'logsheet_invoice') {
            $this->master_model->deleteReccord($master, 'invoice_id', $id, $this->user_id);
        } elseif ($master == 'company_casual_package') {
            $this->master_model->deleteReccord($master, 'id', $id, $this->user_id);
        } elseif ($master == 'trip_request') {
            $this->master_model->deleteReccord($master, 'req_id', $id, $this->user_id);
        } else {
            $this->master_model->deleteReccord($master, $master . '_id', $id, $this->user_id);
        }
        $this->setSuccess(ucfirst($master) . ' has been deleted successfully');
        if ($master == 'salary') {
            header('Location: /admin/employee/salary');
        } else if ($master == 'transaction') {
            header('Location: /admin/bill/new');
        } else if ($master == 'subscription') {
            header('Location: /admin/bill/subscription');
        } else if ($master == 'logsheet_invoice') {
            header('Location: /admin/logsheet');
        } else if ($master == 'company_casual_package') {
            header('Location: /trip/package/list');
        } else if ($master == 'fuel') {
            header('Location: /admin/vehicle/fuel');
        } else if ($master == 'trip' || $master == 'trip_request') {
            header('Location: /trip/list/all');
        } else {
            header('Location: /admin/' . $master . '/list');
        }
        exit;
    }

    public function masterupdate($master, $link)
    {
        $id = $this->encrypt->decode($link);
        $detail = $this->master_model->getMasterDetail($master, $master . '_id', $id);
        if ($master == 'zone') {
            $employee_list = $this->master_model->getMaster('company', $this->admin_id);
            $data['company_list'] = $employee_list;
        }
        $data['title'] = ucfirst($master) . ' update';
        $data['det'] = $detail;
        return view('master.' . $master . '.update', $data);
    }

    #Employee

    public function employeesave(Request $request)
    {
        $join_date = date('Y-m-d', strtotime($request->join_date));
        $employee_id = $this->master_model->saveEmployee($request, $request->employee_code, $request->name, $request->email, $request->mobile, $request->pan, $request->address, $request->adharcard, $request->license, $request->uploaded_file, $request->payment, $join_date, $request->payment_day, $request->account_no, $request->holder_name, $request->ifsc_code, $request->bank_name, $request->account_type, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($employee_id);
        $data['title'] = 'Success Employee';
        $data['success'] = 'Employee has been saved successfully';
        $data['link'] = $link;
        return view('master.employee.saved', $data);
    }

    public function zonesave(Request $request)
    {
        if ($request->zone == '') {
            $request->zone = $request->from . '-' . $request->to;
        }
        $this->master_model->saveZone($request, $this->admin_id, $this->user_id);
        return redirect('/admin/zone/list');
    }

    public function zoneupdatesave(Request $request)
    {
        $this->master_model->updateZone($request, $this->admin_id, $this->user_id);
        return redirect('/admin/zone/list');
    }

    #Employee



    public function employeeupdatesave(Request $request)
    {
        $join_date = date('Y-m-d', strtotime($request->join_date));
        $this->master_model->updateEmployee($request, $request->employee_id, $request->employee_code, $request->photo, $request->name, $request->email, $request->mobile, $request->pan, $request->address, $request->adharcard, $request->license, $request->uploaded_file, $request->payment, $join_date, $request->payment_day, $request->account_no, $request->holder_name, $request->ifsc_code, $request->bank_name, $request->account_type, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($request->employee_id);
        $data['title'] = 'Success Employee';
        $data['success'] = 'Employee has been saved successfully';
        $data['link'] = $link;
        return view('master.employee.saved', $data);
    }

    #Company

    public function companysave(Request $request)
    {
        $join_date = date('Y-m-d', strtotime($request->join_date));
        $company_id = $this->master_model->saveCompany($request->name, $request->email, $request->gst_number, $request->address, $join_date, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($company_id);
        $data['title'] = 'Success Company';
        $data['success'] = 'Company has been saved successfully';
        $data['link'] = $link;
        return view('master.company.saved', $data);
    }

    public function companyupdatesave(Request $request)
    {
        $join_date = date('Y-m-d', strtotime($request->join_date));
        $this->master_model->updateCompany($request->company_id, $request->name, $request->email, $request->gst_number, $request->address, $join_date, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($request->company_id);
        $data['title'] = 'Success Company';
        $data['success'] = 'Company has been saved successfully';
        $data['link'] = $link;
        return view('master.company.saved', $data);
    }

    #Vendor

    public function locationsave(Request $request)
    {
        $this->master_model->saveLocation($request->name, $request->company_id, $this->admin_id, $this->user_id);
        return redirect('/admin/location/list');
    }

    public function vendorsave(Request $request)
    {
        $company_id = $this->master_model->saveVendor($request->business_name, $request->name, $request->email, $request->mobile, $request->gst_number, $request->address, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($company_id);
        $data['title'] = 'Success Vendor';
        $data['success'] = 'Vendor has been saved successfully';
        $data['link'] = $link;
        return view('master.vendor.saved', $data);
    }

    public function vendorupdatesave(Request $request)
    {
        $this->master_model->updateVendor($request->vendor_id, $request->business_name, $request->name, $request->email, $request->mobile, $request->gst_number, $request->address, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($request->vendor_id);
        $data['title'] = 'Success Vendor';
        $data['success'] = 'Vendor has been saved successfully';
        $data['link'] = $link;
        return view('master.vendor.saved', $data);
    }

    #Payment Source

    public function paymentsourcesave(Request $request)
    {
        $company_id = $this->master_model->savePaymentsource($request->name, $request->bank, $request->card_number, $request->type, $request->balance, $this->admin_id, $this->user_id);
        $this->setSuccess('Payment source has been deleted successfully');
        header('Location: /admin/paymentsource/list');
        exit();
    }

    public function paymentsourceupdatesave(Request $request)
    {
        $this->master_model->updatePaymentsource($request->id, $request->name, $request->bank, $request->card_number, $request->type, $request->balance, $this->admin_id, $this->user_id);
        $this->setSuccess('Payment source has been deleted successfully');
        header('Location: /admin/paymentsource/list');
        exit();
    }

    #Vehicle

    public function vehiclesave(Request $request)
    {
        $date = date('Y-m-d', strtotime($request->purchase_date));
        $vehicle_id = $this->master_model->saveVehicle($request->name, $request->brand, $request->car_type, $request->number, $request->model, $date, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($vehicle_id);
        $data['title'] = 'Success Vehicle';
        $data['success'] = 'Vehicle has been saved successfully';
        $data['link'] = $link;
        return view('master.vehicle.saved', $data);
    }

    public function vehicleupdatesave(Request $request)
    {
        $date = date('Y-m-d', strtotime($request->purchase_date));
        $this->master_model->updateVehicle($request->vehicle_id, $request->name, $request->brand, $request->car_type, $request->number, $request->model, $date, $this->admin_id, $this->user_id);
        $link = $this->encrypt->encode($request->vehicle_id);
        $data['title'] = 'Success Vehicle';
        $data['success'] = 'Vehicle has been saved successfully';
        $data['link'] = $link;
        return view('master.vehicle.saved', $data);
    }
}
