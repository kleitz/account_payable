<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Account_model
 *
 * @author mchen
 */
class Payment_process_model extends CI_Model {//To be check, To Be Approve, Approved, Closed
    public $pp_status_opt = array('Draft', 'Cross Check', 'Checked', 'Approved', 'Closed');
    public $pp_status_style = array('label-danger', 'label-warning', 'label-info', 'label-primary', 'label-success');
    
    public $draft = 0;
    public $to_be_check = 1;
    public $to_be_approve = 2;
    public $approved = 3;
    public $closed = 4;
    
    public function get_payment_process_list_dash($start_date='', $end_date='', $field_status=0) {
        $sql  = 'SELECT pp.*, b.branch_name FROM payment_process AS pp ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE pp.pp_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'AND pp_status='.$field_status.' ';
        $sql .= 'ORDER BY last_update DESC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_process_list($start_date='', $end_date='', $field_search='', $keyword='', $field_status=0, $pp_type=0) {
        $sql  = 'SELECT pp.*, b.branch_name FROM payment_process AS pp ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE  ';
        if ($pp_type == 2){
            $sql .= ' pp_type=2 AND ';
        }
        if ($start_date != '' && $end_date != ''){
            $sql .= ' pp.pp_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
            if ($field_status != 0){
                $sql .= ' AND pp_status='.$field_status.' ';
            }
        } else {
            if ($field_status != 0){
                $sql .= ' pp_status='.$field_status.' ';
                if ($keyword != ''){
                    $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
                }
            } else {
                if ($keyword != ''){
                    $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
                } else {
                    $sql .= ' pp.pp_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
                }                
            }
        }
        $sql .= 'ORDER BY last_update DESC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_process_by_status($status=0) {
        $sql  = 'SELECT pp.* FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_status='.$status.' ';
        $sql .= 'ORDER BY pp_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_payment_process_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_process ';
        $sql .= 'WHERE pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_join_by_id($id=0) {
        $sql  = 'SELECT pp.*, s.supplier_name, b.branch_name FROM payment_process AS pp ';
        $sql .= 'INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE pp_id = '.$id;
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
        $sql = 'SELECT * FROM payment_process ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_credit_invoice_by_id($id=0) {
        $sql  = 'SELECT ci.*, c.supplier_name, b.branch_name FROM credit_invoice AS ci ';
        $sql .= 'INNER JOIN supplier AS c ON ci.supplier_id=c.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON ci.branch_id=b.branch_id ';
        $sql .= 'WHERE ci.credit_invoice_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_period_detail($date_event='') {
        $sql  = 'SELECT * FROM period_detail WHERE date_event LIKE "%'.$date_event.'%" ';
        $sql .= 'AND date_type = 2';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pv_id($pv_number='') {
        $sql  = 'SELECT * FROM payment_voucher WHERE pv_number="'.$pv_number.'" ';
        $query = $this->db->query($sql);
        $pv_enc = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $pv_enc = $this->general_model->encrypt_value($row->pv_id);
        }
        return $pv_enc;
    }
    
