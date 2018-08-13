<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Summaryreport
 *
 * @author Hendra McHen
 */
class Summaryreport extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_report_model');
        $this->load->model('general_model');
    }

    public $category_index = 3;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_01) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '', $button = 0, $startd = '', $endd = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)) {
                $this->category = $category;
                $this->module = $module;
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                /* end privilege */
                $this->load->helper('form');
                $data['pagecode'] = $string;
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($startd != '') {
                    $start_date = $startd;
                }
                if ($endd != '') {
                    $end_date = $endd;
                }

                $branch = $this->get_branch_list();
                $data['branch'] = $branch;

                $array_tbl = array(array());
                $array_tbl[0][0] = '<strong>Opening Bank Balance</strong>';
                $array_tbl[1][0] = '<strong>Receipt in Bank</strong>';
                $array_tbl[2][0] = ' # From Revenue Bank';
                $array_tbl[3][0] = ' # Borrow Received';
                $array_tbl[4][0] = ' # Borrow Returned Inward';
                $array_tbl[5][0] = '<strong>Payment from Bank</strong>';
                $array_tbl[6][0] = ' # Expenses';
                $array_tbl[7][0] = ' # O/S Cash Request';
                $array_tbl[8][0] = ' # O/S Third Party';
                $array_tbl[9][0] = ' # O/S Outlet (Borrow Given)';
                // *start | update 2018-05-25
                $array_tbl[10][0] = ' # O/S Borrow Given (CR)';
                // *start | update 2018-05-25
                $array_tbl[11][0] = ' # Borrow Returned Outward';
                $array_tbl[12][0] = '<strong>Closing Balance Before Adjustment</strong>';
                $array_tbl[13][0] = ' # Adjustment Nota Receive';
                $array_tbl[14][0] = '<strong>Closing Bank Balance</strong>';
                /* New array 2018-03-21 */
                $array_total = array(array());

                if ($button == 0) {
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
                } else {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    //$start_date = $year.'-'.$month.'-01';
                    switch ($button) {
                        case 1:
                            $start_date = date('Y-m-d');
                            $end_date = date('Y-m-d');
                            break;
                        case 2:
                            $start_date = date('Y-m-d', strtotime("-1 days"));
                            $end_date = date('Y-m-d', strtotime("-1 days"));
                            break;
                        case 3:
                            $signupdate = $year . '-' . $month . '-' . $day;
                            $signupweek = date("W", strtotime($signupdate));

                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 4:
                            $start_date = $year . '-' . $month . '-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 5:
                            if ($month == 1) {
                                $last_month = '12';
                                $year = $year - 1;
                            } else {
                                $last_month = $month - 1;
                            }
                            $start_date = $year . '-' . $last_month . '-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 6:
                            $start_date = '2018-01-01';
                            $end_date = date('Y-m-d');
                            break;
                    }
                    $opening = $this->get_opening_balance($start_date, $end_date);
                    //$previous_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
                    //$opening = $this->get_previous_balance('2018-01-01', $previous_date);
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
                }
                /* update 27 June 2018 | get previous balance */
                $prev_balance = $this->get_previousbalance($start_date, $end_date);
                $data['prev_balance'] = $prev_balance;
                // update 14 June 2018
                // cek report file by start_date, end_date
                $report_type = 1;
                $reporthistory = $this->general_model->get_report_by_date($start_date, $end_date, $report_type);
                $report_id = 0;
                $checked_name = '0';
                $approved_name = '0';
                $report_file = '0';
                if ($reporthistory->num_rows() != 0) {
                    $row = $reporthistory->row();
                    $report_id = $row->report_file_id;
                    $checked_name = $this->general_model->get_user_by_id($row->checked_by);
                    if ($row->approved_by != 0) {
                        $approved_name = $this->general_model->get_user_by_id($row->approved_by);
                    }
                    $report_file = $row->file_name;
                }
                $data['report_id'] = $report_id;
                $data['checked_name'] = $checked_name;
                $data['approved_name'] = $approved_name;
                $data['report_file'] = $report_file;
                $data['report_type'] = $report_type;
                $data['url_module'] = 'summaryreport';

                //$data['opening'] = $opening;
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
                /*
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
                }*/
                if (sizeof($prev_balance)>0){
                    
                    $k = 1;
                    foreach ($prev_balance as $value) {
                        if ($k == sizeof($prev_balance)){
                            $array_tbl[0][$k] = $value;
                        } else {
                            $array_tbl[0][$k] = number_format($value);
                        }
                        
                        $array_total[0][$k] = $value;
                        $k++;
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
                                $array_tbl[2][$k] = '<a target="_blank" href="' . site_url('receiveinbank/go/20191121214305/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[3][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[4][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[6][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[7][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214304/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[8][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214306/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[9][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[10][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $start_date . '/' . $end_date . '/4/') . '">' . number_format($value->total) . '</a>';
                                $array_total[10][$k] = $value->total;
                                $total = $total + $array_total[10][$k];
                            }
                            $k++;
                        }
                        $array_tbl[10][$k] = $total;
                        $array_total[10][$k] = $total;
                    }
                }

                /*                 * ******************** start change : 2018-05-25 ********************* */
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
                                $array_tbl[11][$k] = '<a target="_blank" href="' . site_url('cashreturned/go/20191121214304/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                                $array_tbl[13][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $start_date . '/' . $end_date) . '/1/">' . number_format($value->total) . '</a>';
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

                $data['array_tbl'] = $array_tbl;

                /* form search */
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Summary Report (from ' . $start_date . ' to ' . $end_date . ')';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };';

                $strtotal = '';
                $footer_total .= $strtotal . '}';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Summary Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/summary_report.php';

                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    public function get_branch_list() {
        $sql = 'SELECT DISTINCT b.branch_name, b.branch_id FROM branch AS b 
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

    public function get_expense($start_date = '', $end_date = '') {
        $sql = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_from_revenue_bank($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(rb.amount)AS total, b.branch_name FROM receive_bank AS rb
        INNER JOIN branch AS b ON b.branch_id=rb.branch_id
        WHERE rb.receive_bank_date BETWEEN "' . $start_date . '" AND "' . $end_date . ' "
        GROUP BY rb.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_outstanding($start_date = '', $end_date = '', $type = 0) {
        $sql = 'SELECT SUM(os.amount) AS total, b.branch_name  FROM outstanding AS os 
        INNER JOIN branch AS b ON os.branch_id=b.branch_id 
        WHERE os.outstanding_status IN (0,1)  
        AND os.outstanding_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND os.outstanding_type=' . $type . ' ';
        $sql .= 'GROUP BY os.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_received($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        WHERE cr.cash_receive_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_borrow_given($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        INNER JOIN branch AS b ON a.branch_id=b.branch_id 
        WHERE cr.cash_receive_status < 2 
        AND cr.cash_receive_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_borrow_returned($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name FROM cash_return AS ct
        INNER JOIN branch AS b ON b.branch_id=ct.branch_id
        WHERE ct.cash_return_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY ct.branch_id ORDER BY b.branch_id';
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

    public function get_previous_balance($start_date = '', $end_date = '') {
        $sql = 'SELECT L.account_id,  SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit FROM ledger AS L 
        INNER JOIN transactions AS t ON L.trans_id=t.trans_id
        WHERE  L.account_id IN (7,8,9,10,11,12,13) AND trans_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
        GROUP BY L.account_id ORDER BY L.account_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_inward($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name, ct.account_to, a.account_id, b.branch_id, ct.cash_return_date 
        FROM cash_return AS ct 
        INNER JOIN account AS a ON a.account_id=ct.account_to 
        INNER JOIN branch AS b ON b.branch_id=a.branch_id 
        WHERE ct.cash_return_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" GROUP BY b.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_adjustment($start_date = '', $end_date = '') {
        $sql = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND expense_type=1 ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function action_checked($start_date = '', $end_date = '') {
        $this->general_model->action_checked($start_date, $end_date, 1, '/summaryreport/go/20191341214301/0/');
    }

    public function action_approved($report_file_id = 0, $start_date = '', $end_date = '') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/summaryreport/go/20191341214301/0/');
    }

    public function do_upload() {
        $this->general_model->do_upload('/summaryreport/go/20191341214301/0/');
    }

    public function get_previousbalance($startdate = '', $enddate = '') {
        $branch = $this->get_branch_list();
        $data['branch'] = $branch;

        $array_tbl = array(array());
        $array_tbl[0][0] = '<strong>Opening Bank Balance</strong>';
        $array_tbl[1][0] = '<strong>Receipt in Bank</strong>';
        $array_tbl[2][0] = ' # From Revenue Bank';
        $array_tbl[3][0] = ' # Borrow Received';
        $array_tbl[4][0] = ' # Borrow Returned Inward';
        $array_tbl[5][0] = '<strong>Payment from Bank</strong>';
        $array_tbl[6][0] = ' # Expenses';
        $array_tbl[7][0] = ' # O/S Cash Request';
        $array_tbl[8][0] = ' # O/S Third Party';
        $array_tbl[9][0] = ' # O/S Outlet (Borrow Given)';
        // *start | update 2018-05-25
        $array_tbl[10][0] = ' # O/S Borrow Given (CR)';
        // *start | update 2018-05-25
        $array_tbl[11][0] = ' # Borrow Returned Outward';
        $array_tbl[12][0] = '<strong>Closing Balance Before Adjustment</strong>';
        $array_tbl[13][0] = ' # Adjustment Nota Receive';
        $array_tbl[14][0] = '<strong>Closing Bank Balance</strong>';
        /* New array 2018-03-21 */
        $array_total = array(array());

        //$start_date = date('Y-m-d', strtotime("-1 days"));
        $start_date = substr($startdate, 0, 8).'01';
        $end_date = date('Y-m-d', strtotime('-1 day', strtotime($enddate)));

        $opening = $this->get_opening_balance($start_date, $end_date);
        //$previous_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
        //$opening = $this->get_previous_balance('2018-01-01', $previous_date);
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

        // update 14 June 2018
        // cek report file by start_date, end_date
        $report_type = 1;
        $reporthistory = $this->general_model->get_report_by_date($start_date, $end_date, $report_type);
        $report_id = 0;
        $checked_name = '0';
        $approved_name = '0';
        $report_file = '0';
        if ($reporthistory->num_rows() != 0) {
            $row = $reporthistory->row();
            $report_id = $row->report_file_id;
            $checked_name = $this->general_model->get_user_by_id($row->checked_by);
            if ($row->approved_by != 0) {
                $approved_name = $this->general_model->get_user_by_id($row->approved_by);
            }
            $report_file = $row->file_name;
        }
        $data['report_id'] = $report_id;
        $data['checked_name'] = $checked_name;
        $data['approved_name'] = $approved_name;
        $data['report_file'] = $report_file;
        $data['report_type'] = $report_type;
        $data['url_module'] = 'summaryreport';

        $data['opening'] = $opening;
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
                        $array_tbl[2][$k] = '<a target="_blank" href="' . site_url('receiveinbank/go/20191121214305/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[3][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[4][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $start_date . '/' . $end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[6][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[7][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214304/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[8][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214306/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[9][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[10][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $start_date . '/' . $end_date . '/4/') . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[11][$k] = '<a target="_blank" href="' . site_url('cashreturned/go/20191121214304/0/' . $start_date . '/' . $end_date) . '">' . number_format($value->total) . '</a>';
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
                        $array_tbl[13][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $start_date . '/' . $end_date) . '/1/">' . number_format($value->total) . '</a>';
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
        $previous = array();
        foreach ($branch as $b) {
            $array_tbl[14][$k] = 0;
            $array_total[14][$k] = 0;
            $previous[$k-1] = 0;
            $k++;
        }
        $array_tbl[14][$k] = 0;
        $array_total[14][$k] = 0;
        $previous[$k-1] = 0;
        $total = 0;
        $k = 1;
        foreach ($branch as $b) {
            $array_total[14][$k] = $array_total[12][$k] + $array_total[13][$k];
            $array_tbl[14][$k] = number_format($array_total[14][$k]);
            $total = $total + $array_total[14][$k];
            $previous[$k-1] = $array_total[14][$k];
            $k++;
        }
        $array_tbl[14][$k] = $total;
        $array_total[14][$k] = $total;
        $previous[$k-1] = $total;
        //$array_tbl[13][$k] = $array_total[11][$k] + $array_total[12][$k];
        /* ============================================================ */

        //$data['array_tbl'] = $array_tbl;

        return $previous;
    }

}
