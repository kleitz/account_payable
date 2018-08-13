<div class="col-xs-12">
    <?php
    $action = 'privilege/checked/';
    echo form_open($action);
    ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <input type="hidden" name="priv_group_id" value="<?php echo $priv_group_id ?>" />
            <input type="hidden" name="pagecode" value="<?php echo $pagecode ?>" />
            <input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id ?>" />
            <input type="submit" class="btn btn-primary" value="Submit">&nbsp;
            <a href="<?php echo site_url('privilege/godetail/'.$pagecode.'/'.$encrypt_id); ?>" class="btn btn-default">Back</a>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered datatable3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Module</th>
                        <th>Feature</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 0;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        echo '<tr>';
                        echo '<td><input type="checkbox" name="checkmo['.$no.']" value="'.$val->module_action_id.'" /></td>';
                        echo '<td>'.$val->module_name.'</td>';
                        echo '<td>'.$val->action_name.'</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
            </table>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
