<!-- /.panel-heading -->
<?php
if ($detail->num_rows() != 0) {
    $row = $detail->row();
    $encrypt_id = $this->general_model->encrypt_value($row->po_detail_id);
    $delete = 'podetail/ac/' . $this->asik_model->category_masterdata;
    $delete .= $this->asik_model->master_07 . '/' . $this->asik_model->action_delete_approve . '/' . $encrypt_id;
    $enc_po_id = $this->general_model->encrypt_value($row->po_id);
    $back = 'podetail/go/' . $this->asik_model->category_masterdata;
    $back .= $this->asik_model->master_07 . '/' . $enc_po_id;
    ?>
    <div class="col-md-12">
        <div class="callout callout-danger">
            <h4>Delete Confirmation!</h4>
            <p>Are you sure you want to delete this data?</p>
        </div>
        <div class="box box-danger">
            <div class="box-header with-border">
                <a href="<?php echo site_url($delete); ?>" class="btn btn-danger btn-sm">Yes</a>
                <a href="<?php echo site_url($back); ?>" class="btn btn-danger btn-sm">No</a>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <td><strong>Quantity</strong></td>
                        <td><?php echo $row->quantity ?></td>
                    </tr>
                    <tr>
                        <td><strong>Item code</strong></td>
                        <td><?php echo $row->item_code ?></td>
                    </tr>
                    <tr>
                        <td><strong>Item name</strong></td>
                        <td><?php echo $row->item_name ?></td>
                    </tr>
                    <tr>
                        <td><strong>Unit cost</strong></td>
                        <td><?php echo $row->price ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
<!-- /.panel-body -->