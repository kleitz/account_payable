<?php
if ($credit_invoice->num_rows()!=0){
    $row = $credit_invoice->row();
    $po_date = ($row->po_date == '' || $row->po_date == '0000-00-00')?'-':$this->general_model->get_string_date($row->po_date);
    $invoice_date = ($row->invoice_date == '' || $row->invoice_date == '0000-00-00')?'-':$this->general_model->get_string_date($row->invoice_date);
    $receive_date = ($row->receive_date == '' || $row->receive_date == '0000-00-00')?'-':$this->general_model->get_string_date($row->receive_date);
?>
<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="<?php echo site_url($back_link) ?>" class="btn btn-default btn-sm">Back</a>
        </div>
        <div class="box-body">
            <?php

                echo '<table class="table">
                    <tr>
                        <th>ID Credit Invoice</th>
                        <td>'.$row->credit_invoice_number.'</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>'.$row->supplier_name.'</td>
                    </tr>
                    <tr>
                        <th>PO Number</th>
                        <td>'.$row->po_number.'</td>
                    </tr>
                    <tr>
                        <th>PO Date</th>
                        <td>'.$po_date.'</td>
                    </tr>
                    <tr>
                        <th>Invoice</th>
                        <td>'.$row->invoice.'</td>
                    </tr>
                    <tr>
                        <th>Invoice Date</th>
                        <td>'.$invoice_date.'</td>
                    </tr>
                    <tr>
                        <th>Receive No.</th>
                        <td>'.$row->receive_no.'</td>
                    </tr>
                    <tr>
                        <th>Receive Date</th>
                        <td>'.$receive_date.'</td>
                    </tr>
                    <tr>
                        <th>Outlet</th>
                        <td>'.$row->branch_name.'</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>'.$row->description.'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.number_format($row->amount).'</td>
                    </tr>
                    
                </table>';
            ?>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $row->credit_invoice_id ?>)">Upload file</button>
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
            
            if ($creditinvoice_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($creditinvoice_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | '.$val->file_name.'</td>
                        <td class="text-right">
                        <a href="'. base_url().'assets/creditinvoice/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>
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
</div>
<?php
}
?>