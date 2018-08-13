<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of privilege_model
 *
 * @author mchen
 */

class Privilege_model extends CI_Model {
    public function get_group_list() {
        $sql  = 'SELECT * FROM privilege_group';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_group_by_id($id=0) {
        $sql  = 'SELECT * FROM privilege_group ';
        $sql .= 'WHERE priv_group_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_priv_detail($priv_group_id=0) {
        $sql = 'SELECT prv.*, mo.module_name, a.action_name FROM privilege_user AS prv
        INNER JOIN module_action AS ma ON prv.module_action_id=ma.module_action_id
        INNER JOIN module AS mo ON mo.module_id=ma.module_id
        INNER JOIN action AS a ON a.action_id=ma.action_id
        WHERE prv.priv_group_id='.$priv_group_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_module_action($wherein) {
        $sql = 'SELECT ma.module_action_id, mo.module_name,  a.action_name FROM  module_action AS ma
        INNER JOIN action AS a ON ma.action_id=a.action_id
        INNER JOIN module AS mo ON ma.module_id=mo.module_id  
        WHERE ma.module_action_id NOT IN('.$wherein.') 
        ORDER BY mo.module_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_privilege_by_priv_id($priv_id=0) {
        $sql = 'SELECT p.*, mo.module_name, a.action_name  FROM privilege_user AS p
        INNER JOIN module_action AS ma ON p.module_action_id=ma.module_action_id
        INNER JOIN module AS mo ON mo.module_id=ma.module_id
        INNER JOIN action AS a ON a.action_id=ma.action_id
        WHERE p.privilege_id='.$priv_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_mo_action() {
        $sql = 'SELECT ma.module_action_id, mo.module_name, a.action_name FROM module_action AS ma
        INNER JOIN module AS mo ON ma.module_id=mo.module_id
        INNER JOIN action AS a ON ma.action_id=a.action_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_group() {
        $priv_group_name = $this->input->post('priv_group_name');
        
        $data = array(
            'priv_group_name' => $priv_group_name
        );
        $this->db->insert('privilege_group', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'Priv Group Name' => $priv_group_name
        );
        $this->log_activity_model->insert_log('Privilege Group', 'Add', $log_desc);
        /*== end log ==*/
    }
    
    public function update_group($id=0) {
        $priv_group_name = $this->input->post('priv_group_name');
        
        $data = array(
            'priv_group_name' => $priv_group_name
        );

        $this->db->where('priv_group_id', $id);
        $this->db->update('privilege_group', $data);
        /*== insert to log activity ==*/
        $this->load->model('log_activity_model');
        $log_desc = array(
            'Priv Group Name' => $priv_group_name
        );
        $this->log_activity_model->insert_log('Privilege Group', 'Edit', $log_desc);
        /*== end log ==*/
    }
    
    public function delete_group($id=0) {
        $group = $this->get_group_by_id($id);
        if ($group->num_rows()!=0){
            $row = $group->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');
            $log_desc = array(
                'Priv Group Name' => $row->priv_group_name
            );
            $this->log_activity_model->insert_log('Privilege Group', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('priv_group_id', $id);
        $this->db->delete('privilege_group');
    }
    
    public function delete_detail($id=0) {
        $priv = $this->get_privilege_by_priv_id($id);
        if ($priv->num_rows()!=0){
            $row = $priv->row();
            /*== insert to log activity ==*/
            $this->load->model('log_activity_model');
            $log_desc = array(
                'Module' => $row->module_name,
                'Action' => $row->action_name
            );
            $this->log_activity_model->insert_log('Privilege Detail', 'Delete', $log_desc);
            /*== end log ==*/
        }
        $this->db->where('privilege_id', $id);
        $this->db->delete('privilege_user');
    }
   
}
