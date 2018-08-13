<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Trans_model
 *
 * @author mchen
 */
class Trans_model extends CI_Model {
/*
 == transactions ==
trans_type: 
0 = Opening Balance
1 = Dues
2 = Outstanding
3 = Purchasing
4 = Expense
*/
    public $table_transaction = 'transactions';
    public $table_account = 'account';

    public function __construct() {
        parent::__construct();
        $this->load->model('account_model');
        $this->load->model('general_model');
        $this->load->model('ledger_model');
    }

    public function get_transaction_by_type($type=0, $start_date='', $end_date=''){
        $sql  = 'SELECT t.*, a.account_id, a.account_code, a.account_name ';
        $sql .= 'FROM '.$this->table_transaction.' AS t ';
        $sql .= 'INNER JOIN '.$this->table_account.' AS a ';
        $sql .= 'ON t.account_id=a.account_id ';
        $sql .= 'WHERE t.trans_type='.$type.' AND t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY t.trans_date DESC';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_closing_balance($start_date='', $end_date=''){
        $sql  = 'SELECT c.*, b.branch_name ';
        $sql .= 'FROM closing_balance AS c ';
        $sql .= 'INNER JOIN branch AS b ';
        $sql .= 'ON c.branch_id=b.branch_id ';
        $sql .= 'WHERE c.closing_balance_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY c.closing_balance_date DESC';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_transaction_received(){
        $sql  = 'SELECT r.*, t.trans_date, t.trans_code, t.description, ';
        $sql .= 't.account_id AS received_from, t.account_relation AS received_by FROM received AS r ';
        $sql .= 'INNER JOIN transactions AS t ON r.trans_id=t.trans_id ';
        $sql .= 'WHERE r.balance != 0';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_received_by_trans_id($trans_id=0){
        $sql  = 'SELECT r.*, t.trans_date, t.trans_code, t.description, ';
        $sql .= 't.account_id AS received_from, t.account_relation AS received_by FROM received AS r ';
        $sql .= 'INNER JOIN transactions AS t ON r.trans_id=t.trans_id ';
        $sql .= 'WHERE r.trans_id='.$trans_id;
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_transaction($date){
        $sql  = 'SELECT t.*, a.account_id, a.account_code, a.account_name ';
        $sql .= 'FROM ' . $this->table_transaction . ' AS t ';
        $sql .= 'INNER JOIN ' . $this->table_account . ' AS a ';
        $sql .= 'ON t.account_id=a.account_id ';
        $sql .= 'WHERE t.trans_date="'.$date.'" ';
        $sql .= 'ORDER BY t.trans_type DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_transaction_by_id($trans_id = 0){
        $sql  = 'SELECT t.*, a.account_id, a.account_code, a.account_name ';
        $sql .= 'FROM ' . $this->table_transaction . ' AS t ';
        $sql .= 'INNER JOIN ' . $this->table_account . ' AS a ';
        $sql .= 'ON t.account_id=a.account_id ';
        $sql .= 'WHERE t.trans_id='.$trans_id.' ';
        $sql .= 'ORDER BY t.trans_type DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_transaction_by_pp($type=0, $start_date='', $end_date=''){
        $sql  = 'SELECT t.*, p.pp_id, p.pp_status ';
        $sql .= 'FROM '.$this->table_transaction.' AS t ';
        $sql .= 'INNER JOIN payment_process AS p ';
        $sql .= 'ON t.pp_id=p.pp_id ';
        $sql .= 'WHERE t.trans_type='.$type.' AND p.pp_status='.$type.' AND t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
        $sql .= 'ORDER BY t.trans_date';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function get_trans_date(){
        $sql = 'SELECT DISTINCT trans_date FROM transactions ORDER BY trans_date DESC';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_last_date(){
        $sql = 'SELECT trans_date FROM transactions ORDER BY trans_date DESC';
        $row = $this->db->query($sql)->row();
        $date = '0';
        if (isset($row)){
            $date = $row->trans_date;
        }
        return $date;
    }
    
    public function get_period_date($period=''){
        $date = 0;
        if ($period != ''){
            $sql  = 'SELECT trans_date FROM transactions ';
            $sql .= 'WHERE trans_date LIKE "'.$period.'%" ';
            $sql .= 'ORDER BY trans_date DESC';
            $query = $this->db->query($sql);
            $date = $period.'-01';
            if ($query->num_rows()!=0){
                $row = $query->row();
                $date = $row->trans_date;
            }
        }
        return $date;
    }
    
    public function get_payment_voucher(){
        $sql  = 'SELECT * FROM payment_voucher';
        $query = $this->db->query($sql);
        return $query;
    } 
    
    public function delete_trans_by_id($id){
        $this->db->where('trans_id', $id);
        $this->db->delete('transactions');
    }
    
    public function insert_opening_balance() {
        $trans_date = $this->input->post('trans_date');
        $trans_code = $this->input->post('trans_code');
        $account_id = $this->input->post('account_id');
        $account_relation = $this->input->post('account_relation');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));

        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => 0,
            'account_id' => $account_id,
            'account_relation' => $account_relation,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );
        
        $account = $this->get_account_by_id($account_relation);
        $branch_id = 0;
        if ($account->num_rows()!=0){
            $row = $account->row();
            $branch_id = $row->branch_id;
        }
        
        $dataop = array(
            'opening_balance_number' => $trans_code,
            'opening_balance_date' => $trans_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id
        );

        $this->db->insert($this->table_transaction, $data);
        $trans_id = $this->db->insert_id(); /*get trans id*/
        $this->db->insert('opening_balance', $dataop);

        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->ledger_model->insert_ledger($trans_id, $account_relation, $amount, 0);
        $this->ledger_model->insert_ledger($trans_id, $account_id, 0, $amount);
    }
    
    public function update_opening_balance($trans_id) {
        $trans_date = $this->input->post('trans_date');
        $trans_code = $this->input->post('trans_code');
        $account_id = $this->input->post('account_id');
        $account_relation = $this->input->post('account_relation');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));

        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => 0,
            'account_id' => $account_id,
            'account_relation' => $account_relation,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );
        
        $account = $this->get_account_by_id($account_relation);
        $branch_id = 0;
        if ($account->num_rows()!=0){
            $row = $account->row();
            $branch_id = $row->branch_id;
        }
        
        $dataop = array(
            'opening_balance_number' => $trans_code,
            'opening_balance_date' => $trans_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id
        );
        
        $this->db->where('trans_id', $trans_id);
        $this->db->update($this->table_transaction, $data);
        $this->db->where('opening_balance_number', $trans_code);
        $this->db->update('opening_balance', $dataop);
 
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->ledger_model->delete_ledger_by_trans_id($trans_id);
        $this->ledger_model->insert_ledger($trans_id, $account_relation, $amount, 0);
        $this->ledger_model->insert_ledger($trans_id, $account_id, 0, $amount);
    }
    
