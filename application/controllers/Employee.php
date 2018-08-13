<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Employee
 *
 * @author mchen
 */
class Employee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('employee_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_05 = $this->asik_model->master_05;
        if ($category == $category_code){
            if (($module == $module_05) && ($string == $category.$module)){
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
                $data['list'] = $this->employee_model->get_employee_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Person List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'employee/employee_list.php';
                /* form field */
                $data['employee_id'] = $this->general_model->draw_hidden_field('employee_id', '');
                $data['full_name'] = $this->general_model->draw_text_field('Full name', 1, 'full_name', '', '', '');
                $this->load->model('branch_model');
                $branch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch->num_rows()!=0){
                    foreach ($branch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Branch', 0, 'branch_id', 0, $branch_opt, '');
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['datatable_title'] = 'Person';
                $data['footer_total'] = '';
                $data['show_modal'] = 'employee/employee_modal.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_employee_ajax() {
        $this->employee_model->get_employee_ajax();
    }
    
    public function ajax_edit($id) {
        $data = $this->employee_model->get_employee_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function employee_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Full name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $full_name = $this->input->post('full_name');
            $branch_id = $this->input->post('branch_id');
            $description = $this->input->post('description');
            $data = array(
                'full_name' => $full_name,
                'branch_id' => $branch_id,
                'description' => $description
            );
            $this->employee_model->insert_employee($data);
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function employee_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $full_name = $this->input->post('full_name');
            $branch_id = $this->input->post('branch_id');
            $description = $this->input->post('description');
            $data = array(
                'full_name' => $full_name,
                'branch_id' => $branch_id,
                'description' => $description
            );

            $this->employee_model->update_employee(array('employee_id' => $this->input->post('employee_id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function employee_delete($id) {
        $this->employee_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}
