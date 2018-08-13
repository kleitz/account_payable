<!-- /.panel-heading -->
<div class="col-xs-12">
    <div class="box">
        <div class="box-body">
            <?php
            if ($detail->num_rows()!=0){
                $row = $detail->row();
                $gender = array('Female', 'Male');
                $status = array('Non-Active', 'Active');
                
            ?>
            <table class="table">
                <tr>
                    <td colspan="2">
                        <img class="img-responsive img-circle" width="200" src="<?php echo base_url().'/assets/img_profile/'.$row->photo ?>" alt="" />
                    </td>
                </tr>
                <tr>
                    <td>Full name</td>
                    <td><?php echo $row->fullname ?></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td><?php echo $gender[$row->gender] ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?php echo $row->address ?></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><?php echo $row->phone ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?php echo $row->email ?></td>
                </tr>
                <tr>
                    <td>Privilege Group</td>
                    <td><?php echo $group_arr[$row->priv_group_id] ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?php echo $status[$row->user_status] ?></td>
                </tr>
            </table>
            <?php } ?>
        </div>
        <div class="box-footer">
            <?php $back = 'users/go/'.$this->asik_model->category_masterdata.$this->asik_model->master_04.'/'; ?>
            <a class="btn btn-default" href="<?php echo site_url($back) ?>">Back</a>
        </div>
    </div>
</div>
<!-- /.panel-body -->