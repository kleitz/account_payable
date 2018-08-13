<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Privilege
 *
 * @author Hendra McHen
 */
class Privilege extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('privilege_model');
    }
    
    public $category_index = 5;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_system;
        $module_01 = $this->asik_model->system_02;
        if ($category == $category_code){
            if (($module == $module_01) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = ''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                $data['list'] = $this->privilege_model->get_group_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Privilege Setting', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'privilege/privilege_list.php';
                /* form */
                $data['priv_group_id'] = $this->general_model->draw_hidden_field('priv_group_id', '');
                $data['priv_group_name'] = $this->general_model->draw_text_field('Group Name', 1, 'priv_group_name', '', '', '');
                $data['show_modal'] = 'privilege/privilege_modal.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->privilege_model->get_group_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function group_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('priv_group_name', 'Group Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->privilege_model->insert_group();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function group_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('priv_group_name', 'Group Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->privilege_model->update_group($this->input->post('priv_group_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function group_delete($id) {
        $this->privilege_model->delete_group($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function godetail($string = '', $encrypt_id = ''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $priv_group_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
                $data['pagecode'] = $string;
                $data['encrypt_id'] = $encrypt_id;
                $data['list'] = $this->privilege_model->get_priv_detail($priv_group_id);
                $data['active_li'] = $this->category_index;
                $group = $this->privilege_model->get_group_by_id($priv_group_id);
                $priv_group_name = '';
                if ($group->num_rows()!=0){
                    $row = $group->row();
                    $priv_group_name = $row->priv_group_name;
                }
                $header = $this->asik_model->draw_header($priv_group_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'privilege/privilege_detail.php';      
                $data['datatable_title'] = 'Privilege';
                $data['footer_total'] = '';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function priv_add($string='', $encrypt_id = '') {
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $this->load->helper('form');
        $priv_group_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $priv = $this->privilege_model->get_priv_detail($priv_group_id);
        $string_priv = '';
        if ($priv->num_rows()!=0){
            foreach ($priv->result() as $val) {
                $string_priv = $string_priv . $val->module_action_id . ',';
            }
            $string_priv = substr($string_priv, 0, strlen($string_priv)-1);
            $data['list'] = $this->privilege_model->get_module_action($string_priv);
        } else {
            $data['list'] = $this->privilege_model->get_mo_action();
        }

        $data['pagecode'] = $string;
        $data['encrypt_id'] = $encrypt_id;
        $data['priv_group_id'] = $priv_group_id;
        
        $data['active_li'] = $this->category_index;
        $group = $this->privilege_model->get_group_by_id($priv_group_id);
        $priv_group_name = '';
        if ($group->num_rows()!=0){
            $row = $group->row();
            $priv_group_name = $row->priv_group_name;
        }
        $header = $this->asik_model->draw_header($priv_group_name, 'Selection', $this->category_index, $category, $module);
        $data['content_header'] = $header;
        $data['halaman'] = 'privilege/privilege_check.php';
        $data['datatable_title'] = 'Privilege';
        $data['footer_total'] = '';
        $this->load->view('template', $data);
    }
    
    public function checked() {
        $checked = $this->input->post('checkmo');
        $priv_group_id = $this->input->post('priv_group_id');
        $pagecode = $this->input->post('pagecode');
        $encrypt_id = $this->input->post('encrypt_id');
        if (isset($checked)){
            $i = 0;
            $data_query = array();
            foreach ($checked as $value) {
                $module_action_id = $value;
                $datastr = array(
                    'priv_group_id' => $priv_group_id,
                    'module_action_id' => $module_action_id
                );    
                $data_query[$i] = $datastr;
                $i++;
            }
            
            $this->db->insert_batch('privilege_user', $data_query);
            
        }
        $back = '/privilege/godetail/'.$pagecode.'/'.$encrypt_id;
        redirect($back);
    }
    
    public function delete_detail($id = 0) {
        $this->privilege_model->delete_detail($id);
        echo json_encode(array("status" => TRUE));
    }
}
