<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Webservice
 *
 * @author Hendra McHen
 */
class Webservice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }

    public function account_list() {
        $sql = 'SELECT * FROM account ';
        $query = $this->db->query($sql);
        return $query;
    }

    public function index() {
        //////
        $data_account = $this->account_list();
        $dataarray = array();
        foreach ($data_account->result_array() as $row) {
            $dataarray[] = $row;
        }
        $string = json_encode($dataarray);
        echo $string;
    }

    public function expensereport($start_date = '', $end_date = '') {
        $expense = $this->get_expense_list($start_date, $end_date);
        $array = array();
        $title_temp = '';
        $branch = $this->get_branch_expense_list();
        if ($expense->num_rows() != 0) {
            $i = 0;
            foreach ($expense->result() as $value) {
                $col = array();
                if ($value->expense_title != $title_temp) {
                    $title_temp = $value->expense_title;
                    $pv_enc = $this->get_pv_id($value->pv_number);
                    $col[0] = $value->expense_title;

                    $k = 0;
                    $totalcol = 0;
                    foreach ($branch as $key => $b) {
                        $col[$k + 1] = 0;
                        if ($value->branch_name == $b) {
                            $col[$key + 1] = $value->amount;
                        }
                        $totalcol = $totalcol + $col[$k + 1];
                        $k++;
                    }
                    $col[$k + 1] = $totalcol;
                    $array[$i] = $col;
                    $i++;
                } else {
                    $title_temp = $value->expense_title;
                    $pv_enc = $this->get_pv_id($value->pv_number);
                    $col[0] = $value->expense_title;
                    $k = 0;
                    foreach ($branch as $key => $b) {
                        $col[$k + 1] = $array[$i - 1][$k + 1];
                        $k++;
                    }
                    $totalcol = 0;
                    $j = 1;
                    foreach ($branch as $key => $b) {
                        if ($value->branch_name == $b) {
                            $col[$key + 1] = $col[$key + 1] + $value->amount;
                        }
                        $totalcol = $totalcol + $col[$j];
                        $j++;
                    }
                    $col[$k + 1] = $totalcol;
                    $array[$i - 1] = $col;
                }
            }
        }

        $string = json_encode($array);
        echo $string;
    }

    public function get_expense_list($start_date = '', $end_date = '', $type = 0) {
        $sql = 'SELECT ex.expense_date, ex.expense_title, ';
        $sql .= 'b.branch_name, ex.amount, ex.expense_status, ex.pv_number FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_status = 0 ';
        //if ($start_date != '' && $end_date != ''){
        if ($type == 0) {
            $where = 'expense_type IN(0,1)';
        } else {
            $where = 'expense_type=1';
        }
        $sql .= 'AND ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND ' . $where;
        //}

        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_branch_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM branch AS b ';
        $sql .= 'ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0) {
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_name;
            }
        }
        return $branch;
    }

    public function get_branch_expense_list() {
        $sql = 'SELECT DISTINCT b.branch_name FROM expense AS ex
        INNER JOIN branch AS b ON ex.branch_id=b.branch_id
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0) {
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_name;
            }
        }
        return $branch;
    }

    public function get_pv_id($pv_number = '') {
        $sql = 'SELECT * FROM payment_voucher WHERE pv_number="' . $pv_number . '" ';
        $query = $this->db->query($sql);
        $pv_enc = '';
        if ($query->num_rows() != 0) {
            $row = $query->row();
            $pv_enc = $this->general_model->encrypt_value($row->pv_id);
        }
        return $pv_enc;
    }

    public function outstandingreport($start_date = '', $end_date = '', $tipe = 0, $outstanding_status = 0) {
        switch ($tipe) {
            case 1:
                $outstanding = $this->get_outstanding_list($start_date, $end_date, 1, $outstanding_status);
                break;
            case 2:
                $outstanding = $this->get_outstanding_list($start_date, $end_date, 2, $outstanding_status);
                break;
            case 3:
                $outstanding = $this->get_outstanding_list($start_date, $end_date, 3, $outstanding_status);
                break;
        }

        $branch = $this->get_branch_list();
        $array_tbl = array(array());
        $baris = 0;

        if ($outstanding->num_rows() != 0) {

            foreach ($outstanding->result() as $value) {
                $total = 0;
                $array_tbl[$baris][0] = $value->outstanding_date;
                $array_tbl[$baris][1] = $value->outstanding_description;
                $k = 2;
                foreach ($branch as $b) {
                    $array_tbl[$baris][$k] = 0;
                    if ($value->branch_name == $b) {
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

        $string = json_encode($array_tbl);
        echo $string;
    }

    public function get_outstanding_list($start_date = '', $end_date = '', $type = 0, $status = '') {
        if ($status == '') {
            $os_status = '0,1';
        } else {
            if ($status == 2) {
                $os_status = '0,1';
            } else {
                $os_status = $status;
            }
        }

        $sql = 'SELECT os.outstanding_number, os.outstanding_date, os.outstanding_description, ';
        $sql .= 'b.branch_name, os.amount, os.outstanding_status, os.pv_number FROM outstanding AS os ';
        $sql .= 'INNER JOIN branch AS b ON os.branch_id=b.branch_id ';
        $sql .= 'WHERE os.outstanding_status IN (' . $os_status . ') ';
        $sql .= 'AND os.outstanding_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'AND os.outstanding_type=' . $type;

        $query = $this->db->query($sql);
        return $query;
    }

    public function ledgerreport($account_id = 0, $start_date = '', $end_date = '') {
        $this->load->model('ledger_model');
        $list = $this->ledger_model->get_ledger_by_account($account_id, $start_date, $end_date);

        $first_date = substr($start_date, 0, 8) . '01';
        if ($start_date == $first_date) {
            $previous_balance = 0;
        } else {
            $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
            $previous_balance = $this->ledger_model->get_previous_balance($account_id, $first_date, $previous_date);
        }

        $array = array();
        $cols = array();
        if ($list->num_rows() != 0) {
            $no = 1;
            $balance = $previous_balance;
            foreach ($list->result() as $val) {
                $remark = '-';
                if (isset($val->remark)) {
                    $remark = $val->remark;
                }
                $debcre = $val->debit + $val->credit;
                if ($val->debit == 0) {
                    $balance = $balance - $debcre;
                } else {
                    $balance = $balance + $debcre;
                }
                ////////////////////////////////////////////////////////
                $ppnumber = '-';
                if (isset($pp_numbers[$val->pv_number])) {
                    $ppnumber = $pp_numbers[$val->pv_number];
                } else {
                    $ppnumber = $val->pv_number;
                }
                $cols[0] = $no;
                $cols[1] = $this->general_model->get_string_date_ver2($val->trans_date);
                $cols[2] = $remark;
                $cols[3] = $ppnumber;
                $cols[4] = $val->description;
                $cols[5] = number_format($val->debit, 2);
                $cols[6] = number_format($val->credit, 2);
                $cols[7] = number_format($balance, 2);
                $array[] = $cols;
                $no++;
                ////////////////////////////////////////////////////////
            }
        }
        $string = json_encode($array);
        echo $string;
    }

    public function summaryreport($start_date = '', $end_date = '') {
        $branch = $this->get_branch_list();

        $array_tbl = array(array());
        $array_tbl[0][0] = 'Opening Bank Balance';
        $array_tbl[1][0] = 'Receipt in Bank';
        $array_tbl[2][0] = ' # From Revenue Bank';
        $array_tbl[3][0] = ' # Borrow Received';
        $array_tbl[4][0] = ' # Borrow Returned Inward';
        $array_tbl[5][0] = 'Payment from Bank';
        $array_tbl[6][0] = ' # Expenses';
        $array_tbl[7][0] = ' # O/S Cash Request';
        $array_tbl[8][0] = ' # O/S Third Party';
        $array_tbl[9][0] = ' # O/S Outlet (Borrow Given)';
        // *start | update 2018-05-25
        $array_tbl[10][0] = ' # O/S Borrow Given (CR)';
        // *start | update 2018-05-25
        $array_tbl[11][0] = ' # Borrow Returned Outward';
        $array_tbl[12][0] = 'Closing Balance Before Adjustment';
        $array_tbl[13][0] = ' # Adjustment Nota Receive';
        $array_tbl[14][0] = 'Closing Bank Balance';
        /* New array 2018-03-21 */
        $array_total = array(array());

        $opening = $this->get_opening_balance($start_date, $end_date);
        $revenue = $this->get_from_revenue_bank($start_date, $end_date);
        $received = $this->get_received($start_date, $end_date);
        $inward = $this->get_inward($start_date, $end_date);
        $expenses = $this->get_expense($start_date, $end_date);
        $outstanding_cr = $this->get_outstanding($start_date, $end_date, 1);
        $outstanding_th = $this->get_outstanding($start_date, $end_date, 3);
        $borrow_given = $this->get_outstanding($start_date, $end_date, 2);
        $borrow_given_cr = $this->get_outstanding($start_date, $end_date, 4);
        $borrow_returned = $this->get_borrow_returned($start_date, $end_date);
        $adjustment = $this->get_adjustment($start_date, $end_date);

        /* ============================================================ */
        //==================== Opening Balance =====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[0][$k] = 0;
            $array_total[0][$k] = 0;
            $k++;
        }
        $array_tbl[0][$k] = 0;
        $array_total[0][$k] = 0;
        $total = 0;

        if ($opening->num_rows() != 0) {
            foreach ($opening->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[0][$k] = number_format($value->total);
                        $array_total[0][$k] = $value->total;
                        $total = $total + $array_total[0][$k];
                    }
                    $k++;
                }
                $array_tbl[0][$k] = $total;
                $array_total[0][$k] = $total;
            }
        }


        // ==================== Receipt in Bank ====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[1][$k] = 0;
            $k++;
        }
        $array_tbl[1][$k] = 0;

        // ==================== from revenue bank ==================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[2][$k] = 0;
            $array_total[2][$k] = 0;
            $k++;
        }
        $array_tbl[2][$k] = 0;
        $array_total[2][$k] = 0;
        $total = 0;
        if ($revenue->num_rows() != 0) {
            foreach ($revenue->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[2][$k] = number_format($value->total);
                        $array_total[2][$k] = $value->total;
                        $total = $total + $array_total[2][$k];
                    }
                    $k++;
                }
                $array_tbl[2][$k] = $total;
                $array_total[2][$k] = $total;
            }
        }

        // ==================== borrow received ====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[3][$k] = 0;
            $array_total[3][$k] = 0;
            $k++;
        }
        $array_tbl[3][$k] = 0;
        $array_total[3][$k] = 0;
        $total = 0;
        if ($received->num_rows() != 0) {
            foreach ($received->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[3][$k] = number_format($value->total);
                        $array_total[3][$k] = $value->total;
                        $total = $total + $array_total[3][$k];
                    }
                    $k++;
                }
                $array_tbl[3][$k] = $total;
                $array_total[3][$k] = $total;
            }
        }

        // ==================== borrow returned inward ====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[4][$k] = 0;
            $array_total[4][$k] = 0;
            $k++;
        }
        $array_tbl[4][$k] = 0;
        $array_total[4][$k] = 0;
        $total = 0;
        if ($inward->num_rows() != 0) {
            foreach ($inward->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[4][$k] = number_format($value->total);
                        $array_total[4][$k] = $value->total;
                        $total = $total + $array_total[4][$k];
                    }
                    $k++;
                }
                $array_tbl[4][$k] = $total;
                $array_total[4][$k] = $total;
            }
        }

        // ==================== Get Total Receipt in Bank ==========
        $k = 1;
        foreach ($branch as $b) {
            $array_total[1][$k] = $array_total[2][$k] + $array_total[3][$k] + $array_total[4][$k];
            $array_tbl[1][$k] = number_format($array_total[1][$k]);
            $k++;
        }
        $array_tbl[1][$k] = $array_total[2][$k] + $array_total[3][$k] + $array_total[4][$k];
        $array_total[1][$k] = $array_total[2][$k] + $array_total[3][$k] + $array_total[4][$k];
        // =========================================================
        // ==================== Payment from Bank ==================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[5][$k] = $k;
            $k++;
        }
        $array_tbl[5][$k] = $k;

        // ======================== Expenses =======================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[6][$k] = 0;
            $array_total[6][$k] = 0;
            $k++;
        }
        $array_tbl[6][$k] = 0;
        $array_total[6][$k] = 0;
        $total = 0;
        if ($expenses->num_rows() != 0) {
            foreach ($expenses->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[6][$k] = number_format($value->total);
                        $array_total[6][$k] = $value->total;
                        $total = $total + $array_total[6][$k];
                    }
                    $k++;
                }
                $array_tbl[6][$k] = $total;
                $array_total[6][$k] = $total;
            }
        }

        // ======================= Outstanding Cash Request =====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[7][$k] = 0;
            $array_total[7][$k] = 0;
            $k++;
        }
        $array_tbl[7][$k] = 0;
        $array_total[7][$k] = 0;
        $total = 0;
        if ($outstanding_cr->num_rows() != 0) {
            foreach ($outstanding_cr->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[7][$k] = number_format($value->total);
                        $array_total[7][$k] = $value->total;
                        $total = $total + $array_total[7][$k];
                    }
                    $k++;
                }
                $array_tbl[7][$k] = $total;
                $array_total[7][$k] = $total;
            }
        }
        // ======================= Outstanding Third Party =====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[8][$k] = 0;
            $array_total[8][$k] = 0;
            $k++;
        }
        $array_tbl[8][$k] = 0;
        $array_total[8][$k] = 0;
        $total = 0;
        if ($outstanding_th->num_rows() != 0) {
            foreach ($outstanding_th->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[8][$k] = number_format($value->total);
                        $array_total[8][$k] = $value->total;
                        $total = $total + $array_total[8][$k];
                    }
                    $k++;
                }
                $array_tbl[8][$k] = $total;
                $array_total[8][$k] = $total;
            }
        }

        // ====================== Outstanding Outlet (Borrow Given) ======================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[9][$k] = 0;
            $array_total[9][$k] = 0;
            $k++;
        }
        $array_tbl[9][$k] = 0;
        $array_total[9][$k] = 0;
        $total = 0;
        if ($borrow_given->num_rows() != 0) {
            foreach ($borrow_given->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[9][$k] = number_format($value->total);
                        $array_total[9][$k] = $value->total;
                        $total = $total + $array_total[9][$k];
                    }
                    $k++;
                }
                $array_tbl[9][$k] = $total;
                $array_total[9][$k] = $total;
            }
        }

        // ====================== OS (Borrow Given Cash Request (CR)) ======================
        /* just show */
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[10][$k] = 0;
            $array_total[10][$k] = 0;
            $k++;
        }
        $array_tbl[10][$k] = 0;
        $array_total[10][$k] = 0;
        $total = 0;
        if ($borrow_given_cr->num_rows() != 0) {
            foreach ($borrow_given_cr->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[10][$k] = number_format($value->total);
                        $array_total[10][$k] = $value->total;
                        $total = $total + $array_total[10][$k];
                    }
                    $k++;
                }
                $array_tbl[10][$k] = $total;
                $array_total[10][$k] = $total;
            }
        }

        /*         * ******************** start change : 2018-05-25 ********************* */
        // ===================== Borrow Return Outward =====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[11][$k] = 0;
            $array_total[11][$k] = 0;
            $k++;
        }
        $array_tbl[11][$k] = 0;
        $array_total[11][$k] = 0;
        $total = 0;
        if ($borrow_returned->num_rows() != 0) {
            foreach ($borrow_returned->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[11][$k] = number_format($value->total);
                        $array_total[11][$k] = $value->total;
                        $total = $total + $array_total[11][$k];
                    }
                    $k++;
                }
                $array_tbl[11][$k] = $total;
                $array_total[11][$k] = $total;
            }
        }

        // ==================== Get Total Payment from Bank ========
        $k = 1;
        foreach ($branch as $b) {
            $array_total[5][$k] = $array_total[6][$k] + $array_total[7][$k] + $array_total[8][$k] + $array_total[9][$k] + $array_total[11][$k];
            $array_tbl[5][$k] = number_format($array_total[5][$k]);

            $k++;
        }
        $array_tbl[5][$k] = $array_total[6][$k] + $array_total[7][$k] + $array_total[8][$k] + $array_total[9][$k] + $array_total[11][$k];
        $array_total[5][$k] = $array_total[6][$k] + $array_total[7][$k] + $array_total[8][$k] + $array_total[9][$k] + $array_total[11][$k];
        //==========================================================
        // ==================== Closing Balance Before Adjustment ====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[12][$k] = 0;
            $k++;
        }
        $array_tbl[12][$k] = 0;
        // ==================== Get total Closing Balance Before Adjustment ========
        $k = 1;
        foreach ($branch as $b) {
            $array_total[12][$k] = $array_total[0][$k] + $array_total[1][$k] - $array_total[5][$k];
            $array_tbl[12][$k] = number_format($array_total[12][$k]);
            $k++;
        }
        $array_tbl[12][$k] = $array_total[0][$k] + $array_total[1][$k] - $array_total[5][$k];
        // ==================== # Adjustment Nota Receive ==================================                    
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[13][$k] = 0;
            $array_total[13][$k] = 0;
            $k++;
        }
        $array_tbl[13][$k] = 0;
        $array_total[13][$k] = 0;
        $total = 0;
        if ($adjustment->num_rows() != 0) {
            foreach ($adjustment->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $array_tbl[13][$k] = number_format($value->total);
                        $array_total[13][$k] = $value->total;
                        $total = $total + $array_total[13][$k];
                    }
                    $k++;
                }
                $array_tbl[13][$k] = $total;
                $array_total[13][$k] = $total;
            }
        }


        // ==================== # Closing Bank Balance ====================
        $k = 1;
        foreach ($branch as $b) {
            $array_tbl[14][$k] = 0;
            $array_total[14][$k] = 0;
            $k++;
        }
        $array_tbl[14][$k] = 0;
        $array_total[14][$k] = 0;
        $total = 0;
        $k = 1;
        foreach ($branch as $b) {
            $array_total[14][$k] = $array_total[12][$k] + $array_total[13][$k];
            $array_tbl[14][$k] = number_format($array_total[14][$k]);
            $total = $total + $array_total[14][$k];
            $k++;
        }
        $array_tbl[14][$k] = $total;
        $array_total[14][$k] = $total;
        //$array_tbl[13][$k] = $array_total[11][$k] + $array_total[12][$k];
        /* ============================================================ */


        $string = json_encode($array_tbl);
        echo $string;
    }
    
    public function get_expense($start_date='', $end_date='') {
        $sql  = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_from_revenue_bank($start_date='', $end_date='') {
        $sql = 'SELECT SUM(rb.amount)AS total, b.branch_name FROM receive_bank AS rb
        INNER JOIN branch AS b ON b.branch_id=rb.branch_id
        WHERE rb.receive_bank_date BETWEEN "'.$start_date.'" AND "'.$end_date.' "
        GROUP BY rb.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_outstanding($start_date='', $end_date='', $type=0) {
        $sql  = 'SELECT SUM(os.amount) AS total, b.branch_name  FROM outstanding AS os 
        INNER JOIN branch AS b ON os.branch_id=b.branch_id 
        WHERE os.outstanding_status IN (0,1)  
        AND os.outstanding_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND os.outstanding_type='.$type.' ';
        $sql .= 'GROUP BY os.branch_id ORDER BY b.branch_id';        
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_received($start_date='', $end_date='') {
        $sql  = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        WHERE cr.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_borrow_given($start_date='', $end_date='') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        INNER JOIN branch AS b ON a.branch_id=b.branch_id 
        WHERE cr.cash_receive_status < 2 
        AND cr.cash_receive_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_borrow_returned($start_date='', $end_date='') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name FROM cash_return AS ct
        INNER JOIN branch AS b ON b.branch_id=ct.branch_id
        WHERE ct.cash_return_date BETWEEN "'.$start_date.'" AND "'.$end_date.'"
        GROUP BY ct.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_opening_balance($start_date='', $end_date='') {
        $sql = 'SELECT SUM(op.amount)AS total, b.branch_name FROM opening_balance AS op
        INNER JOIN branch AS b ON b.branch_id=op.branch_id
        WHERE op.opening_balance_date BETWEEN "'.$start_date.'" AND "'.$end_date.' "
        GROUP BY op.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_previous_balance($start_date='', $end_date='') {
        $sql  = 'SELECT L.account_id,  SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit FROM ledger AS L 
        INNER JOIN transactions AS t ON L.trans_id=t.trans_id
        WHERE  L.account_id IN (7,8,9,10,11,12,13) AND trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
        GROUP BY L.account_id ORDER BY L.account_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_inward($start_date='', $end_date='') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name, ct.account_to FROM cash_return AS ct
        INNER JOIN account AS a ON a.account_id=ct.account_to
        INNER JOIN branch AS b ON b.branch_id=a.branch_id
        WHERE ct.cash_return_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" 
        GROUP BY  b.branch_name ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_adjustment($start_date='', $end_date='') {
        $sql  = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND expense_type=1 ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_report($start_date='', $end_date='') {
        $sql = 'SELECT s.supplier_id,  s.supplier_name, b.branch_name, pp.pp_date, pp.total ';
        $sql .= 'FROM payment_process AS pp ';
        $sql .= 'INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id ';
        $sql .= 'INNER JOIN branch AS b ON pp.branch_id=b.branch_id ';
        $sql .= 'WHERE pp.pp_status < 4 ';
        $sql .= ' AND pp.pp_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'ORDER BY s.supplier_name ASC ';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function supplierreport($start_date = '', $end_date = '') {
        $report_data = $this->get_supplier_report($start_date, $end_date);
        $array = array();
        $supplier_temp = '';
        $branch = $this->get_branch_creditinvoice_list();
        if ($report_data->num_rows() != 0) {
            $i = 0;
            foreach ($report_data->result() as $value) {
                $col = array();
                if ($value->supplier_name != $supplier_temp) {
                    $supplier_temp = $value->supplier_name;
                    $col[0] = $value->supplier_name;
                    $k = 0;
                    $totalcol = 0;
                    foreach ($branch as $key => $b) {
                        $col[$k + 1] = 0;
                        if ($value->branch_name == $b) {
                            $col[$key + 1] = $value->total;
                        }
                        $totalcol = $totalcol + $col[$k + 1];
                        $k++;
                    }
                    $col[$k + 1] = $totalcol;
                    $col[$k + 2] = $value->supplier_id;
                    $array[$i] = $col;
                    $i++;
                } else {
                    $supplier_temp = $value->supplier_name;
                    $col[0] = $value->supplier_name;
                    $k = 0;
                    foreach ($branch as $key => $b) {
                        $col[$k + 1] = $array[$i - 1][$k + 1];
                        $k++;
                    }
                    $totalcol = 0;
                    $j = 1;
                    foreach ($branch as $key => $b) {
                        if ($value->branch_name == $b) {
                            $col[$key + 1] = $col[$key + 1] + $value->total;
                        }
                        $totalcol = $totalcol + $col[$j];
                        $j++;
                    }
                    $col[$k + 1] = $totalcol;
                    $col[$k + 2] = $value->supplier_id;
                    $array[$i - 1] = $col;
                }
            }
        }

        $string = json_encode($array);
        echo $string;
    }
    
    public function get_branch_creditinvoice_list() {
        $sql  = 'SELECT DISTINCT b.branch_name FROM credit_invoice AS ci
        INNER JOIN branch AS b ON ci.branch_id=b.branch_id
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
    
    public function ppdaily($day='') {
        $tanggal = $day != '' ? $day : date('Y-m-d');
        $approved = 3;
        $closed = 4;
        $data = array();
        $all = $this->get_payment_process_by_day($tanggal);
        $approve = $this->get_payment_process_by_status($approved, $day);
        $close = $this->get_payment_process_by_status($closed, $day);
        $data['PP Created'] = $all;
        $data['PP Closed'] = $close;
        $data['Balance Unsettled'] = $approve;
        $string = json_encode($data);
        echo $string;
    }
    
    public function get_payment_process_by_day($day='') {
        $sql  = 'SELECT pp.* FROM payment_process AS pp ';
        $sql .= 'WHERE pp_date="'.$day.'" ';
        $sql .= 'ORDER BY pp_date DESC';
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $total = $query->num_rows();
        }
        return $total;
    }
    
    public function get_payment_process_by_status($status=0, $day='') {
        $sql  = 'SELECT pp.* FROM payment_process AS pp ';
        $sql .= 'WHERE pp.pp_status='.$status.' AND pp_date="'.$day.'" ';
        $sql .= 'ORDER BY pp_date DESC';
        $query = $this->db->query($sql);
        $total = 0;
        if ($query->num_rows()!=0){
            $total = $query->num_rows();
        }
        return $total;
    }
    
    public function updatecr() {
        $sql ="SELECT pv.cash_request_id, pv.trans_id, pv.pv_number FROM payment_voucher AS pv 
INNER JOIN transactions AS t ON pv.trans_id=t.trans_id
WHERE pv.pv_number IN(
'PV180212124709044',
'PV180305130817513',
'PV180303190340507',
'PV180303192842508',
'PV180216181437169',
'PV180217191408182',
'PV180224123728330',
'PV180224123501329',
'PV180225081947383',
'PV180217190942181',
'PV180225085038389',
'PV180225084000386',
'PV180225084818388',
'PV180225090409392',
'PV180225085903391',
'PV180225085619390',
'PV180225084548387',
'PV180225094841395',
'PV180225091634393',
'PV180225091912394',
'PV180305132140515',
'PV180305131522514',
'PV180303143849504',
'PV180303162611505',
'PV180307124552583',
'PV180309112853650',
'PV180309113313651',
'PV180314201932855',
'PV180312200016741',
'PV180313165029790',
'PV180313165614791',
'PV180321152419934',
'PV180321133210933',
'PV180322191904984',
'PV180322192212985',
'PV1803240934401011',
'PV1803241043361017',
'PV1803241545441026',
'PV1803261045551053',
'PV1803280958541123',
'PV1803281136111127',
'PV1804031049041236',
'PV1804031117121238',
'PV1804051147441306',
'PV1804061847441400',
'PV1804061844291399',
'PV1804091823061502',
'PV1804111139451544',
'PV1804111053571538',
'PV1804121119301563',
'PV1804121816261605',
'PV1804121153261566',
'PV1804171254131695',
'PV1804181049101705',
'PV1804231005101812',
'PV1804261140161918',
'PV1804261051031911',
'PV1804261030041910',
'PV1804261304401923',
'PV1804281552021960',
'PV1805011607222079',
'PV1805011616032082',
'PV1805051113172215',
'PV1805051151342228',
'PV1805091942512402',
'PV1805160938322568',
'PV1805161139112569',
'PV1805161339502575',
'PV1805161841432580',
'PV1805211958352624',
'PV1805231052572665',
'PV1803280958541123',
'PV1805160938322568',
'PV1805161339502575',
'PV1805161339502575',
'PV1804061844291399',
'PV1804111053571538',
'PV1804061844291399',
'PV1804061844291399',
'PV1804061844291399',
'PV180225085619390',
'PV180305132140515',
'PV180309113313651'
)";
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $data = array(
                    'trans_id' => $value->trans_id,
                    'pv_number' => $value->pv_number
                );
                $this->db->where('cash_request_id', $value->cash_request_id);
                $this->db->update('cash_request_balance_old', $data);
                echo '<div>OK</div>';
            }
        }
    }
    
    public function experiment01() {
        $sql = 'SELECT * FROM payment_voucher WHERE cash_request_id IN(
        SELECT cr.cash_request_id FROM cash_request_balance_old AS crbo
        INNER JOIN cash_request AS cr ON crbo.cash_request_id=cr.cash_request_id
        WHERE crbo.trans_id=0 ORDER BY cr.cash_request_id
        )';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $no = 1;
            foreach ($query->result() as $value) {
                if ($value->description !=''){
                    $trans_id = $this->get_trans_id($value->description);
                    //echo '<div>'.$no.') '.$trans_id.'</div>';
                    
                    if ($trans_id !=0){
                        $data = array(
                            'trans_id' => $trans_id
                        );
                        $this->db->where('pv_number', $value->pv_number);
                        $this->db->update('payment_voucher', $data);
                        echo '<div>'.$no.') OK</div>';
                    } else {
                        echo '<div>'.$no.') NOT OK</div>';
                    }
                    
                    
                    
                    $no++;
                    /*
                    $data = array(
                        'trans_id' => $trans_id
                    );
                    $this->db->where('pv_number', $value->pv_number);
                    $this->db->update('payment_voucher', $data);
                    echo '<div>OK</div>';*/
                }
                
            }
        }
    }
    
    public function get_trans_id($desc='') {
        $sql = 'SELECT * FROM transactions WHERE description="'.$desc.'"';
        $query = $this->db->query($sql);
        $trans_id = 0;
        if ($query->num_rows()!=0){
            $trans = $query->row();
            $trans_id = $trans->trans_id;
        }
        return $trans_id;
    }
    
    public function experiment02() {
        $sql ='SELECT * FROM payment_voucher WHERE cash_request_id IN(
        SELECT cr.cash_request_id FROM cash_request_balance_old AS crbo
        INNER JOIN cash_request AS cr ON crbo.cash_request_id=cr.cash_request_id
        WHERE crbo.trans_id=0 ORDER BY cr.cash_request_id
        ) AND trans_id!=0';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $no = 1;
            foreach ($query->result() as $value) {
                $data = array(
                    'trans_id' => $value->trans_id,
                    'pv_number' => $value->pv_number
                );
                $this->db->where('cash_request_id', $value->cash_request_id);
                $this->db->update('cash_request_balance_old', $data);
                echo '<div>'.$no.') OK</div>';
            }
        }
    }
    
    public function experiment03() {
        $sql = 'SELECT pv.* FROM payment_voucher AS pv WHERE pv.pv_number IN(
        SELECT crbo.pv_number FROM cash_request_balance_old AS crbo
        INNER JOIN cash_request AS cr ON crbo.cash_request_id=cr.cash_request_id
        WHERE crbo.trans_id=0
        )';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $no = 1;
            foreach ($query->result() as $value) {
                $amount = $value->total + $value->admin_fee;
                $trans_id = $this->get_trans_by_date($value->pv_date, $amount);
                
                $data = array(
                    'trans_id' => $trans_id
                );
                $this->db->where('pv_id', $value->pv_id);
                $this->db->update('payment_voucher', $data);
                
                echo '<div>'.$no.') OK ='.$trans_id.'</div>';
                $no++;
            }
        }
    }
    
    public function get_trans_by_date($tanggal='', $amount=0) {
        $sql = 'SELECT * FROM transactions WHERE trans_date="'.$tanggal.'" AND amount='.$amount;
        $query = $this->db->query($sql);
        $trans_id = 0;
        if ($query->num_rows()!=0){
            $trans = $query->row();
            $trans_id = $trans->trans_id;
        }
        return $trans_id;
    }
    
    public function experiment04() {
        $sql = 'SELECT pv.* FROM payment_voucher AS pv WHERE pv.pv_number IN(
        SELECT crbo.pv_number FROM cash_request_balance_old AS crbo
        INNER JOIN cash_request AS cr ON crbo.cash_request_id=cr.cash_request_id
        WHERE crbo.trans_id=0
        )';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            $no = 1;
            foreach ($query->result() as $value) {
                
                $data = array(
                    'trans_id' => $value->trans_id,
                    'pv_number' => $value->pv_number
                );
                $this->db->where('cash_request_id', $value->cash_request_id);
                $this->db->update('cash_request_balance_old', $data);
                
                echo '<div>'.$no.') OK </div>';
                $no++;
            }
        }
    }
    

}