    public function insert_closing_balance() {
        $trans_date = $this->input->post('trans_date');
        $trans_code = $this->input->post('trans_code');
        $account_id = $this->input->post('account_id');
        $account_relation = $this->input->post('account_relation');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));

        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => 0,
            'account_id' => $account_id,
            'account_relation' => $account_relation,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );
        
        $account = $this->get_account_by_id($account_relation);
        $branch_id = 0;
        if ($account->num_rows()!=0){
            $row = $account->row();
            $branch_id = $row->branch_id;
        }
        
        $datacl = array(
            'closing_balance_number' => $trans_code,
            'closing_balance_date' => $trans_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id
        );

        $this->db->insert($this->table_transaction, $data);
        $trans_id = $this->db->insert_id(); /*get trans id*/
        $this->db->insert('closing_balance', $datacl);

        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->ledger_model->insert_ledger($trans_id, $account_relation, $amount, 0);
        $this->ledger_model->insert_ledger($trans_id, $account_id, 0, $amount);
    }
    
    public function update_closing_balance($trans_id) {
        $trans_date = $this->input->post('trans_date');
        $trans_code = $this->input->post('trans_code');
        $account_id = $this->input->post('account_id');
        $account_relation = $this->input->post('account_relation');
        $description = $this->input->post('description');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));

        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => 0,
            'account_id' => $account_id,
            'account_relation' => $account_relation,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );
        
        $account = $this->get_account_by_id($account_relation);
        $branch_id = 0;
        if ($account->num_rows()!=0){
            $row = $account->row();
            $branch_id = $row->branch_id;
        }
        
        $datacl = array(
            'opening_balance_number' => $trans_code,
            'opening_balance_date' => $trans_date,
            'description' => $description,
            'amount' => $amount,
            'branch_id' => $branch_id
        );
        
        $this->db->where('trans_id', $trans_id);
        $this->db->update($this->table_transaction, $data);
        $this->db->where('closing_balance_number', $trans_code);
        $this->db->update('closing_balance', $datacl);
 
        /* insert to tb_ledger (buku besar) posting jurnal */
        $this->ledger_model->delete_ledger_by_trans_id($trans_id);
        $this->ledger_model->insert_ledger($trans_id, $account_relation, $amount, 0);
        $this->ledger_model->insert_ledger($trans_id, $account_id, 0, $amount);
    }
    
    public function insert_transaction_by_param($trans_date='', $trans_type=0, $account_from=0, $account_to=0, $description='', $amount=0, $pv_number='') {
        $trans_code = $this->general_model->get_generate_number('TR', 'transactions', 'trans_id');
        $data = array(
            'trans_date' => $trans_date,
            'trans_code' => $trans_code,
            'trans_type' => $trans_type,
            'account_id' => $account_from,
            'account_relation' => $account_to,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id'),
            'pv_number' => $pv_number
        );

        $this->db->insert('transactions', $data);
        $trans_id = $this->db->insert_id(); /*get trans id*/
        return $trans_id;
    }

    /* insert to transaction and ledger */
    public function insert_transaction_from_bank_other($trans_date='', $description='', $amount=0, $branch_from=0, $branch_to=0, $pv_number='') {
        $ex_QOI = $this->get_account_id($branch_to, 'Expenses in Bank', 4);
        $bank_QOI = $this->get_account_id($branch_to, 'Petty', 0);
        $loan_QOI = $this->get_account_id($branch_to, 'Loan', 1);
        
        $bank_QTR = $this->get_account_id($branch_from, 'Petty', 0);
        $receivable_QTR = $this->get_account_id($branch_from, 'Receivable', 0);
        
        $trans_id = $this->insert_transaction_by_param($trans_date, 1, $bank_QOI, $ex_QOI, $description, $amount, $pv_number);
        
        /*
        Account		Debit           Credit
        ======================================
        Expense QOI	1	
        Bank QOI			1
        Loan QOI			1
        Bank QOI	1

        Receivable QTR	1
        Bank QTR			1

         */
        $this->load->model('ledger_model');
        // insert to EXPENSE QOI (debit)
        $this->ledger_model->insert_ledger($trans_id, $ex_QOI, $amount, 0, 'EXP');
        // insert to BANK QOI (credit)
        $this->ledger_model->insert_ledger($trans_id, $bank_QOI, 0, $amount, 'EXP');
        // insert to LOAN QOI
        $this->ledger_model->insert_ledger($trans_id, $loan_QOI, 0, $amount, 'O/S');
        // insert to BANK QOI (debit)
        $this->ledger_model->insert_ledger($trans_id, $bank_QOI, $amount, 0, 'RCP');
        /***************************************/
        // insert to RECEIVABLE QTR
        $this->ledger_model->insert_ledger($trans_id, $receivable_QTR, $amount, 0, 'O/S');
        // insert to BANK QTR
        $this->ledger_model->insert_ledger($trans_id, $bank_QTR, 0, $amount, 'O/S');  

        // cek pp dan Proses Ledger Account Payable
        /*
        $ppdata = $this->get_payment_process_by_id($pp_id);
        if ($ppdata->num_rows()!=0){
            $row = $ppdata->row();
            $supplier_id = $row->supplier_id;
            $cash_request_id = $row->cash_request_id;
            if ($supplier_id != 0){
                $account_from = $this->get_account_id($branch_to, 'Account', 1);
                $account_to = $this->get_account_id($branch_to, 'Purchase', 4);
                // insert to Liability (debit)
                $this->ledger_model->insert_ledger($trans_id, $account_from, $amount, 0);
                // insert to EXPENSES (credit)
                $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $amount);
            }
        }*/
        return $trans_id;
    }
    
    public function get_account_id($branch_id=0, $keyword='', $type=0) {
        $account_id = 0;
        $this->load->model('account_model');
        $account = $this->account_model->get_account_by_branch_type($branch_id, $type, $keyword);
        if ($account->num_rows()!=0){
            $row = $account->row();
            $account_id = $row->account_id;
        }
        return $account_id;
    }
    
    public function get_payment_process_by_id($id=0) {
        $sql  = 'SELECT * FROM payment_process ';
        $sql .= 'WHERE pp_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    /* insert to expense transaction and ledger $pv_date, $description, $total_all, $branch_id */
    public function insert_expense_transaction($trans_date='', $description='', $amount=0, $branch_id=0, $keyword='', $pv_number='') {
        $this->load->model('ledger_model');
        $account_from = $this->get_account_id($branch_id, 'Petty '.$keyword);
        $account_to = $this->get_account_id($branch_id, 'Expenses in '.$keyword, 4);
        $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $amount, $pv_number);
        // insert to ASSET (credit)
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount, 'EXP');
        // insert to EXPENSES
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0, 'EXP');
        // cek pp dan Proses Ledger Account Payable
        /*
        $ppdata = $this->get_payment_process_by_id($pp_id);
        if ($ppdata->num_rows()!=0){
            $row = $ppdata->row();
            $supplier_id = $row->supplier_id;
            if ($supplier_id != 0){
                $account_from = $this->get_account_id($branch_id, 'Account', 1);
                $account_to = $this->get_account_id($branch_id, 'Purchase', 4);
                // insert to Liability (debit)
                $this->ledger_model->insert_ledger($trans_id, $account_from, $amount, 0);
                // insert to EXPENSES (credit)
                $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $amount);
            }
        }*/
        return $trans_id;
    }
    
    /* insert to receivable transaction and ledger $pv_date, $description, $total_all, $branch_id */
    public function insert_receivable_transaction($trans_date='', $description='', $amount=0, $branch_id=0, $keyword='', $pv_number='') {
        $account_from = $this->get_account_id($branch_id, 'Petty '.$keyword);
        $account_to = $this->get_account_id($branch_id, 'Receivable');
        $trans_id = $this->insert_transaction_by_param($trans_date, 2, $account_from, $account_to, $description, $amount, $pv_number);
        $this->load->model('ledger_model');
        // insert to ASSET (credit)
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount, 'O/S');
        // insert to Receivable
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0, 'O/S');
        return $trans_id;
    }
    
    public function insert_transaction_cash_return($trans_date='', $account_from=0, $account_to=0, $amount=0, $number='', $description='') {
        $trans_id = $this->insert_transaction_by_param($trans_date, 1, $account_from, $account_to, $description, $amount, $number);
        $this->load->model('ledger_model');
        $branch_from = $this->get_branch_from_account($account_from);
        $branch_to = $this->get_branch_from_account($account_to);
        $account_liability_id = $this->get_account_id($branch_from, 'Loan', 1);
        $account_receivable_id = $this->get_account_id($branch_to, 'Receivable', 0);
        /* 
        Account		Debit	Credit
        =============================
        Receiveable QTR		1
        Petty Bank QTR	1
        Petty Bank QOI		1
        Loan QOI	1
        */

        // insert to ASSET (debit) DUES
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0, 'RCP');
        // insert to ASSET (credit) OUTSTANDING
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount, 'O/S');
        // insert to LIABILITY
        $this->ledger_model->insert_ledger($trans_id, $account_liability_id, $amount, 0, 'O/S');
        // insert to ASSET (debit) Receivable
        $this->ledger_model->insert_ledger($trans_id, $account_receivable_id, 0, $amount, 'O/S');       
        return $trans_id;
    }
    
    public function get_branch_from_account($account_id=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_id='.$account_id;
        $query = $this->db->query($sql);
        $branch_id = 0;
        if ($query->num_rows()!=0){
            $row = $query->row();
            $branch_id = $row->branch_id;
        }
        return $branch_id;
    }
    
    public function insert_expense_transaction_cr($trans_date= '', $description='', $amount=0, $branch_id=0, $cash_request_id=0, $pv_number='') {
        $this->load->model('ledger_model');
        $trans_id = 0;
        $cashrequest = $this->get_cashrequest_by_id($cash_request_id);
        if ($cashrequest->num_rows()!=0){
            $row = $cashrequest->row();
            $branch_cashrequest = $row->branch_id;
            $amount_cashrequest = $row->amount;
            $pv_number = $row->pv_number;
            if ($branch_id == $branch_cashrequest){
                /*
                QTR == QTR
                Expenses in Bank QTR (debit)
                Receivable QTR (credit)

                amount_cashrequest - amount_pp = sisa

                if (sisa)
                petty bank qtr = sisa
                 * */
                $account_from = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
                $account_to = $this->get_account_id($branch_id, 'Receivable', 0);
                $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $amount, $pv_number);
                // insert to EXPENSES
                $this->ledger_model->insert_ledger($trans_id, $account_from, $amount, 0, 'EXP');
                // insert to RECEIVABLE
                $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $amount_cashrequest, 'O/S');
                $sisa = $amount_cashrequest - $amount;
                if ($sisa != 0){
                    $account_sisa = $this->get_account_id($branch_id, 'Petty Bank', 0);
                    $this->ledger_model->insert_ledger($trans_id, $account_sisa, $sisa, 0, 'RCP');
                    // update to cash request
                    $data = array(
                        'cash_return' => $sisa,
                        'cash_request_status' => 4
                    );

                    $this->db->where('cash_request_id', $cash_request_id);
                    $this->db->update('cash_request', $data);
                    
                    $dataos = array(
                        'outstanding_status' => 1
                    );
                    $this->db->where('pv_number', $pv_number);
                    $this->db->update('outstanding', $dataos);
                } else {
                    // update to cash request
                    $data = array(
                        'cash_request_status' => 4
                    );

                    $this->db->where('cash_request_id', $cash_request_id);
                    $this->db->update('cash_request', $data);
                    
                    $dataos = array(
                        'outstanding_status' => 1
                    );
                    $this->db->where('pv_number', $pv_number);
                    $this->db->update('outstanding', $dataos);
                }
            } else {
                /*
                QTR != QTR (QOI)
                Expenses in Bank QOI (debit)
                Loan QOI (credit)
                insert dues (cash receive)
                amount_cashrequest - amount_pp = sisa
                Petty Bank QTR (debit)
                Receivable (credit)
                         * */
                $account_from = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
                $account_to = $this->get_account_id($branch_id, 'Loan', 1);
                $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $amount, $pv_number);
                // insert to EXPENSES
                $this->ledger_model->insert_ledger($trans_id, $account_from, $amount, 0, 'EXP');
                // insert to (Liability) LOAN
                $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $amount, 'O/S');
                $sisa = $amount_cashrequest - $amount;
                // insert to cash receive
                $account_asal = $this->get_account_id($branch_cashrequest, 'Petty Bank', 0);
                $account_tujuan = $this->get_account_id($branch_id, 'Petty Bank', 0);
                $this->insert_cashreceived_from_tr($account_asal, $account_tujuan, $amount, $branch_id);
                if ($sisa != 0){
                    $petty_sisa = $this->get_account_id($branch_cashrequest, 'Petty Bank', 0);
                    $receivable_sisa = $this->get_account_id($branch_cashrequest, 'Receivable', 0);
                    $this->ledger_model->insert_ledger($trans_id, $petty_sisa, $sisa, 0, 'RCP');
                    $this->ledger_model->insert_ledger($trans_id, $receivable_sisa, 0, $sisa, 'O/S');
                    // insert to cash request
                    $data = array(
                        'cash_return' => $sisa,
                        'cash_request_status' => 4
                    );

                    $this->db->where('cash_request_id', $cash_request_id);
                    $this->db->update('cash_request', $data);
                    
                    $dataos = array(
                        'outstanding_status' => 1
                    );
                    $this->db->where('pv_number', $pv_number);
                    $this->db->update('outstanding', $dataos);
                } else {
                    // insert to cash request
                    $data = array(
                        'cash_request_status' => 4
                    );

                    $this->db->where('cash_request_id', $cash_request_id);
                    $this->db->update('cash_request', $data);
                    
                    $dataos = array(
                        'outstanding_status' => 1
                    );
                    $this->db->where('pv_number', $pv_number);
                    $this->db->update('outstanding', $dataos);
                }
            }
        }
        return $trans_id;
    }
    
    public function get_cashrequest_by_id($id=0) {
        $sql  = 'SELECT * FROM cash_request ';
        $sql .= 'WHERE cash_request_id = '.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert_cashreceived_from_tr($account_from=0, $account_to=0, $amount=0, $branch_id=0, $pv_number='', $trans_id=0) {
        $cash_receive_date = date('Y-m-d');
        $cash_receive_number = $this->general_model->get_generate_number('RC', 'cash_receive', 'cash_receive_id');
        $remark = '-';        
        
        $data = array(
            'cash_receive_date' => $cash_receive_date,
            'cash_receive_number' => $cash_receive_number,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'remark' => $remark,
            'branch_id' => $branch_id,
            'paid_off' => 0,
            'cash_receive_status' => 0,
            'trans_id' => $trans_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_receive', $data);
        $branch_id = $this->get_branch_from_account($account_from);
        $outstanding_number = $this->general_model->get_generate_number('OS', 'outstanding', 'outstanding_id');
        $dataos = array(
            'outstanding_number' => $outstanding_number,
            'outstanding_date' => $cash_receive_date,
            'outstanding_description' => $remark,
            'amount' => $amount,
            'outstanding_status' => 0,
            'branch_id' => $branch_id,
            'pv_number' => $pv_number,
            'outstanding_type' => 2,
            'trans_id' => $trans_id
        );
        $this->db->insert('outstanding', $dataos);
    }
    
    public function insert_cashreceived_from_nota_came($trans_date='', $description='', $account_from=0, $account_to=0, $amount=0, $branch_id=0, $pv_number='', $trans_id=0) {
        $cash_receive_number = $this->general_model->get_generate_number('RC', 'cash_receive', 'cash_receive_id');   
        
        $data = array(
            'cash_receive_date' => $trans_date,
            'cash_receive_number' => $cash_receive_number,
            'account_from' => $account_from,
            'account_to' => $account_to,
            'amount' => $amount,
            'remark' => $description,
            'branch_id' => $branch_id,
            'paid_off' => 0,
            'cash_receive_status' => 0,
            'pv_number' => $pv_number,
            'trans_id' => $trans_id,
            'username' => $this->session->userdata('username')
        );
        $this->db->insert('cash_receive', $data);
        $branch_id = $this->get_branch_from_account($account_from);
        $outstanding_number = $this->general_model->get_generate_number('OS', 'outstanding', 'outstanding_id');
        $dataos = array(
            'outstanding_number' => $outstanding_number,
            'outstanding_date' => $trans_date,
            'outstanding_description' => $description,
            'amount' => $amount,
            'outstanding_status' => 0,
            'branch_id' => $branch_id,
            'pv_number' => $pv_number,
            'outstanding_type' => 4,
            'trans_id' => $trans_id
        );
        $this->db->insert('outstanding', $dataos);
    }
    
    public function get_account_by_id($id=0) {
        $sql  = 'SELECT * FROM account ';
        $sql .= 'WHERE account_id='.$id.' ORDER BY account_code';
        $query = $this->db->query($sql);
        return $query;
    }
    /* code is created 20 Maret 2018 */
    public function insert_transaction_from_cashreceive($cash_receive_date='', $cash_receive_number='', $account_from=0, $account_to=0, $remark='', $amount=0, $branch_id=0) {
        // get branch
        $branch_from = $this->get_branch_from_account($account_from);
        $branch_to = $this->get_branch_from_account($account_to);
        
        $loan_to = $this->get_account_id($branch_to, 'Loan', 1);
        
        $receivable_from = $this->get_account_id($branch_from, 'Receivable', 0);
        
        $trans_id = $this->insert_transaction_by_param($cash_receive_date, 1, $account_from, $account_to, $remark, $amount, $cash_receive_number);
        
        /*
        Account		Debit           Credit
        ======================================
        Loan QOI			1
        Bank QOI	1

        Receivable QTR	1
        Bank QTR			1

         */
        $this->load->model('ledger_model');
        // insert to LOAN QOI
        $this->ledger_model->insert_ledger($trans_id, $loan_to, 0, $amount);
        // insert to BANK QOI (debit)
        $this->ledger_model->insert_ledger($trans_id, $account_to, $amount, 0);
        /***************************************/
        // insert to RECEIVABLE QTR
        $this->ledger_model->insert_ledger($trans_id, $receivable_from, $amount, 0);
        // insert to BANK QTR
        $this->ledger_model->insert_ledger($trans_id, $account_from, 0, $amount);  
        return $trans_id;
    }
    
    public function update_transaction_from_cashreceive($trans_id=0, $cash_receive_date='', $account_from=0, $account_to=0, $remark='', $amount=0) {
        // get branch
        $branch_from = $this->get_branch_from_account($account_from);
        $branch_to = $this->get_branch_from_account($account_to);
        
        $loan_to = $this->get_account_id($branch_to, 'Loan', 1);
        
        $receivable_from = $this->get_account_id($branch_from, 'Receivable', 0);
        
        $this->update_transaction_by_param($trans_id, $cash_receive_date, $account_from, $account_to, $remark, $amount);
        
        /*
        Account		Debit           Credit
        ======================================
        Loan QOI			1
        Bank QOI	1

        Receivable QTR	1
        Bank QTR			1

         */
        $this->load->model('ledger_model');
        // insert to LOAN QOI
        $this->ledger_model->update_ledger(array('trans_id'=>$trans_id, 'account_id'=>$loan_to), $loan_to, 0, $amount);
        // insert to BANK QOI (debit)
        $this->ledger_model->update_ledger(array('trans_id'=>$trans_id, 'account_id'=>$account_to), $account_to, $amount, 0);
        /***************************************/
        // insert to RECEIVABLE QTR
        $this->ledger_model->update_ledger(array('trans_id'=>$trans_id, 'account_id'=>$receivable_from), $receivable_from, $amount, 0);
        // insert to BANK QTR
        $this->ledger_model->update_ledger(array('trans_id'=>$trans_id, 'account_id'=>$account_from), $account_from, 0, $amount);  
    }

    public function update_transaction_by_param($trans_id=0, $trans_date='', $account_from=0, $account_to=0, $description='', $amount=0) {
        $data = array(
            'trans_date' => $trans_date,
            'account_id' => $account_from,
            'account_relation' => $account_to,
            'description' => $description,
            'amount' => $amount,
            'user_id' => $this->session->userdata('user_id')
        );
        
        $this->db->where('trans_id', $trans_id);
        $this->db->update('transactions', $data);
    }
    
    /* update pay pp nota came bank | 21 Mei 2018 */
    public function insert_came_nota($trans_date= '', $description='', $amount=0, $branch_id=0, $cash_request_id=0, $pv_number='', $admin_fee=0) {
        $this->load->model('ledger_model');
        $trans_id = 0;
        $total_all = $amount + $admin_fee;
        $cashrequest = $this->get_cashrequest_by_id($cash_request_id);
        if ($cashrequest->num_rows()!=0){
            $row = $cashrequest->row();
            $branch_cashrequest = $row->branch_id;
            $amount_cashrequest = $row->amount;
            //$pv_number = $row->pv_number;
            if ($branch_id == $branch_cashrequest){
                /*
                QTR == QTR
                Expenses in Bank QTR (debit)
                Receivable QTR (credit)

                 * */
                $account_from = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
                $account_to = $this->get_account_id($branch_id, 'Receivable', 0);
                $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $total_all, $pv_number);
                // insert to EXPENSES
                $this->ledger_model->insert_ledger($trans_id, $account_from, $total_all, 0, 'EXP');
                // insert to RECEIVABLE
                $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $total_all, 'O/S');
                // insert to cash request balance
                $this->insert_to_cr_balance($trans_date, $cash_request_id, $trans_id, $pv_number, 0, $amount);
            } else {
                /*
                QTR != QTR (QOI)
                Expenses in Bank QOI (debit)
                Loan QOI (credit)
                insert dues (cash receive)
                Petty Bank QTR (debit)
                Receivable (credit)
                         * */
                $account_from = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
                $account_to = $this->get_account_id($branch_id, 'Loan', 1);
                $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $total_all, $pv_number);
                // insert to cash receive
                $account_asal = $this->get_account_id($branch_cashrequest, 'Petty Bank', 0);
                $account_tujuan = $this->get_account_id($branch_id, 'Petty Bank', 0);
                $this->insert_cashreceived_from_nota_came($trans_date, $description='', $account_asal, $account_tujuan, $total_all, $branch_id, $pv_number, $trans_id);
                
                $ex_QOI = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
                //$bank_QOI = $this->get_account_id($branch_id, 'Petty', 0);
                $loan_QOI = $this->get_account_id($branch_id, 'Loan', 1);

                //$bank_QTR = $this->get_account_id($branch_cashrequest, 'Petty', 0);
                //$receivable_QTR = $this->get_account_id($branch_cashrequest, 'Receivable', 0);

                //$trans_id = $this->insert_transaction_by_param($trans_date, 1, $bank_QOI, $ex_QOI, $description, $amount, $pv_number);

                /*
                Account		Debit           Credit
                ======================================
                Expense QOI	1	
                //Bank QOI			1
                Loan QOI			1
                //Bank QOI	1

                //Receivable QTR	1
                //Bank QTR			1

                 */
                // insert to EXPENSE QOI (debit)
                $this->ledger_model->insert_ledger($trans_id, $ex_QOI, $total_all, 0, 'EXP');
                // insert to BANK QOI (credit)
                //$this->ledger_model->insert_ledger($trans_id, $bank_QOI, 0, $amount, 'EXP');
                // insert to LOAN QOI
                $this->ledger_model->insert_ledger($trans_id, $loan_QOI, 0, $total_all, 'O/S');
                // insert to BANK QOI (debit)
                //$this->ledger_model->insert_ledger($trans_id, $bank_QOI, $amount, 0, 'RCP');
                /***************************************/
                // insert to RECEIVABLE QTR
               // $this->ledger_model->insert_ledger($trans_id, $receivable_QTR, $amount, 0, 'O/S');
                // insert to BANK QTR
                //$this->ledger_model->insert_ledger($trans_id, $bank_QTR, 0, $amount, 'O/S'); 
                
                // insert to cash request balance
                $this->insert_to_cr_balance($trans_date, $cash_request_id, $trans_id, $pv_number, 0, $amount);
                
            }
        }
        return $trans_id;
    }
    
    public function insert_to_cr_balance($balance_date='', $cash_request_id=0, $trans_id=0, $pv_number='', $debit=0, $credit=0) {
        $data = array(
            'balance_date' => $balance_date,
            'cash_request_id' => $cash_request_id,
            'trans_id' => $trans_id,
            'pv_number' => $pv_number,
            'debit' => $debit,
            'credit' => $credit
        );

        $this->db->insert('cash_request_balance', $data);
    }
    
    public function insert_return_to_employee($trans_date= '', $description='', $amount=0, $branch_id=0, $cash_request_id=0, $pv_number='') {
        $this->load->model('ledger_model');
        $trans_id = 0;
        $cashrequest = $this->get_cashrequest_by_id($cash_request_id);
        if ($cashrequest->num_rows()!=0){
            $row = $cashrequest->row();
            $pv_number = $row->pv_number;

            $account_from = $this->get_account_id($branch_id, 'Expenses in Bank', 4);
            $account_to = $this->get_account_id($branch_id, 'Petty Bank', 0);
            $trans_id = $this->insert_transaction_by_param($trans_date, 4, $account_from, $account_to, $description, $amount, $pv_number);
            // insert to EXPENSES
            $this->ledger_model->insert_ledger($trans_id, $account_from, $amount, 0, 'EXP');
            // insert to RECEIVABLE
            $this->ledger_model->insert_ledger($trans_id, $account_to, 0, $amount, 'EXP');
            // insert to cash request balance
            $this->insert_to_cr_balance($trans_date, $cash_request_id, $trans_id, $pv_number, $amount, 0);
       
        }
        return $trans_id;
    }
}
