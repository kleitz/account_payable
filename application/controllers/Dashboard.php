<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author mchen
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }

    public $category_index = 0;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        $category_dash = $this->asik_model->category_dashboard;
        if ($category == $category_dash) {
            if (($module == $this->asik_model->dash_01) && ($string == $category . $module)) {
                $this->category_index = 0;
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
            if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)) {
                $this->category = $category;
                $this->module = $module;
                /* ===== get active period ===== */
                $period_active = 0;
                $period_title = 'Belum Ada Periode Aktif';
                $period_month = '';
                $this->load->model('period_model');
                $period = $this->period_model->get_period_active();

                $start_date = '';
                $end_date = '';
                if ($period->num_rows() != 0) {
                    $row = $period->row();
                    $period_title = $row->period . ' ' . $row->year;
                    $period_active = 1;
                    $period_month = substr($row->start_date, 0, 7);
                    $start_date = $row->start_date;
                    $end_date = $row->end_date;
                }
                /* 2018-04-12 | get period list */
                $period_list = $this->period_model->get_period_list();
                $arr_period = array();
                $arr_period[0] = '<option value="' . $period_month . '">Current month</option>';
                if ($period_list->num_rows() != 0) {
                    $idx = 1;
                    foreach ($period_list->result() as $value) {
                        $substr_period = substr($value->start_date, 0, 7);
                        $arr_period[$idx] = '<option value="' . $substr_period . '">' . $value->period . ' ' . $value->year . '</option>';
                        $idx++;
                    }
                }
                $data['arr_period'] = $arr_period;
                /* ===== end get active period ===== */
                $branch_array = array(array());
                $branch = $this->get_branch();
                if ($branch->num_rows() != 0) {
                    $i = 0;
                    foreach ($branch->result() as $val) {
                        $branch_array[$i][0] = $val->branch_name;
                        $branch_array[$i][1] = '0';
                        $i++;
                    }
                }
                $header = $this->asik_model->draw_header('Dashboard', $period_title, $this->category_index, $category, $module);
                $datenow = date('Y-m');
                $data['bank_balance'] = $this->get_bank_balance($datenow);
                $data['expense'] = $this->get_expense_period($branch_array, $datenow);
                $data['ppstatus'] = $this->get_payment_process_status($datenow);
                $data['outstanding_cr'] = $this->get_outstanding_period($branch_array, $datenow, 1);
                $data['outstanding_ot'] = $this->get_outstanding_period($branch_array, $datenow, 2);
                $data['outstanding_th'] = $this->get_outstanding_period($branch_array, $datenow, 3);
                $get_os_cr = $this->get_outstanding_cash_request()->num_rows();
                $total_remark = $this->get_noremark_cash_request();
                $data['os_cash_request'] = $get_os_cr;
                $data['total_remark'] = $total_remark;
                $data['report_bank_balance'] = $this->get_report_bank_balance($datenow);
                $data['report_expense'] = $this->get_report_expense($datenow);
                $data['content_header'] = $header;
                $data['active_li'] = $this->category_index;
                $data['page_name'] = 'Dashboard';
                $data['halaman'] = 'default.php';
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function index() {
        $this->go($this->asik_model->category_dashboard . $this->asik_model->dash_01);
    }

    public function get_branch() {
        $sql = 'SELECT * FROM branch';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_petty_bank($start_date = '', $end_date = '') {
        $sql = 'SELECT a.account_name, b.branch_name, SUM(l.debit) AS debit, SUM(l.credit) AS credit FROM ledger AS l
        INNER JOIN account AS a ON l.account_id=a.account_id
        INNER JOIN branch AS b ON a.branch_id=b.branch_id
        INNER JOIN transactions AS t ON l.trans_id=t.trans_id
        WHERE a.account_name LIKE "Petty Bank%" ';
        if ($start_date != '' && $end_date != '') {
            $sql .= 'AND t.trans_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        }

        $sql .= 'GROUP BY a.account_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_opening_balance($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(op.amount)AS total, b.branch_name FROM opening_balance AS op
        INNER JOIN branch AS b ON b.branch_id=op.branch_id
        WHERE op.opening_balance_date BETWEEN "' . $start_date . '" AND "' . $end_date . ' "
        GROUP BY op.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_bank_balance($month = '') {
        $sql = 'SELECT b.branch_id, a.account_id, b.branch_name, SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit
        FROM ledger AS L INNER JOIN transactions AS t ON L.trans_id=t.trans_id 
        INNER JOIN account AS a ON L.account_id=a.account_id 
        INNER JOIN branch AS b ON b.branch_id=a.branch_id 
        WHERE L.account_id IN (7,8,9,10,11,12,13) AND t.trans_date LIKE "'.$month.'%" GROUP BY b.branch_id, a.account_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_period_asset($branch_data = array(array())) {
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $signupdate = $year . '-' . $month . '-' . $day;
        $signupweek = date("W", strtotime($signupdate));

        $dto = new DateTime();
        $start_week = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
        $end_week = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');

        $start_month = $year . '-' . $month . '-01';
        $end_month = $end = date("Y-m-t", strtotime($start_month));
        /* inisialisasi */
        for ($i = 0; $i < sizeof($branch_data); $i++) {
            $branch_data[$i][1] = 0;
            $branch_data[$i][2] = 0;
        }
        $data_week = $this->get_opening_balance($start_week, $end_week);
        if ($data_week->num_rows() != 0) {
            for ($i = 0; $i < sizeof($branch_data); $i++) {
                $branch_data[$i][1] = 0;
                foreach ($data_week->result() as $val) {
                    //$total = $val->debit - $val->credit;

                    if ($val->branch_name == $branch_data[$i][0]) {
                        $branch_data[$i][1] = $val->total;
                    }
                }
            }
        }
        $data_month = $this->get_opening_balance($start_month, $end_month);
        if ($data_month->num_rows() != 0) {
            for ($i = 0; $i < sizeof($branch_data); $i++) {
                $branch_data[$i][2] = 0;
                foreach ($data_month->result() as $val) {
                    //$total = $val->debit - $val->credit;

                    if ($val->branch_name == $branch_data[$i][0]) {
                        $branch_data[$i][2] = $val->total;
                    }
                }
            }
        }
        return $branch_data;
    }

    public function get_period_liability($start_date = '', $end_date = '', $keyword = '', $branch_data = array(array())) {
        $sql = 'SELECT a.account_name, b.branch_name, SUM(l.debit) AS debit, SUM(l.credit) AS credit FROM ledger AS l
        INNER JOIN account AS a ON l.account_id=a.account_id
        INNER JOIN branch AS b ON a.branch_id=b.branch_id
        INNER JOIN transactions AS t ON l.trans_id=t.trans_id
        WHERE a.account_name LIKE "' . $keyword . '%" AND t.trans_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
        GROUP BY a.account_id
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        if ($query->num_rows() != 0) {
            for ($i = 0; $i < sizeof($branch_data); $i++) {
                foreach ($query->result() as $val) {
                    $total = $val->credit - $val->debit;
                    if ($val->branch_name == $branch_data[$i][0]) {
                        $branch_data[$i][1] = $total;
                    }
                }
            }
        }
        return $branch_data;
    }

    public function get_payment_process($start_date = '', $end_date = '') {
        $sql = 'SELECT pp.pp_status, COUNT(pp.pp_status) AS total_status, SUM(pp.total)AS total ';
        $sql .= 'FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'GROUP BY pp.pp_status ORDER BY pp.pp_status';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_payment_process_approved() {
        $sql = 'SELECT pp.pp_status, COUNT(pp.pp_status) AS total_status ';
        $sql .= 'FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_status IN (3, 4) GROUP BY pp.pp_status'; // approved & closed
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_expense($start_date = '', $end_date = '') {
        $sql = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_outstanding($start_date = '', $end_date = '', $type=0) {
        $sql  = 'SELECT  b.branch_name, SUM(os.amount) AS total FROM outstanding AS os ';
        $sql .= 'INNER JOIN branch AS b ON os.branch_id=b.branch_id ';
        $sql .= 'WHERE os.outstanding_status = 0 ';
        $sql .= 'AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'AND os.outstanding_type='.$type.' ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_expense_period($branch_data = array(array()), $period='') {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        
        $month_now = date('Y-m');
        

        $start_month = $period . '-01';
        $end_month = date("Y-m-t", strtotime($start_month));
        /* inisialisasi */
        for ($i = 0; $i < sizeof($branch_data); $i++) {
            $branch_data[$i][1] = 0;
            $branch_data[$i][2] = 0;
        }
        if ($period == $month_now){
            $data_today = $this->get_expense($start_date, $end_date);
            if ($data_today->num_rows() != 0) {
                for ($i = 0; $i < sizeof($branch_data); $i++) {
                    $branch_data[$i][1] = 0;
                    foreach ($data_today->result() as $val) {
                        if ($val->branch_name == $branch_data[$i][0]) {
                            $branch_data[$i][1] = $val->total;
                        }
                    }
                }
            }
        }
        
        $data_month = $this->get_expense($start_month, $end_month);
        if ($data_month->num_rows() != 0) {
            for ($i = 0; $i < sizeof($branch_data); $i++) {
                $branch_data[$i][2] = 0;
                foreach ($data_month->result() as $val) {
                    if ($val->branch_name == $branch_data[$i][0]) {
                        $branch_data[$i][2] = $val->total;
                    }
                }
            }
        }
        return $branch_data;
    }

    public function get_payment_process_status($period='') {
        $data_array = array(array());
        $data_array[0][0] = 'To be Cross Check';
        $data_array[1][0] = 'To be Check';
        $data_array[2][0] = 'To be Approve';
        $data_array[3][0] = 'To be Close';
        $data_array[4][0] = 'Close';
        $month_now = date('Y-m');
        $start_month = $period . '-01';
        $end_month = date("Y-m-t", strtotime($start_month));
        $data_uptoday = $this->get_payment_process('2018-01-01', $end_month);
        
        if ($period == $month_now){
            $current = date('Y-m-d');
            $data_today = $this->get_payment_process($current, $current);
            if ($data_today->num_rows() != 0) {
                for ($i = 0; $i < sizeof($data_array); $i++) {
                    $data_array[$i][1] = 0;
                    $data_array[$i][2] = 0;
                    foreach ($data_today->result() as $val) {
                        if ($data_array[$val->pp_status][0] == $data_array[$i][0]) {
                            $data_array[$i][1] = $val->total_status;
                            $data_array[$i][2] = 0;
                        }
                    }
                }
            } else {
                for ($i = 0; $i < sizeof($data_array); $i++) {
                    $data_array[$i][1] = 0;
                    $data_array[$i][2] = 0;
                }
            }
        } else {
            for ($i = 0; $i < sizeof($data_array); $i++) {
                $data_array[$i][1] = 0;
                $data_array[$i][2] = 0;
            }
        }
        

        if ($data_uptoday->num_rows() != 0) {
            for ($i = 0; $i < sizeof($data_array); $i++) {
                $data_array[$i][2] = 0;
                foreach ($data_uptoday->result() as $val) {
                    if ($data_array[$val->pp_status][0] == $data_array[$i][0]) {
                        $data_array[$i][2] = $val->total_status;
                    }
                }
            }
        } else {
            for ($i = 0; $i < sizeof($data_array); $i++) {
                $data_array[$i][2] = 0;
            }
        }

        return $data_array;
    }
    
    public function get_outstanding_period($branch_data = array(array()), $period='', $type=0) {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        
        $month_now = date('Y-m');   
        $start_month = $period . '-01';
        $end_month = date("Y-m-t", strtotime($start_month));
        /* inisialisasi */
        for ($i = 0; $i < sizeof($branch_data); $i++) {
            $branch_data[$i][1] = 0;
            $branch_data[$i][2] = 0;
        }
        if ($period == $month_now){
            $data_today = $this->get_outstanding($start_date, $end_date, $type);
            if ($data_today->num_rows() != 0) {
                for ($i = 0; $i < sizeof($branch_data); $i++) {
                    $branch_data[$i][1] = 0;
                    foreach ($data_today->result() as $val) {
                        if ($val->branch_name == $branch_data[$i][0]) {
                            $branch_data[$i][1] = $val->total;
                        }
                    }
                }
            }
        }
        
        $data_month = $this->get_outstanding($start_month, $end_month, $type);
        if ($data_month->num_rows() != 0) {
            for ($i = 0; $i < sizeof($branch_data); $i++) {
                $branch_data[$i][2] = 0;
                foreach ($data_month->result() as $val) {
                    if ($val->branch_name == $branch_data[$i][0]) {
                        $branch_data[$i][2] = $val->total;
                    }
                }
            }
        }
        return $branch_data;
    }

    public function show_bank_balance($month = '') {
        $report_bank_balance = $this->get_report_bank_balance($month);
        $bank_balance = $this->get_bank_balance($month);
        $str = '<table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Outlet</th>
                        <th class="text-right">Balance</th>
                        <th>C</th>
                        <th>A</th>
                        <th>U</th>
                    </tr>
                </thead>
                <tbody>';

        $total1 = 0;

        if ($bank_balance->num_rows() != 0) {
            $start_month = $month . '-01';
            $end_month = date("Y-m-t", strtotime($start_month));
            $paramdate = $start_month.'/'.$end_month;
            foreach ($bank_balance->result() as $value) {
                $total = $value->total_debit - $value->total_credit;
                $link = '<a href="' . site_url('report/ledger_detail/' . $value->account_id) . '/0/'.$paramdate.'">' . number_format($total, 2) . '</a>';
                $str .= '<tr>';
                $str .= '<th>' . $value->branch_name . '</th>';
                $str .= '<td class="text-right">' . $link . '</td>';
                $str .= '<td>'.$report_bank_balance[$value->account_id][0].'</td>';
                $str .= '<td>'.$report_bank_balance[$value->account_id][1].'</td>';
                $str .= '<td>'.$report_bank_balance[$value->account_id][2].'</td>';
                $str .= '</tr>';
                $total1 = $total1 + $total;
            }
        }

        $str .= '</tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">' . number_format($total1, 2) . '</th>
                        <th>C</th>
                        <th>A</th>
                        <th>U</th>
                    </tr>
                </tfoot>
            </table>';
        echo $str;
    }
    
    public function show_expense($month = '') {
        $branch_array = array(array());
        $branch = $this->get_branch();
        if ($branch->num_rows() != 0) {
            $i = 0;
            foreach ($branch->result() as $val) {
                $branch_array[$i][0] = $val->branch_name;
                $branch_array[$i][1] = '0';
                $i++;
            }
        }
        $expense = $this->get_expense_period($branch_array, $month);
        echo '<table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Outlet</th>
                        <th class="text-right">Today</th>
                        <th class="text-right">This Month</th>
                    </tr>
                </thead>
                <tbody>';
             
                $total1 = 0;
                $total2 = 0;
                
                if (sizeof($expense)!=0){
                    $exurl = $this->asik_model->category_report.$this->asik_model->report_03;
                    $datenow = date('Y-m-d');
                    $start_month = $month . '-01';
                    $end_month = date("Y-m-t", strtotime($start_month));
                    $paramdate = $start_month.'/'.$end_month;
                    for($i=0; $i<sizeof($expense); $i++){
                        $aweek = '0';
                        $amonth = '0';
                        if ($expense[$i][1] != 0){
                            $aweek = '<a href="'. site_url('expensereport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($expense[$i][1], 2) .'</a>';
                        }
                        if ($expense[$i][2] != 0){
                            $amonth = '<a href="'. site_url('expensereport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($expense[$i][2], 2) .'</a>';
                        }
                        echo '<tr>';
                        echo '<th>'.$expense[$i][0].'</th>';
                        echo '<td class="text-right">'.$aweek.'</td>';
                        echo '<td class="text-right">'.$amonth.'</td>';
                        echo '</tr>';
                        $total1 += $expense[$i][1];
                        $total2 += $expense[$i][2];
                    }
                }

                echo '</tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">'. number_format($total1, 2) .'</th>
                        <th class="text-right">'. number_format($total2, 2) .'</th>
                    </tr>
                </tfoot>
            </table>';
            $report_expense = $this->get_report_expense($month);    
            echo '<table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Checked</th>
                        <th>Approved</th>
                        <th>Upload</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>'. $report_expense[0] .'</td>
                        <td>'. $report_expense[1] .'</td>
                        <td>'. $report_expense[2] .'</td>
                    </tr>
                </tbody>
            </table>';
    }
    
    public function show_ppstatus($month = '') {
        $ppstatus = $this->get_payment_process_status($month);
            echo '<table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-right">Today</th>
                        <th class="text-right">Up Today</th>
                    </tr>
                </thead>
                <tbody>';
                
                $strcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                if (sizeof($ppstatus)!=0){
                    $today = '';
                    $balance = '';
                    $datenow = $month . '-'.date('d').'/'.$month . '-'.date('d');
                    $start_month = $month . '-01';
                    $end_month = date("Y-m-t", strtotime($start_month));
                    $paramdate = '2018-01-01/'.$end_month;
                    for($i=0; $i<(sizeof($ppstatus)-1); $i++){
                        if ($ppstatus[$i][1] != 0){
                            $today = '<a href="'. site_url('paymentprocess/dash/'.$strcode.'/'.$i.'/'.$datenow).'">'.$ppstatus[$i][1].'</a>';
                        } else {
                            $today = $ppstatus[$i][1];
                        }

                        if ($ppstatus[$i][2] != 0){
                            $balance = '<a href="'. site_url('paymentprocess/dash/'.$strcode.'/'.$i.'/'.$paramdate).'">'.$ppstatus[$i][2].'</a>';
                        } else {
                            $balance = $ppstatus[$i][2];
                        }
                        echo '<tr>';
                        echo '<td>'.$ppstatus[$i][0].'</td>';
                        echo '<td class="text-right">'.$today.'</td>';
                        echo '<td class="text-right">'.$balance.'</td>';
                        echo '</tr>';
                    }
                }

                echo '</tbody>
            </table>';
    }
    
    public function show_outstanding($month = '', $type=0) {
        $branch_array = array(array());
        $branch = $this->get_branch();
        if ($branch->num_rows() != 0) {
            $i = 0;
            foreach ($branch->result() as $val) {
                $branch_array[$i][0] = $val->branch_name;
                $branch_array[$i][1] = '0';
                $i++;
            }
        }
        $outstanding = $this->get_outstanding_period($branch_array, $month, $type);
        echo '<table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Outlet</th>
                        <th class="text-right">Today</th>
                        <th class="text-right">This Month</th>
                    </tr>
                </thead>
                <tbody>';               
                $total1 = 0;
                $total2 = 0;
                switch ($type) {
                    case 1:
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_04;
                        break;
                    case 2:
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_05;
                        break;
                    case 3:
                        $exurl = $this->asik_model->category_report.$this->asik_model->report_06;
                        break;
                }
                if (sizeof($outstanding)!=0){
                    $datenow = date('Y-m-d');
                    $start_month = $month . '-01';
                    $end_month = date("Y-m-t", strtotime($start_month));
                    $paramdate = $start_month.'/'.$end_month;
                    for($i=0; $i<sizeof($outstanding); $i++){
                        $aweek = '0';
                        $amonth = '0';
                        if ($outstanding[$i][1] != 0){
                            $aweek = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$datenow).'">'. number_format($outstanding[$i][1], 2) .'</a>';
                        }
                        if ($outstanding[$i][2] != 0){
                            $amonth = '<a href="'. site_url('outstandingreport/go/'.$exurl.'/0/'.$paramdate).'">'. number_format($outstanding[$i][2], 2) .'</a>';
                        }
                        echo '<tr>';
                        echo '<th>'.$outstanding[$i][0].'</th>';
                        echo '<td class="text-right">'.$aweek.'</td>';
                        echo '<td class="text-right">'.$amonth.'</td>';
                        echo '</tr>';
                        $total1 += $outstanding[$i][1];
                        $total2 += $outstanding[$i][2];
                    }
                }
                
                echo '</tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-right">'. number_format($total1, 2) .'</th>
                        <th class="text-right">'. number_format($total2, 2) .'</th>
                    </tr>
                </tfoot>
            </table>';
    }
    
    public function get_outstanding_cash_request() {
        $sql  = 'SELECT * FROM cash_request WHERE cash_request_status=3';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_noremark_cash_request() {
        $os = $this->get_outstanding_cash_request();
        $in = '';
        if ($os->num_rows()!=0){
            foreach ($os->result() as $value) {
                $in .= $value->cash_request_id.',';
            }
            $in = substr($in, 0, strlen($in)-1);
        }
        $sql  = 'SELECT * FROM cash_request_remark WHERE cash_request_id IN('.$in.')';
        $remark = $this->db->query($sql)->num_rows();
        $total = $os->num_rows()-$remark;
        return $total;
    }
    
    public function get_report_bank_balance($yearmonth = '') {
        $start_date = $yearmonth . '-01';
        $end_date = $end = date("Y-m-t", strtotime($start_date));
        $sql = 'SELECT * FROM report_file WHERE start_date="' . $start_date . '" AND ';
        $sql .= 'end_date="' . $end_date . '" AND account_id IN(7,8,9,10,11,12,13)';
        $query = $this->db->query($sql);
        $col = array('0','0','0');
        $row = array(array());
        // inisialisasi
        $index = 7;
        for($i=0; $i<7; $i++){
            $row[$index] = $col;
            $index++;
        }
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $col[0] = isset($value->checked_by)?'<i class="fa fa-check"></i>':'0';
                $col[1] = isset($value->approved_by)?'<i class="fa fa-check"></i>':'0';
                $col[2] = isset($value->file_name)?'<i class="fa fa-check"></i>':'0';
                $row[$value->account_id] = $col;
            }
        }
        
        return $row;
    }
    
    public function get_report_expense($yearmonth = '') {
        $start_date = $yearmonth . '-01';
        $end_date = $end = date("Y-m-t", strtotime($start_date));
        $sql = 'SELECT * FROM report_file WHERE start_date="' . $start_date . '" AND ';
        $sql .= 'end_date="' . $end_date . '" AND report_type=3';
        $query = $this->db->query($sql);
        $col = array('0','0','0');
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $col[0] = isset($value->checked_by)?'<i class="fa fa-check"></i>':'0';
                $col[1] = isset($value->approved_by)?'<i class="fa fa-check"></i>':'0';
                $col[2] = isset($value->file_name)?'<i class="fa fa-check"></i>':'0';
            }
        }
        
        return $col;
    }

}
