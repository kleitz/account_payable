<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Outstandingreport
 *
 * @author Hendra McHen
 */
class Outstandingreport extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_04) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
            if (($module == $this->asik_model->report_05) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
            if (($module == $this->asik_model->report_06) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0, $startd='', $endd='', $tipe=0) {
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
                /* end privilege */
                $this->load->helper('form');
                
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                $outstanding_status = $this->input->post('outstanding_status');
                $branch_filter = ($this->input->post('branch_filter')=='')?0:$this->input->post('branch_filter');
                $thirdparty_filter = ($this->input->post('thirdparty_filter')=='')?0:$this->input->post('thirdparty_filter');
                /* update 2018-07-21 new filter */
                $data['branch_filter'] = $branch_filter;
                $data['thirdparty_filter'] = $thirdparty_filter;
                if ($startd != ''){
                    $start_date = $startd;
                }
                if ($endd != ''){
                    $end_date = $endd;
                }  
                
                $report_type = 0;
                $link_code = '';

                if ($button == 0){
                    if ($module == $this->asik_model->report_04){
                        $outstanding = $this->get_outstanding($start_date, $end_date, 1, $outstanding_status);
                        $report_type = 4;
                        $link_code = '20191341214304';
                    }
                    if ($module == $this->asik_model->report_05){
                        if ($tipe == 4){
                            $outstanding = $this->get_outstanding($start_date, $end_date, 4, $outstanding_status, $branch_filter);
                        } else {
                            $outstanding = $this->get_outstanding($start_date, $end_date, 2, $outstanding_status, $branch_filter);
                        }
                        $report_type = 5;
                        $link_code = '20191341214305';
                    }
                    if ($module == $this->asik_model->report_06){
                        $outstanding = $this->get_outstanding_third_party($start_date, $end_date, $outstanding_status, $thirdparty_filter);
                        //$outstanding = $this->get_outstanding($start_date, $end_date, 3, $outstanding_status);
                        $report_type = 6;
                        $link_code = '20191341214306';
                    }
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
                    if ($module == $this->asik_model->report_04){
                        $outstanding = $this->get_outstanding($start_date, $end_date, 1);
                        $report_type = 4;
                        $link_code = '20191341214304';
                    }
                    if ($module == $this->asik_model->report_05){
                        $outstanding = $this->get_outstanding($start_date, $end_date, 2);
                        $report_type = 5;
                        $link_code = '20191341214305';
                    }
                    if ($module == $this->asik_model->report_06){
                        $outstanding = $this->get_outstanding_third_party($start_date, $end_date, $outstanding_status);
                        //$outstanding = $this->get_outstanding($start_date, $end_date, 3);
                        $report_type = 6;
                        $link_code = '20191341214306';
                    }
                }
                
                // update 14 June 2018
                // cek report file by start_date, end_date
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
                
                $data['link_code'] = $link_code;
                               
                $branch = $this->get_branch_list();
                $array_tbl = $outstanding;
                //$baris = 0;
                /*=================== Fetch Outstanding Data ========================*/
//                if ($outstanding->num_rows()!=0){
//     
//                    foreach ($outstanding->result() as $value) {
//                        $total = 0;
//                        $color = '';
//                        if ($value->outstanding_status == 0){
//                            $color = 'red';
//                        }
//                        if ($value->outstanding_status == 1){
//                            $color = '#008749';
//                        }
//                        $pv_enc = $this->get_pv_id($value->pv_number);
//                        $array_tbl[$baris][0] = '<span style="color:'.$color.'">'.$value->outstanding_date.'</span>';
//                        $array_tbl[$baris][1] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->outstanding_description.'</a>';
//                        $k = 2;
//                        foreach ($branch as $b) {
//                            $array_tbl[$baris][$k] = 0;
//                            if ($value->branch_name == $b){
//                                $array_tbl[$baris][$k] = $value->amount;
//                                $total = $total + $value->amount;
//                            }
//                            $k++;
//                        }
//                        $array_tbl[$baris][$k] = 0;
//                        $array_tbl[$baris][$k] = $total;
//                        $baris++;
//                    }
//                }
                $data['array_tbl'] = $array_tbl; 
                $data['branch'] = $branch;
                $outlet[0] = 'All Outlet';
                $getoutlet = $this->get_outlet_list();
                foreach ($getoutlet->result() as $val) {
                    $outlet[$val->branch_id] = $val->branch_name;
                }
                $data['outlet'] = $outlet;
                $thirdparty[0] = 'All third party';
                $getthirdparty = $this->get_third_party_list();
                foreach ($getthirdparty->result() as $val) {
                    $thirdparty[$val->third_party_id] = $val->third_party_name;
                }
                $data['thirdparty'] = $thirdparty;
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['outstanding_status'] = $outstanding_status;
                
                
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                
                $header_title = '';
                $page_name = '';
                if ($module == $this->asik_model->report_04){
                    $header_title = 'Outstanding Cash Request';
                }
                if ($module == $this->asik_model->report_05){
                    $header_title = 'Outstanding Outlet';
                }
                if ($module == $this->asik_model->report_06){
                    $header_title = 'Outstanding Third Party';
                }
                $data['halaman'] = 'report/outstanding_report.php';
                $header = $this->asik_model->draw_header($header_title, 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Outstanding Report';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };';
                    $c = 3;
                    $strtotal = '';
                    foreach ($branch as $key=>$b) {
                        $strtotal .= 'total'.$c.' = api
                                .column('.$c.', { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column('.$c.').footer() ).html(
                                numeral(total'.$c.').format("0,0.00")
                        );';
                        $c++;
                    }
                    $strtotal .= 'alltotal = api
                                .column('.$c.', { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column('.$c.').footer() ).html(
                                numeral(alltotal).format("0,0.00")
                        );';
                    
                $footer_total .= $strtotal . '}';
                    
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
    
    public function get_outstanding($start_date='', $end_date='', $type=0, $status='', $branch_filter=0) {
        if ($status == ''){
            $os_status = '0,1';
        } else {
            if ($status == 2){
                $os_status = '0,1';
            } else {
                $os_status = $status;
            }
        }
        
        $sql  = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, ';
        $sql .= 'b.branch_name, os.amount, os.outstanding_status, os.pv_number FROM outstanding AS os ';
        $sql .= 'INNER JOIN branch AS b ON os.branch_id=b.branch_id ';
        $sql .= 'WHERE os.outstanding_status IN ('.$os_status.') ';
        $sql .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'AND os.outstanding_type='.$type;
        if ($branch_filter !=0){
            $sql .= ' AND os.branch_id='.$branch_filter;
        }
        
        $outstanding = $this->db->query($sql);
        
        
        $branch = $this->get_branch_list();
        $array_tbl = array(array());
        $baris = 0;
        /*=================== Fetch Outstanding Data ========================*/
        if ($outstanding->num_rows()!=0){

            foreach ($outstanding->result() as $value) {
                $total = 0;
                $color = '';
                if ($value->outstanding_status == 0){
                    $color = 'red';
                }
                if ($value->outstanding_status == 1){
                    $color = '#008749';
                }
                $pv_enc = $this->get_pv_id($value->pv_number);
                $array_tbl[$baris][0] = '<span style="color:'.$color.'">'.$value->outstanding_date.'</span>';
                $array_tbl[$baris][1] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->outstanding_description.'</a>';
                $k = 2;
                foreach ($branch as $b) {
                    $array_tbl[$baris][$k] = 0;
                    if ($value->branch_name == $b){
                        $array_tbl[$baris][$k] = $value->amount;
                        $total = $total + $value->amount;
                    }
                    $k++;
                }
                $array_tbl[$baris][$k] = 0;
                $array_tbl[$baris][$k] = $total;
                $baris++;
            }
        }
        
        
        return $array_tbl;
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
    
    public function get_outlet_list() {
        $sql  = 'SELECT b.branch_id, b.branch_name FROM branch AS b 
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_third_party_list() {
        $sql  = 'SELECT t.third_party_id, t.third_party_name FROM third_party AS t 
        ORDER BY t.third_party_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pv_id($pv_number='') {
        $sql  = 'SELECT * FROM payment_voucher WHERE pv_number="'.$pv_number.'" ';
        $query = $this->db->query($sql);
        $pv_enc = '';
        if ($query->num_rows()!=0){
            $row = $query->row();
            $pv_enc = $this->general_model->encrypt_value($row->pv_id);
        }
        return $pv_enc;
    }
    
    public function action_checked($start_date='', $end_date='', $report_type=0, $link_code='') {
        $this->general_model->action_checked($start_date, $end_date, $report_type, '/outstandingreport/go/'.$link_code.'/0/');
    }
    
    public function action_approved($report_file_id = 0, $start_date='', $end_date='', $link_code='') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/outstandingreport/go/'.$link_code.'/0/');
    }
    
    public function do_upload($link_code='') {
        $this->general_model->do_upload('/outstandingreport/go/'.$link_code.'/0/');
    }
    
    public function get_outstanding_third_party($start_date='', $end_date='', $status='', $thirdparty=0) {
        if ($status == ''){
            $os_status = '0,1';
        } else {
            if ($status == 2){
                $os_status = '0,1';
            } else {
                $os_status = $status;
            }
        }
        $sql1 = 'SELECT * FROM outstanding WHERE outstanding_status=-1';
        $sql2 = 'SELECT * FROM outstanding WHERE outstanding_status=-1';
        if ($os_status == '0,1'){
            /* status = 0 (not paid) */
            $sql1 = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, 
            b.branch_name, os.amount, os.outstanding_status, os.pv_number FROM outstanding AS os 
            INNER JOIN branch AS b ON os.branch_id=b.branch_id 
            WHERE os.outstanding_status=0  ';
            if ($start_date != '' && $end_date != ''){
                $sql1 .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            }
            $sql1 .= ' AND os.outstanding_type=3 ';
            /* status = 0 (paid) */
            $sql2 = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, 
            b.branch_name, os.amount AS amount, rb.amount AS rb_amount, os.outstanding_status, os.pv_number FROM outstanding AS os 
            INNER JOIN branch AS b ON os.branch_id=b.branch_id 
            INNER JOIN receive_bank AS rb ON rb.outstanding_id=os.outstanding_id
            WHERE os.outstanding_status=1  ';
            if ($start_date != '' && $end_date != ''){
                $sql2 .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            }
            $sql2 .= 'AND os.outstanding_type=3 ';
        } else {
            if ($os_status == '0'){
                /* status = 0 (not paid) */
                $sql1 = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, 
                b.branch_name, os.amount AS amount, os.outstanding_status, os.pv_number FROM outstanding AS os 
                INNER JOIN branch AS b ON os.branch_id=b.branch_id 
                WHERE os.outstanding_status=0  ';
                if ($start_date != '' && $end_date != ''){
                    $sql1 .='AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
                }
                $sql1 .= 'AND os.outstanding_type=3 ';
            } else {
                /* status = 0 (paid) */
                $sql2 = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, 
                b.branch_name, os.amount AS amount, rb.amount AS rb_amount, os.outstanding_status, os.pv_number FROM outstanding AS os 
                INNER JOIN branch AS b ON os.branch_id=b.branch_id 
                INNER JOIN receive_bank AS rb ON rb.outstanding_id=os.outstanding_id
                WHERE os.outstanding_status=1  ';
                if ($start_date != '' && $end_date != ''){
                    $sql2 .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
                }
                $sql2 .= 'AND os.outstanding_type=3 ';
            }
        }
        
        if ($thirdparty != 0){
            $sql1 .= ' AND third_party_id='.$thirdparty;
            $sql2 .= ' AND third_party_id='.$thirdparty;
        }

        $branch = $this->get_branch_list();
        $array_tbl = array(array());
        
        $outstanding1 = $this->db->query($sql1);
        $outstanding2 = $this->db->query($sql2);

        $baris = 0;
        /*=================== Fetch Outstanding Data Status not close ========================*/
        if ($outstanding1->num_rows()!=0){

            foreach ($outstanding1->result() as $value) {
                $total = 0;
                $color = '';
                if ($value->outstanding_status == 0){
                    $color = 'red';
                }
                if ($value->outstanding_status == 1){
                    $color = '#008749';
                }
                $pv_enc = $this->get_pv_id($value->pv_number);
                $array_tbl[$baris][0] = '<span style="color:'.$color.'">'.$value->outstanding_date.'</span>';
                $array_tbl[$baris][1] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->outstanding_description.'</a>';
                $k = 2;
                foreach ($branch as $b) {
                    $array_tbl[$baris][$k] = 0;
                    if ($value->branch_name == $b){
                        $array_tbl[$baris][$k] = $value->amount;
                        $total = $total + $value->amount;
                    }
                    $k++;
                }
                $array_tbl[$baris][$k] = 0;
                $array_tbl[$baris][$k] = $total;
                $baris++;
            }
        }
        /*=================== Fetch Outstanding Data Status Close ========================*/
        if ($outstanding2->num_rows()!=0){

            foreach ($outstanding2->result() as $value) {
                $total = 0;
                $color = '';
                if ($value->rb_amount >= $value->amount){
                    $color = '#008749';
                } 
                if ($value->rb_amount < $value->amount){
                    $color = 'red';
                }
                $pv_enc = $this->get_pv_id($value->pv_number);
                $array_tbl[$baris][0] = '<span style="color:'.$color.'">'.$value->outstanding_date.'</span>';
                $array_tbl[$baris][1] = '<a href="'. site_url('paymentvoucher/detail/20191231214302/'.$pv_enc).'" target="_blank">'.$value->outstanding_description.'</a>';
                $k = 2;
                foreach ($branch as $b) {
                    $array_tbl[$baris][$k] = 0;
                    if ($value->branch_name == $b){
                        $array_tbl[$baris][$k] = $value->amount;
                        $total = $total + $value->amount;
                    }
                    $k++;
                }
                $array_tbl[$baris][$k] = 0;
                $array_tbl[$baris][$k] = $total;
                $baris++;
            }
        }
        
        return $array_tbl;
    }
    
}
