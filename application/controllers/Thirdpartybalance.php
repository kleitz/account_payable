<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Thirdpartybalance
 *
 * @author hendramchen
 */
class Thirdpartybalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_14) && ($string == $category . $module)) {
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
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                $data['list'] = $this->get_all_balance();
                /* ===== start datatable ===== */
                $data['datatable_title'] = '';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Third Party Balance', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/thirdpartybalance_view.php';
                
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_all_balance() {
        $sql = 'SELECT t.third_party_id, t.third_party_name, SUM(b.debit) AS sum_debit, 
        SUM(b.credit) AS sum_credit FROM third_party_balance AS b
        INNER JOIN third_party AS t ON t.third_party_id=b.third_party_id
        INNER JOIN payment_process AS p ON p.pp_id=b.pp_id
        GROUP BY t.third_party_id
        ORDER BY t.third_party_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_balance_detail($third_party_id=0) {
        $sql  = 'SELECT b.pp_id, b.balance_date, p.pp_number, p.pp_status, br.branch_name, 
        b.debit, b.credit, b.third_party_id 
        FROM third_party_balance AS b
        INNER JOIN payment_process AS p ON p.pp_id=b.pp_id 
        INNER JOIN branch AS br ON p.branch_id=br.branch_id 
        WHERE b.third_party_id='.$third_party_id.' 
        ORDER BY b.balance_date';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_third_party_name($third_party_id=0) {
        $sql  = 'SELECT third_party_name FROM third_party ';
        $sql .= 'WHERE third_party_id='.$third_party_id;
        $query = $this->db->query($sql);
        $name = '';
        if ($query->num_rows() != 0){
            $row = $query->row();
            $name = $row->third_party_name;
        }
        return $name;
    }
    
    public function detail($string = '', $third_party_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                
                $data['detail'] = $this->get_balance_detail($third_party_id);
                $third_party_name = $this->get_third_party_name($third_party_id);
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Third Party Balance';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
    
                    alltotal1 = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(6, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(6).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(7, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(7).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($third_party_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/thirdpartybalance_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
}