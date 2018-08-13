<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Account
 *
 * @author mchen
 */
class Account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('account_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_masterdata;
        $module_01 = $this->asik_model->master_01;
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
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                /* end privilege */
                $data['list'] = $this->account_model->get_account_list();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Chart of Account', 'Account List', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'account/account_list.php';
                /* form */
                $data['account_id'] = $this->general_model->draw_hidden_field('account_id', '');
                $data['account_code'] = $this->general_model->draw_text_field('Account code', 1, 'account_code', '', '', '');
                $data['account_name'] = $this->general_model->draw_text_field('Account name', 1, 'account_name', '', '', '');
                $data['description'] = $this->general_model->draw_text_field('Description', 1, 'description', '', '', '');
                $data['debit'] = $this->general_model->draw_select('Debit', 0, 'debit', 0, array('Decreases','Increases'), '');
                $data['credit'] = $this->general_model->draw_select('Credit', 0, 'credit', 0, array('Decreases','Increases'), '');
                
                $type_opt = array('Asset','Liability','Equity','Revenue','Expense');
                $data['account_type'] = $this->general_model->draw_select('Account type', 0, 'account_type', 0, $type_opt, '');
                $this->load->model('branch_model');
                $branch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch->num_rows()!=0){
                    foreach ($branch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Branch', 0, 'branch_id', 0, $branch_opt, '');
                $data['show_modal'] = 'account/account_modal.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }    

    public function ajax_edit($id) {
        $data = $this->account_model->get_account_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function account_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_code', 'Account code', 'required');
        $this->form_validation->set_rules('account_name', 'Account name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $account_code = $this->input->post('account_code');
            $account_name = $this->input->post('account_name');
            $description = $this->input->post('description');
            $debit = $this->input->post('debit');
            $credit = $this->input->post('credit');
            $account_type = $this->input->post('account_type');
            $branch_id = $this->input->post('branch_id');
            $data = array(
                'account_code' => $account_code,
                'account_name' => $account_name,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
                'account_type' => $account_type,
                'branch_id' => $branch_id
            );
            $this->account_model->insert_account($data);
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function account_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_code', 'Account code', 'required');
        $this->form_validation->set_rules('account_name', 'Account name', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $account_code = $this->input->post('account_code');
            $account_name = $this->input->post('account_name');
            $description = $this->input->post('description');
            $debit = $this->input->post('debit');
            $credit = $this->input->post('credit');
            $account_type = $this->input->post('account_type');
            $branch_id = $this->input->post('branch_id');
            $data = array(
                'account_code' => $account_code,
                'account_name' => $account_name,
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
                'account_type' => $account_type,
                'branch_id' => $branch_id
            );

            $this->account_model->update_account(array('account_id' => $this->input->post('account_id')), $data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function account_delete($id) {
        $this->account_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}
