<?php
$actionlink = 'receiveinbank/insert_return_cash_emp/';
echo form_open($actionlink); 
?>
<div class="col-md-12">
    <div class="box box-success">
        <div class="box-header with-border">
            &nbsp;
        </div>
        <div class="box-body">
            <?php
            echo $receive_bank_id;
            echo $receive_bank_number;
            echo $number_disable;
            echo $receive_bank_date;
            echo $description;
            echo $amount;
            //echo $amount_disable;
            echo $branch_id;
            echo $cash_request_id;
            echo $branch_disable;
            echo $cash_request_disable;
            ?>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Save" />
            <a href="<?php echo site_url('receiveinbank/go/20191121214305') ?>" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
<?php echo form_close();  ?>