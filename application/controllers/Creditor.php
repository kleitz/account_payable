<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Creditor
 *
 * @author mchen
 */
class Creditor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('creditor_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_masterdata) {
            if (($module == $this->asik_model->master_06) && ($string == $category . $module)) {
                $this->category_index = 1;
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('user_level'))) {
                $this->category = $category;
                $this->module = $module;
                $mdl = $this->asik_model->category_masterdata . $this->asik_model->master_06;
                $action_module = 'creditor/ac/' . $mdl;
                $go_module = 'creditor/go/' . $mdl;
                $data['action_mo'] = $action_module;
                $data['go_mo'] = $go_module;
                $data['list'] = $this->creditor_model->get_creditor_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Creditor List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'creditor/creditor_list.php';
                /* form input */
                $data['creditor_id'] = $this->general_model->draw_hidden_field('creditor_id', '');
                $data['creditor_name'] = $this->general_model->draw_text_field('Creditor name', 1, 'creditor_name', '', '', '');
                $data['address'] = $this->general_model->draw_text_field('Address', 1, 'address', '', '', '');
                $data['phone'] = $this->general_model->draw_text_field('Phone', 1, 'phone', '', '', '');
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['show_modal'] = 'creditor/creditor_modal.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function ajax_edit($id) {
        $data = $this->creditor_model->get_creditor_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function creditor_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('creditor_name', 'Creditor name', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->creditor_model->insert_creditor();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function creditor_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('creditor_name', 'Creditor name', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->creditor_model->update_creditor($this->input->post('creditor_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function creditor_delete($id) {
        $this->creditor_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

}
