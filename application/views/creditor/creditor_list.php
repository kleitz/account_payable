<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <button class="btn btn-primary btn-sm" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Creditor</button>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Creditor Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($list->num_rows() != 0) {
                    $inc = 1;
                    foreach ($list->result() as $val):
                        ?>
                        <tr>
                            <td><?php echo $inc ?></td>
                            <td><?php echo $val->creditor_name ?></td>
                            <td><?php echo $val->address ?></td>
                            <td><?php echo $val->phone ?></td>
                            <td><?php echo $val->description ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="edit_data(<?php echo $val->creditor_id; ?>)">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="delete_data(<?php echo $val->creditor_id; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                        $inc++;
                    endforeach;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>