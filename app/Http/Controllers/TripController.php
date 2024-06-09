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

use App\Model\Employee;
use App\Model\Trip;
use App\Model\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class TripController extends Controller
{

    private $employee_model;
    private $master_model;
    private $trip_model;
    private $schedulerMobile = array('9730946150', '8879391658');
    private $tripAsignMobile = array('9730946150', '8879391658');
    private $reviewMobile = array('9730946150');

    function __construct()
    {
        parent::__construct();

        $this->employee_model = new Employee();
        $this->master_model = new Master();
        $this->trip_model = new Trip();
        $this->validateSession(array(1, 4));
    }

    public function addtrip()
    {
        $this->validateSession(array(1, 2, 3));

        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $data['company_list'] = $company_list;

        $data['current_date'] = date('d-m-Y');
        $data['title'] = 'Create Trip';
        return view('trip.create', $data);
    }

    public function savetrip(Request $request)
    {
        $this->validateSession(array(1, 2, 3));
        $data = $_POST;
        $pickup_date = date('Y-m-d', strtotime($request->date));
        $time = date('H:i:s', strtotime($request->pickup_time));
        $data['date'] = $pickup_date;
        $data['pickup_time'] = $time;
        $data['passengers'] = implode(',', $_POST['passengers_name']);
        $trip_id = $this->trip_model->saveTrip($data, $this->user_id);

        $link = $this->encrypt->encode($trip_id);
        $long_url = 'http://siddhivinayaktravel.in/trip/schedule/' . $link;
        $short_url = $this->master_model->getShortUrl($long_url);
        $sms = "Client has added trip click to schedule trip " . $short_url;
        foreach ($this->schedulerMobile as $mobile) {
            // $this->sms_send($mobile, $sms);
        }
        $data['title'] = 'Success Trip';
        $data['success'] = 'Trip has been saved successfully';
        $data['link'] = $link;
        return view('trip.saved', $data);
    }

    public function listtrip($type = 'all')
    {
        $this->validateSession(array(1, 2, 3, 4));
        if ($type == 'all') {
            $array = array('Requested', 'Assigned', 'Completed', 'Rejected');
        } else if ($type == 'upcoming') {
            $array = array('Requested', 'Assigned');
        } else if ($type == 'past') {
            $array = array('Completed');
        }
        if ($this->user_type == 3) {
            $list = $this->trip_model->getTripList($this->user_id, $array);
        } else {
            $list = $this->trip_model->getAdminTripList($array);
        }
        $int = 0;
        foreach ($list as $item) {
            $link = '';
            if ($item->trip_id > 0) {
                $link = $this->encrypt->encode($item->trip_id);
            }
            $list[$int]->req_link = $this->encrypt->encode($item->req_id);
            $list[$int]->link = $link;
            $int++;
        }
        $data['addnewlink'] = '/trip/add';
        $data['list'] = $list;
        $data['current_date'] = date('d-m-Y');
        $data['title'] = ucfirst($type) . ' Trips';
        return view('trip.list', $data);
    }

    public function createshorturl()
    {
        $data['title'] = 'Create Short URL';
        return view('employee.shorturl', $data);
    }

    public function savereview()
    {
        $id = $this->trip_model->saveReview($_POST, 'Guest');
        $this->setSuccess($_POST['type'] . ' has been send successfully');
        $link = $this->encrypt->encode($id);
        $link = 'http://siddhivinayaktravel.in/trip/review/' . $link;
        $short_url = $this->master_model->getShortUrl($link);
        $sms = "Passenger Send  " . $_POST['type'] . " for Trip check below " . $short_url;
        foreach ($this->reviewMobile as $mobile) {
            $this->sms_send($mobile, $sms);
        }
        header('Location: /trip/' . $_POST['link']);
        exit;
    }

    public function saveshorturl()
    {
        $short_url = $this->master_model->getShortUrl($_POST['long_url']);
        $data['title'] = 'Create Short URL';
        $data['short_url'] = $short_url;
        return view('employee.shorturl', $data);
    }

    public function schedule($link)
    {
        $this->validateSession(array(1, 4));
        $id = $this->encrypt->decode($link);
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id, 'admin_id');
        $employee_list = $this->master_model->getMaster('employee', $this->admin_id, 'admin_id');
        $detail = $this->master_model->getMasterDetail('trip_request', 'req_id', $id);
        $data['vehicle_list'] = $vehicle_list;
        $data['employee_list'] = $employee_list;
        $data['det'] = $detail;
        $data['req_id'] = $id;
        $data['title'] = 'Trip Schedule';
        return view('trip.schedule', $data);
    }

    public function packagesave()
    {
        $id = $this->trip_model->savePackage($_POST, $this->admin_id, $this->user_id);
        $this->setSuccess('Package has been send successfully');
        header('Location: /trip/package/list');
        exit;
    }

    public function package()
    {
        $this->validateSession(array(1, 4));
        $company_list = $this->master_model->getMaster('company', $this->admin_id, 'admin_id');
        $data['company_list'] = $company_list;
        $data['title'] = 'Casual package';
        return view('trip.package', $data);
    }

    public function packageList()
    {
        $this->validateSession(array(1, 4));
        $package_list = $this->trip_model->getPackageList($this->admin_id);
        $data['list'] = $package_list;
        $data['addnewlink'] = '/trip/package';
        $data['title'] = 'Casual package';
        return view('trip.package_list', $data);
    }

    public function complete($link)
    {
        $this->validateSession(array(1, 4));
        $id = $this->encrypt->decode($link);
        $detail = $this->master_model->getMasterDetail('trip', 'trip_id', $id);
        $package_list = $this->master_model->getMaster('company_casual_package', $detail->company_id, 'company_id');
        $employee_list = $this->master_model->getMaster('employee', $this->admin_id, 'admin_id');
        $data['package_list'] = $package_list;
        $data['employee_list'] = $employee_list;
        $data['det'] = $detail;
        $data['trip_id'] = $id;
        $data['title'] = 'Trip Complete';
        return view('trip.complete', $data);
    }

    public function review($link)
    {
        $data['login_type'] = '';
        $id = $this->encrypt->decode($link);
        $rdetail = $this->master_model->getMasterDetail('review_complaints', 'id', $id);
        $detail = $this->master_model->getMasterDetail('trip', 'trip_id', $rdetail->trip_id);
        $empdetail = $this->master_model->getMasterDetail('employee', 'employee_id', $detail->employee_id);
        $vehidetail = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $detail->vehicle_id);
        $data['rdet'] = $rdetail;
        $data['edet'] = $empdetail;
        $data['vdet'] = $vehidetail;
        $data['det'] = $detail;
        $data['link'] = $link;
        $data['trip_id'] = $id;
        $data['title'] = 'Review Details';
        return view('trip.review', $data);
    }

    public function schedulesave(Request $request)
    {
        $this->validateSession(array(1, 4));
        $data = $_POST;
        $detail = $this->master_model->getMasterDetail('trip_request', 'req_id', $_POST['req_id']);
        $data['date'] = $detail->date;
        $data['time'] = $detail->time;
        $data['company_id'] = $detail->company_id;
        $data['passengers'] = $detail->passengers;
        $data['total_passengers'] = $detail->total_passengers;
        $data['pickup_location'] = $detail->pickup_location;
        $data['drop_location'] = $detail->drop_location;
        $data['vehicle_type'] = $detail->vehicle_type;
        $data['admin_id'] = $this->admin_id;
        $data['note'] = $detail->note;
        $trip_id = $this->trip_model->saveTripDetail($data, $this->user_id);
        $link = $this->encrypt->encode($trip_id);
        $long_url = 'http://siddhivinayaktravel.in/trip/' . $link;
        $short_url = $this->master_model->getShortUrl($long_url);
        $sms = "Trip Assigned for " . $detail->passengers . " on " . date('d M Y', strtotime($detail->date)) . ' ' . date('h:i A', strtotime($detail->time)) . " Pickup from " . $detail->pickup_location . " " . $short_url;
        foreach ($this->tripAsignMobile as $mobile) {
            // $this->sms_send($mobile, $sms);
        }
        $this->master_model->updateTableColumn('trip_request', 'trip_id', $trip_id, 'req_id', $_POST['req_id'], $this->user_id);
        $this->master_model->updateTableColumn('trip_request', 'status', 'Assigned', 'req_id', $_POST['req_id'], $this->user_id);

        $data['title'] = 'Success Trip';
        $data['success'] = 'Trip has been scheduled successfully';
        $data['link'] = $link;
        return view('trip.saved', $data);
    }

    public function completesave(Request $request)
    {
        $this->validateSession(array(1, 4));
        $data = [];
        $package = $this->master_model->getMasterDetail('company_casual_package', 'id', $_POST['package_id']);

        $data['status'] = 'Completed';
        $data['package_id'] = $_POST['package_id'];
        $data['extra_km'] = ($_POST['extra_km'] > 0) ? $_POST['extra_km'] : 0;
        $data['extra_hour'] = ($_POST['extra_hour'] > 0) ? $_POST['extra_hour'] : 0;
        $data['toll_parking'] = ($_POST['toll_parking'] > 0) ? $_POST['toll_parking'] : 0;
        $data['vendor_amount'] = ($_POST['vendor_amount'] > 0) ? $_POST['vendor_amount'] : 0;

        $data['package_amount'] = $package->package_amount;
        $data['extra_km_amount'] = $package->extra_km * $data['extra_km'];
        $data['extra_hour_amount'] = $package->extra_hour * $data['extra_hour'];
        $data['total_amount'] = $data['package_amount'] + $data['extra_km_amount'] + $data['extra_hour_amount'] + $data['toll_parking'];
        $trip_id = $this->trip_model->updateTrip($request->trip_id, $data);
        $this->master_model->updateTableColumn('trip_request', 'status', 'Completed', 'trip_id', $request->trip_id, $this->user_id);

        $this->setSuccess('Trip has been send successfully');
        header('Location: /trip/list/all');
        exit;
    }

    public function trip($link)
    {
        if (Session::get('success_message')) {
            $data['success_message'] = Session::get('success_message');
            $_SESSION['success_message'] = '';
            CustomSession::remove('success_message');
        }

        $data['login_type'] = '';
        $id = $this->encrypt->decode($link);
        $detail = $this->master_model->getMasterDetail('trip', 'trip_id', $id);
        $empdetail = $this->master_model->getMasterDetail('employee', 'employee_id', $detail->employee_id);
        $vehidetail = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $detail->vehicle_id);
        $data['edet'] = $empdetail;
        $data['vdet'] = $vehidetail;
        $data['det'] = $detail;
        $data['link'] = $link;
        $data['trip_id'] = $id;
        $data['title'] = 'Trip Details';
        return view('trip.view', $data);
    }

    public function rating($link, $rating)
    {
        $id = $this->encrypt->decode($link);
        switch ($rating) {
            case 1:
                $text = 'Disaster';
                break;
            case 2:
                $text = 'Bad';
                break;
            case 3:
                $text = 'Ok';
                break;
            case 4:
                $text = 'Good';
                break;
            case 5:
                $text = 'Awesome';
                break;
        }
        $this->trip_model->updateTripRating($rating, $id);
        echo $text;
    }

    public function short($link)
    {
        $detail = $this->master_model->getMasterDetail('short_url', 'short_url', $link);
        if (!empty($detail)) {
            header('Location: ' . $detail->long_url, 301);
            exit;
        } else {
            header('Location: /404', 301);
            exit;
        }
    }
}
