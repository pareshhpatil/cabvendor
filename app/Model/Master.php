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
use Exception;

class Master extends Model
{

    public function getMaster($table, $admin_id, $column_name = 'admin_id', $extra_col = null, $extra_val = null)
    {
        $retObj = DB::table($table)
            ->select(DB::raw('*'))
            ->where('is_active', 1)
            ->where($column_name, $admin_id);
        if ($extra_col != null) {
            $retObj = $retObj->where($extra_col, $extra_val);
        }
        return $retObj->get();
    }

    public function getWherein($table, $column_name, $array)
    {
        $retObj = DB::table($table)
            ->select(DB::raw('*'))
            ->where('is_active', 1)
            ->whereIn($column_name, $array)
            ->get();
        return $retObj;
    }

    public function getMasterDetail($table, $column, $id)
    {
        $retObj = DB::table($table)
            ->select(DB::raw('*'))
            ->where($column, $id)
            ->first();
        return $retObj;
    }

    public function getMasterValue($table, $column, $id, $value)
    {
        $retObj = DB::table($table)
            ->select(DB::raw($value))
            ->where($column, $id)
            ->first();
        if (empty($retObj)) {
            return false;
        }
        return $retObj->{$value};
    }

    public function deleteReccord($table, $column, $id, $user_id)
    {
        DB::table($table)
            ->where($column, $id)
            ->update([
                'is_active' => 0,
                'last_update_by' => $user_id
            ]);
    }

    public function updateTableColumn($table, $column_name, $value, $search_column, $id, $user_id)
    {
        DB::table($table)
            ->where($search_column, $id)
            ->update([
                $column_name => $value,
                'last_update_by' => $user_id
            ]);
    }

    public function updateTableColumnIn($table, $column_name, $value, $search_column, $id, $user_id)
    {
        DB::table($table)
            ->where($search_column, $id)
            ->update([
                $column_name => $value,
                'last_update_by' => $user_id
            ]);
    }

