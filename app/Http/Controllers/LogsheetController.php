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

use App\Model\Logsheet;
use App\Model\Master;
use App\Model\Bill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use PDF;
use Excel;

class LogsheetController extends Controller
{

    private $logsheet_model;
    private $master_model;

    function __construct()
    {
        parent::__construct();

        $this->logsheet_model = new Logsheet();
        $this->master_model = new Master();
    }

    public function logsheet()
    {
        $this->validateSession(array(1, 2));
        if (isset($_POST['type'])) {
            $type = $_POST['type'];
        } else {
            $type = 1;
        }

        if (isset($_POST['company_id'])) {
            $company_id = $_POST['company_id'];
        } else {
            $company_id = 0;
        }

        if ($type == 1) {
            $ttype = 'is_paid';
            $val = 0;
        } elseif ($type == 2) {
            $ttype = 'gst_paid';
            $val = 0;
        } else {
            $ttype = 'is_active';
            $val = 1;
        }
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $invoice_list = $this->logsheet_model->getLogsheetBill($this->admin_id, $ttype, $val, $company_id);
        $int = 0;
        foreach ($invoice_list as $item) {
            if ($type == 2 && $item->invoice_number == '') {
                unset($invoice_list[$int]);
            } else {
                $link = $this->encrypt->encode($item->invoice_id);
                $invoice_list[$int]->link = $link;
            }
            $int++;
        }



        $data['type'] = $type;
        $data['title'] = 'Invoices';
        $data['current_date'] = date('d-m-Y');
        $data['company_id'] = $company_id;
        $data['vehicle_list'] = $vehicle_list;
        $data['company_list'] = $company_list;
        $data['invoice_list'] = $invoice_list;
        $data['user_type'] = $this->user_type;
        return view('logsheet.list', $data);
    }

    public function printlogsheet($link)
    {
        $this->validateSession(array(1));
        $data['link'] = $link;
        $id = $this->encrypt->decode($link);
        $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
        $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');
        $data['invoice'] = $invoice;
        $data['vehicle_id'] = $invoice->vehicle_id;
        $data['company_id'] = $invoice->company_id;
        $data['work_order_no'] = $invoice->work_order_no;
        $data['po_number'] = $invoice->po_number;
        $data['month'] = date('M-Y', strtotime($invoice->date));
        $date = $invoice->date;

        $type = 1;
        $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
        $int = 0;
        foreach ($list as $item) {
            $type = $item->type;
            $link = $this->encrypt->encode($item->logsheet_id);
            $list[$int]->link = $link;
            if (substr($item->total_time, 0, 1) == '-') {
                $datetime1 = strtotime($item->date . ' ' . $item->start_time);
                $todate = date('Y-m-d', strtotime($item->date . ' + 1 days'));
                $datetime2 = strtotime($todate . ' ' . $item->close_time);
                $interval = $datetime2 - $datetime1;

                $total_time = round($interval / 60 / 60, 2);
                $extra_time = $total_time - 12;
                if (substr($total_time, -2) == '.5') {
                    $total_time = substr($total_time, 0, -2) . ':30';
                } else if (substr($total_time, -2) == '75') {
                    $total_time = substr($total_time, 0, -3) . ':45';
                } else {
                    $total_time = $total_time . ':00';
                }

                if (substr($extra_time, -2) == '.5') {
                    $extra_time = substr($extra_time, 0, -2) . ':30';
                } else if (substr($extra_time, -2) == '75') {
                    $extra_time = substr($extra_time, 0, -3) . ':45';
                } else {
                    $extra_time = $extra_time . ':00';
                }
                $list[$int]->total_time = $total_time;
                $list[$int]->extra_time = $extra_time;
                if ($item->day_night == 'Night') {
                    $list[$int]->extra_time = '00:00';
                }
            }
            $int++;
        }
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
        $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
        $logsheet_detail = json_decode(json_encode($logsheet_detail), true);
        $data['logsheet_detail'] = $logsheet_detail;
        $data['vehicle'] = $vehicle;
        $data['company'] = $company;
        $data['list'] = $list;
        $data['title'] = 'Logsheet Print';
        $data['type'] = $type;
        $type = ($type == 2) ? 'location' : 'default';
        return view('logsheet.' . $type . '_excel', $data);
    }

    public function printbill($link)
    {
        $this->validateSession(array(1));
        $id = $this->encrypt->decode($link);
        $data['link'] = $link;
        $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
        $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');
        $admin = $this->master_model->getMasterDetail('admin', 'admin_id', $this->admin_id);
        $data['admin'] = $admin;
        $data['invoice'] = $invoice;

        $data['vehicle_id'] = $invoice->vehicle_id;
        $data['company_id'] = $invoice->company_id;
        $data['work_order_no'] = $invoice->work_order_no;
        $data['po_number'] = $invoice->po_number;
        $data['month'] = date('M-Y', strtotime($invoice->date));
        $date = $invoice->date;

        $type = 1;
        $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
        $int = 0;
        foreach ($list as $item) {
            $type = $item->type;
            $link = $this->encrypt->encode($item->logsheet_id);
            $list[$int]->link = $link;
            $int++;
        }

        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
        $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
        $logsheet_detail = json_decode(json_encode($logsheet_detail), true);
        $data['logsheet_detail'] = $logsheet_detail;
        $data['vehicle'] = $vehicle;
        $data['company'] = $company;
        $data['word_money'] = $this->displaywords($invoice->grand_total);
        $data['list'] = $list;
        $data['title'] = 'Bill Print';
        $data['type'] = $type;

        if ($this->admin_id == 3) {
            return view('logsheet.bill_idea', $data);
        } else {
            return view('logsheet.bill', $data);
        }
    }

