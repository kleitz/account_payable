<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Printout
 *
 * @author Hendra McHen
 */
class Printout extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('ppdetail_model');
        $this->load->model('payment_voucher_model');
        $this->load->model('cashreturned_model');
        $this->load->model('cashrequest_model');
    }
    
    public function pp($string='') {
        $pp_id = substr($string, 8, strlen($string)); //$this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $info = $this->ppdetail_model->get_payment_process($pp_id);
        if ($info->num_rows()!=0){
            $row = $info->row();
            $data['pp_date'] = isset($row->pp_date)? $this->general_model->get_string_date($row->pp_date): '_____';
            $data['pp_due_date'] = (isset($row->pp_due_date)&& $row->pp_due_date!='0000-00-00')? $this->general_model->get_string_date($row->pp_due_date): '_____';
            $data['pp_number'] = $row->pp_number;
            
            $header1 = 'PROSES PEMBAYARAN';
            $header2 = 'PAYMENT PROCESS';
            
            if($row->supplier_type == 1){
                $header1 = 'PROSES PEMBAYARAN KREDIT SUPPLIER';
                $header2 = 'PAYMENT PROCESS FOR SUPPLIER INSTALLMENT';
            }
            
            if ($row->pp_type == 2){
                $header1 = 'BIAYA KASIR';
                $header2 = 'CASHIER EXPENSES';
            }
            
            $data['header1'] = $header1;
            $data['header2'] = $header2;
            $data['pv_number'] = $row->pv_number;
            $data['pp_title'] = $row->pp_title;
        }
        $data['detail'] = $this->ppdetail_model->get_detail_by_pp_id($pp_id);
        $data['prepared'] = $this->ppdetail_model->get_user_by_id($row->prepare_by);
        $data['checked'] = $this->ppdetail_model->get_user_by_id($row->checked_by);
        $data['approved'] = $this->ppdetail_model->get_user_by_id($row->approved_by);
        $this->load->view('ppdetail/pp_print', $data);
    }
    
    public function pv($string='') {
        $pv_number = '';
        $nota_from = '';
        $pv_title = '';
        $total = 0;
        $description = '';
        $admin_fee = 0;
        $payment_mode = 0;
        $payment_mode_text = '';
        $bank_id = 0;
        $bank_outlet = '';
        $account_name = '';
        $bank_account_name = '';
        $bank_account_num = '';
        $bank_name_to = '';
        $received_name = '';
        $pv_date = '';
        $paid_by = 0;
        $branch_id = 0;
        $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
        $pv_id = substr($string, 8, strlen($string));
        $datapv = $this->payment_voucher_model->get_payment_voucher_by_id($pv_id);
        if ($datapv->num_rows() != 0) {
            $row = $datapv->row();
            $pv_number = $row->pv_number;
            $pv_title = $row->pv_title;
            $pv_date = $row->pv_date;
            $nota_from = $row->nota_from;
            $total = $row->total;
            $branch_id = $row->branch_id;
            $description = $row->description;
            $admin_fee = $row->admin_fee;
            $payment_mode = $row->payment_mode;
            $payment_mode_text = $payment_opt[$row->payment_mode];
            $account_id = $row->account_id == '' ? 0 : $row->account_id;
            $this->load->model('account_model');
            $account = $this->account_model->get_account_by_id($account_id);
            if ($account->num_rows() != 0) {
                $ar = $account->row();
                $account_name = $ar->account_name;
            }
            $bank_id = $row->bank_id == '' ? 0 : $row->bank_id;
            $this->load->model('bank_model');
            $bankdata = $this->bank_model->get_bank_account_by_id($bank_id);
            if ($bankdata->num_rows() != 0) {
                $br = $bankdata->row();
                $bank_outlet = $br->bank_account_name . ' ('.$br->bank_account_no.')';
            }
            $bank_account_name = $row->bank_account_name_to;
            $bank_account_num = $row->bank_account_num_to;
            $bank_name_to = $row->bank_name_to;
            $bank_cek_from = $row->bank_cek_from;
            $bank_bg_from = $row->bank_bg_from;
            $received_name = $row->received_name;
            $paid_by = $row->paid_by;
            if ($row->pp_id != 0) {
                $caption_number = 'PP';
            } else {
                $caption_number = 'CR';
            }
            $data['caption_number'] = $caption_number;
        }
        // daa branch
        $this->load->model('branch_model');
        $databranch = $this->branch_model->get_branch_list();
        $branch_opt = array();
        if ($databranch->num_rows() != 0) {
            foreach ($databranch->result() as $value) {
                $branch_opt[$value->branch_id] = $value->branch_name;
            }
        }
        $data['outlet'] = $branch_opt[$branch_id];

        $data['pv_id'] = $pv_id;
        $data['pv_number'] = $pv_number;
        $data['nota_from'] = $nota_from;
        $data['pv_title'] = $pv_title;
        $data['total'] = $total;
        $data['description'] = $description;
        $data['admin_fee'] = $admin_fee;
        $data['payment_mode'] = $payment_mode;
        $data['payment_mode_text'] = $payment_mode_text;
        $data['bank_id'] = $bank_id;
        $data['bank_outlet'] = $bank_outlet;
        $data['account_name'] = $account_name;
        $data['bank_account_name_to'] = $bank_account_name;
        $data['bank_account_num_to'] = $bank_account_num;
        $data['bank_name_to'] = $bank_name_to;
        $data['received_name'] = $received_name;
        $data['pv_date'] = $pv_date;
        $data['paid_by'] = $paid_by;
        $this->load->view('paymentvoucher/pv_print', $data);
    }
    
    public function cashreturn($string='') {
        $cash_return_id = substr($string, 8, strlen($string));
        $cashreturn = $this->cashreturned_model->get_cash_returned_by_id($cash_return_id);  
        $return_number = '';
        $return_date = '';
        $account_from = '';
        $account_to = '';
        $amount = '';
        $return_mode = '';
        $branch_name = '';
        $remark = '';
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_keyword(0, 'Petty');
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
        if ($cashreturn->num_rows()!=0){
            $row = $cashreturn->row();
            $return_number = $row->cash_return_number;
            $return_date = $this->general_model->get_string_date($row->cash_return_date);
            $account_from = $account_opt[$row->account_from];
            $account_to = $account_opt[$row->account_to];
            $amount = number_format($row->amount);
            $return_mode = $payment_opt[$row->return_mode];
            $branch_name = $row->branch_name;
            $remark = $row->remark;
        }
        $data['return_number'] = $return_number;
        $data['return_date'] = $return_date;
        $data['account_from'] = $account_from;
        $data['account_to'] = $account_to;
        $data['amount'] = $amount;
        $data['return_mode'] = $return_mode;
        $data['branch_name'] = $branch_name;
        $data['remark'] = $remark;

        $this->load->view('cashreturned/cashreturned_print', $data);
    }
    
    public function cashrequest($string='') {
        $cash_request_id = substr($string, 8, strlen($string));
        $detail = $this->cashrequest_model->get_cashrequest_by_id($cash_request_id);
        $description = '';
        $remark = '';
        $cash_request_number = '';
        $cash_request_date = '';
        $employee_name = '';
        $branch_name = '';
        $payment_mode = '';
        $amount = '';
        $cash_request_status = '';
        $prepared = '';
        $checked = '';
        $approved = '';
        if ($detail->num_rows()!=0){
            $row = $detail->row();
            $description = nl2br($row->description);
            $remark = nl2br($row->remark);
            $cash_request_number = $row->cash_request_number;
            $cash_request_date = $this->general_model->get_string_date($row->cash_request_date);
            $employee_name = $row->employee_name;
            $branch_name = $row->branch_name;
            $payment_mode = $this->cashrequest_model->payment_mode_opt[$row->payment_mode];
            $amount = number_format($row->amount);
            $cash_request_status = $this->cashrequest_model->cashrequest_status[$row->cash_request_status];
            $prepared = $this->general_model->get_user_by_id($row->prepared_by);
            $checked = $this->general_model->get_user_by_id($row->checked_by);
            $approved = $this->general_model->get_user_by_id($row->approved_by);
        }       
        
        $data['description'] = $description;
        $data['remark'] = $remark;
        $data['cash_request_number'] = $cash_request_number;
        $data['cash_request_date'] = $cash_request_date;
        $data['employee_name'] = $employee_name;
        $data['branch_name'] = $branch_name;
        $data['payment_mode'] = $payment_mode;
        $data['amount'] = $amount;
        $data['cash_request_status'] = $cash_request_status;
        $data['prepared'] = $prepared;
        $data['checked'] = $checked;
        $data['approved'] = $approved;

        $this->load->view('cashrequest/cashrequest_print', $data);
    }
}
