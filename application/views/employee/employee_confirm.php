<!-- /.panel-heading -->
<?php
if ($employee->num_rows() != 0) {
    $row = $employee->row();
    $encrypt_id = $this->general_model->encrypt_value($row->employee_id);
    $delete = 'employee/ac/' . $this->asik_model->category_masterdata;
    $delete .= $this->asik_model->master_05 . '/' . $this->asik_model->action_delete_approve . '/' . $encrypt_id;

    $back = 'employee/go/' . $this->asik_model->category_masterdata;
    $back .= $this->asik_model->master_05 . '/';
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
                            <td><strong>Name</strong></td>
                            <td><?php echo $row->full_name ?></td>
                        </tr>
                        <tr>
                            <td><strong>Branch</strong></td>
                            <td><?php echo $row->branch_name ?></td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong></td>
                            <td><?php echo $row->description ?></td>
                        </tr>
                    </table>
                </div>
            </div>
    </div>
<?php } ?>
<!-- /.panel-body -->