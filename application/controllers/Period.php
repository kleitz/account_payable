<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Period
 *
 * @author mchen
 */
class Period extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('period_model');
    }

    private $go = FALSE;
    public $category_index = 4;
    public $category = '';
    public $module = '';

    public function set_go($go = FALSE) {
        $this->go = $go;
    }

    public function get_go() {
        return $this->go;
    }

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_masterdata) {
            if (($module == $this->asik_model->master_06) && ($string == $category . $module)) {
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
                $this->set_go(TRUE);
                $this->view_page();
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function view_page() {
        if ($this->get_go()) {
            $mdl = $this->asik_model->category_masterdata . $this->asik_model->master_06;
            $category = $this->asik_model->category_masterdata;
            $module = $this->asik_model->master_06;
            $go_module = 'period/go/' . $mdl;
            $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
            $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
            $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);

            $data['closing_link'] = 'period/closing_confirm';
            $data['active_link'] = 'period/set_active';
            $data['nonactive_link'] = 'period/set_non_active';
            $data['calendar_link'] = 'period/calendar';
            $data['back_link'] = $go_module;
            
            $data['active_li'] = $this->category_index;
            $data['page_name'] = 'Period List';
            $header = $this->asik_model->draw_header('Period List', 'View', $this->category_index, $this->category, $this->module);
            $data['content_header'] = $header;
            $data['is_active'] = $this->period_model->is_active()->num_rows();
            $data['list'] = $this->period_model->get_period_list();
            $data['halaman'] = 'period/period_list.php';
            /* form */
            $data['period_id'] = $this->general_model->draw_hidden_field('period_id', '');
            $data['year'] = $this->general_model->draw_text_field('Year', 1, 'year', '', 'Contoh: 2017', '');
            $data['sorting'] = $this->general_model->draw_text_field('Sort', 1, 'sorting', '', 'Contoh: 1', '');
            $data['period'] = $this->general_model->draw_text_field('Period', 1, 'period', '', 'Contoh: Januari', '');
            $data['start_date'] = $this->general_model->draw_datepicker('Start Date', 1, 'start_date', '');
            $data['end_date'] = $this->general_model->draw_datepicker('End Date', 1, 'end_date', '');
            $data['closed'] = $this->general_model->draw_hidden_field('closed', '');
            $data['active_status'] = $this->general_model->draw_hidden_field('active_status','');
            
            //$data['period_tt'] = $this->general_model->draw_datepicker('Tanggal Tukar', 1, 'period_tt', '');
            //$data['period_tp'] = $this->general_model->draw_datepicker('Tanggal Pembayaran', 1, 'period_tp', '');
            $data['datatable_title'] = 'Period';
            $data['footer_total'] = '';
            $data['show_modal'] = 'period/period_modal.php';
            $this->load->view('template', $data);
        } else {
            show_404();
        }
    }
    
    public function closing_confirm($id = '') {
        //$id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $this->period_model->set_closing($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function set_active($id = '') {
        //$id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $this->period_model->set_active($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function set_non_active($id = '') {
        //$id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $this->period_model->set_non_active($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function get_validation($id = 0) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('year', 'Tahun', 'required');
        $this->form_validation->set_rules('sorting', 'Urut', 'required');
        $this->form_validation->set_rules('period', 'Periode', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->draw_form($id);
        } else {
            $period_id = $this->input->post('period_id');
            if ($period_id == 0) {
                $this->period_model->insert_period();
            } else {
                $this->period_model->update_period($period_id);
            }
        }
    }
    
    public function draw_form($id){
        $this->load->helper('form');        
        $period_id = 0;
        $year = $this->input->post('year');
        $sorting = $this->input->post('sorting');
        $period = $this->input->post('period');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $closed = 0;
        $active_status = 0;
        if ($id != 0){
            $row = $this->period_model->get_period_by_id($id)->row();
            if (isset($row)){
                $period_id = $row->period_id;
                $year = $row->year;
                $sorting = $row->sorting;
                $period = $row->period;
                $start_date = $row->start_date;
                $end_date = $row->end_date;
                $closed = $row->closed;
                $active_status = $row->active_status;
            }
        }
        $data['period_id'] = $this->general_model->draw_hidden_field('period_id', $period_id);
        $data['year'] = $this->general_model->draw_text_field('Year', 1, 'year', '', 'Contoh: 2017', $year);
        $data['sorting'] = $this->general_model->draw_text_field('Sort', 1, 'sorting', '', 'Contoh: 1', $sorting);
        $data['period'] = $this->general_model->draw_text_field('Period', 1, 'period', '', 'Contoh: Januari', $period);
        
        $data['start_date'] = $this->general_model->draw_datepicker('Start Date', 1, 'start_date', $start_date);
        $data['end_date'] = $this->general_model->draw_datepicker('End Date', 1, 'end_date', $end_date);
        $data['closed'] = $this->general_model->draw_hidden_field('closed', $closed);
        $data['active_status'] = $this->general_model->draw_hidden_field('active_status',$active_status);

        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('Period', 'Form', $this->category_index, $this->category, $this->module);
        $data['content_header'] = $header;
        $data['halaman'] = 'period/period_form.php';
        $this->load->view('template', $data);
    }
    
    public function set_confirm_config($id){
        $data['active_li'] = $this->category_index;
        $header = $this->asik_model->draw_header('Period', 'Delete Confirm', $this->category_index, $this->category, $this->module);
        $data['content_header'] = $header;
        $data['row'] = $this->period_model->get_period_by_id($id)->row();
        $data['halaman'] = 'period/period_confirm.php';
        $this->load->view('template', $data);
    }
    
    public function ajax_edit($id) {
        $data = $this->period_model->get_period_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function period_add() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('year', 'Tahun', 'required');
        $this->form_validation->set_rules('sorting', 'Urut', 'required');
        $this->form_validation->set_rules('period', 'Periode', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->period_model->insert_period();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function period_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('year', 'Tahun', 'required');
        $this->form_validation->set_rules('sorting', 'Urut', 'required');
        $this->form_validation->set_rules('period', 'Periode', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->period_model->update_period($this->input->post('period_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function period_delete($id) {
        $this->period_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function calendar($encrypt_id = '') {
        $period_id = $this->general_model->decrypt_value($encrypt_id); /* DECRYPT */
        $data['active_li'] = $this->category_index;
            
        $header = $this->asik_model->draw_header('Calendar Supplier', 'View', $this->category_index, $this->asik_model->category_masterdata, $this->asik_model->master_06);
        $data['content_header'] = $header;
        $data['halaman'] = 'period/calendar.php';
        $start_date = '';
        $period = $this->period_model->get_period_by_id($period_id);
        if ($period->num_rows()!=0){
            $row = $period->row();
            $start_date = $row->start_date;
            $period_name = $row->period .' '.$row->year;
        }

        $data['period_name'] = $period_name;
        $timestamp = strtotime(date($start_date));
        $day = date('D', $timestamp);

        $end = date("Y-m-t", strtotime($start_date));
        $substr = substr($end, 8,10);

        $hari = array(
            "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
        );
        $tgl = array(array());
        $g = 0;
        $r = 0;
        while ($g < 6){
            for($h=0; $h<7; $h++){
                $tgl[$r][$h] = "&nbsp;";
            }
            $g++;
            $r++;
        }

        $t = 0;
        $next = 0;
        for($i = 0; $i<$r; $i++){
            for($j=0; $j<7; $j++){
                if ($day == $hari[$j] && $next == 0){
                    $next = 1;
                }
                if ($next == 1){
                    $t++;
                    $tgl[$i][$j] = $t;
                    if ($t == $substr){
                        $next = 2;
                    }
                }
            } 
        }
        $data['r'] = $r;
        $arr_tt = array();
        $arr_tp = array();
        $period_detail = $this->get_period_detail($period_id);
        if ($period_detail->num_rows()!=0){
            foreach ($period_detail->result() as $value) {
                if ($value->date_type == 1){
                    $arr_tt[] = $value->date_day;
                } else {
                    $arr_tp[] = $value->date_day;
                }
            }
        }
        
        $data['tgl'] = $tgl;
        $data['arr_tt'] = $arr_tt;
        $data['arr_tp'] = $arr_tp;
        /* form */
        $data['period_id'] = $this->general_model->draw_hidden_field('period_id', $period_id);
        $data['start_date'] = $this->general_model->draw_hidden_field('start_date', $start_date);
        $data['tanggal'] = $this->general_model->draw_hidden_field('tanggal', '');
        $event_opt = array("Default", "Tanggal Tukar", "Tanggal Pembayaran");
        $data['choose'] = $this->general_model->draw_select('Select event', 0, 'choose', 0, $event_opt, '', 0);
        $mdl = $this->asik_model->category_masterdata . $this->asik_model->master_06;
        $data['back_link'] = 'period/go/'.$mdl;
        $data['show_modal'] = 'period/calendar_modal.php';
        $this->load->view('template', $data);
    }
    
    public function calendar_add() {
        $period_id = $this->input->post('period_id');
        $start_date = $this->input->post('start_date');
        $tanggal = $this->input->post('tanggal');
        $choose = $this->input->post('choose');

        $ym = substr($start_date, 0, 7);
        $date_event = $ym.'-'.$tanggal;
        if ($choose != 0){
            $perioddetail = $this->get_period_detail_by_day($period_id, $tanggal);
            if ($perioddetail->num_rows()!=0){
                $row = $perioddetail->row();
                $period_detail_id = $row->period_detail_id;
                $data = array(
                    'period_id' => $period_id,
                    'date_day' => $tanggal,
                    'date_event' => $date_event,
                    'date_type' => $choose
                );
                $this->db->where('period_detail_id', $period_detail_id);
                $this->db->update('period_detail', $data);
            } else {
                $data = array(
                    'period_id' => $period_id,
                    'date_day' => $tanggal,
                    'date_event' => $date_event,
                    'date_type' => $choose
                );
                $this->db->insert('period_detail', $data);
            }
            
        } else {
            $perioddetail = $this->get_period_detail_by_day($period_id, $tanggal);
            if ($perioddetail->num_rows()!=0){
                $row = $perioddetail->row();
                $period_detail_id = $row->period_detail_id;
                $this->db->where('period_detail_id', $period_detail_id);
                $this->db->delete('period_detail');
            }
        }
        
        echo json_encode(array("status" => TRUE));
    }
    
    public function get_period_detail($period_id=0) {
        $sql = 'SELECT * FROM period_detail WHERE period_id='.$period_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_period_detail_by_day($period_id=0, $day=0) {
        $sql = 'SELECT * FROM period_detail WHERE period_id='.$period_id.' AND date_day='.$day;
        $query = $this->db->query($sql);
        return $query;
    }
}
