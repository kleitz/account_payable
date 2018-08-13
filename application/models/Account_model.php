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
class Account_model extends CI_Model {
    public $account_type_opt = array('Asset', 'Liability', 'Equity', 'Revenue', 'Expense');

    public function get_account_list() {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'ORDER BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_account_by_id($id=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_id='.$id.' ORDER BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_account_by_type($type=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_type='.$type.' ORDER BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_account_by_branch_type($branch_id=0, $type=0, $keyword='') {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE branch_id='.$branch_id.' AND account_type='.$type. ' AND account_name LIKE "'.$keyword.'%"';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_account_by_keyword($type=0, $keyword='') {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_type='.$type. ' AND account_name LIKE "'.$keyword.'%"';
        $query = $this->db->query($sql);
        return $query;
    }

    public function insert_account($data) {
        $this->db->insert('account', $data);
        
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $this->load->model('branch_model');
        $account_code = $this->input->post('account_code');
        $account_name = $this->input->post('account_name');
        $description = $this->input->post('description');
        $debit = $this->input->post('debit');
        $credit = $this->input->post('credit');
        $account_type = $this->input->post('account_type');
        $branch_id = $this->input->post('branch_id');
        $branch =$this->branch_model->get_branch_by_id($branch_id);
        $branch_name = '';
        if ($branch->num_rows()!=0){
            $row = $branch->row();
            $branch_name = $row->branch_name;
        }
        $log_desc = array(
            'account code' => $account_code,
            'account name' => $account_name,
            'description' => $description,
            'debit' => $debit,
            'credit' => $credit,
            'account type' => $account_type,
            'branch' => $branch_name
        );
        $this->log_activity_model->insert_log('Account', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_account($where, $data) {
        $this->db->update('account', $data, $where);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $this->load->model('branch_model');
        $account_code = $this->input->post('account_code');
        $account_name = $this->input->post('account_name');
        $description = $this->input->post('description');
        $debit = $this->input->post('debit');
        $credit = $this->input->post('credit');
        $account_type = $this->input->post('account_type');
        $branch_id = $this->input->post('branch_id');
        $branch =$this->branch_model->get_branch_by_id($branch_id);
        $branch_name = '';
        if ($branch->num_rows()!=0){
            $row = $branch->row();
            $branch_name = $row->branch_name;
        }
        
        $log_desc = array(
            'account code' => $account_code,
            'account name' => $account_name,
            'description' => $description,
            'debit' => $debit,
            'credit' => $credit,
            'account type' => $account_type,
            'branch' => $branch_name
        );
        $this->log_activity_model->insert_log('Account', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $account = $this->get_account_by_id($id);
        $log_desc = array();
        
        if ($account->num_rows()!=0){
            $row = $account->row();
            $this->load->model('branch_model');
            $branch = $this->branch_model->get_branch_by_id($row->branch_id);
            $branch_name = '';
            if ($branch->num_rows()!=0){
                $rowbr = $branch->row();
                $branch_name = $rowbr->branch_name;
            }
            
            $log_desc = array(
                'account code' => $row->account_code,
                'account name' => $row->account_name,
                'description' => $row->description,
                'debit' => $row->debit,
                'credit' => $row->credit,
                'account type' => $row->account_type,
                'branch' => $branch_name
            );
        }
        
        $this->log_activity_model->insert_log('Account', 'Delete', $log_desc);
        /*== end log ==*/
        $this->db->where('account_id', $id);
        $this->db->delete('account');
    }

}
