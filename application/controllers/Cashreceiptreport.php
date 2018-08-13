<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cashreceiptreport
 *
 * @author Hendra McHen
 */
class Cashreceiptreport extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_02) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0, $startd='', $endd='') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $this->load->helper('form');
                
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }
                
                if ($button == 0){
                    $receipt_in_bank = $this->get_receipt_in_bank($start_date, $end_date);
                    $receipt_in_outlet = $this->get_receipt_in_outlet($start_date, $end_date);
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
                            $start_date = date('Y-m-d',strtotime("-1 days"));
                            $end_date = date('Y-m-d',strtotime("-1 days"));
                            break;
                        case 3:
                            $signupdate = $year.'-'.$month.'-'.$day;
                            $signupweek = date("W",strtotime($signupdate));

                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 4:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 5:
                            if ($month == 1){
                                $last_month = '12';
                                $year = $year - 1;
                            } else {
                                $last_month = $month - 1;
                            }
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 6:
                            $start_date = '2018-01-01';
                            $end_date = date('Y-m-d');
                            break;
                    }
                    $receipt_in_bank = $this->get_receipt_in_bank($start_date, $end_date);
                    $receipt_in_outlet = $this->get_receipt_in_outlet($start_date, $end_date);
                }
                
                // update 14 June 2018
                // cek report file by start_date, end_date
                $report_type = 2;
                $reporthistory = $this->general_model->get_report_by_date($start_date, $end_date, $report_type);
                $report_id = 0;
                $checked_name = '0';
                $approved_name = '0';
                $report_file = '0';
                if ($reporthistory->num_rows()!=0){
                    $row = $reporthistory->row();
                    $report_id = $row->report_file_id;
                    $checked_name = $this->general_model->get_user_by_id($row->checked_by);
                    if ($row->approved_by != 0){
                        $approved_name = $this->general_model->get_user_by_id($row->approved_by);
                    }
                    $report_file = $row->file_name;
                }
                $data['report_id'] = $report_id;
                $data['checked_name'] = $checked_name;
                $data['approved_name'] = $approved_name;
                $data['report_file'] = $report_file;
                $data['report_type'] = $report_type;
                $data['url_module'] = 'cashreceiptreport';
                               
                $branch = $this->get_branch_list();
                $array_tbl = array(array());
                /*inisialisasi*/
                $array_tbl[0][0] = '0';
                $array_tbl[0][1] = '0';
                $k = 2;
                foreach ($branch as $b) {
                    $array_tbl[0][$k] = 0;
                    $k++;
                }
                $array_tbl[0][$k] = 0;
                $baris = 1;
                /*=================== Receipt in Bank ========================*/
                if ($receipt_in_bank->num_rows()!=0){
                    $array_tbl[0][0] = '<strong>Dates</strong>';
                    $array_tbl[0][1] = '<strong>Receipt in Bank</strong>';
                    $k = 2;
                    foreach ($branch as $b) {
                        $array_tbl[0][$k] = 0;
                        $k++;
                    }
                    $array_tbl[0][$k] = 0;
                    foreach ($receipt_in_bank->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->receive_bank_id);
                        $total = 0;
                        $array_tbl[$baris][0] = $value->receive_bank_date;
                        $array_tbl[$baris][1] = '<a href="'. site_url('receiveinbank/detail/20191121214305/'.$enc_id).'" target="_blank">' .$value->receive_bank_number . '</a>';
                        $k = 2;
                        foreach ($branch as $b) {
                            $array_tbl[$baris][$k] = 0;
                            if ($value->branch_name == $b){
                                $array_tbl[$baris][$k] = $value->amount;
                                $total = $total + $array_tbl[$baris][$k];
                            }
                            $k++;
                        }
                        $array_tbl[$baris][$k] = 0;
                        $array_tbl[$baris][$k] = $total;
                        $baris++;
                    }
                    /* get Total Receipt in Bank 
                    $k = 2;
                    foreach ($branch as $b) {
                        $array_tbl[0][$k] = $arrtotal[$k];
                        $k++;
                    }
                    $array_tbl[0][$k] = 0;*/
                }
                /*======================== end =================================*/
                /*=================== Receipt in Outlet ========================*/
                if ($receipt_in_outlet->num_rows()!=0){
                    $array_tbl[$baris][0] = '<strong>Dates</strong>';
                    $array_tbl[$baris][1] = '<strong>Receipt in Outlet</strong>';
                    $k = 2;
                    foreach ($branch as $b) {
                        $array_tbl[$baris][$k] = 0;
                        $k++;
                    }
                    $array_tbl[$baris][$k] = 0;
                    $baris++;
                    foreach ($receipt_in_outlet->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->cash_receive_id);
                        $total = 0;
                        $array_tbl[$baris][0] = $value->cash_receive_date;
                        $array_tbl[$baris][1] = '<a href="'. site_url('cashreceived/detail/20191121214303/'.$enc_id).'" target="_blank">'.$value->cash_receive_number.'</a>';
                        $k = 2;
                        foreach ($branch as $b) {
                            $array_tbl[$baris][$k] = 0;
                            if ($value->branch_name == $b){
                                $array_tbl[$baris][$k] = $value->amount;
                                $total = $total + $array_tbl[$baris][$k];
                            }
                            $k++;
                        }
                        $array_tbl[$baris][$k] = 0;
                        $array_tbl[$baris][$k] = $total;
                        $baris++;
                    }
                }
                /*======================== end =================================*/
                        
                $data['array_tbl'] = $array_tbl; 
                $data['branch'] = $branch;
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Cash Receipt Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/cashreceipt_report.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Cash Receipt Report';
                $footer_total = '';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_receipt_in_bank($start_date='', $end_date='') {
        $sql  = 'SELECT r.*, b.branch_name FROM receive_bank AS r ';
        $sql .= 'INNER JOIN branch AS b ON r.branch_id=b.branch_id ';
        $sql .= 'WHERE r.receive_bank_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_receipt_in_outlet($start_date='', $end_date='') {        
        $sql  = 'SELECT cr.*, b.branch_name FROM cash_receive AS cr ';
        $sql .= 'INNER JOIN branch AS b ON cr.branch_id=b.branch_id ';
        $sql .= 'WHERE cr.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM branch AS b 
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0){
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_name;
            }
        }
        return $branch;
    }
    
    public function action_checked($start_date='', $end_date='') {
        $this->general_model->action_checked($start_date, $end_date, 2, '/cashreceiptreport/go/20191341214302/0/');
    }
    
    public function action_approved($report_file_id = 0, $start_date='', $end_date='') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/cashreceiptreport/go/20191341214302/0/');
    }
    
    public function do_upload() {
        $this->general_model->do_upload('/cashreceiptreport/go/20191341214302/0/');
    }
    
}