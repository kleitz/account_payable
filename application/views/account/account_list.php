<div class="col-xs-12">
    <div class="box">
        <div class="box-header">
            <?php
            if ($action_add_val){
                echo '<button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Account</button>';
            }
            ?>
        </div>
        
        <!-- Custom Tabs -->
        <?php $type_opt = array('Asset','Liability','Equity','Revenue','Expense'); ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <?php
                foreach ($type_opt as $key => $type) {
                    $active = $key == 0 ? "active" : "";
                    ?>
                    <li class="<?php echo $active ?>"><a href="#<?php echo $key ?>" data-toggle="tab"><?php echo $type ?></a></li>
                    <?php
                }
                ?>
            </ul>
            <div class="tab-content">
                <?php
                foreach ($type_opt as $key => $type) {
                    $active = $key == 0 ? "active" : "";
                    ?>
                    <div class="tab-pane <?php echo $active ?>" id="<?php echo $key ?>">

                        <div class="box-body table-responsive no-padding">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Type</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($list->num_rows() != 0) {
                                    $inc = 1;
                                    foreach ($list->result() as $val):
                                        /*$encrypt_id = $this->general_model->encrypt_value($val->account_id);*/
                                        $debet_str = $val->debit == 1 ? "(+) Increases" : "(-) Decreases";
                                        $credit_str = $val->credit == 1 ? "(+) Increases" : "(-) Decreases";
                                        if ($val->account_type == $key) {
                                        ?>
                                        <tr>
                                            <td><?php echo $inc ?></td>
                                            <td><?php echo $val->account_code ?></td>
                                            <td><?php echo $val->account_name ?></td>
                                            <td><?php echo $debet_str ?></td>
                                            <td><?php echo $credit_str ?></td>
                                            <td><?php echo $this->account_model->account_type_opt[$val->account_type] ?></td>
                                            <td class="text-right">
                                                <?php
                                                if ($action_edit_val){
                                                    echo '<button class="btn btn-default btn-sm" onclick="edit_data('.$val->account_id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                                                }
                                                if ($action_delete_val){
                                                    echo '<button class="btn btn-default btn-sm" onclick="delete_data('.$val->account_id.')"><i class="fa fa-trash"></i></button>';
                                                }
                                                ?>
                                                
                                                
                                            </td>
                                        </tr>
                                        <?php
                                        $inc++;
                                        }
                                    endforeach;
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        
    </div>
</div>

