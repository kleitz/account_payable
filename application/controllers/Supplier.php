<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplier
 *
 * @author mchen
 */
class Supplier extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_02 = $this->asik_model->master_02;
        if ($category == $category_code){
            if (($module == $module_02) && ($string == $category.$module)){
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
                $data['list'] = $this->supplier_model->get_supplier_list();
                $data['supplier_id'] = $this->general_model->draw_hidden_field('supplier_id', '');
                $data['supplier_name'] = $this->general_model->draw_text_field('Supplier name', 1, 'supplier_name', '', '', '');
                $data['address'] = $this->general_model->draw_text_field('Address', 1, 'address', '', '', '');
                $data['email'] = $this->general_model->draw_text_field('Email', 1, 'email', '', '', '');
                $data['phone'] = $this->general_model->draw_text_field('Phone', 1, 'phone', '', '', '');
                $data['account_name'] = $this->general_model->draw_text_field('Account name', 1, 'account_name', '', '', '');
                $data['account_number'] = $this->general_model->draw_text_field('Account number', 1, 'account_number', '', '', '');
                $data['bank_name'] = $this->general_model->draw_text_field('Bank name', 1, 'bank_name', '', '', '');
                $data['supplier_type'] = $this->general_model->draw_select('Supplier Type', 0, 'supplier_type', 0, array('Cash Supplier','Credit Supplier'), '');
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Supplier List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'supplier/supplier_list.php';
                $data['show_modal'] = 'supplier/supplier_modal.php';
                $data['datatable_title'] = 'Supplier';
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
        $data = $this->supplier_model->get_supplier_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function supplier_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('supplier_name', 'Branch Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $supplier_name = $this->input->post('supplier_name');
            $address = $this->input->post('address');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');            
            $account_name = $this->input->post('account_name');
            $account_number = $this->input->post('account_number');
            $bank_name = $this->input->post('bank_name');
            $supplier_type = $this->input->post('supplier_type');
            
            $data = array(
                'supplier_name' => $supplier_name,
                'address' => $address,
                'email' => $email,
                'phone' => $phone,
                'account_name' => $account_name,
                'account_number' => $account_number,
                'bank_name' => $bank_name,
                'supplier_type' => $supplier_type
            );
            $insert = $this->supplier_model->insert_supplier($data);
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function supplier_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('supplier_name', 'Branch Name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $supplier_name = $this->input->post('supplier_name');
            $address = $this->input->post('address');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $account_name = $this->input->post('account_name');
            $account_number = $this->input->post('account_number');
            $bank_name = $this->input->post('bank_name');
            $supplier_type = $this->input->post('supplier_type');
            
            $data = array(
                'supplier_name' => $supplier_name,
                'address' => $address,
                'email' => $email,
                'phone' => $phone,
                'account_name' => $account_name,
                'account_number' => $account_number,
                'bank_name' => $bank_name,
                'supplier_type' => $supplier_type
            );

            $this->supplier_model->update_supplier(array('supplier_id' => $this->input->post('supplier_id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function supplier_delete($id) {
        $this->supplier_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
}
