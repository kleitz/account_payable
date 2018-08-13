<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Supplier</button>
        </div>
        <div class="box-body table-responsive">
            
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier Name</th>
                    <th>Account Name</th>
                    <th>Account No.</th>
                    <th>Bank</th>
                    <th>Type</th>
                    <th class="text-right">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($list->num_rows() != 0) {
                    $inc = 1;
                    $type_opt = array('Cash Supplier', 'Credit Supplier');
                    foreach ($list->result() as $val):
                        $encrypt_id = $this->general_model->encrypt_value($val->supplier_id);
                        ?>
                        <tr>
                            <td><?php echo $inc ?></td>
                            <td><?php echo $val->supplier_name ?></td>
                            <td><?php echo $val->account_name ?></td>
                            <td><?php echo $val->account_number ?></td>
                            <td><?php echo $val->bank_name ?></td>
                            <td><?php echo $type_opt[$val->supplier_type] ?></td>
                            <td class="text-right">
                                <button class="btn btn-default btn-sm" onclick="edit_data(<?php echo $val->supplier_id; ?>)"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-default btn-sm" onclick="delete_data(<?php echo $val->supplier_id; ?>)"><i class="fa fa-trash"></i></button>
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
                        <th>Supplier Name</th>
                        <th>Account Name</th>
                        <th>Account No.</th>
                        <th>Bank</th>
                        <th>Type</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
