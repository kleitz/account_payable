<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transin
 *
 * @author JUNA
 */
class Transin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('trans_model');
        $this->load->model('general_model');
        $this->load->helper('date');
        $this->load->model('creditor_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_transaction;
        $module_01 = $this->asik_model->trans_01;
        if ($category == $category_code){
            if (($module == $module_01) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $date_search = ''){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('user_level'))){
                $this->category = $category;
                $this->module = $module;                
                /* ===== get active period ===== */
                $period_active = 0;
                $period_title = 'Belum Ada Periode Aktif';
                $period_month = '';
                $this->load->model('period_model');
                $period = $this->period_model->get_period_active();
                if ($period->num_rows()!=0){
                    $row = $period->row();
                    $period_title = $row->period.' '.$row->year;
                    $period_active = 1;
                    $period_month = substr($row->start_date, 0, 7);
                }
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                /* ===== end get active period ===== */
                $date = $date_search == '' ? $this->trans_model->get_period_date($period_month) : $date_search;
                $tanggal = $date != 0 ? $this->general_model->get_string_date($date) : '-';

                $data['active_li'] = $this->category_index;
                
                //$this->load->helper('form');
                
                $title = 'Transaction (Cash in)';
                $data['list'] = $this->trans_model->get_transaction_by_type(1, $date);
                $data['halaman'] = 'transaction/trans_in.php';                    
                
                $header = $this->asik_model->draw_header($title, 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;

                
                /* form input 
trans_id
trans_date
trans_code
trans_type
account_id
account_relation
description
amount
branch_id
supplier_id
employee_id
                 */
                $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
                $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', '');
                $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', '');
                $this->load->model('account_model');
                $account = $this->account_model->get_account_list();
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 0, $account_opt, '');
                $data['referral_account'] = $this->general_model->draw_select('Referral Account', 0, 'referral_account', 0, $account_opt, '');
                $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $this->load->model('creditor_model');
                $creditor = $this->creditor_model->get_creditor_list();
                $resource_opt = array();
                $resource_opt[0] = 'Withdraw';
                if ($creditor->num_rows()!=0){
                    foreach ($creditor->result() as $value) {
                        $resource_opt[$value->creditor_id] = $value->creditor_name;
                    }
                }
                $data['resource_from'] = $this->general_model->draw_select('Resource From', 0, 'resource_from', 0, $resource_opt, '');
                $data['show_modal'] = 'transaction/transin_modal.php';
                
                
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }

    public function sc($module = '') {
        $inp_search = $this->input->post('inp_search');
        $this->go($module, $inp_search);
    }
    
    public function ajax_edit($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function trans_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_transin();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_transin($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans_delete($id) {
        $this->trans_model->delete_transin_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
}
