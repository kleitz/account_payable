<!-- /.panel-heading -->
<?php
if (isset($row)) {
    $encrypt_id = $this->general_model->encrypt_value($row->period_id);
    $delete = 'period/ac/' . $this->asik_model->category_configuration;
    $delete .= $this->asik_model->config_03 . '/' . $this->asik_model->action_delete_approve . '/' . $encrypt_id;

    $back = 'period/go/' . $this->asik_model->category_configuration;
    $back .= $this->asik_model->config_03 . '/';
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
                        <td><strong>Year</strong></td>
                        <td><?php echo $row->year ?></td>
                    </tr>
                    <tr>
                        <td><strong>Period</strong></td>
                        <td><?php echo $row->period ?></td>
                    </tr>
                    <tr>
                        <td><strong>Start Date</strong></td>
                        <td><?php echo $row->start_date ?></td>
                    </tr>
                    <tr>
                        <td><strong>End Date</strong></td>
                        <td><?php echo $row->end_date ?></td>
                    </tr>
                </table>


            </div>
        </div>
    </div>
<?php } ?>
<!-- /.panel-body -->