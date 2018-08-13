<?php
if ($cash_request_detail->num_rows()!=0){
    $row = $cash_request_detail->row();
?>
<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
            
            <?php
            if ($row->cash_request_status < 2){
                echo '<a href="#" class="btn btn-default btn-sm" onclick="edit_data('.$row->cash_request_id.')">Edit</a>&nbsp;';
                echo '<a href="#" class="btn btn-default btn-sm" onclick="delete_data('.$row->cash_request_id.')">Delete</a>&nbsp;';
            } 
            
            if ($balance_check == 0 && $row->cash_request_status == 3){
                echo '<a href="'. site_url($closing_link).'" class="btn btn-success btn-sm">Closing Cash Request</a>';
            }
            ?>
            <a target="_blank" href="<?php echo site_url($print_link) ?>" class="btn btn-default btn-sm">Print</a>
        </div>
        <div class="box-body">
            <?php                
                $description = nl2br($row->description);
                $remark = nl2br($row->remark);
                echo '<table class="table">
                    <tr>
                        <th>Number</th>
                        <td>'.$row->cash_request_number.'</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>'.$this->general_model->get_string_date($row->cash_request_date).'</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>'.$row->employee_name.'</td>
                    </tr>
                    <tr>
                        <th>Outlet (Branch)</th>
                        <td>'.$row->branch_name.'</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <div><strong>Description</strong></div>'.
                        $description.'
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <div><strong>Remark</strong></div>'.
                        $remark.'
                        </td>
                    </tr>
                    <tr>
                        <th>Payment mode</th>
                        <td>'.$this->cashrequest_model->payment_mode_opt[$row->payment_mode].'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.number_format($row->amount).'</td>
                    </tr>';
                /*
                    <tr>
                        <th>Cash return</th>
                        <td>'.number_format($row->cash_return).'</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>'.number_format($row->amount - $row->cash_return).'</td>
                    </tr>
                 * */
                 echo '<tr>
                        <th>Status</th>
                        <td>'.$this->cashrequest_model->cashrequest_status[$row->cash_request_status].'</td>
                    </tr>
                </table>';
            ?>
        </div>
        <div class="box-footer">
            <div class="col-sm-4">
                <h4>Prepared by</h4>
                <strong><?= $this->cashrequest_model->get_fullname_by_user_id($row->prepared_by) ?></strong>
            </div>
            <div class="col-sm-4">
                <h4>Checked by</h4>
                <?php
               
                
                if ($row->checked_by == 0 && $action_checked){
                    echo '<a href="'. site_url('cashrequest/checked/'.$encrypt_id).'" class="btn btn-default btn-sm">Checked</a>';
                } 
                if ($row->checked_by != 0){
                    echo '<strong>'.$this->cashrequest_model->get_fullname_by_user_id($row->checked_by).'</strong>';
                }
                ?>
            </div>
            <div class="col-sm-4">
                <h4>Approved by</h4>
                <?php
                if ($row->approved_by == 0 && $action_approved){
                    echo '<a href="'. site_url('cashrequest/approved/'.$encrypt_id).'" class="btn btn-default btn-sm">Approve</a>';
                } 
                if ($row->approved_by != 0){
                    echo '<strong>'.$this->cashrequest_model->get_fullname_by_user_id($row->approved_by).'</strong>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-primary btn-sm" onclick="upload_file(<?php echo $row->cash_request_id ?>)">Upload file</button>
        </div>
        <div class="box-body">
            <?php
            
            $iconfile = array(
                '.jpg' => 'fa-file-image-o',
                '.png' => 'fa-file-image-o',
                '.jpeg' => 'fa-file-image-o',
                '.pdf' => 'fa-file-pdf-o',
                '.doc' => 'fa-file-word-o',
                '.docx' => 'fa-file-word-o',
                '.xls' => 'fa-file-excel-o',
                '.xlsx' => 'fa-file-excel-o'
            );
            
            if ($cash_request_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($cash_request_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | '.$val->file_name.'</td>
                        <td class="text-right">
                            <a href="'. base_url().'assets/cashrequest/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>&nbsp;|&nbsp;
                            <a href="'. site_url('cashrequest/delete_file/'.$val->cash_request_file_id.'/'.$val->file_name.'/'.$encrypt_id) .'">
                            <i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>';
                }
                echo '</table>';
            } else {
                echo 'Belum ada file.';
            }
            ?>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-header with-border">
            <?php
            echo form_open('cashrequest/add_remark');
            echo '<input type="hidden" name="cash_request_id" value="'.$row->cash_request_id.'">';
            echo '<input type="hidden" name="encrypt_id" value="'.$encrypt_id.'">';
            echo '<input type="text" name="remark" class="form-control" placeholder="type remark then enter..">';
            echo form_close();
            ?>
        </div>
        <div class="box-body">
            <?php
            if ($remark_list->num_rows()!=0){
                echo '<table class="table">';
                foreach ($remark_list->result() as $value) {
                    echo '<tr>
                    <td>'.$value->remark.'</td>
                    <td class="text-right"><i class="fa fa-close"></i></td>
                    </tr>';
                }
                echo '</table>';
            } else {
                echo 'There is no remark';
            }
            ?>
        </div>
    </div>
    
    <div class="box box-default">
        <div class="box-header with-border">
            <a href="<?php echo site_url('paymentprocess/ppgeneral/20191231214301/0/'.$row->cash_request_id.'/0/') ?>" target="_blank" class="btn btn-primary btn-sm">Add PP (Nota Come)</a>
        </div>
        <div class="box-body">
            <?php
            $total_pp = 0;
            if ($pp_cash_request->num_rows()!=0){
                echo '<table class="table table-striped table-bordered">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Date</th>';
                echo '<th>Outlet</th>';
                echo '<th class="text-right">Amount</th>';
                echo '<th>Status</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                $pp_status_style = array(
                    '<span class="label label-danger">Draft</span>',
                    '<span class="label label-warning">Cross Check</span>',
                    '<span class="label label-info">Checked</span>',
                    '<span class="label label-primary">Approved</span>',
                    '<span class="label label-success">Closed</span>'
                );
                $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                foreach ($pp_cash_request->result() as $value) {
                    $enc_id = $this->general_model->encrypt_value($value->pp_id);
                    $detail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/0/';
                    echo '<tr>';
                    echo '<td><a  href="'. site_url($detail).'" target="_blank">'.$this->general_model->get_string_date($value->pp_date).'</a></td>';
                    echo '<td>'.$value->branch_name.'</td>';
                    echo '<td class="text-right">'.number_format($value->total,0).'</td>';
                    echo '<td>'.$pp_status_style[$value->pp_status].'</td>';
                    if ($value->pp_status == 4){
                        echo '<td><span class="label label-success">Paid</span></td>';
                    } else {
                        if ($value->pp_status < 3){
                            echo '<td><a href="#" class="btn btn-default btn-sm">Pay</a></td>';
                        } else if($value->pp_status == 3){
                            echo '<td><a href="'. site_url('paymentvoucher/pay/20191231214302/'.$enc_id.'/0/1').'" target="_blank" class="btn btn-success btn-sm">Pay</a></td>';
                        }                        
                    }
                    
                    echo '</tr>';
                    $total_pp = $total_pp + $value->total;
                }
                echo '</tbody>';
                echo '<tfoot>';
                echo '<tr>';
                echo '<th>Date</th>';
                echo '<th>Outlet</th>';
                echo '<th class="text-right">'. number_format($total_pp,0).'</th>';
                echo '<th>Status</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</tfoot>';
                echo '</table>';
            } else {
                echo 'No Data Available';
            }
            ?>
        </div>
        <div class="box-footer">
            <div><strong>Cash Request Balance & Action</strong></div>
            <?php
            $balance = $row->amount - $total_pp;
            $action = '';
            if ($balance == 0 || $balance_check == 0){
                $action = '<span class="label label-success">Balance</span>';
            } else {
                if ($balance_check != 0){
                    if ($balance > 0){
                        $action = '<a href="'. site_url('receiveinbank/return_by_emp/20191121214305/'.$row->cash_request_id.'/'.$balance.'/').'" target="_blank" class="btn btn-success btn-sm">Return to Outlet</a>';
                    } else {
                        $action = '<a href="'.site_url('paymentprocess/ppgeneral/20191231214301/0/'.$row->cash_request_id.'/0/0/'.$row->employee_id).'" target="_blank" class="btn btn-success btn-sm">Return to Employee</a>';
                    }
                }
            }
            echo '<table class="table table-striped table-bordered">';
            echo '<thead>';
            echo '<tr>';
            echo '<th class="text-right">Debit</th>';
            echo '<th class="text-right">Credit</th>';
            echo '<th class="text-right">Balance</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            $saldoawal = $row->amount;
            $saldo = 0;
            foreach ($crbalance_list->result() as $val) {
                if ($val->debit == $saldoawal){
                    $saldo = $saldoawal;
                } else {
                    $saldo = $saldo + $val->debit;
                }
                if ($val->credit != 0){
                    $saldo = $saldo - $val->credit;
                }
                echo '<tr>';
                echo '<td class="text-right">'. number_format($val->debit).'</td>';
                echo '<td class="text-right">'. number_format($val->credit).'</td>';
                echo '<td class="text-right">'. number_format($saldo).'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';
            echo '<th colspan="3" class="text-right">'.$action.'&nbsp;</th>';
            echo '</tr>';
            echo '</tfoot>';
            echo '</table>';
            ?>
        </div>
    </div>
</div>
<?php
}
?>