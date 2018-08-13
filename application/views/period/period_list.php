<!-- /.panel-heading -->
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <?php
            if ($action_add_val){
                echo '<button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Period</button>';
            } else { echo '&nbsp;';}
            ?>            
        </div>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Year</th>
                        <th>Period</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Sort</th>
                        <th>Closing</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($list)) {
                        $inc = 1;
                        $arr_closed = array('No', 'Yes');
                        $arr_status = array('Non-Active', 'Active');
                        foreach ($list->result() as $value) {
                            $encrypt_id = $this->general_model->encrypt_value($value->period_id);
        
                            $closing = $closing_link . '/' . $encrypt_id;
                            $active = $active_link . '/' . $encrypt_id;
                            $nonactive = $nonactive_link . '/' . $encrypt_id;
                            $tr_class = '';
                            if ($value->active_status == 1) {
                                $tr_class = ' class="success"';
                            }
                            if ($value->closed == 1) {
                                $tr_class = ' class="info"';
                            }
                            ?>
                            <tr <?php echo $tr_class ?>>
                                <td><?php echo $inc ?></td>
                                <td><?php echo $value->year ?></td>
                                <td><?php echo $value->period ?></td>
                                <td><?php echo $this->general_model->get_string_date($value->start_date) ?></td>
                                <td><?php echo $this->general_model->get_string_date($value->end_date) ?></td>
                                <td><?php echo $value->sorting ?></td>
                                <td><?php echo $arr_closed[$value->closed] ?></td>
                                <td><?php echo $arr_status[$value->active_status] ?></td>
                                <td class="text-right">
                                    <?php
                                    echo '<a class="btn btn-default btn-sm" href="'. site_url($calendar_link.'/'.$encrypt_id).'"><i class="fa fa-calendar"></i> Calendar</a>&nbsp;';
                                    if ($value->closed == 1) {
                                        if ($value->active_status == 0 && $is_active == 0) {
                                            echo '<a class="btn btn-default btn-sm" onclick="active('.$value->period_id.')" href="#"><i class="fa fa-check"></i> Active</a>&nbsp;';
                                        } else {
                                            echo '<a class="btn btn-default btn-sm" onclick="non_active('.$value->period_id.')" href="#"><i class="fa fa-ban"></i> Non-Active</a>&nbsp;';
                                        }
                                    } else {
                                        if ($value->active_status == 1) {
                                            echo '<a class="btn btn-default btn-sm" onclick="non_active('.$value->period_id.')" href="#"><i class="fa fa-ban"></i> Non-Active</a>&nbsp;';
                                            echo '<a class="btn btn-default btn-sm" onclick="closing('.$value->period_id.')" href="#"><i class="fa fa-lock"></i> Closing</a>&nbsp;';
                                        } else {

                                            
                                            if ($action_edit_val){
                                            echo '<a class="btn btn-default btn-sm" href="#" onclick="edit_data('.$value->period_id.')"><i class="fa fa-edit"></i> Edit</a>&nbsp;';
                                            }
                                            if ($action_delete_val){
                                            echo '<a class="btn btn-default btn-sm" href="#" onclick="delete_data('.$value->period_id.')"><i class="fa fa-trash-o"></i> Delete</a>&nbsp;';
                                            }
                                            if ($is_active == 0) {
                                                echo '<a class="btn btn-default btn-sm" onclick="active('.$value->period_id.')" href="#"><i class="fa fa-check"></i> Active</a>&nbsp;';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $inc++;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Year</th>
                        <th>Period</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Sort</th>
                        <th>Closing</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- /.panel-body -->