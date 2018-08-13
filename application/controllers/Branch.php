<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Branch
 *
 * @author mchen
 */
class Branch extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('branch_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_03 = $this->asik_model->master_03;
        if ($category == $category_code){
            if (($module == $module_03) && ($string == $category.$module)){
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
                $data['list'] = $this->branch_model->get_branch_list();
                $data['branch_id'] = $this->general_model->draw_hidden_field('branch_id', '');
                $data['branch_name'] = $this->general_model->draw_text_field('Branch name', 1, 'branch_name', '', '', '');
                $data['address'] = $this->general_model->draw_text_field('Address', 1, 'address', '', '', '');
                $data['email'] = $this->general_model->draw_text_field('Email', 1, 'email', '', '', '');
                $data['phone'] = $this->general_model->draw_text_field('Phone', 1, 'phone', '', '', '');
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Branch List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'branch/branch_list.php';
                $data['show_modal'] = 'branch/branch_modal.php';
                $data['datatable_title'] = 'Branch';
                $data['footer_total'] = '';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->branch_model->get_branch_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function branch_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('branch_name', 'Branch Name', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $branch_name = $this->input->post('branch_name');
            $address = $this->input->post('address');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $data = array(
                'branch_name' => $branch_name,
                'address' => $address,
                'email' => $email,
                'phone' => $phone
            );
            $insert = $this->branch_model->insert_branch($data);
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function branch_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('branch_name', 'Branch Name', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $branch_name = $this->input->post('branch_name');
            $address = $this->input->post('address');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $data = array(
                'branch_name' => $branch_name,
                'address' => $address,
                'email' => $email,
                'phone' => $phone
            );

            $this->branch_model->update_branch(array('branch_id' => $this->input->post('branch_id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function branch_delete($id) {
        $this->branch_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
