<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of General_model
 *
 * @author mchen
 */

class General_model extends CI_Model {


    public function __construct() {
        parent::__construct();
        $this->load->helper('date');
    }

    public function encrypt_value($plain_text = '') {
        $this->load->library('encryption');
        $this->encryption->initialize(
                array(
                    'cipher' => 'aes-256',
                    'mode' => 'ctr',
                    'key' => $this->config->item('encryption_key')
                )
        );
        $ciphertext = $this->encryption->encrypt($plain_text);
        $encode = $this->safe_b64encode($ciphertext);
        return $encode;
    }

    public function decrypt_value($ciphertext = 'test') {
        $this->load->library('encryption');
        $this->encryption->initialize(
                array(
                    'cipher' => 'aes-256',
                    'mode' => 'ctr',
                    'key' => $this->config->item('encryption_key')
                )
        );
        /* DECRYPT */        
        $decode = $this->safe_b64decode($ciphertext);
        $decrypt =  $this->encryption->decrypt($decode);
        return $decrypt;
    }

    public function secure($text = '') {
        $this->load->helper('security');
        $hash = do_hash($text) . $text;
        return $hash;
    }

    public function get_month($m = '') {
        $arr = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );
        if ($m != ''){
            return $arr[$m];
        }
    }

    public function get_string_date($date) {
        $str_date = substr($date, 8, 2) . ' ' . $this->get_month(substr($date, 5, 2)) . ' ' . substr($date, 0, 4);
        return $str_date;
    }
    
    public function get_string_date_ver2($date) {
        $arr = array(
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Aug',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dec',
        );
        $str_date = substr($date, 8, 2) . ' ' . $arr[substr($date, 5, 2)] . ' ' . substr($date, 0, 4);
        return $str_date;
    }

    public function check_passwd($hash, $password) {
        if (is_php('5.5') && password_verify($password, $hash)) {
            return TRUE;
        } else if ($hash === crypt($password, $hash)) {
            return TRUE;
        }
        return FALSE;
    }
    
    public function safe_b64encode($string) {
        $data = str_replace(array('+', '/', '='), array('-', '_', '~'), $string);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-', '_', '~'), array('+', '/', '='), $string);
        return $data;
    }
    
    public function draw_text_field($label='', $require=0, $name='', $id='', $placeholder='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $html  = '<div class="form-group" id="'.$name.'">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<input type="text" class="form-control" name="'.$name.'" id="'.$id.'" placeholder="'.$placeholder.'" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_text_disabled($label='', $name='', $val=''){
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.'</label>';
        $html .= '<input type="text" class="form-control" name="'.$name.'" value="'.$val.'" disabled>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_password_field($label='', $require=0, $name='', $id='', $placeholder='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<input type="password" class="form-control" name="'.$name.'" id="'.$id.'" placeholder="'.$placeholder.'" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_email_field($label='', $require=0, $name='', $id='', $placeholder='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<input type="email" class="form-control" name="'.$name.'" id="'.$id.'" placeholder="'.$placeholder.'" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_tfield_addon($label='', $require=0, $name='', $id='', $placeholder='', $val='', $addon=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $html = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<div class="input-group">';
        $html .= '<input type="text" class="form-control" name="'.$name.'" id="'.$id.'" placeholder="'.$placeholder.'" value="'.$val.'">';
        $html .= '<span class="input-group-addon">'.$addon.'</span>';
        $html .= '</div>';
        $html .= '</div>';   
        $html .= '</div>';   
        $html .= '</div>';   
        return $html;
    }
    
    public function draw_select($label='', $require=0, $name='', $default_opt=0, $option=array(), $selected='', $select2=0){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $class_select2 = $select2 == 1 ? 'select2':'';
        $style = $select2 == 1 ? 'style="width: 100%;':'';
        $html = '<div class="form-group" id="'.$name.'">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<select name="'.$name.'" class="form-control '.$class_select2.'" '.$style.'>';
        $html .= $default_opt == 1 ? '<option value="0">-SELECT-</option>':'';
        foreach ($option as $key => $value) {
            if ($selected == $key){
                $html .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
            } else {
                $html .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_hidden_field($name='', $val=''){
        $html = '<input type="hidden" name="'.$name.'" value="'.$val.'">';
        return $html;
    }
    
    public function draw_textarea($label='', $require=0, $name='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<textarea name="'.$name.'" class="form-control" rows="3" placeholder="type text here...">'.$val.'</textarea>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_caption($label='', $id_name='', $val=''){
        $html = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.'</label>';
        $html .= '<div id="'.$id_name.'">'.$val.'</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function draw_datepicker($label='', $require=0, $name='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';        
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<div class="input-group date">';
        $html .= '<div class="input-group-addon">';
        $html .= '<i class="fa fa-calendar"></i>';
        $html .= '</div>';
        $html .= '<input type="text" name="'.$name.'" class="form-control pull-right datepicker" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_input_number($label='', $require=0, $name='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';        
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<input type="text" name="'.$name.'" class="form-control numberic" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function draw_input_currency($label='', $require=0, $name='', $val=''){
        $req = $require == 1 ? '<span style="color: red;">*</span>':'';        
        $html  = '<div class="form-group">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<label style="color:#575757">'.$label.' '.$req.'</label>';
        $html .= '<input type="text" name="'.$name.'" class="form-control currency" value="'.$val.'">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
    
    public function change_decimal($param) {
        $replace = str_replace(',', '', $param);
        if (substr($replace, 0,2)=='Rp'){
            $decimal = substr($replace, 4, strlen($replace));
        } else {
            $decimal = $replace;
        }
        
        return $decimal;
    }
    
    public function get_generate_number($prefix='', $table='', $field_id='') {
        $string = date('YmdHis');
        $substr = substr($string, 2);
        $now = $substr;
        $code = $this->get_last_id($table, $field_id);
        $number = $prefix.$now.$code;
        return $number;
    }
    
    public function get_last_id($table='', $field_id='') {
        $code = '';
        $sql  = 'SELECT * FROM '.$table.' ';
        $sql .= 'ORDER BY '.$field_id.' DESC LIMIT 1';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $row = $query->row_array();
            $id = $row[$field_id] + 1;
            $code = $this->generate_code($id);
        } else {
            $code = $this->generate_code(1);
        }
        return $code;
    }


    public function generate_code($id = 0) {
        $length = strlen($id);
        $str = '';
        switch ($length) {
            case 1:
                $str = '00' . $id;
                break;
            case 2:
                $str = '0' . $id;
                break;
            default:
                $str = $id;
                break;
        }
        return $str;
    }
    
    /*
     * Add some functions | 14 June 2018
     */
    
    public function get_report_by_date($start_date='', $end_date='', $report_type=0, $account_id=0) {
        $sql  = 'SELECT * FROM report_file ';
        $sql .= 'WHERE start_date="'.$start_date.'" AND end_date="'.$end_date.'" AND report_type='.$report_type;
        if ($account_id !=0){
            $sql .= ' AND account_id='.$account_id;
        }
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_report_by_id($id=0) {
        $sql  = 'SELECT * FROM report_file ';
        $sql .= 'WHERE report_file_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_user_by_id($id = '') {
        $sql = 'SELECT * FROM users WHERE user_id =' . $id;
        $query = $this->db->query($sql);
        $fullname = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $fullname = $row->fullname;
        }
        return $fullname;
    }
    
    public function action_checked($start_date='', $end_date='', $report_type=0, $link='', $account_id=0) {
        $data = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'checked_by' => $this->session->userdata('user_id'),
            'report_type' => $report_type,
            'account_id' => $account_id
        );
        $this->db->insert('report_file', $data);
        $back = $link . $start_date.'/'.$end_date;
        redirect($back);
    }
    
    public function action_approved($report_id = 0, $start_date='', $end_date='', $link='') {
        $data = array(
            'approved_by' => $this->session->userdata('user_id')
        );
        $this->db->where('report_file_id', $report_id);
        $this->db->update('report_file', $data);
        $back = $link . $start_date.'/'.$end_date;
        redirect($back);
    }
    
    public function do_upload($link='') {
        $id = $this->input->post('report_file_id');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'up'.date('YmdHis');
        
        $config['upload_path'] = './assets/reportfile/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 9000;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->update_file_name($id, $info['orig_name']);
        }

        $getreport = $this->get_report_by_id($id);
        $start_date = ''; $end_date = '';
        if ($getreport->num_rows()!=0){
            $row = $getreport->row();
            $start_date = $row->start_date;
            $end_date = $row->end_date;
        }
        $back = $link . $start_date.'/'.$end_date;
        redirect($back);
    }
    
    public function update_file_name($id=0, $file='') {
        $data = array(
            'file_name'=> $file
        );
        $this->db->where('report_file_id', $id);
        $this->db->update('report_file', $data);
    }
    
}
