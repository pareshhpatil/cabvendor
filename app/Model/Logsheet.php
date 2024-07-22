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

class Logsheet extends Model
{

    public function saveLogsheetbill($vehicle_id, $company_id, $date, $start_km, $end_km, $start_time, $close_time, $daynight, $remark, $toll, $type, $pick_drop, $from, $to, $user_id, $admin_id, $status, $holiday = 0)
    {
        $id = DB::table('logsheet_bill')->insertGetId(
            [
                'admin_id' => $admin_id,
                'vehicle_id' => $vehicle_id,
                'driver_id' => 0,
                'company_id' => $company_id,
                'start_km' => $start_km,
                'end_km' => $end_km,
                'total_km' => $end_km - $start_km,
                'start_time' => $start_time,
                'close_time' => $close_time,
                'date' => $date,
                'toll' => $toll,
                'remark' => $remark,
                'day_night' => $daynight,
                'from' => $from,
                'to' => $to,
                'pick_drop' => $pick_drop,
                'holiday' => $holiday,
                'type' => $type,
                'status' => $status,
                'created_by' => $user_id,
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveLogsheetInvoice($invoice_number, $vehicle_id, $company_id, $date, $bill_date, $cgst, $sgst, $igst, $total_gst, $base_total, $grand_total, $toll, $type, $work_order_no, $po_number, $user_id, $admin_id)
    {
        $id = DB::table('logsheet_invoice')->insertGetId(
            [
                'admin_id' => $admin_id,
                'invoice_number' => $invoice_number,
                'vehicle_id' => $vehicle_id,
                'company_id' => $company_id,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'total_gst' => $total_gst,
                'base_total' => $base_total,
                'date' => $date,
                'bill_date' => $bill_date,
                'grand_total' => $grand_total,
                'type' => $type,
                'work_order_no' => $work_order_no,
                'po_number' => $po_number,
                'toll' => $toll,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveLogsheetDetail($invoice_id, $particular_name, $unit, $qty, $rate, $amount, $is_deduct, $user_id)
    {
        $id = DB::table('logsheet_detail')->insertGetId(
            [
                'invoice_id' => $invoice_id,
                'particular_name' => $particular_name,
                'unit' => $unit,
                'qty' => $qty,
                'rate' => $rate,
                'amount' => $amount,
                'is_deduct' => $is_deduct,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveInvoiceExpense($invoice_id, $request_id, $amount, $user_id)
    {
        $id = DB::table('invoice_expense')->insertGetId(
            [
                'invoice_id' => $invoice_id,
                'request_id' => $request_id,
                'amount' => $amount,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateLogsheetInvoice($invoice_id, $vehicle_id, $company_id, $date, $bill_date, $cgst, $sgst, $igst, $total_gst, $base_total, $grand_total, $toll, $type, $work_order_no, $po_number, $user_id, $admin_id)
    {
        DB::table('logsheet_invoice')
            ->where('invoice_id', $invoice_id)
            ->where('admin_id', $admin_id)
            ->update(
                [
                    'admin_id' => $admin_id,
                    'vehicle_id' => $vehicle_id,
                    'company_id' => $company_id,
                    'cgst' => $cgst,
                    'sgst' => $sgst,
                    'igst' => $igst,
                    'total_gst' => $total_gst,
                    'base_total' => $base_total,
                    'date' => $date,
                    'bill_date' => $bill_date,
                    'grand_total' => $grand_total,
                    'type' => $type,
                    'work_order_no' => $work_order_no,
                    'po_number' => $po_number,
                    'toll' => $toll,
                    'last_update_by' => $user_id
                ]
            );
    }

    public function updateLogsheetDetail($id, $particular_name, $unit, $qty, $rate, $amount, $is_deduct, $user_id)
    {
        DB::table('logsheet_detail')
            ->where('id', $id)
            ->update(
                [
                    'particular_name' => $particular_name,
                    'unit' => $unit,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $amount,
                    'is_deduct' => $is_deduct,
                    'last_update_by' => $user_id
                ]
            );
    }

    public function approveLogsheetDetail($id, $user_id)
    {
        DB::table('logsheet_bill')
            ->where('logsheet_id', $id)
            ->update(
                [
                    'status' => 1,
                    'last_update_by' => $user_id
                ]
            );
    }

    function getInvoiceNumber($invoice_id)
    {
        if ($invoice_id > 0) {
            $retObj = DB::table('sequence')
                ->select(DB::raw('*'))
                ->where('id', $invoice_id)
                ->first();

            if (!isset($retObj->prefix)) {
                return '';
            } else {
                $current_number = $retObj->current_number + 1;
                $prefix = $retObj->prefix;
                DB::table('sequence')
                    ->where('id', $invoice_id)
                    ->update([
                        'current_number' => $current_number
                    ]);
            }

            switch (strlen($current_number)) {
                case 1:
                    return $prefix . '00' . $current_number;
                    break;
                case 2:
                    return $prefix . '0' . $current_number;
                    break;
                default:
                    return $prefix . $current_number;
                    break;
            }
        } else {
            return '';
        }
    }

    public function getBillData($date, $company_id, $vehicle_id)
    {
        if ($company_id == 105) {
            $startDate = new \DateTime($date); // Replace with your desired starting date
            $to_date = $startDate->format('Y-m-25');
            $startDate->modify('-1 month');
            $from_date = $startDate->format('Y-m-26');
            $retObj = DB::table('logsheet_bill')
                ->select(DB::raw("*,TIMEDIFF(close_time,start_time) as total_time,TIMEDIFF( TIMEDIFF( close_time,start_time),'12:00:00') as extra_time"))
                ->where('is_active', 1)
                ->where('status', 1)
                ->where('company_id', $company_id)
                ->where('vehicle_id', $vehicle_id)
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date)
                ->orderBy('date', 'asc')
                ->orderBy('start_km', 'asc')
                ->get();
        } else {
            $retObj = DB::table('logsheet_bill')
                ->select(DB::raw("*,TIMEDIFF(close_time,start_time) as total_time,TIMEDIFF( TIMEDIFF( close_time,start_time),'12:00:00') as extra_time"))
                ->where('is_active', 1)
                ->where('status', 1)
                ->where('company_id', $company_id)
                ->where('vehicle_id', $vehicle_id)
                ->where(DB::raw("(DATE_FORMAT(date,'%Y-%m'))"), date('Y-m', strtotime($date)))
                ->orderBy('date', 'asc')
                ->orderBy('start_km', 'asc')
                ->get();
        }
        return $retObj;
    }

    public function getCasualPendingBills($company_id)
    {
        $retObj = DB::table('trip as t')
            ->join('company_casual_package as p', 't.package_id', '=', 'p.id')
            ->select(DB::raw("t.*,p.vehicle_type as vehicle_type,p.package_name"))
            ->where('t.is_active', 1)
            ->where('t.status', 'Completed')
            ->where('billed', 0)
            ->where('t.company_id', $company_id)
            ->get();
        return $retObj;
    }

    public function getMonthLogsheet($vehicle_id, $month)
    {
        $retObj = DB::table('logsheet_bill')
            ->select(DB::raw("*,TIMEDIFF(close_time,start_time) as total_time,TIMEDIFF( TIMEDIFF( close_time,start_time),'12:00:00') as extra_time"))
            ->where('is_active', 1)
            ->where('status', 1)
            ->where('vehicle_id', $vehicle_id)
            ->where(DB::raw("(DATE_FORMAT(date,'%Y-%m'))"), $month)
            ->orderBy('date', 'asc')
            ->orderBy('start_km', 'asc')
            ->get();
        return $retObj;
    }

    public function getMonthVehicle($month)
    {
        $retObj = DB::table('logsheet_bill')
            ->select(DB::raw("distinct vehicle_id,admin_id,company_id"))
            ->where('is_active', 1)
            ->where('status', 1)
            ->whereIn('company_id', [1, 8])
            ->where(DB::raw("(DATE_FORMAT(date,'%Y-%m'))"), $month)
            ->orderBy('date', 'asc')
            ->get();
        return $retObj;
    }

    public function getPendingBillData($admin_id)
    {
        $retObj = DB::table('logsheet_bill as l')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'l.vehicle_id')
            ->join('company as c', 'c.company_id', '=', 'l.company_id')
            ->join('user as u', 'u.user_id', '=', 'l.created_by')
            ->select(DB::raw("l.*,v.name as vehicle_name,c.name as company_name,u.name as user_name"))
            ->where('l.is_active', 1)
            ->where('l.status', 0)
            ->where('l.admin_id', $admin_id)
            ->orderBy('l.date', 'asc')
            ->get();
        return $retObj;
    }

    public function getLogsheetBill($admin_id, $type, $val, $company_id, $from_date = null)
    {
        $retObj = DB::table('logsheet_invoice as l')
            ->join('vehicle as v', 'v.vehicle_id', '=', 'l.vehicle_id')
            ->join('company as c', 'c.company_id', '=', 'l.company_id')
            ->where('l.' . $type, $val)
            ->where('l.is_active', 1)
            ->where('l.admin_id', $admin_id);
        if ($company_id > 0) {
            $retObj = $retObj->where('l.company_id', $company_id);
        }
        if ($from_date != null) {
            $retObj = $retObj->whereDate('l.date', '>', $from_date);
        }
        $retObj = $retObj->select(DB::raw('l.invoice_id,l.total_gst,l.invoice_number,l.date,l.bill_date,l.grand_total,v.name as vehicle_name,c.name as company_name'))
            ->get();
        return $retObj;
    }

    function getInvoiceId($company_id, $vehicle_id, $date, $admin_id)
    {
        $retObj = DB::table('logsheet_invoice')
            ->select(DB::raw('invoice_id'))
            ->where('company_id', $company_id)
            ->where('vehicle_id', $vehicle_id)
            ->where('date', $date)
            ->where('admin_id', $admin_id)
            ->first();
        if (isset($retObj->invoice_id)) {
            return $retObj->invoice_id;
        } else {
            return 0;
        }
    }
}
