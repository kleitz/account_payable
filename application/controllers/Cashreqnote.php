<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashreqnote
 *
 * @author JUNA
 */
class Cashreqnote extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('cashreqnote_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_02) && ($string == $category . $module)) {
                $this->category_index = 1;
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '', $button=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_excel_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_exp_excel);
                $data['action_pdf_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_exp_pdf);
                $data['action_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['action_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                /* end privilege */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Cash Request', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                if ($button == 0){
                    $data['list'] = $this->cashreqnote_model->get_cashreqnote_list($start_date, $end_date);
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    switch ($button) {
                        case 1:
                            $start_date = date('Y-m-d');
                            $end_date = date('Y-m-d');
                            break;
                        case 2:
                            $signupdate = $year.'-'.$month.'-'.$day;
                            $signupweek = date("W",strtotime($signupdate));

                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 3:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 4:
                            $last_month = $month - 1;
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                    }
                    $data['list'] = $this->cashreqnote_model->get_cashreqnote_list($start_date, $end_date);
                }
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['halaman'] = 'cashreqnote/cashreqnote_list.php';
                
                /*===== form input =====*/
                $today = date('Y-m-d');
                $number = $this->general_model->get_generate_number('CR');
                $data['note_id'] = $this->general_model->draw_hidden_field('note_id', '');
                $data['note_number'] = $this->general_model->draw_hidden_field('note_number', $number);
                $data['note_number_disable'] = $this->general_model->draw_text_disabled('Number', 'note_number_disable', $number);
                $data['note_date'] = $this->general_model->draw_hidden_field('note_date', $today);
                $data['note_date_disable'] = $this->general_model->draw_text_disabled('Date', 'note_date_disable', $this->general_model->get_string_date($today));
                $this->load->model('employee_model');
                $employee = $this->employee_model->get_employee_list();
                $emp_opt = array();
                if ($employee->num_rows()!=0){
                    foreach ($employee->result() as $value) {
                        $emp_opt[$value->employee_id] = $value->full_name;
                    }
                }
                $this->load->model('branch_model');
                $branch = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch->num_rows()!=0){
                    foreach ($branch->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['employee_id'] = $this->general_model->draw_select('Employee', 0, 'employee_id', 1, $emp_opt, '', 1);
                $emp_ket = 'Type employee name, if there is no data in Master Employee';
                $data['employee_name'] = $this->general_model->draw_text_field('Employee name', 1, 'employee_name', '', $emp_ket, '');
                $data['branch_id'] = $this->general_model->draw_select('Branch', 0, 'branch_id', 0, $branch_opt, '');
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['remark'] = $this->general_model->draw_textarea('Remark', 1, 'remark', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $payment_opt = array('Cash', 'Transfer ATM', 'Online', 'Cek', 'BG');
                $data['payment_mode'] = $this->general_model->draw_select('Payment Method', 0, 'payment_mode', 0, $payment_opt, '', 0);
                $this->load->model('account_model');
                $account = $this->account_model->get_account_by_keyword(0, 'Petty');
                $account_opt = array();
                if ($account->num_rows()!=0){
                    foreach ($account->result() as $value) {
                        $account_opt[$value->account_id] = $value->account_name;
                    }
                }
                $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
                $data['cash_return'] = $this->general_model->draw_input_currency('Cash Return', 1, 'cash_return', '');
                $data['show_modal'] = 'cashreqnote/cashreqnote_modal.php';
                /*===== end form input =====*/
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function ajax_edit($id) {
        $data = $this->cashreqnote_model->get_cashreqnote_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function cashreqnote_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('note_number', 'Note number', 'required');
        $this->form_validation->set_rules('note_date', 'Note date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->cashreqnote_model->insert_cashreqnote();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function cashreqnote_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('note_number', 'Note number', 'required');
        $this->form_validation->set_rules('note_date', 'Note date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->cashreqnote_model->update_cashreqnote($this->input->post('note_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function cashreqnote_delete($id) {
        $this->cashreqnote_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_upload() {
        $id = $this->input->post('note_id');
        $note = $this->cashreqnote_model->get_cashreqnote_by_id($id);
        if ($note->num_rows()!=0){
            $row = $note->row();
            unlink('./assets/files/' . $row->file_name);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'cashreqnote-'.$id;
        $config['upload_path'] = './assets/files/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 3540;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->cashreqnote_model->update_file_name($id, $info['orig_name']);
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashreqnote/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/';
        redirect($back);
    }
    
    public function do_upload_pr() {
        $id = $this->input->post('note_id');
        $note = $this->cashreqnote_model->get_cashreqnote_by_id($id);
        if ($note->num_rows()!=0){
            $row = $note->row();
            unlink('./assets/files/' . $row->file_pr);
        }
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'pr-'.$id;
        $config['upload_path'] = './assets/files/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = 3540;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->cashreqnote_model->update_file_pr($id, $info['orig_name']);
        }
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashreqnote/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/';
        redirect($back);
    }
    
    public function checked($encrypt_id = '') {
        $note_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $data = array(
            'checked_by' => $this->session->userdata('user_id'),
            'note_status' => 1
        );

        $this->db->where('note_id', $note_id);
        $this->db->update('cash_request_note', $data);
        /*== redirect == purchaseorder/go/20191121214307*/
        $back = '/cashreqnote/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_02 . '/3/';
        redirect($back);
    }
    
    public function trans_pay() {
        $note_id = $this->input->post('note_id');
        $account_id = $this->input->post('account_id');
        $data = array(
            'note_status' => 2,
            'account_id' => $account_id
        );

        $this->db->where('note_id', $note_id);
        $this->db->update('cash_request_note', $data);
        $this->cashreqnote_model->insert_trans_cashreq($note_id, $account_id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans_cash_return() {
        $note_id = $this->input->post('note_id');
        $cash_return = $this->general_model->change_decimal($this->input->post('cash_return'));
        
        $data = array(
            'cash_return' => $cash_return
        );

        $this->db->where('note_id', $note_id);
        $this->db->update('cash_request_note', $data);
        
        $this->cashreqnote_model->insert_trans_cash_return($note_id, $cash_return);
        echo json_encode(array("status" => TRUE));
    }
    
    
}
