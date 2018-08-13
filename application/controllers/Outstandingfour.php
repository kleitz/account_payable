<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Outstandingfour
 *
 * @author Hendra McHen
 */
class Outstandingfour extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_05) && ($string == $category . $module)) {
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
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                /* end privilege */
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
                    //$report_data = $this->supplier_report_model->get_supplier_report($start_date, $end_date);
                    $outstanding = $this->get_outstanding($start_date, $end_date);
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
                    $outstanding = $this->get_outstanding($start_date, $end_date);
                }
                               
                $array = array();
                $description_temp = '';
                $branch = $this->get_branch_list();
                if ($outstanding->num_rows()!=0){
                    $i = 0;
                    foreach ($outstanding->result() as $value) {
                        $col = array();
                        if ($value->outstanding_description != $description_temp){
                            $description_temp = $value->outstanding_description;
                            $col[0] = $value->outstanding_description;
                            $k = 0;
                            $totalcol = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = 0;
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $value->amount;
                                }
                                $totalcol = $totalcol + $col[$k+1];
                                $k++;
                            }
                            $col[$k+1] = $totalcol;
                            $array[$i] = $col;
                            $i++;
                        } else {
                            $description_temp = $value->outstanding_description;
                            $col[0] = $value->outstanding_description;
                            $k = 0;
                            foreach ($branch as $key=>$b) {
                                $col[$k+1] = $array[$i-1][$k+1];
                                $k++;
                            }
                            $totalcol = 0;
                            $j = 1;
                            foreach ($branch as $key=>$b) {
                                if ($value->branch_name == $b){
                                    $col[$key+1] = $col[$key+1] + $value->amount;
                                }
                                $totalcol = $totalcol + $col[$j];
                                $j++;
                            }
                            $col[$k+1] = $totalcol;
                            $array[$i-1] = $col;
                        }
                    }
                }
                $data['array'] = $array; 
                $data['branch'] = $branch;
                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['pagecode'] = $string;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Outstanding Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/outstanding_report.php';
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
                    $c = 2;
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
    
    public function get_outstanding($start_date='', $end_date='') {
        $sql  = 'SELECT os.outstanding_date, os.outstanding_description, ';
        $sql .= 'b.branch_name, os.amount, os.outstanding_status FROM outstanding AS os ';
        $sql .= 'INNER JOIN branch AS b ON os.branch_id=b.branch_id ';
        $sql .= 'WHERE os.outstanding_status = 0 ';
        //if ($start_date != '' && $end_date != ''){
            $sql .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"';
        //}
        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM outstanding AS os
        INNER JOIN branch AS b ON os.branch_id=b.branch_id
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
}