    public function saveEmployee($request, $code, $name, $email, $mobile, $pan, $address, $adharcard, $license, $image_file, $payment, $join_date, $payment_day, $account_no, $account_holder_name, $ifsc_code, $bank_name, $account_type, $admin_id, $user_id)
    {
        $photo = $this->uploadImage($request);
        $id = DB::table('employee')->insertGetId(
            [
                'admin_id' => $admin_id,
                'employee_code' => $code,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'pan' => $pan,
                'address' => $address,
                'adharcard' => $adharcard,
                'license' => $license,
                'photo' => $photo,
                'payment' => $payment,
                'join_date' => $join_date,
                'payment_day' => $payment_day,
                'account_no' => $account_no,
                'account_holder_name' => $account_holder_name,
                'ifsc_code' => $ifsc_code,
                'bank_name' => $bank_name,
                'account_type' => $account_type,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }



    public function saveZone($request, $admin_id, $user_id)
    {
        $id = DB::table('zone')->insertGetId(
            [
                'admin_id' => $admin_id,
                'company_id' => $request->company_id,
                'zone' => $request->zone,
                'from' => $request->from,
                'to' => $request->to,
                'car_type' => $request->car_type,
                'svk_km' => $request->svk_km,
                'vendor_km' => $request->vendor_km,
                'admin_km' => $request->admin_km,
                'company_km' => $request->company_km,
                'svk_amount' => $request->svk_amount,
                'vendor_amount' => $request->vendor_amount,
                'admin_amount' => $request->admin_amount,
                'company_amount' => $request->company_amount,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }


    public function updateZone($request, $admin_id, $user_id)
    {
        DB::table('zone')
            ->where('zone_id', $request->zone_id)
            ->update(
                [
                    'admin_id' => $admin_id,
                    'company_id' => $request->company_id,
                    'zone' => $request->zone,
                    'from' => $request->from,
                    'to' => $request->to,
                    'car_type' => $request->car_type,
                    'svk_km' => $request->svk_km,
                    'vendor_km' => $request->vendor_km,
                    'admin_km' => $request->admin_km,
                    'company_km' => $request->company_km,
                    'svk_amount' => $request->svk_amount,
                    'vendor_amount' => $request->vendor_amount,
                    'admin_amount' => $request->admin_amount,
                    'company_amount' => $request->company_amount,
                    'last_update_by' => $user_id
                ]
            );
    }

    public function saveFormEmployee($request, $code, $name, $email, $mobile, $pan, $address, $adharcard, $license, $image_file, $payment, $join_date, $payment_day, $account_no, $account_holder_name, $ifsc_code, $bank_name, $account_type, $admin_id, $user_id)
    {
        $photo = $this->uploadImage($request);
        $id = DB::table('test_employee')->insertGetId(
            [
                'admin_id' => $admin_id,
                'employee_code' => $code,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'pan' => $pan,
                'address' => $address,
                'adharcard' => $adharcard,
                'license' => $license,
                'photo' => $photo,
                'payment' => $payment,
                'join_date' => $join_date,
                'payment_day' => $payment_day,
                'account_no' => $account_no,
                'account_holder_name' => $account_holder_name,
                'ifsc_code' => $ifsc_code,
                'bank_name' => $bank_name,
                'account_type' => $account_type,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateEmployee($request, $employee_id, $code, $photo, $name, $email, $mobile, $pan, $address, $adharcard, $license, $image_file, $payment, $join_date, $payment_day, $account_no, $account_holder_name, $ifsc_code, $bank_name, $account_type, $admin_id, $user_id)
    {
        $image = $this->uploadImage($request);
        if ($image != '') {
            $photo = $image;
        }
        DB::table('employee')
            ->where('employee_id', $employee_id)
            ->update([
                'employee_code' => $code,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'pan' => $pan,
                'address' => $address,
                'adharcard' => $adharcard,
                'license' => $license,
                'photo' => $photo,
                'payment' => $payment,
                'join_date' => $join_date,
                'payment_day' => $payment_day,
                'account_no' => $account_no,
                'account_holder_name' => $account_holder_name,
                'ifsc_code' => $ifsc_code,
                'bank_name' => $bank_name,
                'account_type' => $account_type,
                'last_update_by' => $user_id
            ]);
    }

    public function saveCompany($name, $email, $gst_number, $address, $join_date, $admin_id, $user_id)
    {
        $id = DB::table('company')->insertGetId(
            [
                'admin_id' => $admin_id,
                'name' => $name,
                'email' => $email,
                'gst_number' => $gst_number,
                'address' => $address,
                'join_date' => $join_date,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function savePaymentStatement($source_id, $from_id, $paid_date, $amount, $transaction_type, $type, $narrative, $user_id)
    {
        $id = DB::table('payment_statement')->insertGetId(
            [
                'source_id' => $source_id,
                'from_id' => $from_id,
                'paid_date' => $paid_date,
                'amount' => $amount,
                'transaction_type' => $transaction_type,
                'type' => $type,
                'narrative' => $narrative,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }



    public function updateCompany($company_id, $name, $email, $gst_number, $address, $join_date, $admin_id, $user_id)
    {
        DB::table('company')
            ->where('company_id', $company_id)
            ->update([
                'name' => $name,
                'email' => $email,
                'gst_number' => $gst_number,
                'address' => $address,
                'join_date' => $join_date,
                'last_update_by' => $user_id
            ]);
    }

    public function saveVendor($business_name, $name, $email, $mobile, $gst_number, $address, $admin_id, $user_id)
    {
        $id = DB::table('vendor')->insertGetId(
            [
                'admin_id' => $admin_id,
                'business_name' => $business_name,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'gst_number' => $gst_number,
                'address' => $address,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveLocation($name, $company_id, $admin_id, $user_id)
    {
        $id = DB::table('location')->insertGetId(
            [
                'admin_id' => $admin_id,
                'company_id' => $company_id,
                'name' => $name,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateVendor($vendor_id, $business_name, $name, $email, $mobile, $gst_number, $address, $admin_id, $user_id)
    {
        DB::table('vendor')
            ->where('vendor_id', $vendor_id)
            ->update([
                'business_name' => $business_name,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'gst_number' => $gst_number,
                'address' => $address,
                'last_update_by' => $user_id
            ]);
    }

    public function savePaymentsource($name, $bank, $card_number, $type,  $balance, $admin_id, $user_id)
    {
        $id = DB::table('paymentsource')->insertGetId(
            [
                'admin_id' => $admin_id,
                'name' => $name,
                'bank' => $bank,
                'card_number' => $card_number,
                'type' => $type,
                'balance' => $balance,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updatePaymentsource($id, $name, $bank, $card_number, $type, $balance, $admin_id, $user_id)
    {
        DB::table('paymentsource')
            ->where('paymentsource_id', $id)
            ->update([
                'name' => $name,
                'bank' => $bank,
                'card_number' => $card_number,
                'type' => $type,
                'balance' => $balance,
                'last_update_by' => $user_id
            ]);
    }

    public function saveVehicle($name, $brand, $car_type, $number, $model, $purchase_date, $admin_id, $user_id)
    {
        $id = DB::table('vehicle')->insertGetId(
            [
                'admin_id' => $admin_id,
                'name' => $name,
                'brand' => $brand,
                'car_type' => $car_type,
                'number' => $number,
                'model' => $model,
                'purchase_date' => $purchase_date,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateVehicle($vehicle_id, $name, $brand, $car_type, $number, $model, $purchase_date, $admin_id, $user_id)
    {
        DB::table('vehicle')
            ->where('vehicle_id', $vehicle_id)
            ->update([
                'name' => $name,
                'brand' => $brand,
                'car_type' => $car_type,
                'number' => $number,
                'model' => $model,
                'purchase_date' => $purchase_date,
                'last_update_by' => $user_id
            ]);
    }

    public function uploadImage($request, $folder = 'employee')
    {
        if ($request->hasFile('uploaded_file')) {
            if ($request->file('uploaded_file')->isValid()) {
                try {
                    $file = $request->file('uploaded_file');
                    $name = time() . '.' . $file->getClientOriginalExtension();

                    $request->file('uploaded_file')->move("dist/uploads/" . $folder, $name);
                    return $name;
                } catch (Exception $e) {
                }
            }
        }
        return '';
    }



    public function saveShortURL($str, $long)
    {
        $id = DB::table('short_url')->insertGetId(
            [
                'short_url' => $str,
                'long_url' => $long
            ]
        );
        return $id;
    }

    function generateRandomString($length = 6)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789bcdfghjklmnpqrstvwxyz', ceil($length / strlen($x)))), 1, $length);
    }

    public function getShortUrl($long_url)
    {
        $exist = true;
        while ($exist == true) {
            $str = $this->generateRandomString(6);
            $data = $this->getMasterDetail('short_url', 'short_url', $str);
            if (empty($data)) {
                $exist = false;
            }
        }
        $this->saveShortURL($str, $long_url);
        return 'http://siddhivinayaktravel.in/s/' . $str;
    }

    public function getBalance($employee_id)
    {
        $retObj = DB::table('employee')
            ->select(DB::raw('balance'))
            ->where('employee_id', $employee_id)
            ->first();
        return $retObj->balance;
    }

    public function updateBankBalance($amount, $source_id, $minus = 1)
    {
        $data = $this->getMasterDetail('paymentsource', 'paymentsource_id', $source_id);
        $current_balance = $data->balance;
        if ($minus == 1) {
            $current_balance = $current_balance - $amount;
        } else {
            $current_balance = $current_balance + $amount;
        }
        $this->updateTableColumn('paymentsource', 'balance', $current_balance, 'paymentsource_id', $source_id, $data->created_by);
    }

    public function updateEmployeeBalance($amount, $employee_id, $minus = 1)
    {
        $current_balance = $this->getBalance($employee_id);
        if ($minus == 1) {
            $current_balance = $current_balance - $amount;
        } else {
            $current_balance = $current_balance + $amount;
        }
        $this->updateTableColumn('employee', 'balance', $current_balance, 'employee_id', $employee_id, 0);
    }


    public function getZoneList($admin_id)
    {
        $retObj = DB::table('zone as z')
            ->join('company as c', 'c.company_id', '=', 'z.company_id')
            ->select(DB::raw("z.*,c.name as company_name"))
            ->where('z.is_active', 1)
            ->where('z.admin_id', $admin_id)
            ->orderBy('z.zone_id', 'desc')
            ->get();
        return $retObj;
    }


    public function saveTransaction($request_id, $amount)
    {
        $id = DB::table('payment_transaction')->insertGetId(
            [
                'request_id' => $request_id,
                'amount' => $amount,
                'created_at' => date('Y-m-d H:i:s')
            ]
        );
        return $id;
    }

    public function updateTransaction($id, $status, $referenceId, $utr, $json,$message)
    {
        DB::table('payment_transaction')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'referenceId' => $referenceId,
                'json' => $json,
                'message' => $message,
                'utr' => $utr
            ]);
    }
}
