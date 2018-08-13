<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Payment_voucher_model
 *
 * @author Hendra McHen
 */
class Payment_voucher_model extends CI_Model {

    public function get_payment_voucher_list($start_date='', $end_date='') {
        $sql  = 'SELECT * FROM payment_voucher  ';
        $sql .= 'WHERE pv_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY last_update DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_voucher_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_voucher ';
        $sql .= 'WHERE pv_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_voucher_by_pv_number($pv_number='') {
        $sql  = 'SELECT * FROM payment_voucher ';
        $sql .= 'WHERE pv_number = "'.$pv_number.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_by_id($id=0) {
        $sql  = 'SELECT * FROM supplier ';
        $sql .= 'WHERE supplier_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_record() {
        $sql = 'SELECT * FROM payment_voucher ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_process_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_process ';
        $sql .= 'WHERE pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashrequest_by_id($id=0) {
        $sql  = 'SELECT cash_request.*, branch.branch_name FROM cash_request ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request.branch_id ';
        $sql .= 'WHERE cash_request.cash_request_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_voucher_file_by_pv_id($pv_id=0) {
        $sql  = 'SELECT * FROM payment_voucher_file ';
        $sql .= 'WHERE pv_id = '.$pv_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_payment_voucher() {
        $pp_id = $this->input->post('pp_id');     
        $cash_request_id = $this->input->post('cash_request_id');
        /* ======= untuk PP ======= */
        if (isset($pp_id) && $pp_id != 0){
            $pv_id = $this->insert_payment_voucher_pp($pp_id);
        }
        /* ======= untuk cash request ======= */
        if (isset($cash_request_id) && $cash_request_id != 0){
            $pv_id = $this->insert_payment_voucher_cashrequest($cash_request_id);
        }
        /* == Redirect ==*/
        if ($pv_id != 0){
            $enc_id = $this->general_model->encrypt_value($pv_id);
            $back = '/paymentvoucher/detail/' . $this->asik_model->category_configuration;
            $back .= $this->asik_model->config_02. '/'.$enc_id;
        } else {
            $back = '/paymentvoucher/go/' . $this->asik_model->category_configuration;
            $back .= $this->asik_model->config_02;
        }
        redirect($back);
    }
    
    public function insert_payment_voucher_pp($pp_id = 0) {
        $pv_number = $this->input->post('pv_number');
        $check_pv = $this->get_payment_voucher_by_pv_number($pv_number);
        if ($check_pv->num_rows() == 0) {
            $pv_date = $this->input->post('pv_date'); ///date('Y-m-d');
            /* auto (get from) */
            $pv_title = '';
            $branch_id = 0;
            $total = 0;
            $payment_mode = 0;
            $nota_from = '';
            /* manual (from input) */
            $description = $this->input->post('description');
            $admin_fee = $this->general_model->change_decimal($this->input->post('admin_fee'));
            $account_id = $this->input->post('account_id');
            $bank_id = $this->input->post('bank_id');
            $bank_account_name_to = $this->input->post('bank_account_name_to');
            $bank_account_num_to = $this->input->post('bank_account_num_to');
            $bank_name_to = $this->input->post('bank_name_to');
            $bank_cek_from = $this->input->post('bank_cek_from');
            $bank_bg_from = $this->input->post('bank_bg_from');
            $received_name = $this->input->post('received_name');
            //// jika pembayaran melalui cash request
            $cash_from_cashrequest = $this->input->post('cash_from_cashrequest');
            if ($cash_from_cashrequest == ''){
                $cash_from_cashrequest = 0;
            }
            /*             * *** cek bank id | 2018-03-20 **** */
            if ($bank_id == '') {
                $bank_id = 0;
            }
            /* get data payment process */
            $ppdata = $this->get_payment_process_by_id($pp_id);
            $pp_type = 0;
            $employee_id = 0;
            $arr = array();
            // update field third_party_id 2018-08-11
            $third_party_id = 0;
            if ($ppdata->num_rows() != 0) {
                $row = $ppdata->row();
                $pv_title = $row->pp_title;
                $branch_id = $row->branch_id;
                $total = $row->total;
                $payment_mode = $row->payment_mode;
                $nota_from = $row->pp_number;
                /* update 2018-03-28 */
                $pp_type = $row->pp_type;
                /// update 2018-05-24
                $employee_id = $row->employee_id;
                // update 2018-08-07
                $arr[0] = $row->supplier_id;
                $arr[1] = $row->vendor_id;
                $arr[2] = $row->third_party_id;
                // update 2018-08-11
                $third_party_id = isset($row->third_party_id) ? $row->third_party_id:0;
            }

            /* ======= Update Status PP dan Credit Invoice ======= */
            $this->update_pp_status($pp_id, $pv_number);

            $this->load->model('trans_model');
            /* ======= jika metode pembayaran nya adalah BANK ====================== */
            if (isset($bank_id) && $bank_id != 0 && $cash_from_cashrequest == 0) {
                $this->load->model('bank_model');
                $bank = $this->bank_model->get_bank_account_by_id($bank_id);
                $keyword = 'Bank';
                if ($bank->num_rows() != 0) {
                    $row = $bank->row();
                    $branch_bank = $row->branch_id;
                    if ($branch_id != $branch_bank) { /* pinjam dari outlet lain */
                        /* proses ini dilakukan jika branch id tidak sama dengan branch pp */
                        $account_from = $this->get_account_id($branch_bank, 'Petty Bank');
                        $account_to = $this->get_account_id($branch_id, 'Petty Bank');
                        $this->load->model('cashreceived_model');
                        $total_all = $total + $admin_fee;
                        $trans_id = $this->trans_model->insert_transaction_from_bank_other($pv_date, $description, $total_all, $branch_bank, $branch_id, $pv_number);
                        $this->cashreceived_model->insert_cashreceived_from_pv($pv_date, $account_from, $account_to, $total_all, $pv_title, $pv_number, $branch_id, $trans_id);
                        $this->insert_to_outstanding($pv_date, $pv_title, $total_all, $branch_bank, $pv_number, 2, $trans_id);
                        // 2018-03-17 | insert to expense
                        $this->insert_to_expense($pv_date, $pv_title, $total_all, $branch_id, $pv_number, $trans_id);
                    } else {
                        $total_all = $total + $admin_fee;
                        if ($pp_type != 3) {
                            $trans_id = $this->trans_model->insert_expense_transaction($pv_date, $description, $total_all, $branch_id, $keyword, $pv_number);
                            // insert to expense table
                            $this->insert_to_expense($pv_date, $pv_title, $total_all, $branch_id, $pv_number, $trans_id);
                        } else {
                            // pp outstanding
                            $account_from = $this->get_account_id($branch_bank, 'Petty Bank');
                            $account_to = $this->get_account_id($branch_bank, 'Receivable');
                            $trans_id = $this->trans_model->insert_transaction_by_param($pv_date, 2, $account_from, $account_to, $description, $total_all, $pv_number);
                            $this->load->model('ledger_model');
                            // insert to BANK QTR
                            $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $total_all, 'O/S3');
                            // insert to RECEIVABLE QTR
                            $this->ledger_model->insert_ledger($trans_id, $account_to, $total_all, 0, 'O/S3');
                            $this->insert_to_outstanding($pv_date, $pv_title, $total_all, $branch_id, $pv_number, $pp_type, $trans_id, $third_party_id);
                        }
                    }
                }
            }
            /* jika kembali nota (cash dari cash request) */
            if (isset($cash_from_cashrequest) && $cash_from_cashrequest != 0) {
                
                if ($employee_id != 0){
                    $total_all = $total + $admin_fee;
                    $trans_id = $this->trans_model->insert_return_to_employee($pv_date, $description, $total_all, $branch_id, $cash_from_cashrequest, $pv_number, $employee_id);
                    $this->insert_to_expense($pv_date, $pv_title, $total_all, $branch_id, $pv_number, $trans_id, 0);
                } else {
                    $total_all = $total + $admin_fee;
                    //$trans_id = $this->trans_model->insert_expense_transaction_cr($pv_date, $description, $total_all, $branch_id, $cash_from_cashrequest, $pv_number);
                    // insert to expense table
                    //$this->insert_to_expense($pv_title, $total_all, $branch_id, $pv_number, $trans_id, 1);
                    //==============================================================//
                    $cashreq = $this->get_cashrequest_by_id($cash_from_cashrequest);
                    $branch_cr = 0;
                    if ($cashreq->num_rows()!=0){
                        $crow = $cashreq->row();
                        $branch_cr = $crow->branch_id;
                    }
                    $trans_id = $this->trans_model->insert_came_nota($pv_date, $description, $total, $branch_id, $cash_from_cashrequest, $pv_number, $admin_fee);
                    // insert to expense table
                    $ex_type = 0;
                    if ($branch_id == $branch_cr){
                        $ex_type = 1;
                    }
                    $this->insert_to_expense($pv_date, $pv_title, $total_all, $branch_id, $pv_number, $trans_id, $ex_type);
                }
            }

            $data = array(
                'pv_number' => $pv_number,
                'pv_title' => $pv_title,
                'pv_date' => $pv_date,
                'nota_from' => $nota_from,
                'pp_id' => $pp_id,
                'cash_request_id' => 0,
                'branch_id' => $branch_id,
                'total' => $total,
                'payment_mode' => $payment_mode,
                'description' => $description,
                'admin_fee' => $admin_fee,
                'account_id' => $account_id,
                'bank_account_name_to' => $bank_account_name_to,
                'bank_account_num_to' => $bank_account_num_to,
                'bank_name_to' => $bank_name_to,
                'bank_id' => $bank_id,
                'bank_cek_from' => $bank_cek_from,
                'bank_bg_from' => $bank_bg_from,
                'received_name' => $received_name,
                'username' => $this->session->userdata('username'),
                'trans_id' => $trans_id
            );
            $this->db->insert('payment_voucher', $data);
            $pv_id = $this->db->insert_id();
            // update 2018-08-07
            if ($pv_id != 0 && ($pp_type == 1 || $pp_type == 3 || $pp_type == 4)){
                $this->insert_balance($pv_id, $pv_date, $trans_id, $total, $pp_id, $pp_type, $arr);
            }
            return $pv_id;
        } else {
            return 0;
        }
    }

    public function insert_payment_voucher_cashrequest($cash_request_id = 0) {
        $pv_number = $this->input->post('pv_number');
        $check_pv = $this->get_payment_voucher_by_pv_number($pv_number);
        if ($check_pv->num_rows() == 0) {

            $pv_date = $this->input->post('pv_date'); /// date('Y-m-d');
            /* auto (get from) */
            $pv_title = '';
            $branch_id = 0;
            $total = 0;
            $payment_mode = 0;
            $nota_from = '';
            /* manual (from input) */
            $description = $this->input->post('description');
            $admin_fee = $this->general_model->change_decimal($this->input->post('admin_fee'));
            $account_id = $this->input->post('account_id');
            $bank_id = $this->input->post('bank_id');
            $bank_account_name_to = $this->input->post('bank_account_name_to');
            $bank_account_num_to = $this->input->post('bank_account_num_to');
            $bank_name_to = $this->input->post('bank_name_to');
            $bank_cek_from = $this->input->post('bank_cek_from');
            $bank_bg_from = $this->input->post('bank_bg_from');
            $received_name = $this->input->post('received_name');
            $crdata = $this->get_cashrequest_by_id($cash_request_id);
            if ($crdata->num_rows() != 0) {
                $row = $crdata->row();
                $pv_title = $row->employee_name;
                $branch_id = $row->branch_id;
                $total = $row->amount;
                $payment_mode = $row->payment_mode;
                $nota_from = $row->cash_request_number;
            }


            /* ======= Update Cash Request status ======= */
            $this->update_cash_request_status($cash_request_id, $pv_number);

            $this->load->model('trans_model');
            /* ======= jika metode pembayaran nya adalah BANK ======= */

            $total_all = $total + $admin_fee;
            $trans_id = $this->trans_model->insert_receivable_transaction($pv_date, $description, $total_all, $branch_id, 'Bank', $pv_number);
            // insert to cash request balance | 23 May 2018
            // code $total_all is changed with $total, because to insert cash request balance not include admin fee
            $this->trans_model->insert_to_cr_balance($pv_date, $cash_request_id, $trans_id, $pv_number, $total, 0);
            // insert to outstanding
            $this->insert_to_outstanding($pv_date, $pv_title, $total_all, $branch_id, $pv_number, 1, $trans_id);
            // insert payment voucher
            $data = array(
                'pv_number' => $pv_number,
                'pv_title' => $pv_title,
                'pv_date' => $pv_date,
                'nota_from' => $nota_from,
                'pp_id' => 0,
                'cash_request_id' => $cash_request_id,
                'branch_id' => $branch_id,
                'total' => $total,
                'payment_mode' => $payment_mode,
                'description' => $description,
                'admin_fee' => $admin_fee,
                'account_id' => $account_id,
                'bank_account_name_to' => $bank_account_name_to,
                'bank_account_num_to' => $bank_account_num_to,
                'bank_name_to' => $bank_name_to,
                'bank_id' => $bank_id,
                'bank_cek_from' => $bank_cek_from,
                'bank_bg_from' => $bank_bg_from,
                'received_name' => $received_name,
                'username' => $this->session->userdata('username'),
                'trans_id' => $trans_id
            );
            $this->db->insert('payment_voucher', $data);
            $pv_id = $this->db->insert_id();
            return $pv_id;
        } else {
            return 0;
        }
    }

    public function get_account_id($branch_id=0, $keyword='', $type=0) {
        $account_id = 0;
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_branch_type($branch_id, $type, $keyword);
        if ($account->num_rows()!=0){
            $row = $account->row();
            $account_id = $row->account_id;
        }
        return $account_id;
    }

    public function update_payment_voucher($pv_id=0) {
        $pv_date = $this->input->post('pv_date');
        $pp_id = $this->input->post('pp_id');        
        $description = $this->input->post('description');
        $admin_fee = $this->general_model->change_decimal($this->input->post('admin_fee'));
        $payment_mode = $this->input->post('payment_mode');
        $branch_id = 0;
        $total = 0;
        $data = array(
            'pv_date' => $pv_date,
            'pp_id' => $pp_id,
            'branch_id' => $branch_id,
            'description' => $description,
            'admin_fee' => $admin_fee,
            'total' => $total,
            'payment_mode' => $payment_mode,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pv_id', $pv_id);
        $this->db->update('payment_voucher', $data);
        return $pp_id;
    }
    /*
    public function delete_by_id($id){
        $detail = $this->get_detail_by_pp_id($id);
        if ($detail->num_rows()!=0){
            foreach ($detail->result() as $value) {
                $po_id = $value->po_id;
                if (isset($po_id) || $po_id != 0){
                     update purchase_order 
                    $data = array(
                        'po_status' => 1
                    );

                    $this->db->where('po_id', $po_id);
                    $this->db->update('purchase_order', $data);
                }
            }
        }
        $this->db->where('pp_id', $id);
        $this->db->delete('payment_voucher');
    }*/
    
    public function get_detail_by_pp_id($pp_id=0) {
        $sql  = 'SELECT * FROM payment_voucher_detail ';
        $sql .= 'WHERE pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'pv_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('payment_voucher_file', $data);
    }
    
    public function get_fullname_by_id($id) {
        $fullname = '';
        $sql  = 'SELECT * FROM users ';
        $sql .= 'WHERE user_id = '.$id;
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $row = $query->row();
            $fullname = $row->fullname;
        }
        return $fullname;
    }

    public function update_pp_status($pp_id = 0, $pv_number='') {
        /*update status pp */
        $data = array(
            'pp_status' => 4,
            'pv_number' => $pv_number
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        $this->update_credit_invoice_status($pp_id);
    }
    
    public function update_credit_invoice_status($pp_id = 0) {
        $this->load->model('ppdetail_model');
        $detail = $this->ppdetail_model->get_detail_by_pp_id($pp_id);
        if ($detail->num_rows()!=0){
            foreach ($detail->result() as $value) {
                $credit_invoice_id = $value->credit_invoice_id;
                if ($credit_invoice_id != 0){
                    $datapo = array(
                        'po_status' => 2
                    );
                    $this->db->where('credit_invoice_id', $credit_invoice_id);
                    $this->db->update('credit_invoice', $datapo);
                }
            }
        }
    }
    
    public function update_cash_request_status($cash_request_id=0, $pv_number='') {
        /* cash request update */
        $data = array(
            'cash_request_status' => 3,
            'pv_number' => $pv_number
        );
        $this->db->where('cash_request_id', $cash_request_id);
        $this->db->update('cash_request', $data);
    }
    
    public function insert_to_outstanding($pv_date='', $description='', $amount=0, $branch_id=0, $pv_number='', $outstanding_type=0, $trans_id=0, $third_party_id=0) {
        /* 
         * ===== OUTSTANDING TYPE =====
         * O/S CASH REQUEST = 1
         * O/S OUTLET = 2
         * O/S THIRD PARTY = 3
         */
        $outstanding_number = $this->general_model->get_generate_number('OS', 'outstanding', 'outstanding_id');
        $now = $pv_date; //date('Y-m-d');
        $data = array(
            'outstanding_number' => $outstanding_number,
            'outstanding_date' => $now,
            'outstanding_description' => $description,
            'amount' => $amount,
            'outstanding_status' => 0,
            'branch_id' => $branch_id,
            'pv_number' => $pv_number,
            'outstanding_type' => $outstanding_type,
            'trans_id' => $trans_id,
            'third_party_id' => $third_party_id
        );
        $this->db->insert('outstanding', $data);
    }
    
    public function insert_to_expense($pv_date='', $title='', $amount=0, $branch_id=0, $pv_number='', $trans_id=0, $ex_type=0) {
        $expense_number = $this->general_model->get_generate_number('EX', 'expense', 'expense_id');
        $now = $pv_date; //date('Y-m-d');
        $data = array(
            'expense_number' => $expense_number,
            'expense_date' => $now,
            'expense_title' => $title,
            'amount' => $amount,
            'expense_status' => 0,
            'pv_number' => $pv_number,
            'branch_id' => $branch_id,
            'trans_id' => $trans_id,
            'expense_type' => $ex_type
        );
        $this->db->insert('expense', $data);
    }
    
    public function insert_balance($pv_id=0, $pv_date='', $trans_id=0, $total=0, $pp_id=0, $pp_type=0, $arr=array()) {
        $table = '';
        $value_id = 0;
        $key = '';
        switch ($pp_type) {
            case 1:
                $table = 'supplier_balance';
                $key = 'supplier_id';
                $value_id = $arr[0];
                break;

            case 3:
                $table = 'project_balance';
                $key = 'vendor_id';
                $value_id = $arr[1];
                break;
            
            case 4:
                $table = 'third_party_balance';
                $key = 'third_party_id';
                $value_id = $arr[2];
                break;
        }
        $data = array(
            'balance_date' => $pv_date,
            ''.$key => $value_id,
            'pp_id' => $pp_id,
            'pv_id' => $pv_id,
            'trans_id' => $trans_id,
            'credit' => $total
        );
        $this->db->insert($table, $data);
    }
}