    public function insert_payment_process($pp_type=0) {
        $pp_number = $this->input->post('pp_number');
        $payment_mode = $this->input->post('payment_mode');
        $pp_date = $this->input->post('pp_date');
        $pp_due_date = '';
        $cash_request_id = 0;
        $third_party_id = 0;
        $vendor_id = 0;
        
        // for detail pp (2018-03-13)
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        $price = $this->general_model->change_decimal($this->input->post('price'));
        
        $total = 0;
        if ($unit >= 1 && $price > 0){
            $total = $unit * $price;
        } else {
            $total = 0;
        } 
        
        switch ($pp_type) {
            case 0:
                $pp_title = $this->input->post('pp_title');
                $supplier_id = 0;
                $supplier_type = 0;
                $branch_id = $this->input->post('branch_id');
                $cash_request_id = $this->input->post('cash_request_id');
                break;
            case 1:
                $supplier_type = 1;
                /*isi dari supplier id adalah credit_invoice_id, sehingga get data po*/
                $supplier_id = $this->input->post('supplier_id');
                $po_data = $this->get_credit_invoice_by_id($supplier_id);
                if ($po_data->num_rows()!=0){
                    $row = $po_data->row();
                    $supplier_id = $row->supplier_id;
                    $branch_id = $row->branch_id;
                    $pp_title = $row->supplier_name;
                }
                /*get due date from calendar supplier*/
                $substr_date = substr($pp_date, 0, 7);
                $date_now = substr($pp_date, 8, 10);
                $calendar = $this->get_period_detail($substr_date);
                if ($calendar->num_rows()!=0){
                    foreach ($calendar->result() as $val) {
                        $date_event = substr($val->date_event, 8, 10);
                        if ($date_event > $date_now){
                            $pp_due_date = $val->date_event;
                            break;
                        }
                    }
                }
                break;
            case 2:
                $pp_title = $this->input->post('pp_title');
                $supplier_id = 0;
                $branch_id = $this->input->post('branch_id');
                $supplier_type = 1;
                break;
            case 3:
                $supplier_id = 0;
                $branch_id = $this->input->post('branch_id');
                $supplier_type = 0;
                $third_party_id = $this->input->post('third_party_id');
                $pp_title = $this->input->post('pp_title');
                //$third_party_name = $this->input->post('third_party_name');
                /* third party 
                if (isset($third_party_id) && $third_party_id != 0){
                    $thirdparty = $this->get_third_party_by_id($third_party_id);
                    if ($thirdparty->num_rows()!=0){
                        $row = $thirdparty->row();
                        $pp_title = $row->third_party_name;
                    }
                } else {
                    $check_third= $this->get_third_party_by_name($third_party_name);
                    if ($check_third->num_rows()==0){
                        $datathird= array(
                            'third_party_name' => $third_party_name,
                            'description' => '-'
                        );
                        $this->db->insert('third_party', $datathird);
                        $third_party_id = $this->db->insert_id();
                        $pp_title = $third_party_name;
                    } else {
                        $rowthird = $check_third->row();
                        $third_party_id = $rowthird->third_party_id;
                        $pp_title = $third_party_name;
                    }
                }     */           
                break;
            case 4:
                $pp_title = $this->input->post('pp_title');
                $supplier_id = 0;
                $branch_id = $this->input->post('branch_id');
                $supplier_type = 0;
                $vendor_id = $this->input->post('vendor_id');
                //$vendor_name = $this->input->post('vendor_name');
                /* vendor */
//                if ($vendor_id == 0){
//                    $check_vendor= $this->get_vendor_by_name($vendor_name);
//                    if ($check_vendor->num_rows()==0){
//                        $datavendor= array(
//                            'vendor_name' => $vendor_name,
//                            'description' => '-'
//                        );
//                        $this->db->insert('vendor', $datavendor);
//                        $vendor_id = $this->db->insert_id();
//                    } else {
//                        $rowvendor = $check_vendor->row();
//                        $vendor_id = $rowvendor->vendor_id;
//                    }
//                }
                break;
        }
        
        $data = array(
            'pp_title' => $pp_title,
            'pp_number' => $pp_number,
            'pp_date' => $pp_date,
            'pp_due_date' => $pp_due_date,
            'total' => $total,
            'payment_mode' => $payment_mode,
            'prepare_by' => $this->session->userdata('user_id'),
            'cross_check_by' => 0,
            'checked_by' => 0,
            'approved_by' => 0,
            'pp_status' => 0,
            'pp_type' => $pp_type,
            'supplier_type' => $supplier_type,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'cash_request_id' => $cash_request_id,
            'third_party_id' => $third_party_id,
            'vendor_id' => $vendor_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('payment_process', $data);
        $pp_id = $this->db->insert_id(); /*get trans id*/
        
        if ($pp_type != 1){
            // insert to detail pp
            $this->insert_detail($pp_id, $job_order, $description, $unit, $price, $branch_id);
        }
        
        return $pp_id;
    }

    public function update_payment_process($pp_type=0) {
        $pp_id = $this->input->post('pp_id');
        $pp_number = $this->input->post('pp_number');
        $pp_date = $this->input->post('pp_date');
        $payment_mode = $this->input->post('payment_mode');
        $supplier_id = 0;
        $cash_request_id = 0;
        switch ($pp_type) {
            case 0:
                $pp_title = $this->input->post('pp_title');
                $branch_id = $this->input->post('branch_id');
                $cash_request_id = $this->input->post('cash_request_id');
                break;
            case 1:
                $branch_id = $this->input->post('branch_id');
                $supplier_id = $this->input->post('supplier_id');
                $supplierdata = $this->get_supplier_by_id($supplier_id);
                if ($supplierdata->num_rows()!=0){
                    $row = $supplierdata->row();
                    $pp_title = $row->supplier_name;
                }
                break;
            case 2:
                $pp_title = $this->input->post('pp_title');
                $branch_id = $this->input->post('branch_id');
                break;
            case 3:
                $pp_title = $this->input->post('pp_title');
                $branch_id = $this->input->post('branch_id');
                break;
            case 4:
                $pp_title = $this->input->post('pp_title');
                $branch_id = $this->input->post('branch_id');
                break;
        }
        
        $data = array(
            'pp_title' => $pp_title,
            'pp_number' => $pp_number,
            'pp_date' => $pp_date,
            'payment_mode' => $payment_mode,
            'prepare_by' => $this->session->userdata('user_id'),
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'cash_request_id' => $cash_request_id,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data);
        return $pp_id;
    }
    
    public function delete_by_id($id){
        $detail = $this->get_detail_by_pp_id($id);
        if ($detail->num_rows()!=0){
            foreach ($detail->result() as $value) {
                $credit_invoice_id = $value->credit_invoice_id;
                if (isset($credit_invoice_id) || $credit_invoice_id != 0){
                    /* update credit_invoice */
                    $data = array(
                        'po_status' => 0
                    );

                    $this->db->where('credit_invoice_id', $credit_invoice_id);
                    $this->db->update('credit_invoice', $data);
                }
            }
        }
        $this->db->where('pp_id', $id);
        $this->db->delete('payment_process');
    }
    
    public function get_detail_by_pp_id($pp_id=0) {
        $sql  = 'SELECT * FROM payment_process_detail ';
        $sql .= 'WHERE pp_id = '.$pp_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    /* Insert Account Payable from PP Supplier */
    public function insert_account_payable($pp_id=0) {
        $trans_date = date('Y-m-d');
        $trans_code = $this->general_model->get_generate_number('TR', 'transactions', 'trans_id');
        $data_pp = $this->get_pp_join_by_id($pp_id);
        $description = '';
        $branch_id = 0;
        $amount = 0;
        if ($data_pp->num_rows()!=0){
            $row = $data_pp->row();
            $description =  $row->branch_name.' : '.$row->supplier_name;
            $branch_id = $row->branch_id;
            $amount = $row->total;
        }
        $account_liability_id = 0;
        $account_expense_id = 0;

        /* get data account */
        $this->load->model('account_model');
        $account_liability = $this->account_model->get_account_by_branch_type($branch_id, 1, 'Account Payable');
        if ($account_liability->num_rows()!=0){
            $row1 = $account_liability->row();
            $account_liability_id = $row1->account_id;
        }
        $account_expense = $this->account_model->get_account_by_branch_type($branch_id, 4, 'Purchase');
        if ($account_expense->num_rows()!=0){
            $row2 = $account_expense->row();
            $account_expense_id = $row2->account_id;
        }

        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => 3,
            'account_id' => $account_liability_id,
            'account_relation' => $account_expense_id,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );

        $this->db->insert('transactions', $data);
        $trans_id = $this->db->insert_id(); /*get trans id*/

        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->load->model('ledger_model');
        $this->ledger_model->insert_ledger($trans_id, $account_liability_id, 0,  $amount);
        $this->ledger_model->insert_ledger($trans_id, $account_expense_id, $amount, 0);
    }
    
    public function insert_detail($pp_id=0, $job_order='', $description='', $unit=0, $price=0, $branch_id=0) {
        
        $total = 0;
        if ($unit >= 1 && $price > 0){
            $total = $unit * $price;
        } else {
            $total = 0;
        } 
        
         // get data pp

        $act_title = $this->get_branch_name($branch_id);
        $data = array(
            'pp_id' => $pp_id,
            'act_title' => $act_title,
            'branch_id' => $branch_id,
            'job_order' => $job_order,
            'description' => $description,
            'unit' => $unit,
            'price' => $price,
            'total' => $total
        );
        $this->db->insert('payment_process_detail', $data);
    }
    
    public function get_branch_name($branch_id=0) {
        $sql  = 'SELECT branch_name FROM branch ';
        $sql .= 'WHERE branch_id = '.$branch_id;
        $query = $this->db->query($sql);
        $branch_name = '-';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_name = $row->branch_name;
        }
        return $branch_name; 
    }
    
