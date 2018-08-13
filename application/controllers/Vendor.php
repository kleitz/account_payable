<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vendor
 *
 * @author hendramchen
 */
class Vendor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('vendor_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_09 = $this->asik_model->master_09;
        if ($category == $category_code){
            if (($module == $module_09) && ($string == $category.$module)){
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
                $data['list'] = $this->vendor_model->get_vendor_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Vendor List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'vendor/vendor_list.php';
                /* form field */
                $data['vendor_id'] = $this->general_model->draw_hidden_field('vendor_id', '');
                $data['vendor_name'] = $this->general_model->draw_text_field('Vendor Name', 1, 'vendor_name', '', '', '');
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['phone'] = $this->general_model->draw_text_field('Phone', 1, 'phone', '', '', '');
                $data['datatable_title'] = 'Vendor';
                $data['footer_total'] = '';
                $data['show_modal'] = 'vendor/vendor_modal.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_vendor_ajax() {
        $this->vendor_model->get_vendor_ajax();
    }
    
    public function ajax_edit($id) {
        $data = $this->vendor_model->get_vendor_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function vendor_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('vendor_name', 'Vendor name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->vendor_model->insert_vendor();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function vendor_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('vendor_name', 'Vendor name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->vendor_model->update_vendor();
            echo json_encode(array("status" => TRUE));
        }
    }

    public function vendor_delete($id) {
        $this->vendor_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}