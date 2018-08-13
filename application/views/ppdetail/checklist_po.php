<?php

$action = 'ppdetail/submit_process/' . $enc_pp_id;
echo form_open($action);
?>
<div class="col-lg-12">
    <input type="submit" class="btn btn-primary btn-sm" value="Submit">&nbsp;
    <a href="<?php echo site_url('ppdetail/go/'.$this->asik_model->category_configuration.$this->asik_model->config_03.'/'.$enc_pp_id); ?>" class="btn btn-default btn-sm">Back</a>
    <div>&nbsp;</div>
</div>
<?php
if ($list->num_rows()!=0){
    $no = 0;
    foreach ($list->result() as $val) {
?>
<div class="col-lg-4">
    <div class="list-group">
        <a href="#" class="list-group-item"><input type="checkbox" name="checkpo[<?php echo $no ?>]" value="<?php echo $val->po_id ?>" /></a>
        <a href="#" class="list-group-item active">
            <h4 class="list-group-item-heading"><?php echo $val->supplier_name ?></h4>
            <p class="list-group-item-text"><?php echo $val->supplier_address ?></p>
        </a>
        <a href="#" class="list-group-item">
            <h4 class="list-group-item-heading"><?php echo $val->branch_name ?></h4>
            <strong>Order Date: </strong> <?php echo $val->po_date ?><br>
            <strong>PO Number : </strong><?php echo $val->po_number ?><br>
            <strong>Invoice   : </strong><?php echo $val->invoice ?><br>
            <strong>Due Date  : </strong><?php echo $val->due_date ?><br>  
        </a>
        <a href="#" class="list-group-item">
            <h4 class="list-group-item-heading">Total : <?php echo number_format($val->total) ?></h4>
        </a>
    </div>
</div>
<?php 
        $no++;
    }
} else {
    echo '<div class="col-lg-12">Transaksi Purchase Order kosong.</div>';
} 
?>
<?php echo form_close(); ?>