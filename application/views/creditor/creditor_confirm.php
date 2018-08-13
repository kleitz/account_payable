<!-- /.panel-heading -->
<?php
if ($creditor->num_rows() != 0) {
    $row = $creditor->row();
    $encrypt_id = $this->general_model->encrypt_value($row->creditor_id);
    $delete = 'creditor/ac/' . $this->asik_model->category_masterdata;
    $delete .= $this->asik_model->master_06 . '/' . $this->asik_model->action_delete_approve . '/' . $encrypt_id;

    $back = 'creditor/go/' . $this->asik_model->category_masterdata;
    $back .= $this->asik_model->master_06 . '/';
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
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box-body no-padding">

                            <table class="table table-striped">
                                <tr>
                                    <td><strong>creditor name</strong></td>
                                    <td><?php echo $row->creditor_name ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address</strong></td>
                                    <td><?php echo $row->address ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone</strong></td>
                                    <td><?php echo $row->phone ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Description</strong></td>
                                    <td><?php echo $row->description ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- /.panel-body -->