    public function download($link)
    {
        $this->downloadbill($link);
        $this->downloadlogsheet($link);
    }

    public function downloadbill($link, $admin_id = 0)
    {
        if ($admin_id > 0) {
            $this->admin_id = $admin_id;
            $admin = $this->master_model->getMasterDetail('admin', 'admin_id', $this->admin_id);
            $data['company_name'] = $admin->company_name;
            $data['company_logo'] = 'https://admin.svktrv.in/dist/img/1704035064.png';
            $link = $link / 1234;
        } else {
            $this->validateSession(array(1));
            $admin = $this->master_model->getMasterDetail('admin', 'admin_id', $this->admin_id);
        }

        $id = $this->encrypt->decode($link);
        $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
        $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');

        $data['admin'] = $admin;
        $data['invoice'] = $invoice;
        $data['vehicle_id'] = $invoice->vehicle_id;
        $data['company_id'] = $invoice->company_id;
        $data['work_order_no'] = $invoice->work_order_no;
        $data['po_number'] = $invoice->po_number;
        $data['month'] = date('M-Y', strtotime($invoice->date));
        $date = $invoice->date;

        $type = 1;
        $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
        $int = 0;
        foreach ($list as $item) {
            $type = $item->type;
            $link = $this->encrypt->encode($item->logsheet_id);
            $list[$int]->link = $link;
            $int++;
        }
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
        $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
        $logsheet_detail = json_decode(json_encode($logsheet_detail), true);
        $data['logsheet_detail'] = $logsheet_detail;
        $data['vehicle'] = $vehicle;
        $data['company'] = $company;
        $data['word_money'] = $this->displaywords($invoice->grand_total);
        $data['list'] = $list;
        $data['title'] = 'Download Bill';
        $data['type'] = $type;
        if (isset($_SERVER['QUERY_STRING'])) {
            $data['po'] = $_SERVER['QUERY_STRING'];
        } else {
            $data['po'] = '';
        }

        $type = ($type == 2) ? 'location' : 'default';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => '',
            'format' => 'A4',
            'default_font_size' => 0,
            'default_font' => '',
            'margin_left' => 4,
            'margin_right' => 4,
            'margin_bottom' => 4,
            'margin_header' => 9,
            'margin_footer' => 9,
        ]);
        $mpdf->WriteHTML(\View::make('pdf.bill2')->with($data)->render());
        $mpdf->Output('bill_' . $vehicle->name . '_' . $invoice->invoice_number . '.pdf', 'D');
        die();




        $pdf = PDF::loadView('pdf.bill', $data);

        return $pdf->download('bill_' . $vehicle->name . '_' . $invoice->invoice_number . '.pdf');
    }


    public function exportExcel($column, $name)
    {
        try {

            Excel::create($name, function ($excel) use ($column) {
                foreach ($column as $sheet_name => $cols) {

                    $excel->sheet($sheet_name, function ($sheet) use ($cols) {
                        $sheet->fromArray($cols);
                        if (!empty($cols)) {
                            $sheet->row(1, function ($row) {
                                // call cell manipulation methods
                                $row->setBackground('#2874A6');
                                $row->setFontColor('#ffffff');
                            });
                        }
                        $sheet->freezeFirstRow();
                        $sheet->setAutoSize(true);
                    });
                }
            })->export('xlsx');
        } catch (Exception $e) {
        }
    }

    public function storeExcel($column, $name)
    {
        try {

            Excel::create($name, function ($excel) use ($column) {
                foreach ($column as $sheet_name => $cols) {

                    $excel->sheet($sheet_name, function ($sheet) use ($cols) {
                        $sheet->fromArray($cols);
                        if (!empty($cols)) {
                            $sheet->row(1, function ($row) {
                                // call cell manipulation methods
                                $row->setBackground('#2874A6');
                                $row->setFontColor('#ffffff');
                            });
                        }
                        $sheet->freezeFirstRow();
                        $sheet->setAutoSize(true);
                    });
                }
            })->store('xlsx', 'uploads/excel');
        } catch (Exception $e) {
        }
    }

    public function downloadexcel($month)
    {
        $this->validateSession(array(1));
        //$array=array(41,200,203);
        //$id=$array[2];
        $ids = $this->logsheet_model->getMonthVehicle($month);
        foreach ($ids as $idrow) {
            $id = $idrow->vehicle_id;
            if ($idrow->company_id == 8) {
                $comp = 'T-';
            } else {
                $comp = 'S-';
            }
            $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $id);
            $type = 1;
            $list = $this->logsheet_model->getMonthLogsheet($id, $month);
            $int = 0;
            $export = array();
            foreach ($list as $key => $item) {
                $export[$vehicle->number][$key]['Cab Number'] = $vehicle->number;
                $export[$vehicle->number][$key]['Date'] = date('d/m/Y', strtotime($item->date));
                $export[$vehicle->number][$key]['Start KM'] = $item->start_km;
                $export[$vehicle->number][$key]['End KM'] = $item->end_km;
                $export[$vehicle->number][$key]['Total KM'] = $item->total_km;
                $export[$vehicle->number][$key]['Start Time'] = date('h:i a', strtotime($item->start_time));
                $export[$vehicle->number][$key]['Close time'] = date('h:i a', strtotime($item->close_time));
                $extra_time = $item->extra_time;
                $total_time = $item->total_time;
                if (substr($item->total_time, 0, 1) == '-') {
                    $datetime1 = strtotime($item->date . ' ' . $item->start_time);
                    $todate = date('Y-m-d', strtotime($item->date . ' + 1 days'));
                    $datetime2 = strtotime($todate . ' ' . $item->close_time);
                    $interval = $datetime2 - $datetime1;

                    $total_time = round($interval / 60 / 60, 2);
                    $extra_time = $total_time - 12;
                    if (substr($total_time, -2) == '.5') {
                        $total_time = substr($total_time, 0, -2) . ':30';
                    } else {
                        $total_time = $total_time . ':00';
                    }

                    if (substr($extra_time, -2) == '.5') {
                        $extra_time = substr($extra_time, 0, -2) . ':30';
                    } else {
                        $extra_time = $extra_time . ':00';
                    }
                    //$list[$int]->total_time = $total_time;
                    //$list[$int]->extra_time = $extra_time;
                    if ($item->day_night == 'Night') {
                        $extra_time = '00:00';
                    }
                }
                $export[$vehicle->number][$key]['Total Hour'] = $total_time;
                $export[$vehicle->number][$key]['Extra Hour'] = $extra_time;
                $export[$vehicle->number][$key]['Toll Amount'] = $item->toll;
                $export[$vehicle->number][$key]['Remark'] = $item->remark;
                $export[$vehicle->number][$key]['Day/Night'] = $item->day_night;
            }
            if (!empty($export)) {
                $this->storeExcel($export, $comp . 'Sep-2020 ' . $vehicle->number);
            }
        }
    }

    public function downloadlogsheet($link)
    {
        $this->validateSession(array(1));
        $id = $this->encrypt->decode($link);
        $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
        $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');
        $data['invoice'] = $invoice;
        $data['vehicle_id'] = $invoice->vehicle_id;
        $data['company_id'] = $invoice->company_id;
        $data['work_order_no'] = $invoice->work_order_no;
        $data['po_number'] = $invoice->po_number;
        $data['month'] = date('M-Y', strtotime($invoice->date));
        $date = $invoice->date;

        $type = 1;
        $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
        $int = 0;
        foreach ($list as $item) {
            $type = $item->type;
            $link = $this->encrypt->encode($item->logsheet_id);
            $list[$int]->link = $link;
            if (substr($item->total_time, 0, 1) == '-') {
                $datetime1 = strtotime($item->date . ' ' . $item->start_time);
                $todate = date('Y-m-d', strtotime($item->date . ' + 1 days'));
                $datetime2 = strtotime($todate . ' ' . $item->close_time);
                $interval = $datetime2 - $datetime1;

                $total_time = round($interval / 60 / 60, 2);
                $extra_time = $total_time - 12;
                if (substr($total_time, -2) == '.5') {
                    $total_time = substr($total_time, 0, -2) . ':30';
                } elseif (substr($total_time, -3) == '.75') {
                    $total_time = substr($total_time, 0, -3) + 1 . ':15';
                } else {
                    $total_time = $total_time . ':00';
                }

                if (substr($extra_time, -2) == '.5') {
                    $extra_time = substr($extra_time, 0, -2) . ':30';
                } elseif (substr($extra_time, -3) == '.75') {
                    $extra_time = substr($extra_time, 0, -3) + 1 . ':15';
                } else {
                    $extra_time = $extra_time . ':00';
                }
                $list[$int]->total_time = $total_time;
                $list[$int]->extra_time = $extra_time;
                if ($item->day_night == 'Night') {
                    $list[$int]->extra_time = '00:00';
                }
            }
            $int++;
        }
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
        $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
        $logsheet_detail = json_decode(json_encode($logsheet_detail), true);
        $data['logsheet_detail'] = $logsheet_detail;
        $data['vehicle'] = $vehicle;
        $data['company'] = $company;
        $data['list'] = $list;
        $data['title'] = 'Download Logsheet';
        $data['type'] = $type;
        $type = ($type == 2) ? 'location' : 'default';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML(\View::make('pdf.logsheet2')->with($data)->render());
        $mpdf->Output('logsheet_' . $vehicle->name . '_' . $invoice->invoice_number . '.pdf', 'D');
        die();
        //$pdf = PDF::loadView('pdf.logsheet', $data);
        //return $pdf->download('logsheet_' . $vehicle->name . '_' . $invoice->invoice_number . '.pdf');
    }

    public function logsheetbillsave(Request $request)
    {
        $this->validateSession(array(1, 2));
        $date = date('Y-m-d', strtotime($request->date));
        $bill_date = date('Y-m-d', strtotime($request->bill_date));

        if ($this->admin_id == 3) {
            $toll = ($_POST['amount'][count($_POST['amount']) - 1] > 0) ? $_POST['amount'][count($_POST['amount']) - 1] : 0;
        } else {
            $toll = ($_POST['amount'][count($_POST['amount']) - 1] > 0) ? $_POST['amount'][count($_POST['amount']) - 1] : 0;
        }

        if ($request->invoice_id > 0) {
            $invoice_id = $request->invoice_id;
            $invoice_id = $this->logsheet_model->updateLogsheetInvoice($invoice_id, $request->vehicle_id, $request->company_id, $date, $bill_date, $request->cgst, $request->sgst, $request->igst, $request->total_gst, $request->base_total, $request->grand_total, $toll, $request->type, $request->work_order_no, $request->po_number, $this->user_id, $this->admin_id);
        } else {
            $invoice_number = $this->logsheet_model->getInvoiceNumber($request->invoice_seq);
            $invoice_id = $this->logsheet_model->saveLogsheetInvoice($invoice_number, $request->vehicle_id, $request->company_id, $date, $bill_date, $request->cgst, $request->sgst, $request->igst, $request->total_gst, $request->base_total, $request->grand_total, $toll, $request->type, $request->work_order_no, $request->po_number, $this->user_id, $this->admin_id);
        }

        $this->master_model->updateTableColumn('logsheet_invoice', 'narrative', $_POST['narrative'], 'invoice_id', $invoice_id, $this->user_id);
        $int = 0;
        foreach ($_POST['int'] as $row) {
            if ($_POST['detail_id'][$int] > 0) {
                $this->logsheet_model->updateLogsheetDetail($_POST['detail_id'][$int], $_POST['particular_name'][$int], $_POST['unit'][$int], $_POST['qty'][$int], $_POST['rate'][$int], $_POST['amount'][$int], $_POST['is_deduct'][$int], $this->user_id);
            } else {
                $this->logsheet_model->saveLogsheetDetail($invoice_id, $_POST['particular_name'][$int], $_POST['unit'][$int], $_POST['qty'][$int], $_POST['rate'][$int], $_POST['amount'][$int], $_POST['is_deduct'][$int], $this->user_id);
            }
            $int++;
        }
        if ($_POST['expense_amount'] > 0) {
            foreach ($_POST['rcheck'] as $req_id) {
                $amount = $_POST['req_' . $req_id];
                $this->logsheet_model->saveInvoiceExpense($invoice_id, $req_id, $amount, $this->user_id);
                $reqdet = $this->master_model->getMasterDetail('request', 'request_id', $req_id);
                $amount = $reqdet->pending_amount - $amount;
                $this->master_model->updateTableColumn('request', 'pending_amount', $amount, 'request_id', $req_id, $this->user_id);
                if ($amount == 0) {
                    $this->master_model->updateTableColumn('request', 'adjust_status', 1, 'request_id', $req_id, $this->user_id);
                }
                $this->master_model->updateTableColumn('logsheet_invoice', 'expense_amount', $_POST['expense_amount'], 'invoice_id', $invoice_id, $this->user_id);
            }
        }

        if ($_POST['trip_ids'] != '') {
            $this->master_model->updateTableColumn('trip', 'billed', 1, 'trip_id', json_decode($_POST['trip_ids'], 1), $this->user_id);
        }

        $this->setSuccess('Logsheet Bill has been save successfully');
        header('Location: /admin/logsheet');
        exit;
    }

    public function approve()
    {
        $this->validateSession(array(1, 2));
        if (isset($_POST['item'])) {
            foreach ($_POST['item'] as $id) {
                if (isset($_POST['delete'])) {
                    $this->master_model->deleteReccord('logsheet_bill', 'logsheet_id', $id, $this->user_id);
                } else {
                    $this->logsheet_model->approveLogsheetDetail($id, $this->user_id);
                }
            }
        }
        $list = $this->logsheet_model->getPendingBillData($this->admin_id);
        $data['list'] = $list;
        $data['title'] = 'Approve bill';
        return view('employee.default_bill', $data);
    }

    public function generatebill($link = null)
    {
        $this->validateSession(array(1));
        $id = 0;
        $data['admin_id'] = $this->admin_id;
        $data['work_order_no'] = '';
        $data['po_number'] = '';
        $data['narrative'] = '';
        if (isset($_POST['vehicle_id'])) {
            $data['vehicle_id'] = $_POST['vehicle_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['month'] = $_POST['date'];
            $date = date('Y-m-d', strtotime('01-' . $_POST['date']));
            //$id = $this->logsheet_model->getInvoiceId($_POST['company_id'], $_POST['vehicle_id'], $date, $this->admin_id);
        } else {
            $data['vehicle_id'] = 0;
            $data['company_id'] = 0;
            $data['month'] = '';
        }
        $data['bill_type'] = 'fixed';
        if ($link == 'casual') {
            $data['bill_type'] = 'casual';
            $link = null;
        }

        if ($link != null) {
            $id = $this->encrypt->decode($link);
        }
        if ($id > 0) {
            $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
            $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');
            $data['invoice'] = $invoice;
            $data['vehicle_id'] = $invoice->vehicle_id;
            $data['company_id'] = $invoice->company_id;
            $data['work_order_no'] = $invoice->work_order_no;
            $data['po_number'] = $invoice->po_number;
            $data['narrative'] = $invoice->narrative;
            $data['month'] = date('M-Y', strtotime($invoice->date));
            $date = $invoice->date;
        }

        $type = 1;
        $list = [];
        if ($data['bill_type'] == 'casual' && $data['company_id'] > 0 && empty($_POST['trip_id'])) {
            $casual_list = $this->logsheet_model->getCasualPendingBills($data['company_id']);
            $data['casual_list'] = $casual_list;
            $data['invoice_id'] = 0;
        }

        if ($data['vehicle_id'] > 0) {
            $sequence = $this->master_model->getMaster('sequence', $this->admin_id);
            $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
            $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);
            $data['vehicle'] = $vehicle;
            $data['sequence'] = $sequence;
            $data['company'] = $company;
        }


        $packages = [];
        $extrakm = [];
        $extrahour = [];
        $driverallownce = 0;
        $toll_parking = 0;
        $data['trip_ids'] = '';
        if (!empty($_POST['trip_id'])) {
            foreach ($_POST['trip_id'] as $trip_id) {
                $array['vehicle_type'] = $_POST['vehicle_type' . $trip_id];
                $array['package_name'] = $_POST['package_name' . $trip_id];
                $array['package_amount'] = $_POST['package_amount' . $trip_id];
                $array['extra_km'] = $_POST['extra_km' . $trip_id];
                $array['extra_km_rate'] = $_POST['extra_km_rate' . $trip_id];
                $array['extra_hour'] = $_POST['extra_hour' . $trip_id];
                $array['extra_hour_rate'] = $_POST['extra_hour_rate' . $trip_id];
                $array['driver_amount'] = $_POST['driver_amount' . $trip_id];
                $array['toll_parking'] = $_POST['toll_parking' . $trip_id];
                $driverallownce = $driverallownce + $array['driver_amount'];
                $toll_parking = $toll_parking + $array['toll_parking'];


                if (isset($packages[$array['package_name'] . $array['package_amount']])) {
                    $parray = $packages[$array['package_name'] . $array['package_amount']];
                    $parray['qty'] = $parray['qty'] + 1;
                    $parray['amount'] = $parray['amount'] + $array['package_amount'];
                    $packages[$array['package_name'] . $array['package_amount']] = $parray;
                } else {
                    $packages[$array['package_name'] . $array['package_amount']] =
                        array('particular_name' => $array['package_name'], 'unit' => 'Trip', 'qty' => 1, 'rate' => $array['package_amount'], 'amount' => $array['package_amount'], 'is_deduct' => '0', 'id' => 0);
                }

                if ($array['extra_km'] > 0) {
                    if (isset($extrakm[$array['vehicle_type'] . $array['extra_km_rate']])) {
                        $parray = $extrakm[$array['vehicle_type'] . $array['extra_km_rate']];
                        $parray['qty'] = $parray['qty'] + $array['extra_km'];
                        $extra_km_amount = $array['extra_km'] * $array['extra_km_rate'];
                        $parray['amount'] = $parray['amount'] + $extra_km_amount;
                        $extrakm[$array['vehicle_type'] . $array['extra_km_rate']] = $parray;
                    } else {
                        $extrakm[$array['vehicle_type'] . $array['extra_km_rate']] =
                            array('particular_name' => $array['vehicle_type'] . ' Extra KM', 'unit' => 'KM', 'qty' => $array['extra_km'], 'rate' => $array['extra_km_rate'], 'amount' => $array['extra_km'] * $array['extra_km_rate'], 'is_deduct' => '0', 'id' => 0);
                    }
                }

                if ($array['extra_hour'] > 0) {
                    if (isset($extrahour[$array['vehicle_type'] . $array['extra_hour_rate']])) {
                        $parray = $extrahour[$array['vehicle_type'] . $array['extra_hour_rate']];
                        $parray['qty'] = $parray['qty'] + $array['extra_hour'];
                        $extra_hour_amount = $array['extra_hour'] * $array['extra_hour_rate'];
                        $parray['amount'] = $parray['amount'] + $extra_hour_amount;
                        $extrahour[$array['vehicle_type'] . $array['extra_hour_rate']] = $parray;
                    } else {
                        $extrahour[$array['vehicle_type'] . $array['extra_hour_rate']] =
                            array('particular_name' => $array['vehicle_type'] . ' Extra Hour', 'unit' => 'Hour', 'qty' => $array['extra_hour'], 'rate' => $array['extra_hour_rate'], 'amount' => $array['extra_hour'] * $array['extra_hour_rate'], 'is_deduct' => '0', 'id' => 0);
                    }
                }
            }

            foreach ($packages as $row) {
                $logsheet_detail[] = $row;
            }

            foreach ($extrakm as $row) {
                $logsheet_detail[] = $row;
            }

            foreach ($extrahour as $row) {
                $logsheet_detail[] = $row;
            }

            if ($driverallownce > 0) {
                $logsheet_detail[] = array('particular_name' => 'Driver allowance', 'unit' => 'DA', 'qty' => '1', 'rate' => $driverallownce, 'amount' => $driverallownce, 'is_deduct' => '0', 'id' => 0);
            }
            if ($toll_parking > 0) {
                $logsheet_detail[] = array('particular_name' => 'Toll /Parking', 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => $toll_parking, 'is_deduct' => '0', 'id' => 0);
            }
            $data['list'] = [];
            $data['logsheet_detail'] = $logsheet_detail;
            $data['bill_type'] = 'fixed';
            $data['trip_ids'] = json_encode($_POST['trip_id']);
        }


        if ($data['vehicle_id'] > 0 && $data['bill_type'] != 'casual') {
            $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
            $int = 0;
            foreach ($list as $k => $item) {

                $type = $item->type;
                $link2 = $this->encrypt->encode($item->logsheet_id);
                if (substr($item->total_time, 0, 1) == '-') {
                    $datetime1 = strtotime($item->date . ' ' . $item->start_time);
                    $todate = date('Y-m-d', strtotime($item->date . ' + 1 days'));
                    $datetime2 = strtotime($todate . ' ' . $item->close_time);
                    $interval = $datetime2 - $datetime1;

                    $total_time = round($interval / 60 / 60, 2);

                    $extra_time = $total_time - 12;

                    if (substr($total_time, -2) == '.5') {
                        $total_time = substr($total_time, 0, -2) . ':30';
                    } elseif (substr($total_time, -3) == '.75') {
                        $total_time = substr($total_time, 0, -3) + 1 . ':15';
                    } else {
                        $total_time = $total_time . ':00';
                    }


                    if (substr($extra_time, -2) == '.5') {
                        $extra_time = substr($extra_time, 0, -2) . ':30';
                    } elseif (substr($extra_time, -3) == '.75') {
                        $extra_time = substr($extra_time, 0, -3) + 1 . ':15';
                    } else {
                        $extra_time = $extra_time . ':00';
                    }
                    $list[$int]->total_time = $total_time;
                    $list[$int]->extra_time = $extra_time;
                }
                if ($item->day_night == 'Night') {
                    $list[$int]->extra_time = '00:00';
                }
                $list[$int]->link = $link2;
                $int++;
            }


            if (empty($logsheet_detail)) {
                $total_days = date(' t ', strtotime($date));
                if ($this->admin_id == 3) {
                    $array[] = array('particular_name' => 'Idea Cellular Limited, MUMBAI ' . $data['month'], 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => $vehicle->number, 'unit' => '3000', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => date('d-m-Y', strtotime($date)) . ' to ' . $total_days . date('-m-Y', strtotime($date)), 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => '3000kms /26day/12hrs', 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => ' EXTRA KMS', 'unit' => '', 'qty' => '', 'rate' => '10.00', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => ' EXTRA HOURS', 'unit' => '', 'qty' => '', 'rate' => '100.00', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => ' DEDUCTION (-)', 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '1', 'id' => 0);
                    $array[] = array('particular_name' => 'Toll /Parking', 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                } else {
                    $total_days = (float) $total_days;
                    $day_rate = round(45000 / $total_days, 2);
                    $array[] = array('particular_name' => 'Fixed monthly charges', 'unit' => 'Month', 'qty' => '1', 'rate' => '45000.00', 'amount' => '45000.00', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => 'Extra Day', 'unit' => 'Day', 'qty' => '', 'rate' => $day_rate, 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => 'Extra KM.', 'unit' => 'KM', 'qty' => '', 'rate' => '12.00', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => 'Extra Hour', 'unit' => 'Hour', 'qty' => '', 'rate' => '65.00', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                    $array[] = array('particular_name' => 'Break down', 'unit' => 'Day', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '1', 'id' => 0);
                    $array[] = array('particular_name' => 'Toll /Parking', 'unit' => '', 'qty' => '', 'rate' => '', 'amount' => '', 'is_deduct' => '0', 'id' => 0);
                }
                $logsheet_detail = $array;
            } else {
                $logsheet_detail = json_decode(json_encode($logsheet_detail), true);
            }
            $data['logsheet_detail'] = $logsheet_detail;

            $data['list'] = $list;
        }






        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $expense_list = array();
        if ($data['company_id'] > 0) {
            $bill_model = new Bill();
            $expense_list = $bill_model->getPendingRequest($data['company_id'], $this->admin_id);
        }
        //dd($expense_list);

        $data['title'] = 'Generate Bill';
        $data['vehicle_list'] = $vehicle_list;
        $data['company_list'] = $company_list;
        $data['expense_list'] = $expense_list;
        $data['type'] = $type;
        if ($this->admin_id == 3) {
            $type = 'idea';
        } else {
            $type = ($type == 2) ? 'location' : 'default';
        }
        return view('logsheet.' . $type . '_bill', $data);
    }

    public function getlogsheet()
    {
        $this->validateSession(array(1, 2));
        $id = 0;
        $data['admin_id'] = $this->admin_id;
        if (isset($_POST['vehicle_id'])) {
            $data['vehicle_id'] = $_POST['vehicle_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['month'] = $_POST['date'];
            $date = date('Y-m-d', strtotime('01-' . $_POST['date']));
            $id = $this->logsheet_model->getInvoiceId($_POST['company_id'], $_POST['vehicle_id'], $date, $this->admin_id);
        } else {
            $data['vehicle_id'] = 0;
            $data['company_id'] = 0;
            $data['month'] = '';
        }


        $type = 1;
        if ($data['vehicle_id'] > 0) {
            $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
            $int = 0;
            foreach ($list as $item) {
                $type = $item->type;
                $link = $this->encrypt->encode($item->logsheet_id);
                if (substr($item->total_time, 0, 1) == '-') {
                    $datetime1 = strtotime($item->date . ' ' . $item->start_time);
                    $todate = date('Y-m-d', strtotime($item->date . ' + 1 days'));
                    $datetime2 = strtotime($todate . ' ' . $item->close_time);
                    $interval = $datetime2 - $datetime1;

                    $total_time = round($interval / 60 / 60, 2);
                    $extra_time = $total_time - 12;
                    if (substr($total_time, -2) == '.5') {
                        $total_time = substr($total_time, 0, -2) . ':30';
                    } else {
                        $total_time = $total_time . ':00';
                    }

                    if (substr($extra_time, -2) == '.5') {
                        $extra_time = substr($extra_time, 0, -2) . ':30';
                    } else {
                        $extra_time = $extra_time . ':00';
                    }
                    $list[$int]->total_time = $total_time;
                    $list[$int]->extra_time = $extra_time;
                }
                if ($item->day_night == 'Night') {
                    $list[$int]->extra_time = '00:00';
                }
                $list[$int]->link = $link;
                $int++;
            }
            $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
            $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);

            $logsheet_detail = array();
            $data['logsheet_detail'] = $logsheet_detail;
            $data['vehicle'] = $vehicle;
            $data['company'] = $company;
            $data['list'] = $list;
        }
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $data['title'] = 'Get Logsheet';
        $data['vehicle_list'] = $vehicle_list;
        $data['company_list'] = $company_list;
        $data['type'] = $type;
        $type = 'show';
        return view('logsheet.' . $type . '_bill', $data);
    }

    public function logsheetdelete($link)
    {
        $id = $this->encrypt->decode($link);
        $this->master_model->deleteReccord('logsheet_bill', 'logsheet_id', $id, $this->user_id);
        exit;
    }

    public function confirmLogsheet(Request $request)
    {
        $this->validateSession(array(1, 2));
        $type = $request->type;
        $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $request->vehicle_id);
        $company = $this->master_model->getMasterDetail('company', 'company_id', $request->company_id);
        $toll = ($_POST['toll_amount'] > 0) ? $_POST['toll_amount'] : 0;
        $totalkm = $_POST['end_km'] - $_POST['start_km'];
        echo '<table class="table table-bordered table-condensed" style="margin: 0px 0 0px 0 !important;">';
        echo ' <tr><td><b>Vehicle number:</b></td><td>' . $vehicle->name . '</td></tr>';
        echo ' <tr><td><b>Comapny name:</b></td><td>' . $company->name . '</td></tr>';
        echo ' <tr><td><b>Date:</b></td><td>' . $_POST['date'] . '</td></tr>';
        echo ' <tr><td><b>Start KM:</b></td><td>' . $_POST['start_km'] . '</td></tr>';
        echo ' <tr><td><b>End KM:</b></td><td>' . $_POST['end_km'] . '</td></tr>';
        echo ' <tr><td><b>Total KM:</b></td><td>' . $totalkm . '</td></tr>';
        if ($type == 1) {
            echo ' <tr><td><b>Start Time:</b></td><td>' . date('h:i A', strtotime($_POST['start_time'])) . '</td></tr>';
            echo ' <tr><td><b>Close Time:</b></td><td>' . date('h:i A', strtotime($_POST['close_time'])) . '</td></tr>';
        } else {
            echo ' <tr><td><b>From Location:</b></td><td>' . $_POST['from'] . '</td></tr>';
            echo ' <tr><td><b>To Location:</b></td><td>' . $_POST['to'] . '</td></tr>';
        }
        echo ' <tr><td><b>Toll:</b></td><td>' . $toll . '</td></tr>';
        echo ' <tr><td><b>Remark:</b></td><td>' . $_POST['remark'] . '</td></tr>';
        echo '</tbody></table>';
    }

    public function saveLogsheet(Request $request)
    {
        $this->validateSession(array(1, 2));
        $type = $request->type;
        $pick_drop = $request->pickup;
        $vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
        $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : 0;
        $_POST['toll_amount'] = ($_POST['toll_amount'] > 0) ? $_POST['toll_amount'] : 0;
        $start_time = date('H:i:s', strtotime($request->start_time));
        $close_time = date('H:i:s', strtotime($request->close_time));
        if ($type == 2) {
            $start_time = '00:00:00';
            $close_time = '00:00:00';
        } else {
            $pick_drop = '';
            $_POST['from'] = '';
            $_POST['to'] = '';
        }
        $day_night = (isset($request->day_night)) ? $request->day_night : 'Day';
        if ($this->user_type == 1) {
            $status = 1;
        } else {
            $status = 0;
        }
        $date = date('Y-m-d', strtotime($request->date));
        $result = $this->logsheet_model->saveLogsheetbill($request->vehicle_id, $request->company_id, $date, $request->start_km, $request->end_km, $start_time, $close_time, $day_night, $_POST['remark'], $_POST['toll_amount'], $type, $pick_drop, $_POST['from'], $_POST['to'], $this->user_id, $this->admin_id, $status);
        echo 'Logsheet has been saved successfully';
    }



    public function generateLogsheetMonth($link = null)
    {
        $this->validateSession(array(1));
        $id = 0;
        $data['admin_id'] = $this->admin_id;
        $data['user_id'] = $this->user_id;
        $data['work_order_no'] = '';
        $data['po_number'] = '';
        if (isset($_POST['vehicle_id'])) {
            $data['vehicle_id'] = $_POST['vehicle_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['month'] = $_POST['date'];
            $date = date('Y-m-d', strtotime('01-' . $_POST['date']));
            //$id = $this->logsheet_model->getInvoiceId($_POST['company_id'], $_POST['vehicle_id'], $date, $this->admin_id);
        } else {
            $data['vehicle_id'] = 0;
            $data['company_id'] = 0;
            $data['month'] = '';
        }

        if ($link != null) {
            $id = $this->encrypt->decode($link);
        }
        if ($id > 0) {
            $invoice = $this->master_model->getMasterDetail('logsheet_invoice', 'invoice_id', $id);
            $logsheet_detail = $this->master_model->getMaster('logsheet_detail', $id, 'invoice_id');
            $data['invoice'] = $invoice;
            $data['vehicle_id'] = $invoice->vehicle_id;
            $data['company_id'] = $invoice->company_id;
            $data['work_order_no'] = $invoice->work_order_no;
            $data['po_number'] = $invoice->po_number;
            $data['month'] = date('M-Y', strtotime($invoice->date));
            $date = $invoice->date;
        }

        $type = 1;
        if ($data['vehicle_id'] > 0) {
            $list = $this->logsheet_model->getBillData($date, $data['company_id'], $data['vehicle_id']);
            $int = 0;



            $sequence = $this->master_model->getMaster('sequence', $this->admin_id);
            $vehicle = $this->master_model->getMasterDetail('vehicle', 'vehicle_id', $data['vehicle_id']);
            $company = $this->master_model->getMasterDetail('company', 'company_id', $data['company_id']);


            //$data['logsheet_detail'] = $logsheet_detail;
            $data['vehicle'] = $vehicle;
            $data['sequence'] = $sequence;
            $data['company'] = $company;
            if (count($list) == 0) {
                if ($data['company_id'] == 105) {

                    $lastday = date('t', strtotime($date));
                    for ($i = 26; $i <= $lastday; $i++) {
                        $array['date'] = date("Y-m-" . $i, strtotime($date));
                        $array['holiday'] = $this->isWeekend($array['date']);
                        $array['start_km'] = '';
                        $array['end_km'] = '';
                        $array['total_km'] = '';
                        $array['start_time'] = '09:00';
                        $array['close_time'] = '21:00';
                        $array['total_time'] = '12:00';
                        $array['extra_time'] = '00:00';
                        $array['toll'] = '0';
                        $array['remark'] = ($array['holiday'] == 1) ? 'Sunday' : '';
                        $list[] = $array;
                    }

                    for ($i = 1; $i <= 25; $i++) {
                        $array['date'] = date("Y-m-" . $i, strtotime($date));
                        $array['holiday'] = $this->isWeekend($array['date']);
                        $array['start_km'] = '';
                        $array['end_km'] = '';
                        $array['total_km'] = '';
                        $array['start_time'] = '09:00';
                        $array['close_time'] = '21:00';
                        $array['total_time'] = '12:00';
                        $array['extra_time'] = '00:00';
                        $array['toll'] = '0';
                        $array['remark'] = ($array['holiday'] == 1) ? 'Sunday' : '';
                        $list[] = $array;
                    }
                } else {
                    $lastday = date('t', strtotime($date));
                    for ($i = 1; $i <= $lastday; $i++) {
                        $array['date'] = date("Y-m-" . $i, strtotime($date));
                        $array['holiday'] = $this->isWeekend($array['date']);
                        $array['start_km'] = '';
                        $array['end_km'] = '';
                        $array['total_km'] = '';
                        $array['start_time'] = '09:00';
                        $array['close_time'] = '21:00';
                        $array['total_time'] = '12:00';
                        $array['extra_time'] = '00:00';
                        $array['toll'] = '0';
                        $array['remark'] = ($array['holiday'] == 1) ? 'Sunday' : '';
                        $list[] = $array;
                    }
                }


                $data['list'] = json_decode(json_encode($list));
            } else {
                $data['list'] = $list;
            }
        }
        $vehicle_list = $this->master_model->getMaster('vehicle', $this->admin_id);
        $company_list = $this->master_model->getMaster('company', $this->admin_id);
        $expense_list = array();
        if ($link == null) {
            $bill_model = new Bill();
            $expense_list = $bill_model->getPendingRequest(0, $this->admin_id);
        }

        $data['title'] = 'Generate Monthly Logsheet';
        $data['vehicle_list'] = $vehicle_list;
        $data['company_list'] = $company_list;
        $data['expense_list'] = $expense_list;
        $data['type'] = $type;
        return view('logsheet.month_logsheet', $data);
    }

    public function monthsave(Request $request)
    {
        $data = $request->all();

        foreach ($data['date'] as $key => $a) {
            $row['date'] = $data['date'][$key];
            $row['start_km'] = $data['start_km'][$key];
            $row['end_km'] = $data['end_km'][$key];
            $row['start_time'] = $data['start_time'][$key];
            $row['close_time'] = $data['close_time'][$key];
            $row['total_time'] = $data['total_time'][$key];
            $row['extra_time'] = $data['extra_time'][$key];
            $row['toll'] = $data['toll'][$key];
            $row['remark'] = $data['remark'][$key];
            $row['holiday'] = $data['holiday'][$key];
            $this->saveLogsheetMonth($row, 1);
        }

        $this->setSuccess('Logsheet Bill has been save successfully');
        header('Location: /admin/logsheet/getlogsheet');
        exit;
    }

    public function saveLogsheetMonth($data, $noresponse = 0)
    {
        $request = json_decode(json_encode($data));
        $type = '1';
        $pick_drop = '';
        $vehicle_id = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : 0;
        $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : 0;
        $holiday = isset($request->holiday) ? $request->holiday : 0;
        $toll_amount = ($request->toll > 0) ? $request->toll : 0;
        $start_time = date('H:i:s', strtotime($request->start_time));
        $close_time = date('H:i:s', strtotime($request->close_time));

        $day_night = (isset($request->day_night)) ? $request->day_night : 'Day';
        $status = 1;
        $date = date('Y-m-d', strtotime($request->date));

        $result = $this->logsheet_model->saveLogsheetbill($vehicle_id, $company_id, $date, $request->start_km, $request->end_km, $start_time, $close_time, $day_night, $request->remark, $toll_amount, $type, $pick_drop, '', '', $_POST['user_id'], $_POST['admin_id'], $status, $holiday);
        if ($noresponse == 0) {
            echo 'Logsheet has been saved successfully';
        }
    }



    function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 7);
    }
}
