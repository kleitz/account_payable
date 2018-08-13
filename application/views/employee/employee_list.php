<!-- /.panel-heading -->
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <?php
            if ($action_add_val){
                echo '<button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Person</button>';
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
                        <th>Nama</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $inc = 1;
                    foreach ($list->result() as $employee):
                        ?>
                        <tr>
                            <td><?php echo $inc ?></td>
                            <td><?php echo $employee->full_name ?></td>
                            <td><?php echo $employee->branch_name ?></td>
                            <td><?php echo $employee->description ?></td>
                            <td class="text-right">
                                <?php
                                if ($action_edit_val){
                                    echo '<button class="btn btn-default btn-sm" onclick="edit_data('.$employee->employee_id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                                }
                                if ($action_delete_val){
                                    echo '<button class="btn btn-default btn-sm" onclick="delete_data('.$employee->employee_id.')"><i class="fa fa-trash"></i></button>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $inc++;
                    endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- /.panel-body -->