<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Employee_model
 *
 * @author mchen
 */
class Employee_model extends CI_Model {

    public function get_employee_ajax() {
        $output = '{ "data": [';
        $sql  = 'SELECT employee.*, branch.branch_name FROM employee ';
        $sql .= 'INNER JOIN branch ON employee.branch_id=branch.branch_id ';
        $sql .= 'ORDER BY full_name';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $no = 1;
            foreach ($query->result() as $value) {
                $output .= "[";
                $output .= '"'.$no.'",';
                $output .= '"'.$value->full_name.'",';
                $output .= '"'.$value->branch_name.'",';
                $output .= '"'.$value->description.'",';
                $output .= '"';
                $output .= '<div class=\"text-right\">';
                $output .= '<button class=\"btn btn-default btn-sm\" onclick=\"edit_data('.$value->employee_id.')\"><i class=\"fa fa-edit\"></i></button>&nbsp;';
                $output .= '<button class=\"btn btn-default btn-sm\" onclick=\"delete_data('.$value->employee_id.')\"><i class=\"fa fa-trash\"></i></button>';
                $output .= '</div>';
                $output .= '"';
                
                $output .= "],";
                $no++;
            }
        }
        $output = substr($output, 0, strlen($output)-1);
        $output .= '] }';
        
        echo $output;
    }

    public function get_employee_list() {
        $sql  = 'SELECT employee.*, branch.branch_name FROM employee ';
        $sql .= 'INNER JOIN branch ON employee.branch_id=branch.branch_id ';
        $sql .= 'ORDER BY full_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_employee_by_id($id=0) {
        $sql  = 'SELECT employee.*, branch.branch_name FROM employee ';
        $sql .= 'INNER JOIN branch ON employee.branch_id=branch.branch_id ';
        $sql .= 'WHERE employee_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_employee($data) {
        $this->db->insert('employee', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $full_name = $this->input->post('full_name');
        $branch_id = $this->input->post('branch_id');
        $description = $this->input->post('description');
        $log_desc = array(
            'full_name' => $full_name,
            'branch' => $branch_id,
            'description' => $description
        );
        $this->log_activity_model->insert_log('Employee', 'Add', $log_desc);
        /*== end log ==*/
        return $this->db->insert_id();
    }
    
    public function update_employee($where, $data) {
        $this->db->update('employee', $data, $where);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $full_name = $this->input->post('full_name');
        $branch_id = $this->input->post('branch_id');
        $description = $this->input->post('description');
        $log_desc = array(
            'full_name' => $full_name,
            'branch' => $branch_id,
            'description' => $description
        );
        $this->log_activity_model->insert_log('Employee', 'Edit', $log_desc);
        /*== end log ==*/
        return $this->db->affected_rows();
    }
    
    public function delete_by_id($id) {
        /*== insert to log activity ==*/
        $employee = $this->get_employee_by_id($id);
        if ($employee->num_rows()!=0){
            $row = $employee->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');

            $log_desc = array(
                'full name' => $row->full_name,
                'branch' => $row->branch_id,
                'description' => $row->description
            );
            $this->log_activity_model->insert_log('Employee', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('employee_id', $id);
        $this->db->delete('employee');
    }
}