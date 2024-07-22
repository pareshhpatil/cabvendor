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

class Bill extends Model
{

    public function getTransactionList($admin_id)
    {
        $retObj = DB::table('transaction as a')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'a.employee_id')
            ->join('paymentsource as p', 'p.paymentsource_id', '=', 'a.source_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->where('a.status', 1)
            ->select(DB::raw('a.*,e.name as employee_name,p.name as payment_source'))
            ->get();
        return $retObj;
    }

    public function getIncomeList($admin_id)
    {
        $retObj = DB::table('income_payment as a')
            ->leftJoin('company as e', 'e.company_id', '=', 'a.company_id')
            ->join('paymentsource as p', 'p.paymentsource_id', '=', 'a.source_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->select(DB::raw('a.*,e.name as company_name,p.name as payment_source'))
            ->get();
        return $retObj;
    }

    public function getStatement($source_id)
    {
        $retObj = DB::table('transaction as a')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'a.employee_id')
            ->where('a.source_id', $source_id)
            ->where('a.is_active', 1)
            ->where('a.status', 1)
            ->select(DB::raw('a.*,concat(e.name,' - ',e.account_holder_name) as employee_name'))
            ->get();
        return $retObj;
    }

    public function getRequestList($admin_id)
    {
        $retObj = DB::table('request as a')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'a.employee_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->select(DB::raw("a.*,concat(e.name,'-',e.account_holder_name) as employee_name"))
            ->get();
        return $retObj;
    }

