<?php

namespace App\Models;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payout
 *
 * @author Paresh
 */

use Log;
use App\Models\ParentModel;
use Illuminate\Support\Facades\DB;
use Exception;

class RideModel extends ParentModel
{

    public function passengerUpcomingRides($id, $single = 0)
    {
        $retObj = DB::table('ride_passenger as p')
            ->join('ride as r', 'r.id', '=', 'p.ride_id')
            ->leftJoin('driver as d', 'd.id', '=', 'r.driver_id')
            ->leftJoin('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('p.is_active', 1)
            ->where('r.is_active', 1)
            ->where('p.status', 0)
            ->whereDate('r.date', '>=', date('Y-m-d'))
            ->where('p.passenger_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(pickup_time, "%a %d %b %y %l:%i %p") as pickup_time , p.id as pid'));
        if ($single == 1) {
            $array = $retObj->first();
        } else {
            $array = $retObj->get();
        }
        return json_decode(json_encode($array), 1);
    }

    public function passengerLiveRide($id)
    {
        $retObj = DB::table('ride_passenger as p')
            ->join('ride as r', 'r.id', '=', 'p.ride_id')
            ->leftJoin('driver as d', 'd.id', '=', 'r.driver_id')
            ->leftJoin('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('p.is_active', 1)
            ->where('r.is_active', 1)
            ->where('p.status', 1)
            ->where('p.passenger_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(pickup_time, "%a %d %b %y %l:%i %p") as pickup_time,DATE_FORMAT(pickup_time, "%l:%i %p") as only_time,d.name as driver_name, p.id as pid'))
            ->first();
        return json_decode(json_encode($retObj), 1);
    }


    public function driverLiveRide($id)
    {
        $retObj = DB::table('ride as r')
            ->join('driver as d', 'd.id', '=', 'r.driver_id')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('r.is_active', 1)
            ->where('r.status', 2)
            ->where('r.driver_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(start_time, "%a %d %b %y %l:%i %p") as pickup_time,DATE_FORMAT(start_time, "%l:%i %p") as only_time,d.name as driver_name, r.id as pid , start_location as pickup_location ,end_location as drop_location'))
            ->first();
        return json_decode(json_encode($retObj), 1);
    }

    public function driverUpcomingRides($id, $single = 0)
    {
        $retObj = DB::table('ride as r')
            ->join('driver as d', 'd.id', '=', 'r.driver_id')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('r.is_active', 1)
            ->whereIn('r.status', [1,2])
            ->where('r.driver_id', $id)
            ->whereDate('r.date', '>=', date('Y-m-d'))
            ->select(DB::raw('*,DATE_FORMAT(start_time, "%a %d %b %y %l:%i %p") as pickup_time, r.id as pid , start_location as pickup_location ,end_location as drop_location'));
        if ($single == 1) {
            $array = $retObj->first();
        } else {
            $array = $retObj->get();
        }
        return json_decode(json_encode($array), 1);
    }

    public function driverPastRides($id)
    {
        $retObj = DB::table('ride as r')
            ->join('driver as d', 'd.id', '=', 'r.driver_id')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('r.is_active', 1)
            ->where('r.status', 5)
            ->where('r.driver_id', $id)
            ->whereDate('r.date', '<=', date('Y-m-d'))
            ->orderBy('r.id', 'desc')
            ->select(DB::raw('*,DATE_FORMAT(start_time, "%a %d %b %y %l:%i %p") as pickup_time , r.id as pid, start_location as pickup_location ,end_location as drop_location'));
        $array = $retObj->get();
        return json_decode(json_encode($array), 1);
    }
    public function driverAllRides($id)
    {
        $retObj = DB::table('ride as r')
            ->join('driver as d', 'd.id', '=', 'r.driver_id')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('r.is_active', 1)
            ->where('r.driver_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(start_time, "%a %d %b %y %l:%i %p") as pickup_time , r.id as pid, start_location as pickup_location ,end_location as drop_location'));
        $array = $retObj->get();
        return json_decode(json_encode($array), 1);
    }

    public function passengerPastRides($id)
    {
        $retObj = DB::table('ride_passenger as p')
            ->join('ride as r', 'r.id', '=', 'p.ride_id')
            ->leftJoin('driver as d', 'd.id', '=', 'r.driver_id')
            ->leftJoin('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('p.is_active', 1)
            ->where('r.is_active', 1)
            ->whereDate('p.pickup_time', '<=', date('Y-m-d'))
            ->where('p.status', '>', 1)
            ->where('p.passenger_id', $id)
            ->orderBy('p.id', 'desc')

            ->select(DB::raw('*,DATE_FORMAT(pickup_time, "%a %d %b %y %l:%i %p") as pickup_time, p.id as pid'))
            ->get();
        return json_decode(json_encode($retObj), 1);
    }



    public function passengerAllRides($id)
    {
        $retObj = DB::table('ride_passenger as p')
            ->join('ride as r', 'r.id', '=', 'p.ride_id')
            ->leftJoin('driver as d', 'd.id', '=', 'r.driver_id')
            ->leftJoin('vehicle as v', 'v.vehicle_id', '=', 'r.vehicle_id')
            ->where('p.is_active', 1)
            ->where('r.is_active', 1)
            ->where('p.passenger_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(pickup_time, "%l:%i %p") as pickup_time , p.id as pid'));
        $array = $retObj->get();
        return json_decode(json_encode($array), 1);
    }

    public function passengerBookingRides($id)
    {
        $retObj = DB::table('ride_request as p')
            ->where('p.is_active', 1)
            ->where('p.passenger_id', $id)
            ->select(DB::raw('*,DATE_FORMAT(time, "%a %d %b %y %l:%i %p") as pickup_time'))
            ->get();
        return json_decode(json_encode($retObj), 1);
    }


    public function getRidePassenger($ride_id)
    {
        $retObj = DB::table('ride_passenger as p')
            ->join('users as r', 'r.parent_id', '=', 'p.passenger_id')
            ->where('r.user_type', 5)
            ->where('p.is_active', 1)
            ->where('p.ride_id', $ride_id)
            ->select(DB::raw('p.id,r.address ,p.status,p.otp,TIME_FORMAT(p.pickup_time, "%H %i %p") as pickup_time ,TIME_FORMAT(p.drop_time, "%H %i %p") as drop_time ,p.pickup_location,p.drop_location,r.icon,r.location,r.address,r.name,r.gender,r.mobile'))
            ->get();
        return json_decode(json_encode($retObj), 1);
    }
}