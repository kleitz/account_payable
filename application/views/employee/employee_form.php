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
        <div class="box-header with-border">
            <h3 class="box-title"><span style="color: red;">*</span> Wajib isi data.</h3>
        </div>
        <?php
        $encrypt_id = $this->general_model->encrypt_value('0');
        $action = 'employee/ac/' . $this->asik_model->category_masterdata;
        $action .= $this->asik_model->master_05 . '/' . $this->asik_model->action_add . '/' . $encrypt_id;
        echo form_open($action);
        ?>
        <div class="box-body">

            <?php
            echo $employee_id;
            echo $full_name;
            echo $branch_id;
            echo $description;
            ?>

        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <?php
            $link = 'employee/go/' . $this->asik_model->category_masterdata;
            $link .= $this->asik_model->master_05 . '/';
            ?>
            <a href="<?php echo site_url($link); ?>" class="btn btn-default">Close</a>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>