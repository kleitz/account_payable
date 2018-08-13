<?php

/**
 * Description of Trans
 *
 * @author mchen
 */
class Trans extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('trans_model');
        $this->load->model('general_model');
        $this->load->helper('date');
    }
    
    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function is_check_module($string='', $category='', $module=''){
        $category_code = $this->asik_model->category_transaction;
        $module_01 = $this->asik_model->trans_01;
        $module_02 = $this->asik_model->trans_02;
        $module_03 = $this->asik_model->trans_03;
        $module_04 = $this->asik_model->trans_04;
        $module_05 = $this->asik_model->trans_05;
        $module_06 = $this->asik_model->trans_06;
        $module_07 = $this->asik_model->trans_07;
        if ($category == $category_code){
            if (($module == $module_01) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_02) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_03) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_04) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_05) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_06) && ($string == $category.$module)){
                return TRUE;
            }
            if (($module == $module_07) && ($string == $category.$module)){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
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
                /* ===== end get active period ===== */
                
                $this->load->helper('form');
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                
                if ($module == $this->asik_model->trans_01){                    
                    $data = $this->trans01_form();
                    $header = $this->asik_model->draw_header('Closing Balance', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans01_view';
                    $modal = 'trans01_modal';
                    $type = 0;
                }
                if ($module == $this->asik_model->trans_02){
                    $data = $this->trans02_form();
                    $header = $this->asik_model->draw_header('Cash Received', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans02_view';
                    $modal = 'trans02_modal';
                    $type = 1;
                }
                if ($module == $this->asik_model->trans_03){
                    $data = $this->trans03_form();
                    $header = $this->asik_model->draw_header('Cash Returned', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans03_view';
                    $modal = 'trans03_modal';
                    $type = 2;
                }
                if ($module == $this->asik_model->trans_04){
                    $data = $this->trans04_form();
                    $header = $this->asik_model->draw_header('Cash Request', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans04_view';
                    $modal = 'trans04_modal';
                    $type = 3;                   
                }
                if ($module == $this->asik_model->trans_05){
                    $data = $this->trans05_form();
                    $header = $this->asik_model->draw_header('Account Payable', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans05_view';
                    $modal = 'trans05_modal';
                    $type = 4;
                }
                if ($module == $this->asik_model->trans_06){
                    $data = $this->trans06_form();
                    $header = $this->asik_model->draw_header('Payment Voucher', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans06_view';
                    $modal = 'trans06_modal';
                    $type = 5;
                }
                if ($module == $this->asik_model->trans_07){
                    $data = $this->trans07_form();
                    $header = $this->asik_model->draw_header('Expenses', $period_title, $this->category_index, $category, $module);
                    $halaman = 'trans07_view';
                    $modal = 'trans07_modal';
                    $type = 6;
                }
                /*=============================================================*/
                if ($button == 0){
                    $data['list'] = $this->trans_model->get_transaction_by_type($type, $start_date, $end_date);
                    if ($type == 4){
                        $this->load->model('payment_process_model');
                        $data['payment_process_list'] = $this->payment_process_model->get_payment_process_by_status(3);
                        $data['list'] = $this->trans_model->get_transaction_by_pp($type, $start_date, $end_date);
                    }
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    //////////////////                    
                    switch ($button) {
                        case 1:
                            $start_date = date('Y-m-d');
                            $end_date = date('Y-m-d');
                            break;
                        case 2:
                            $signupdate=$year.'-'.$month.'-'.$day;
                            $signupweek=date("W",strtotime($signupdate));
                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 3:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $year.'-'.$month.'-31';
                            break;
                        case 4:
                            $last_month = $month - 1;
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $year.'-'.$last_month.'-31';
                            break;
                    }
                    $data['list'] = $this->trans_model->get_transaction_by_type($type, $start_date, $end_date);
                    if ($type == 4){
                        $this->load->model('payment_process_model');
                        $data['payment_process_list'] = $this->payment_process_model->get_payment_process_by_status(3);
                        $data['list'] = $this->trans_model->get_transaction_by_pp($type, $start_date, $end_date);
                    }
                }
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $data['period_title'] = $period_title;
                $data['period_active'] = $period_active;
                $data['period_month'] = $period_month;
                $data['content_header'] = $header;
                $data['halaman'] = 'transaction/'.$halaman.'.php';  
                $data['show_modal'] = 'transaction/'.$modal.'.php';
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function info($string='', $index=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            $text = array(
                'Account Asal sama dengan Account Tujuan, silahkan ulangi transaksi.',
                'Test ke 2'
            );
            $header = $this->asik_model->draw_header('Warning', '', 3, $category, $module);      
            $data['content_header'] = $header;
            $data['active_li'] = 3;
            $data['info'] = $text[$index];
            $data['back_link'] = 'trans/go/'.$string;
            $this->load->view('template', $data);
        } else {
            show_404();
        }
    }

    public function trans01_form() {        
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_type(2);
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $account2 = $this->account_model->get_account_by_type(0);
        $account_opt2 = array();
        if ($account2->num_rows()!=0){
            foreach ($account2->result() as $value) {
                $account_opt2[$value->account_id] = $value->account_name;
            }
        }
        $data['account_opt'] = $account_opt2;
        $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
        $data['account_relation'] = $this->general_model->draw_select('Account Relation', 0, 'account_relation', 1, $account_opt2, '', 1);
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
        return $data;
    }
    
    public function trans02_form() {
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_keyword(0, 'Petty');
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $data['account_opt'] = $account_opt;
        $data['account_id'] = $this->general_model->draw_select('Received From', 0, 'account_id', 1, $account_opt, '', 1);
        $data['account_relation'] = $this->general_model->draw_select('Received By', 0, 'account_relation', 1, $account_opt, '', 1);
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
        return $data;
    }
    
    public function trans03_form() {
        $data['list_received'] = $this->trans_model->get_transaction_received();
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_received_id'] = $this->general_model->draw_hidden_field('trans_received_id', '');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_type(0);
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $data['account_opt'] = $account_opt;
        $data['account_id'] = $this->general_model->draw_hidden_field('account_id', '');
        $data['account_relation'] = $this->general_model->draw_hidden_field('account_relation', '');
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
        return $data;
    }
    
    public function trans04_form() {
        $this->load->model('cashreqnote_model');
        $data['cashreq_list'] = $this->cashreqnote_model->get_cashreqnote_draft();
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        
        $mode = array('Petty Cash', 'Petty Bank');
        $data['note_id'] = $this->general_model->draw_hidden_field('note_id', '');
        $data['payment_mode'] = $this->general_model->draw_select('Payment Mode', 0, 'payment_mode', 0, $mode, '');
        return $data;
    }
    
    public function trans05_form() {
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', '');
        return $data;
    }
    
    public function trans06_form() {
        $this->load->model('payment_process_model');
        $data['payment_process_list'] = $this->payment_process_model->get_payment_process_by_status(4);
        $data['list_pv'] = $this->trans_model->get_payment_voucher();
        /* form field */
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['branch_id'] = $this->general_model->draw_hidden_field('branch_id', '');
        $data['pv_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['pv_date'] = $this->general_model->draw_datepicker('Date', 1, 'pv_date', '');
        $data['pv_number'] = $this->general_model->draw_text_field('Number', 1, 'pv_number', '', '', '');
        $data['pp_id'] = $this->general_model->draw_hidden_field('pp_id', '');
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['admin_fee'] = $this->general_model->draw_input_currency('Admin fee', 0, 'admin_fee', '');
        $mode = array('Petty Cash', 'Petty Bank');
        $data['payment_mode'] = $this->general_model->draw_select('Payment Mode', 0, 'payment_mode', 0, $mode, '');
        $data['received_by'] = $this->general_model->draw_text_field('Received by', 1, 'received_by', '', '', '');
        $data['total'] = $this->general_model->draw_hidden_field('total', '');
        return $data;
    }
    
    public function trans07_form() {
        /* form field */
        $today = date('Y-m-d');
        $number = $this->general_model->get_generate_number('TR');
        $data['trans_id'] = $this->general_model->draw_hidden_field('trans_id', '');
        $data['trans_date'] = $this->general_model->draw_datepicker('Trans Date', 1, 'trans_date', $today);
        $data['trans_code'] = $this->general_model->draw_text_field('Trans Code', 1, 'trans_code', '', '', $number);
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_keyword(4, 'Expense');
        $account_opt = array();
        if ($account->num_rows()!=0){
            foreach ($account->result() as $value) {
                $account_opt[$value->account_id] = $value->account_name;
            }
        }
        $data['account_opt'] = $account_opt;
        $data['account_id'] = $this->general_model->draw_select('Account', 0, 'account_id', 1, $account_opt, '', 1);
        $data['description'] = $this->general_model->draw_text_field('Description', 0, 'description', '', '', '');
        $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
        return $data;
    }
    
    public function trans01_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans01_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        $trans_date = $this->input->post('trans_date');
        $substr_trans_date = substr($trans_date, 0, 7);
        $substr_start_date = '';
        $this->load->model('period_model');
        $period = $this->period_model->get_period_active();
        if ($period->num_rows()!=0){
            $row = $period->row();
            $substr_start_date = substr($row->start_date, 0, 7);
        }
        if ($substr_trans_date == $substr_start_date){
            if ($this->form_validation->run() == TRUE) {
                $this->trans_model->insert_trans01();
                echo json_encode(array("status" => TRUE));
            }
        }       
        
    }
    
    public function trans01_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans01($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans01_delete($id) {
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans02_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans02_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        $account1 = $this->input->post('account_id');
        $account2 = $this->input->post('account_relation');
        if ($this->form_validation->run() == TRUE) {
            if ($account1 != $account2){
                $this->trans_model->insert_trans02();
                echo json_encode(array("status" => TRUE));
            }
        }
    }
    
    public function trans02_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans02($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans02_delete($id) {
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    public function trans03_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function trans03_ajax_get_received($trans_id) {
        $data = $this->trans_model->get_received_by_trans_id($trans_id)->row();
        echo json_encode($data);
    }
   
    public function trans03_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_trans03();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans03_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans03($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans03_delete($id) {
        $data = $this->trans_model->get_transaction_by_id($id);
        if ($data->num_rows()!=0){
            $row = $data->row();
            $this->load->model('ledger_model');
            $this->ledger_model->delete_received_credit($row->trans_received_id, $row->amount);
        }
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans04_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans04_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_trans04();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans04_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans04($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans04_delete($id, $note_id) {
        $this->trans_model->delete_trans_by_id($id);
        $data = array('note_status'=>0);
        $this->db->where('note_id', $note_id);
        $this->db->update('cash_request_note', $data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans05_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans05_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_trans05();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans05_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans05($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans05_delete($id, $pp_id) {
        $this->trans_model->delete_trans_by_id($id);
        $data_pp = array(
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data_pp);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans06_ajax_pp($pp_id) {
        $this->load->model('payment_process_model');
        $data = $this->payment_process_model->get_payment_process_by_id($pp_id)->row();
        echo json_encode($data);
    }
    
    public function trans06_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans06_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('pv_date', 'Date', 'required');
        $this->form_validation->set_rules('pv_number', 'Number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->insert_trans06();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans06_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('pv_date', 'Date', 'required');
        $this->form_validation->set_rules('pv_number', 'Number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans06($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function trans06_delete($id, $pp_id) {
        $this->trans_model->delete_trans_by_id($id);
        $data_pp = array(
            'pp_status' => 3
        );
        $this->db->where('pp_id', $pp_id);
        $this->db->update('payment_process', $data_pp);
        echo json_encode(array("status" => TRUE));
    }
    
    public function trans07_ajax_view($id) {
        $data = $this->trans_model->get_transaction_by_id($id)->row();
        echo json_encode($data);
    }
   
    public function trans07_add() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        $trans_date = $this->input->post('trans_date');
        $substr_trans_date = substr($trans_date, 0, 7);
        $substr_start_date = '';
        $this->load->model('period_model');
        $period = $this->period_model->get_period_active();
        if ($period->num_rows()!=0){
            $row = $period->row();
            $substr_start_date = substr($row->start_date, 0, 7);
        }
        if ($substr_trans_date == $substr_start_date){
            if ($this->form_validation->run() == TRUE) {
                $this->trans_model->insert_trans07();
                echo json_encode(array("status" => TRUE));
            }
        }    
    }
    
    public function trans07_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('trans_date', 'Trans Date', 'required');
        $this->form_validation->set_rules('trans_code', 'Trans Code', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->trans_model->update_trans07($this->input->post('trans_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function trans07_delete($id) {
        $this->trans_model->delete_trans_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}
