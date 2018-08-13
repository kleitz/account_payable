<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Creditinvoice
 *
 * @author Hendra McHen
 */
class Creditinvoice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
        $this->load->model('credit_invoice_model');
    }

    public $category_index = 1;
    public $category = '';
    public $module = '';


    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_transaction) {
            if (($module == $this->asik_model->trans_01) && ($string == $category . $module)) {
                $this->category_index = 1;
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function go($string = '', $button=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                /* end privilege */
                $this->load->helper('form');
                $this->load->helper('file');
                $data['pagecode'] = $string;
                /* field search */
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                $field_search = $this->input->post('field_search');
                $date_search = $this->input->post('date_search');
                $keyword = $this->input->post('keyword');
                
                if ($button == 0){
                    $data['list'] = $this->credit_invoice_model->get_credit_invoice_list($start_date, $end_date, $field_search, $keyword, $date_search);
                    $arr_pp = $this->credit_invoice_model->get_pp_detail($start_date, $end_date, $field_search, $keyword, $date_search);
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
                    $data['list'] = $this->credit_invoice_model->get_credit_invoice_list($start_date, $end_date, $field_search, $keyword, $date_search);
                    $arr_pp = $this->credit_invoice_model->get_pp_detail($start_date, $end_date, $field_search, $keyword, $date_search);
                }
                
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                
                $data['field_search'] = $field_search;
                $data['date_search'] = $date_search;
                
                $data['arr_pp'] = $arr_pp;
                
                $type_date = array(
                    'PO Date', 'Invoice Date', 'Receive Date'
                );
                $data['type_date'] = $type_date;
                
                $fields = array(
                    "credit_invoice_number"=>"ID", 
                    "supplier_name"=>"Supplier", 
                    "po_number"=>"PO. No", 
                    "invoice"=>"Invoice No.", 
                    "branch_name"=>"Outlet"
                    );
                $data['field_opt'] = $fields;
                
                
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Credit Invoice', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'cinvoice/cinvoice_list.php';
                /* form */
                
                $credit_invoice_number = $this->general_model->get_generate_number('CI', 'credit_invoice', 'credit_invoice_id');
                $data['credit_invoice_id'] = $this->general_model->draw_hidden_field('credit_invoice_id', '');
                $data['credit_invoice_number'] = $this->general_model->draw_hidden_field('credit_invoice_number', $credit_invoice_number);
                $data['credit_invoice_number_disabled'] = $this->general_model->draw_text_disabled('Credit Invoice ID', 'credit_invoice_number_disabled', $credit_invoice_number);
                $data['po_number'] = $this->general_model->draw_text_field('PO No.', 1, 'po_number', '', '', '');
                $data['po_date'] = $this->general_model->draw_datepicker('PO Date', 1, 'po_date', '');
                $data['invoice'] = $this->general_model->draw_text_field('Supplier Invoice No.', 1, 'invoice', '', '', '');
                $data['invoice_date'] = $this->general_model->draw_datepicker('Supplier Invoice Date', 1, 'invoice_date', '');
                $data['receive_no'] = $this->general_model->draw_text_field('Receive No.', 0, 'receive_no', '', '', '');
                $data['receive_date'] = $this->general_model->draw_datepicker('Receive Date', 0, 'receive_date', '');
                
                $this->load->model('supplier_model');
                $supplier = $this->supplier_model->get_supplier_list();
                $supplier_opt = array();
                if ($supplier->num_rows()!=0){
                    foreach ($supplier->result() as $value) {
                        $supplier_opt[$value->supplier_id] = $value->supplier_name;
                    }
                }
                
                $data['supplier_id'] = $this->general_model->draw_select('Supplier', 0, 'supplier_id', 1, $supplier_opt, '', 1);
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, '', 0);
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', '');
                $data['show_modal'] = 'cinvoice/cinvoice_modal.php';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Credit Invoice';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(8)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Total over this page
                    pageTotal = api
                            .column(8, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(8).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                }';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                /* get footer info */
                $datafooter = $this->credit_invoice_model->get_last_record();
                $username = '';
                $lastupdate = '';
                $transid = 0;
                if ($datafooter->num_rows()!=0){
                    $row = $datafooter->row();
                    $username = $row->username;
                    $lastupdate = $row->last_update;
                    $transid = $row->credit_invoice_number;
                }
                $data['username'] = $username;
                $data['last_update'] = $lastupdate;
                $data['transid'] = $transid;
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function goform($string = '', $credit_invoice_id = 0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                /* end privilege */
                /* get data po by id */
                $credit_invoice_number = $this->general_model->get_generate_number('CI', 'credit_invoice', 'credit_invoice_id');
                $po_number = '';
                $po_date = '';
                $invoice = '';
                $invoice_date = '';
                $receive_no = '';
                $receive_date = '';
                $supplier_id = 0;
                $branch_id = 0;
                $description = '';
                $amount = 0;
                $podata = $this->credit_invoice_model->get_credit_invoice_by_id($credit_invoice_id);
                if ($podata->num_rows()!=0){
                    $row = $podata->row();
                    $credit_invoice_number = $row->credit_invoice_number;
                    $po_number = $row->po_number;
                    $po_date = $row->po_date;
                    $invoice = $row->invoice;
                    $invoice_date = $row->invoice_date;
                    $receive_no = $row->receive_no;
                    $receive_date = $row->receive_date;
                    $supplier_id = $row->supplier_id;
                    $branch_id = $row->branch_id;
                    $description = $row->description;
                    $amount = $row->amount;
                }
                $this->load->helper('form');
                /*======= form field =======*/
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Credit Invoice';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(3)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Total over this page
                    pageTotal = api
                            .column(3, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(3).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                }';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['credit_invoice_id'] = $this->general_model->draw_hidden_field('credit_invoice_id', $credit_invoice_id);
                $data['credit_invoice_number'] = $this->general_model->draw_hidden_field('credit_invoice_number', $credit_invoice_number);
                $data['credit_invoice_number_disabled'] = $this->general_model->draw_text_disabled('Credit Invoice ID', 'credit_invoice_number_disabled', $credit_invoice_number);
                $data['po_number'] = $po_number;
                $data['po_date'] = $po_date;
                $data['invoice'] = $invoice;
                $data['invoice_date'] = $invoice_date;
                $data['receive_no'] = $receive_no;
                $data['receive_date'] = $receive_date;
                
                $this->load->model('supplier_model');
                $supplier = $this->supplier_model->get_supplier_list();
                $supplier_opt = array();
                if ($supplier->num_rows()!=0){
                    foreach ($supplier->result() as $value) {
                        $supplier_opt[$value->supplier_id] = $value->supplier_name;
                    }
                }
                
                $data['supplier_id'] = $this->general_model->draw_select('Supplier', 0, 'supplier_id', 1, $supplier_opt, $supplier_id, 1);
                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }
                $data['branch_id'] = $this->general_model->draw_select('Outlet (Branch)', 0, 'branch_id', 0, $branch_opt, $branch_id, 0);
                $data['description'] = $this->general_model->draw_textarea('Description', 1, 'description', $description);
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 1, 'amount', $amount);
                /*====== end form ======*/
                $data['show_modal'] = 'cinvoice/cinvoice_modal.php';
                $data['pagecode'] = $string;
                $data['list'] = $this->credit_invoice_model->get_credit_invoice_by_datenow();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Credit Invoice', 'Add', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'cinvoice/cinvoice_form.php';
                $this->load->view('template', $data);
            }
        }
    }
    
    public function godetail($string = '', $credit_invoice_id = 0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['action_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['action_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['action_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['action_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                /* end privilege */
                /* get data po by id */
                
                $credit_invoice = $this->credit_invoice_model->get_credit_invoice_by_id($credit_invoice_id);
                $data['credit_invoice'] = $credit_invoice;
                $creditinvoice_file = $this->credit_invoice_model->get_file_by_credit_id($credit_invoice_id);
                $data['creditinvoice_file'] = $creditinvoice_file;
                $this->load->helper('form');
                $data['show_modal'] = 'cinvoice/cinvoice_file_modal.php';
                $data['pagecode'] = $string;
                $data['back_link'] = 'creditinvoice/go/'.$string;
                $data['list'] = $this->credit_invoice_model->get_credit_invoice_by_datenow();
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Credit Invoice', 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'cinvoice/cinvoice_detail.php';
                $this->load->view('template', $data);
            }
        }
    }

    public function ajax_edit($id) {
        $data = $this->credit_invoice_model->get_credit_invoice_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function purchaseorder_action() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('po_date', 'PO date', 'required');
        $this->form_validation->set_rules('po_number', 'PO number', 'required');
        $this->form_validation->set_rules('invoice', 'Invoice number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $credit_invoice_id = $this->input->post('credit_invoice_id');
            if ($credit_invoice_id != 0){
                $this->credit_invoice_model->update_credit_invoice($credit_invoice_id);
            } else {
                $this->credit_invoice_model->insert_credit_invoice();
            }
            /*== redirect == */
            $back = '/creditinvoice/goform/' . $this->asik_model->category_transaction;
            $back .= $this->asik_model->trans_01 . '/';
            redirect($back);
            //echo json_encode(array("status" => TRUE));
        }
    }
    
    public function purchaseorder_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('po_date', 'PO date', 'required');
        $this->form_validation->set_rules('po_number', 'PO number', 'required');
        $this->form_validation->set_rules('invoice', 'Invoice number', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->credit_invoice_model->update_credit_invoice($this->input->post('credit_invoice_id'));
            echo json_encode(array("status" => TRUE));
        }
    }

    public function purchaseorder_delete($id) {
        $this->credit_invoice_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function do_upload__() {
        $this->load->library('upload');
        $id = $this->input->post('credit_invoice_id');
        
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'file_po-'.$id;
        $config['upload_path'] = './assets/credit_invoice/po/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|xlsx|xls';
        $config['max_size'] = 3540;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;
        
        $this->upload->initialize($config);

        if ($this->upload->do_upload('userfile1')) {
            $info = $this->upload->data();
            $this->credit_invoice_model->update_file_name($id, 'file_po', $info['orig_name']);
        }
        /*******************************************/
        $filename = 'file_invoice-'.$id;
        $config['upload_path'] = './assets/credit_invoice/invoice/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|xlsx|xls';
        $config['max_size'] = 3540;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;
        
        $this->upload->initialize($config);

        if ($this->upload->do_upload('userfile2')) {
            $info = $this->upload->data();
            $this->credit_invoice_model->update_file_name($id, 'file_invoice', $info['orig_name']);
        }
        /*******************************************/
        $filename = 'file_upreceive-'.$id;
        $config['upload_path'] = './assets/credit_invoice/upreceive/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|xlsx|xls';
        $config['max_size'] = 3540;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;
        
        $this->upload->initialize($config);

        if ($this->upload->do_upload('userfile3')) {
            $info = $this->upload->data();
            $this->credit_invoice_model->update_file_name($id, 'file_upreceive', $info['orig_name']);
        }
        
        /*== redirect == */
        $back = '/creditinvoice/go/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_01 . '/';
        redirect($back);
    }
    
    public function do_upload() {
        $id = $this->input->post('credit_invoice_id');
        $number = $this->general_model->get_generate_number('F', 'credit_invoice', 'credit_invoice_id');
        $this->load->helper('form');
        $this->load->helper('file');
        $filename = 'creditinvoce-'.$number;
        
        $config['upload_path'] = './assets/creditinvoice/';
        $config['allowed_types'] = 'jpg|png|jpeg|pdf|doc|docx|xlsx|xls';
        $config['max_size'] = 3540;
        $config['overwrite'] = TRUE;
        $config['file_name'] = $filename;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('userfile')) {
            $info = $this->upload->data();
            $this->credit_invoice_model->update_file_name($id, $info['orig_name'], strtolower($info['file_ext']));
        }

        $back = '/creditinvoice/godetail/' . $this->asik_model->category_transaction;
        $back .= $this->asik_model->trans_01 . '/'.$id;
        redirect($back);
    }
    
}