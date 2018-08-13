<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sysprop_model
 *
 * @author mchen
 */
class Sysprop_model extends CI_Model {
    
    public function get_sys_prop($sys_prop_name=''){
        $sql  = 'SELECT * FROM tb_sys_property ';
        $sql .= 'WHERE sys_prop_name="'.$sys_prop_name.'"';
        $query = $this->db->query($sql);
        return $query;
    }
    
}
