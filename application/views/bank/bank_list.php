<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <?php
            if ($action_add_val){
                echo '<button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</button>';
            } else {
                echo '&nbsp;';
            }
            ?>
        </div>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Bank ID</th>
                    <th>Bank Name</th>
                    <th>Account Name</th>
                    <th>Account No.</th>
                    <th>Outlet</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($list->num_rows() != 0) {
                    $inc = 1;
                    $status = array("Non-Active", "Active");
                    foreach ($list->result() as $val):
                        ?>
                        <tr>
                            <td><?php echo $inc ?></td>
                            <td><?php echo $val->bank_code ?></td>
                            <td><?php echo $val->bank_name ?></td>
                            <td><?php echo $val->bank_account_name ?></td>
                            <td><?php echo $val->bank_account_no ?></td>
                            <td><?php echo $val->branch_name ?></td>
                            <td><?php echo $status[$val->bank_status] ?></td>
                            <td class="text-right">
                                <?php
                                if ($action_edit_val){
                                    echo '<button class="btn btn-default btn-sm" onclick="edit_data('.$val->bank_id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                                }
                                if ($action_delete_val){
                                    echo '<button class="btn btn-default btn-sm" onclick="delete_data('.$val->bank_id.')"><i class="fa fa-trash"></i></button>';
                                }
                                ?>                                
                            </td>
                        </tr>
                        <?php
                        $inc++;
                    endforeach;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Bank ID</th>
                        <th>Bank Name</th>
                        <th>Account Name</th>
                        <th>Account No.</th>
                        <th>Outlet</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
