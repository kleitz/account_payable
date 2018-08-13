<!-- /.panel-heading -->
<div class="col-md-12">
    <?php if (validation_errors() != "") { ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Peringatan!</h4>
            <?php echo validation_errors(); ?>
        </div>
    <?php } ?>
    <div class="box box-info">
        <?php
        $encrypt_id = $this->general_model->encrypt_value('0');
        $action = 'podetail/ac/' . $this->asik_model->category_masterdata;
        $action .= $this->asik_model->master_07 . '/' . $this->asik_model->action_add . '/' . $encrypt_id;
        echo form_open($action);
        ?>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" class="form-control" name="datenow" id="today">
                    <?php
                    echo $po_detail_id;
                    echo $po_id;
                    echo $item_code;
                    echo $quantity;
                    echo $item_name;
                    echo $price;
                    echo $discount;

                    ?>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <?php
            $po_enc_id = $this->general_model->encrypt_value($poid);
            $link = 'podetail/go/' . $this->asik_model->category_masterdata;
            $link .= $this->asik_model->master_07 . '/'.$po_enc_id;
            ?>
            <a href="<?php echo site_url($link); ?>" class="btn btn-default">Close</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>