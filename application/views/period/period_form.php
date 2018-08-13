<!-- /.panel-heading -->
<div class="col-md-12">
    <?php if (validation_errors() != "") { ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Peringatan!</h4>
            <?php echo validation_errors(); ?>
        </div>
    <?php } ?>
    <div class="box box-default">

        <?php
        $encrypt_id = $this->general_model->encrypt_value('0');
        $action = 'period/ac/' . $this->asik_model->category_configuration;
        $action .= $this->asik_model->config_03 . '/' . $this->asik_model->action_add . '/' . $encrypt_id;
        echo form_open($action);
        ?>
        <div class="box-body">

            <?php
            echo $period_id;
            echo $year;
            echo $sorting;
            echo $period;
            echo $start_date;
            echo $end_date;
            echo $closed;
            echo $active_status;
            ?>

        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <?php
            $link = 'period/go/' . $this->asik_model->category_configuration;
            $link .= $this->asik_model->config_03 . '/';
            ?>
            <a href="<?php echo site_url($link); ?>" class="btn btn-default">Close</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>