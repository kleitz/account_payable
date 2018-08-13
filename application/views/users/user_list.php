<!-- /.panel-heading -->
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <button class="btn btn-primary" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New User</button> 
        </div>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $inc = 1;
                    $arr_status = array('Non-Active', 'Active');
                    foreach ($list->result() as $user):
                        $encrypt_id = $this->general_model->encrypt_value($user->user_id);
                        ?>
                        <tr>
                            <td><?php echo $inc ?></td>
                            <td><?php echo $user->fullname ?></td>
                            <td><?php echo $user->username ?></td>
                            <td><?php echo $user->email ?></td>
                            <td><?php echo $arr_status[$user->user_status] ?></td>
                            <td>
                                <!-- Single button -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="<?php echo site_url('users/vdetail/'.$encrypt_id) ?>"><i class="fa fa-th-list"></i> View Detail</a></li>
                                        <li><a onclick="upload_file(<?php echo $user->user_id; ?>)" href="#"><i class="fa fa-image"></i> Upload photo</a></li>
                                        <li><a onclick="change_password(<?php echo $user->user_id; ?>)" href="#"><i class="fa fa-lock"></i> Change password</a></li>
                                        <li><a onclick="edit_data(<?php echo $user->user_id; ?>)" href="#"><i class="fa fa-edit"></i> Edit</a></li>
                                        <li><a onclick="delete_data(<?php echo $user->user_id; ?>)" href="#"><i class="fa fa-trash-o"></i> Delete</a></li>
                                    </ul>
                                </div>
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
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- /.panel-body -->