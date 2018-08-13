<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Thirdparty
 *
 * @author Hendra McHen
 */
class Thirdparty extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('thirdparty_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_08 = $this->asik_model->master_08;
        if ($category == $category_code){
            if (($module == $module_08) && ($string == $category.$module)){
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
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                /* end privilege */
                $data['list'] = $this->thirdparty_model->get_third_party_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Third Party List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'thirdparty/third_party_list.php';
                /* form field */
                $data['third_party_id'] = $this->general_model->draw_hidden_field('third_party_id', '');
                $data['third_party_name'] = $this->general_model->draw_text_field('Third Party Name', 1, 'third_party_name', '', '', '');
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['datatable_title'] = 'Third Party';
                $data['footer_total'] = '';
                $data['show_modal'] = 'thirdparty/third_party_modal.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_third_party_ajax() {
        $this->thirdparty_model->get_third_party_ajax();
    }
    
    public function ajax_edit($id) {
        $data = $this->thirdparty_model->get_third_party_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function third_party_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('third_party_name', 'Thrid party name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->thirdparty_model->insert_third_party();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function third_party_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('third_party_name', 'Thrid party Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->thirdparty_model->update_third_party();
            echo json_encode(array("status" => TRUE));
        }
    }

    public function third_party_delete($id) {
        $this->thirdparty_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}