    public function get_third_party_by_id($id=0) {
        $sql  = 'SELECT * FROM third_party ';
        $sql .= 'WHERE third_party_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_third_party_by_name($name='') {
        $sql  = 'SELECT * FROM third_party ';
        $sql .= 'WHERE third_party_name = "'.$name.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_vendor_by_id($id=0) {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'WHERE vendor_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_vendor_by_name($name='') {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'WHERE vendor_name = "'.$name.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_ppgeneral($pp_type=0) {
        $pp_number = $this->input->post('pp_number');
        $payment_mode = $this->input->post('payment_mode');
        $pp_date = $this->input->post('pp_date');
        $pp_due_date = '';
        $cash_request_id = $this->input->post('cash_request_id');
        $employee_id = $this->input->post('employee_id');
        
        // for detail pp (2018-03-13)
        $job_order = $this->input->post('job_order');
        $description = $this->input->post('description');
        $unit = $this->input->post('unit');
        $price = $this->general_model->change_decimal($this->input->post('price'));
        
        $total = 0;
        if ($unit >= 1 && $price > 0){
            $total = $unit * $price;
        } else {
            $total = 0;
        }

        $pp_title = $this->input->post('pp_title');
        $supplier_id = 0;
        $supplier_type = 0;
        $branch_id = $this->input->post('branch_id');
                

        
        $data = array(
            'pp_title' => $pp_title,
            'pp_number' => $pp_number,
            'pp_date' => $pp_date,
            'pp_due_date' => $pp_due_date,
            'total' => $total,
            'payment_mode' => $payment_mode,
            'prepare_by' => $this->session->userdata('user_id'),
            'cross_check_by' => 0,
            'checked_by' => 0,
            'approved_by' => 0,
            'pp_status' => 0,
            'pp_type' => $pp_type,
            'supplier_type' => $supplier_type,
            'supplier_id' => $supplier_id,
            'branch_id' => $branch_id,
            'cash_request_id' => $cash_request_id,
            'third_party_id' => 0,
            'vendor_id' => 0,
            'employee_id' => $employee_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('payment_process', $data);
        $pp_id = $this->db->insert_id(); /*get trans id*/

        $this->insert_detail($pp_id, $job_order, $description, $unit, $price, $branch_id);

        return $pp_id;
    }
}