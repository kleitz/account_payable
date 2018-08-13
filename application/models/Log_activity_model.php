<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log_activity_model
 *
 * @author mchen
 */
class Log_activity_model extends CI_Model {

    public function get_activity_list($start_date='', $end_date=''){
        $sql  = 'SELECT lg.*, u.fullname FROM log_activity AS lg ';
        $sql .= 'INNER JOIN users AS u ON lg.user_id=u.user_id ';
        $sql .= 'WHERE lg.log_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER by lg.log_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_activity_by_id($id=0){
        $sql  = 'SELECT lg.*, u.fullname FROM log_activity AS lg ';
        $sql .= 'INNER JOIN users AS u ON lg.user_id=u.user_id ';
        $sql .= 'WHERE lg.log_activity_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_log($module_name='', $action_name='', $description=array()) {
        $log_description = '';
        if (sizeof($description)!=0){
            $log_description = '<table class="table table-bordered table-striped table-hover">';
            foreach ($description as $key => $value) {
                $log_description .= '<tr><th>' . $key .'</th><td>'.$value . '</td></tr>';
            }
            $log_description .= '</table>';
        }
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'module_name' => $module_name,
            'action_name' => $action_name,
            'description' => $log_description,
            'log_date' => date('Y-m-d')
        );
        $this->db->insert('log_activity', $data);
    }
}
