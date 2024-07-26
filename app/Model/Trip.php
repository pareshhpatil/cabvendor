<?php

namespace App\Model;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Paresh
 */

use Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{

    public function saveTrip($data, $user_id)
    {
        $id = DB::table('trip_request')->insertGetId(
            [
                'vehicle_type' => $data['vehicle_type'],
                'date' => $data['date'],
                'company_id' => $data['company_id'],
                'time' => $data['pickup_time'],
                'total_passengers' => $data['total_passengers'],
                'passengers' => $data['passengers'],
                'pickup_location' => $data['pickup_location'],
                'drop_location' => $data['drop_location'],
                'note' => $data['note'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveReview($data, $user_id)
    {
        $id = DB::table('review_complaints')->insertGetId(
            [
                'trip_id' => $data['trip_id'],
                'type' => $data['type'],
                'name' => $data['name'],
                'text' => $data['note'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function savePackage($data, $admin_id, $user_id)
    {
        $id = DB::table('company_casual_package')->insertGetId(
            [
                'company_id' => $data['company_id'],
                'vehicle_type' => $data['vehicle_type'],
                'package_name' => $data['package_name'],
                'package_amount' => $data['package_amount'],
                'extra_km' => $data['extra_km'],
                'extra_hour' => $data['extra_hour'],
                'admin_id' => $admin_id,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveTripDetail($data, $user_id)
    {
        $id = DB::table('trip')->insertGetId(
            [
                'admin_id' => $data['admin_id'],
                'employee_id' => $data['employee_id'],
                'vehicle_id' => $data['vehicle_id'],
                'company_id' => $data['company_id'],
                'vendor_id' => $data['vendor_id'],
                'vehicle_type' => $data['vehicle_type'],
                'date' => $data['date'],
                'time' => $data['time'],
                'total_passengers' => $data['total_passengers'],
                'passengers' => $data['passengers'],
                'pickup_location' => $data['pickup_location'],
                'drop_location' => $data['drop_location'],
                'emails' => $data['emails'],
                'mobiles' => $data['mobiles'],
                'note' => $data['note'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveDriver($data, $user_id)
    {
        $id = DB::table('driver')->insertGetId(
            [
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'address' => $data['address'],
                'license' => $data['license'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }


    public function saveRide($data, $user_id)
    {
        $id = DB::table('ride')->insertGetId(
            [
                'title' => $data['title'],
                'project_id' => $data['project_id'],
                'driver_id' => $data['driver_id'],
                'vehicle_id' => $data['vehicle_id'],
                'date' => $data['date'],
                'type' => $data['type'],
                'status' => $data['status'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'total_passengers' => $data['total_passengers'],
                'start_location' => $data['start_location'],
                'end_location' => $data['end_location'],
                'created_by' => $user_id,
                'casual' => 1,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function savePassenger($data, $user_id)
    {
        $id = DB::table('passenger')->insertGetId(
            [
                'project_id' => $data['project_id'],
                'employee_name' => $data['name'],
                'passenger_type' => 3,
                'gender' => $data['gender'],
                'address' => $data['address'],
                'location' => $data['location'],
                'mobile' => $data['mobile'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveRidePassenger($data, $user_id)
    {
        $id = DB::table('ride_passenger')->insertGetId(
            [
                'passenger_id' => $data['passenger_id'],
                'ride_id' => $data['ride_id'],
                'pickup_time' => $data['pickup_time'],
                'pickup_location' => $data['pickup_location'],
                'drop_location' => $data['drop_location'],
                'otp' => $data['otp'],
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateTrip($id, $data)
    {
        DB::table('trip')
            ->where('trip_id', $id)
            ->update($data);
    }

    public function updateTripRating($rating, $id)
    {
        DB::table('trip')
            ->where('trip_id', $id)
            ->update([
                'rating' => $rating
            ]);
    }

    public function updateTable($table, $id_col, $id, $data)
    {
        DB::table($table)
            ->where($id_col, $id)
            ->update($data);
    }

    public function getTripList($user_id, $array)
    {
        $retObj = DB::table('trip_request as a')
            ->join('company as v', 'v.company_id', '=', 'a.company_id')
            ->select(DB::raw('a.*,v.name as company_name'))
            ->where('a.is_active', 1)
            ->where('a.created_by', $user_id)
            ->whereIn('a.status', $array)
            ->get();
        return $retObj;
    }

    public function getAdminTripList($array)
    {
        $retObj = DB::table('trip_request as a')
            ->join('company as v', 'v.company_id', '=', 'a.company_id')
            ->select(DB::raw('a.*,v.name as company_name'))
            ->where('a.is_active', 1)
            ->whereIn('a.status', $array)
            ->get();
        return $retObj;
    }


    public function getPackageList($admin_id)
    {
        $retObj = DB::table('company_casual_package as a')
            ->join('company as v', 'v.company_id', '=', 'a.company_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->select(DB::raw('a.*,v.name as company_name'))
            ->get();
        return $retObj;
    }
}
