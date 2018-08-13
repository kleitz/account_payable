<?php
if ($pp_info->num_rows()!=0){
    $row = $pp_info->row();
    $label = array();
    $label[0] = 'Tanggal';
    $label[1] = 'Jatuh Tempo';
    $label[2] = 'PROSES PEMBAYARAN';
    $label[3] = 'NO KWITANSI';
    if ($this->session->userdata('ap_lang')== 1){
        $label[0] = 'Date';
        $label[1] = 'Due Date';
        $label[2] = 'PAYMENT PROCESS';
        $label[3] = 'PV NO';
    }
    $to_be_approve = 2;
    if ($credit_invoice->num_rows()!=0 && $row->pp_status < $to_be_approve){
?>
<form name="formsupplier" method="post" action="<?php echo site_url('ppdetail/ppsupplier_add/') ?>">
<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-left">
                <h4>List Of Credit Invoice</h4>
            </div>
            <div class="pull-right" style="text-align: right">
                <h4><?php echo $row->supplier_name ?></h4>
            </div>
        </div>
        <div class="box-body table-responsive">
            <?php
            echo $encrypt_id;
            echo $pp_id;
            ?>
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all"/></th>
                        <th>ID</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <?php
                $total_all = 0;
                
                    
                    echo '<tbody>';
                    foreach ($credit_invoice->result() as $val) {
                        echo '<tr>';
                        echo '<td><input type="checkbox" class="checkbox" name="chk_invoice[]" value="'.$val->credit_invoice_id.'" /></td>';
                        echo '<td>'.$val->credit_invoice_number.'</td>';
                        echo '<td>'.$val->po_number.'</td>';
                        echo '<td>'.$val->po_date.'</td>';
                        echo '<td>'.$val->invoice.'</td>';
                        echo '<td>'.$val->invoice_date.'</td>';
                        echo '<td class="text-right">'.number_format($val->amount,2).'</td>';
                        echo '</tr>';
                        $total_all += $val->amount;
                    }
                    echo '</tbody>';
               
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th class="text-right"><?php echo number_format($total_all, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="box-footer">
            <input class="btn btn-primary btn-sm" type="submit" name="submit" value="Submit" />
        </div>
    </div>
</div>
</form>
<?php
    }
?>

<div class="col-md-9" id="printableArea">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-left">
                <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
                <h4><?php echo $label[0] ?>: <?php echo isset($row->pp_date)? $this->general_model->get_string_date($row->pp_date): '_____' ?></h4>
                <h4><?php echo $label[1] ?>: <?php echo (isset($row->pp_due_date)&&$row->pp_due_date!='0000-00-00')? $this->general_model->get_string_date($row->pp_due_date): '_____' ?></h4>
            </div>
            <div class="pull-right" style="text-align: right">
                <h4>PP NUMBER: <?php echo $row->pp_number ?></h4>
                <?php
                $h3 = 'PROSES PEMBAYARAN';
                $h5 = 'PAYMENT PROCESS';
                if($row->supplier_type == 1){
                    $h3 = 'PROSES PEMBAYARAN KREDIT SUPPLIER';
                    $h5 = 'PAYMENT PROCESS FOR SUPPLIER INSTALLMENT';
                }
                ?>
                <h3><?php echo $label[2] ?></h3>
                <div>
                    <?php echo $label[3] .': ';
                        if (isset($row->pv_number)){
                            $pv_enc = $this->ppdetail_model->get_pv_id($row->pv_number);
                            echo '<a href="'. site_url('paymentvoucher/detail/20191231214302/').$pv_enc.'">'.$row->pv_number.'</a>'; 
                        } else {
                            echo '_ _ _';
                        }
                    ?>
                </div>
                <h4><?php echo $row->pp_title ?></h4>
            </div>
        </div>
        <div class="box-body table-responsive">
            <p>
                <a target="_blank" href="<?php echo site_url($print_link) ?>" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
                <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
            </p>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Dept/Act</th>
                        <th>Job Order</th>
                        <th>Description</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Total</th>
                        <?php
                        if ($row->pp_status < 2){
                            echo '<th class="text-right">Action</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($detail->result() as $val) {
                        $credit_invoice_number = '<div>[<a href="'. site_url('creditinvoice/godetail/20191121214301/'.$val->credit_invoice_id.'').'">'.$val->credit_invoice_number.'</a>]</div>';
                        $re_desc = str_replace("\n", '<br>', $val->description);
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->act_title.'</td>';
                        echo '<td>'.$val->job_order.'</td>';
                        echo '<td>'.$credit_invoice_number.$re_desc.'</td>';
                        echo '<td>'.$val->unit.'</td>';
                        echo '<td class="text-right">'.number_format($val->price).'</td>';
                        echo '<td class="text-right">'.number_format($val->total).'</td>';
                        if ($row->pp_status < 2){
                            echo '<td class="text-right">';
                            echo '<button class="btn btn-danger btn-sm" onclick="delete_data('.$val->pp_detail_id.')"><i class="fa fa-trash"></i></button>';
                            echo '</td>';
                        }
                        
                        echo '</tr>';
                        $total_all += $val->total;
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>No</th>
                        <th>Dept/Act</th>
                        <th>Job Order</th>
                        <th>Description</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th class="text-right">'. number_format($total_all).'</th>';
                    if ($row->pp_status < 2){
                        echo '<th class="text-right">Action</th>';
                    }
                    echo '</tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <div class="box-footer">
            <div class="col-md-3">
                <h4>Prepared By</h4>
                <div><?php echo $this->ppdetail_model->get_user_by_id($row->prepare_by) ?></div>
            </div>
            <div class="col-md-3">
                <h4>Cross Check By</h4>
                <div>
                <?php
                if ($row->cross_check_by == 0 && $row->pp_status == 0){
                    if ($action_cross_check){
                        echo '<a href="'. site_url($cross_check_link).'" class="btn btn-success btn-sm">Cross Check</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Cross Check</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($row->cross_check_by);
                }
                ?>
                </div>
            </div>
            <div class="col-md-3">
                <h4>Checked By</h4>
                <div>
                <?php
                if ($row->checked_by == 0 && $row->pp_status == 1){
                    if ($action_checked){
                        echo '<a href="'. site_url($check_link).'" class="btn btn-success btn-sm">Check</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Check</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($row->checked_by);
                }
                ?>
                </div>
            </div>
            <div class="col-md-3">
                <h4>Approved By</h4>
                <div>
                <?php
                if ($row->approved_by == 0 && ($row->pp_status == 2)){
                    if ($action_approved){
                        echo '<a href="'.site_url($approve_supplier_link).'" class="btn btn-success btn-sm">Approve</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Approve</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($row->approved_by);
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
?>
<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">
            <strong>List File from Credit Invoice</strong>
        </div>
        <div class="box-body">
            <?php
            if ($cinvoice_file->num_rows()!=0){
                foreach ($cinvoice_file->result() as $value) {
                    echo '<div><a href="'. base_url().'assets/creditinvoice/'.$value->file_name.'" target="_blank">'.$value->file_name.'</a></div>';
                }
            }
            ?>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $row->pp_id ?>)">Upload file</button>
        </div>
        <div class="box-body">
            <?php
            
            if ($paymentprocess_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($paymentprocess_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | <span style="font-size:11px;">'.$val->file_name.'</span></td>
                        <td class="text-right">
                            <a href="'. base_url().'assets/paymentprocess/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>
                            &nbsp;|&nbsp;';
                            if ($row->pp_status < 3){
                                echo '<a href="'. site_url('ppdetail/delete_file/'.$val->pp_file_id.'/'.$val->file_name.'/'.$up_encrypt_id.'/'.$pp_type) .'">';
                                echo '<i class="fa fa-trash-o"></i></a>';
                            }
                        echo '</td>
                    </tr>';
                }
                echo '</table>';
            } else {
                echo 'Belum ada file.';
            }
            ?>
        </div>
    </div>
</div>
<?php
}
?>