    public function getPendingRequest($company_id = 0, $admin_id)
    {
        $retObj = DB::table('request as a')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'a.employee_id')
            ->leftJoin('company as c', 'c.company_id', '=', 'a.company_id')
            ->where('a.adjust_status', 0)
            ->where('a.pending_amount', '>', 0)
            ->where('a.is_active', 1)
            ->where('a.admin_id', $admin_id);
        if ($company_id > 0) {
            $retObj =  $retObj->where('a.company_id', $company_id);
        }
        $retObj =  $retObj->select(DB::raw("a.*,concat(e.name,'-',e.account_holder_name) as employee_name,c.name as company_name"))
            ->get();
        return $retObj;
    }

    public function getBillList($admin_id)
    {
        $retObj = DB::table('transaction as a')
            ->join('employee as v', 'v.employee_id', '=', 'a.employee_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->where('a.status', 0)
            ->select(DB::raw("a.*,v.name,v.account_no,v.account_holder_name"))
            ->get();
        return $retObj;
    }
    public function getBillListGroup($admin_id)
    {
        $retObj = DB::table('transaction as a')
            ->join('employee as v', 'v.employee_id', '=', 'a.employee_id')
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->where('a.status', 0)
            ->groupBy(['employee_id', 'paid_date'])
            ->select(DB::raw('paid_date,a.employee_id,group_concat(a.narrative) as narrative,sum(amount) as amount,group_concat(v.name) as name,max(transaction_id) as transaction_id'))
            ->get();
        return $retObj;
    }

    public function saveBill($employee_id, $category, $company_id, $vehicle_id, $pending_amount, $remark, $date, $amount, $user_id, $admin_id, $bill_month = '2014-01-01')
    {
        $id = DB::table('request')->insertGetId(
            [
                'admin_id' => $admin_id,
                'employee_id' => $employee_id,
                'category' => $category,
                'company_id' => $company_id,
                'vehicle_id' => $vehicle_id,
                'pending_amount' => $pending_amount,
                'date' => $date,
                'bill_month' => $bill_month,
                'amount' => $amount,
                'note' => $remark,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveCredit($from_id, $to_id, $remark, $date, $amount, $user_id, $admin_id)
    {
        $id = DB::table('payment_credit')->insertGetId(
            [
                'admin_id' => $admin_id,
                'from_id' => $from_id,
                'source_id' => $to_id,
                'paid_date' => $date,
                'amount' => $amount,
                'narrative' => $remark,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function saveTransaction($status, $employee_id, $date, $amount, $payment_mode, $remark, $ref_no, $payment_source, $type, $user_id, $admin_id)
    {
        $id = DB::table('transaction')->insertGetId(
            [
                'admin_id' => $admin_id,
                'status' => $status,
                'employee_id' => $employee_id,
                'amount' => $amount,
                'paid_date' => $date,
                'narrative' => $remark,
                'payment_mode' => $payment_mode,
                'ref_no' => $ref_no,
                'source_id' => $payment_source,
                'type' => $type,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateTransaction($status, $id, $amount, $payment_mode, $source_id, $date, $user_id)
    {
        DB::table('transaction')
            ->where('transaction_id', $id)
            ->update([
                'status' => $status,
                'payment_mode' => $payment_mode,
                'source_id' => $source_id,
                'amount' => $amount,
                'paid_date' => $date,
                'last_update_by' => $user_id
            ]);
    }


    public function saveIncome($company_id, $source_id, $amount, $date, $payment_mode, $narrative, $user_id, $admin_id)
    {
        $id = DB::table('income_payment')->insertGetId(
            [
                'company_id' => $company_id,
                'source_id' => $source_id,
                'amount' => $amount,
                'date' => $date,
                'payment_mode' => $payment_mode,
                'narrative' => $narrative,
                'admin_id' => $admin_id,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function invoicePayment($income_id, $invoice_id, $tds, $amount, $user_id)
    {
        $id = DB::table('invoice_payment')->insertGetId(
            [
                'income_id' => $income_id,
                'invoice_id' => $invoice_id,
                'tds' => $tds,
                'amount' => $amount,
                'total_amount' => $amount + $tds,
                'invoice_amount' => $amount + $tds,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }


    public function getEMPSubscriptionList($admin_id)
    {
        $retObj = DB::table('subscription as a')
            ->join('employee as ea', 'ea.employee_id', '=', 'a.employee_id')
            ->select(DB::raw('a.*,ea.name as employee_name'))
            ->where('a.admin_id', $admin_id)
            ->where('a.is_active', 1)
            ->get();
        return $retObj;
    }

    public function saveSubscription($employee_id, $company_id, $type, $category,  $mode, $repeat_every, $day, $amount, $note, $admin_id, $user_id)
    {
        $id = DB::table('subscription')->insertGetId(
            [
                'employee_id' => $employee_id,
                'company_id' => $company_id,
                'category' => $category,
                'mode' => $mode,
                'repeat_every' => $repeat_every,
                'type' => $type,
                'day' => $day,
                'amount' => $amount,
                'note' => $note,
                'admin_id' => $admin_id,
                'created_by' => $user_id,
                'created_date' => date('Y-m-d H:i:s'),
                'last_update_by' => $user_id
            ]
        );
        return $id;
    }

    public function updateSubscription($subscription_id, $day, $amount, $note, $user_id)
    {
        DB::table('subscription')
            ->where('subscription_id', $subscription_id)
            ->update([
                'day' => $day,
                'amount' => $amount,
                'note' => $note,
                'last_update_by' => $user_id
            ]);
    }
	
	public function updateWhatsappStatus($id, $status,$time)
    {
        DB::table('whatsapp_messages')
            ->where('message_id', $id)
            ->update([
                'status' => $status,
                'last_update_date' => date('Y-m-d H:i:s', $time)
            ]);
    }
	
	 public function saveWhatsapp($mobile, $name, $type, $status, $message_type, $message, $message_id)
    {
        $id = DB::table('whatsapp_messages')->insertGetId(
            [
                'mobile' => $mobile,
                'name' => $name,
                'type' => $type,
                'status' => $status,
                'message_type' => $message_type,
                'message' => $message,
                'message_id' => $message_id,
                'created_date' => date('Y-m-d H:i:s')
            ]
        );
        return $id;
    }
}
