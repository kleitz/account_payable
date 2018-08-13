<div class="col-xs-12">
    <div class="box">
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
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($account_list->num_rows() != 0) {
                                    $inc = 1;
                                    foreach ($account_list->result() as $val):
                                        if ($val->account_type == $key) {
                                        ?>
                                        <tr>
                                            <td><?php echo $inc ?></td>
                                            <td><?php echo $val->account_code ?></td>
                                            <td><?php echo $val->account_name ?></td>
                                            <td>
                                                <a class="btn btn-success btn-sm" href="<?php echo site_url('report/ledger_detail/'.$val->account_id) ?>">View</a>
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

