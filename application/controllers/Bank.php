<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bank
 *
 * @author Hendra McHen
 */
class Bank extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('bank_model');
    }

    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_masterdata) {
            if (($module == $this->asik_model->master_07) && ($string == $category . $module)) {
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
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $category = $this->asik_model->category_masterdata;
                $module = $this->asik_model->master_07;
                
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);

                $data['back_link'] = 'bank/go/'.$string;

                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Bank List', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                
                $data['list'] = $this->bank_model->get_bank_account_list();
                $data['halaman'] = 'bank/bank_list.php';
                /* form */
        
                $number = $this->general_model->get_generate_number('BK','bank_account','bank_id');
                $status = array("Non-Active", "Active");
                $data['bank_id'] = $this->general_model->draw_hidden_field('bank_id', '');
                $data['bank_code'] = $this->general_model->draw_hidden_field('bank_code', $number);
                $data['bank_code_disabled'] = $this->general_model->draw_text_disabled('Bank ID', 'bank_code_disabled', $number);
                $data['bank_name'] = $this->general_model->draw_text_field('Bank Name', 1, 'bank_name', '', 'BCA', '');
                $data['bank_address'] = $this->general_model->draw_text_field('Bank Address', 0, 'bank_address', '', '', '');
                $data['bank_account_name'] = $this->general_model->draw_text_field('Bank Account Name', 1, 'bank_account_name', '', '', '');
                $data['bank_account_no'] = $this->general_model->draw_text_field('Bank Account No.', 1, 'bank_account_no', '', '', '');
                $data['bank_remark'] = $this->general_model->draw_textarea('Remark', 0, 'bank_remark', '');
                $data['bank_status'] = $this->general_model->draw_select('Status', 0, 'bank_status', 0, $status, 1, 0);
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                $data['datatable_title'] = 'Bank Account';
                $data['footer_total'] = '';
                $data['show_modal'] = 'bank/bank_modal.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->bank_model->get_bank_account_by_id($id)->row();
        echo json_encode($data);
    }
            
    public function bank_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('bank_code', 'Bank code', 'required');
        $this->form_validation->set_rules('bank_name', 'Bank name', 'required');
        $this->form_validation->set_rules('bank_account_name', 'Account name', 'required');
        $this->form_validation->set_rules('bank_account_no', 'Account number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->bank_model->insert_bank_account();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function bank_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('bank_code', 'Bank code', 'required');
        $this->form_validation->set_rules('bank_name', 'Bank name', 'required');
        $this->form_validation->set_rules('bank_account_name', 'Account name', 'required');
        $this->form_validation->set_rules('bank_account_no', 'Account number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->bank_model->update_bank_account($this->input->post('bank_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function bank_delete($id) {
        $this->bank_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    
}