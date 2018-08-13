<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashrequest_model
 *
 * @author Hendra McHen
 */
class Cashrequest_model extends CI_Model {
    public $cashrequest_status = array(
        '<span class="label label-warning">To Be Check</span>', 
        '<span class="label label-info">To Be Approve</span>', 
        '<span class="label label-primary">Approved</span>', 
        '<span class="label label-danger">Outstanding</span>',
        '<span class="label label-success">Closed</span>'
        );
    public $to_be_check = 0;
    public $to_be_approve = 1;
    public $approved = 2;
    public $paid = 3;
    public $closed = 4;
    
    public  $tr_status = array("warning", "info", "primary", "success");
    
    public $payment_mode_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
    
    public function get_cashrequest_list($start_date='', $end_date='', $field_search='', $keyword=''){
        $sql  = 'SELECT cash_request.*, branch.branch_name FROM cash_request ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request.branch_id ';
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' cash_request.cash_request_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' cash_request.cash_request_date BETWEEN "" AND "" ';
            }
        }
        $sql .= 'ORDER BY last_update DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashrequest_by_status($status=0) {
        $sql  = 'SELECT *, branch.branch_name FROM cash_request ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request.branch_id ';
        $sql .= 'WHERE cash_request.cash_request_status = '.$status;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashrequest() {
        $sql  = 'SELECT * FROM cash_request ';
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
    
    public function get_last_record() {
        $sql = 'SELECT * FROM cash_request ORDER BY last_update DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_fullname_by_user_id($user_id=0) {
        $sql = 'SELECT * FROM users WHERE user_id ='.$user_id;
        $query = $this->db->query($sql);
        $fullname = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $fullname = $row->fullname;
        }
        return $fullname;
    }
    
    public function get_employee_by_id($id=0) {
        $sql  = 'SELECT * FROM employee ';
        $sql .= 'WHERE employee_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_file_by_cashreq_id($cashreqid=0) {
        $sql  = 'SELECT * FROM cash_request_file ';
        $sql .= 'WHERE cash_request_id = '.$cashreqid;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_remark_list($cash_request_id=0) {
        $sql  = 'SELECT * FROM cash_request_remark ';
        $sql .= 'WHERE cash_request_id = '.$cash_request_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_employee_by_name($name='') {
        $sql  = 'SELECT * FROM employee ';
        $sql .= 'WHERE full_name = "'.$name.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_by_cr_id($cash_request_id=0) {
        $sql  = 'SELECT pp.pp_id, pp.pp_status, pp.pp_number, pp.pp_date, pp.total, b.branch_name, pp.pv_number 
        FROM payment_process AS pp
        INNER JOIN branch AS b ON pp.branch_id=b.branch_id
        WHERE pp.cash_request_id='.$cash_request_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_cashrequest() {
        $cash_request_number = $this->input->post('cash_request_number');
        $cash_request_date = $this->input->post('cash_request_date');
        $employee_id = $this->input->post('employee_id');
        $employee_name = $this->input->post('employee_name');
        $description = $this->input->post('description');
        $remark = $this->input->post('remark');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $payment_mode = $this->input->post('payment_mode');
        /* employee */
        if (isset($employee_id) && $employee_id != 0){
            $employee = $this->get_employee_by_id($employee_id);
            if ($employee->num_rows()!=0){
                $row = $employee->row();
                $employee_name = $row->full_name;
            }
        } else {
            $check_emp = $this->get_employee_by_name($employee_name);
            if ($check_emp->num_rows()==0){
                $dataemp = array(
                    'full_name' => $employee_name,
                    'branch_id' => $branch_id,
                    'description' => '-'
                );
                $this->db->insert('employee', $dataemp);
                $emp_new_id = $this->db->insert_id();
                $employee_id = $emp_new_id;
            } else {
                $rowemp = $check_emp->row();
                $employee_id = $rowemp->employee_id;
            }
        }

        $data = array(
            'cash_request_number' => $cash_request_number,
            'cash_request_date' => $cash_request_date,
            'employee_id' => $employee_id,
            'employee_name' => $employee_name,
            'description' => $description,
            'remark' => $remark,
            'prepared_by' => $this->session->userdata('user_id'),
            'amount' => $amount,
            'cash_request_status' => 0,
            'branch_id' => $branch_id,
            'payment_mode' => $payment_mode,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_request', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'note number' => $cash_request_number,
            'note date' => $cash_request_date,
            'employee' => $employee_id,
            'description' => $description,
            'prepared by' => $this->session->userdata('user_id'),
            'amount' => $amount,
            'note status' => 0,
            'branch id' => $branch_id,
            'payment_mode' => $payment_mode
        );
        $this->log_activity_model->insert_log('Cash Request', 'Add', $log_desc);
        /*== end log ==*/
    }

    public function update_cashrequest($id) {
        $cash_request_number = $this->input->post('cash_request_number');
        $cash_request_date = $this->input->post('cash_request_date');
        $employee_id = $this->input->post('employee_id');
        $employee_name = $this->input->post('employee_name');
        $description = $this->input->post('description');
        $remark = $this->input->post('remark');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $payment_mode = $this->input->post('payment_mode');
        /* employee */
        $employee = $this->get_employee_by_id($employee_id);
        if ($employee->num_rows()!=0){
            $row = $employee->row();
            $employee_name = $row->full_name;
        }
        $data = array(
            'cash_request_number' => $cash_request_number,
            'cash_request_date' => $cash_request_date,
            'employee_id' => $employee_id,
            'employee_name' => $employee_name,
            'description' => $description,
            'remark' => $remark,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'payment_mode' =>$payment_mode,
            'username' => $this->session->userdata('username')
        );

        $this->db->where('cash_request_id', $id);
        $this->db->update('cash_request', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'cash_request_number' => $cash_request_number,
            'cash_request_date' => $cash_request_date,
            'employee_id' => $employee_id,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'payment_mode' =>$payment_mode
        );
        $this->log_activity_model->insert_log('Cash Request', 'Edit', $log_desc);
        /*== end log ==*/
    }
    
    public function delete_by_id($id) {
        $note = $this->get_cashrequest_by_id($id);
        if ($note->num_rows()!=0){
            $row = $note->row();
            /*== insert to log activity ==  */
            $this->load->model('log_activity_model');
            $log_desc = array(
                'cash_request_number' => $row->cash_request_number,
                'cash_request_date' => $row->cash_request_date,
                'employee_id' => $row->employee_id,
                'description' => $row->description,
                'amount' => $row->amount,
                'branch_id' => $row->branch_id
            );
            $this->log_activity_model->insert_log('Cash Request', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('cash_request_id', $id);
        $this->db->delete('cash_request');
    }
    
    public function update_file_name($id=0, $file='', $type='') {
        $data = array(
            'cash_request_id' => $id,
            'file_name'=> $file,
            'file_type'=> $type
        );
        $this->db->insert('cash_request_file', $data);
    }
    
    public function update_file_pr($id=0, $file='') {
        $data = array('file_pr'=>$file);
        $this->db->where('cash_request_id', $id);
        $this->db->update('cash_request', $data);
    }
    
    public function update_status($id=0, $status=0, $received_date='', $payment_method=0) {
        $data = array(
            'note_status'=>$status,
            'checked_by' =>$this->session->userdata('user_id'),
            'received_date' => $received_date,
            'payment_method' => $payment_method
        );
        $this->db->where('cash_request_id', $id);
        $this->db->update('cash_request', $data);
    }
    
    public function insert_trans_cashreq($cash_request_id=0, $account_asset_id = 0) {
        $trans_date = date('Y-m-d');
        $trans_code = $this->general_model->get_generate_number('TR', 'transactions', 'trans_id');
        
        $cashnote = $this->cashrequest_model->get_cashrequest_by_id($cash_request_id);
        if ($cashnote->num_rows()!=0){
            $row = $cashnote->row();
            $description = $row->description;
            $amount = $row->amount;
            $payment_mode = $row->payment_mode;
            $this->load->model('account_model');
            $account_expense_id = 0;
            $account_expense = $this->account_model->get_account_by_branch_type($row->branch_id, 4, 'Cash');
            if ($account_expense->num_rows()!=0){
                $row = $account_expense->row();
                $account_expense_id = $row->account_id;
            }
            
            $data = array(
                'trans_date' => $trans_date,
                'trans_code' => $trans_code,
                'trans_type' => 3,
                'account_id' => $account_expense_id,
                'account_relation' => $account_asset_id,
                'description' => $description,
                'amount' => $amount,
                'user_id' => $this->session->userdata('user_id'),
                'cash_request_id' => $cash_request_id,
                'payment_mode' => $payment_mode
            );

            $this->db->insert('transactions', $data);
            $trans_id = $this->db->insert_id(); /*get trans id*/
            $this->load->model('ledger_model');
            $this->ledger_model->insert_ledger($trans_id, $account_expense_id, $amount, 0);
            $this->ledger_model->insert_ledger($trans_id, $account_asset_id, 0, $amount);
        }
    }
    
    public function insert_trans_cash_return($cash_request_id=0, $cash_return = 0) {
        $trans_date = date('Y-m-d');
        $trans_code = $this->general_model->get_generate_number('TR', 'transactions', 'trans_id');
        
        $cashnote = $this->get_cashrequest_by_id($cash_request_id);
        if ($cashnote->num_rows()!=0){
            $row = $cashnote->row();
            $description = $row->description;
            $amount = $cash_return;
            $payment_mode = $row->payment_mode;
            $account_asset_id = $row->account_id;
            $this->load->model('account_model');
            $account_expense_id = 0;
            $account_expense = $this->account_model->get_account_by_branch_type($row->branch_id, 4, 'Cash');
            if ($account_expense->num_rows()!=0){
                $row = $account_expense->row();
                $account_expense_id = $row->account_id;
            }
            
            $data = array(
                'trans_date' => $trans_date,
                'trans_code' => $trans_code,
                'trans_type' => 2,
                'account_id' => $account_asset_id,
                'account_relation' => $account_expense_id,
                'description' => $description,
                'amount' => $amount,
                'user_id' => $this->session->userdata('user_id'),
                'cash_request_id' => $cash_request_id,
                'payment_mode' => $payment_mode
            );

            $this->db->insert('transactions', $data);
            $trans_id = $this->db->insert_id(); /*get trans id*/
            $this->load->model('ledger_model');
            $this->ledger_model->insert_ledger($trans_id, $account_expense_id, 0, $amount);
            $this->ledger_model->insert_ledger($trans_id, $account_asset_id, $amount, 0);
        }
    }
    // update 28 July 2018
    public function get_arr_pv($start_date='', $end_date='', $field_search='', $keyword='') {
        $list = $this->get_cashrequest_list($start_date, $end_date, $field_search, $keyword);
        $arr = array();
        if ($list->num_rows()!=0){
            $in_cr = '';
            foreach ($list->result() as $value) {
                $in_cr .= $value->cash_request_id.',';
            }
            $incr = substr($in_cr, 0, strlen($in_cr)-1);
            $sql = 'SELECT * FROM payment_voucher WHERE cash_request_id IN('.$incr.')';
            $query = $this->db->query($sql);
            if ($query->num_rows()!=0){
                foreach ($query->result() as $value) {
                    $arr[$value->cash_request_id] = $value->pv_id;
                }
            }
        }
        return $arr;
    }
}