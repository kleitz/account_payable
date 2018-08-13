<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashreqnote_model
 *
 * @author JUNA
 */
class Cashreqnote_model extends CI_Model {
    
    public function get_cashreqnote_list($start_date='', $end_date='') {
        $sql  = 'SELECT cash_request_note.*, employee.full_name, branch.branch_name FROM cash_request_note ';
        $sql .= 'INNER JOIN employee ON cash_request_note.employee_id=employee.employee_id ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request_note.branch_id ';
        $sql .= 'WHERE cash_request_note.note_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashreqnote_draft() {
        $sql  = 'SELECT cash_request_note.*, employee.full_name, branch.branch_name FROM cash_request_note ';
        $sql .= 'INNER JOIN employee ON cash_request_note.employee_id=employee.employee_id ';
        $sql .= 'INNER JOIN branch ON branch.branch_id=cash_request_note.branch_id ';
        $sql .= 'WHERE cash_request_note.note_status = 0';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_cashreqnote_by_id($id=0) {
        $sql  = 'SELECT cash_request_note.*, employee.full_name FROM cash_request_note ';
        $sql .= 'INNER JOIN employee ON cash_request_note.employee_id=employee.employee_id ';
        $sql .= 'WHERE cash_request_note.note_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_cashreqnote() {
        $note_number = $this->input->post('note_number');
        $note_date = $this->input->post('note_date');
        $employee_id = $this->input->post('employee_id');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $payment_mode = $this->input->post('payment_mode');

        $data = array(
            'note_number' => $note_number,
            'note_date' => $note_date,
            'employee_id' => $employee_id,
            'description' => $description,
            'prepared_by' => $this->session->userdata('user_id'),
            'amount' => $amount,
            'note_status' => 0,
            'branch_id' => $branch_id,
            'payment_mode' => $payment_mode
        );
        $this->db->insert('cash_request_note', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'note number' => $note_number,
            'note date' => $note_date,
            'employee' => $employee_id,
            'description' => $description,
            'prepared by' => $this->session->userdata('user_id'),
            'amount' => $amount,
            'note status' => 0,
            'branch id' => $branch_id,
            'payment_mode' => $payment_mode
        );
        $this->log_activity_model->insert_log('Cash Request Note', 'Add', $log_desc);
        /*== end log ==*/
    }

    public function update_cashreqnote($id) {
        $note_number = $this->input->post('note_number');
        $note_date = $this->input->post('note_date');
        $employee_id = $this->input->post('employee_id');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $payment_mode = $this->input->post('payment_mode');
        
        $data = array(
            'note_number' => $note_number,
            'note_date' => $note_date,
            'employee_id' => $employee_id,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'payment_mode' =>$payment_mode
        );

        $this->db->where('note_id', $id);
        $this->db->update('cash_request_note', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'note_number' => $note_number,
            'note_date' => $note_date,
            'employee_id' => $employee_id,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id,
            'payment_mode' =>$payment_mode
        );
        $this->log_activity_model->insert_log('Cash Request Note', 'Edit', $log_desc);
        /*== end log ==*/
    }
    
    public function delete_by_id($id) {
        $note = $this->get_cashreqnote_by_id($id);
        if ($note->num_rows()!=0){
            $row = $note->row();
            /*== insert to log activity ==  */
            $this->load->model('log_activity_model');
            $log_desc = array(
                'note_number' => $row->note_number,
                'note_date' => $row->note_date,
                'employee_id' => $row->employee_id,
                'description' => $row->description,
                'amount' => $row->amount,
                'branch_id' => $row->branch_id
            );
            $this->log_activity_model->insert_log('Cash Request Note', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('note_id', $id);
        $this->db->delete('cash_request_note');
    }
    
    public function update_file_name($id=0, $file='') {
        $data = array('file_name'=>$file);
        $this->db->where('note_id', $id);
        $this->db->update('cash_request_note', $data);
    }
    
    public function update_file_pr($id=0, $file='') {
        $data = array('file_pr'=>$file);
        $this->db->where('note_id', $id);
        $this->db->update('cash_request_note', $data);
    }
    
    public function update_status($id=0, $status=0, $received_date='', $payment_method=0) {
        $data = array(
            'note_status'=>$status,
            'checked_by' =>$this->session->userdata('user_id'),
            'received_date' => $received_date,
            'payment_method' => $payment_method
        );
        $this->db->where('note_id', $id);
        $this->db->update('cash_request_note', $data);
    }
    
    public function insert_trans_cashreq($note_id=0, $account_asset_id = 0) {
        $trans_date = date('Y-m-d');
        $trans_code = $this->general_model->get_generate_number('TR');
        
        $cashnote = $this->cashreqnote_model->get_cashreqnote_by_id($note_id);
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
                'note_id' => $note_id,
                'payment_mode' => $payment_mode
            );

            $this->db->insert('transactions', $data);
            $trans_id = $this->db->insert_id(); /*get trans id*/
            $this->load->model('ledger_model');
            $this->ledger_model->insert_ledger($trans_id, $account_expense_id, $amount, 0);
            $this->ledger_model->insert_ledger($trans_id, $account_asset_id, 0, $amount);
        }
    }
    
    public function insert_trans_cash_return($note_id=0, $cash_return = 0) {
        $trans_date = date('Y-m-d');
        $trans_code = $this->general_model->get_generate_number('TR');
        
        $cashnote = $this->cashreqnote_model->get_cashreqnote_by_id($note_id);
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
                'note_id' => $note_id,
                'payment_mode' => $payment_mode
            );

            $this->db->insert('transactions', $data);
            $trans_id = $this->db->insert_id(); /*get trans id*/
            $this->load->model('ledger_model');
            $this->ledger_model->insert_ledger($trans_id, $account_expense_id, 0, $amount);
            $this->ledger_model->insert_ledger($trans_id, $account_asset_id, $amount, 0);
        }
    }
}
