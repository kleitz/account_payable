<?php 
$actionlink = 'paymentprocess/ppgeneral/20191231214301/';
if ($param_pp_id == 0){
    $actionlink = 'paymentprocess/ppgeneral/20191231214301/0/0/1/';
} else {
    $actionlink = 'paymentprocess/ppgeneral/20191231214301/'.$param_pp_id.'/0/2/';
}
echo form_open($actionlink); 
?>
<div class="col-md-12">
    <div class="box box-success">
        <div class="box-header with-border">
            <h4 class="text-green">Header of PP</h4>
            <?php 
            echo $pp_id; 
            echo $pp_number;
            ?>
            <table width="100%" class="table">
                <tr>
                    <td>
                        <?php echo $pp_number_disabled; ?>
                    </td>
                    <td>
                        <?php echo $pp_date; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $pp_title; ?>
                    </td>
                    <td>
                        <?php echo $branch_id; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $payment_mode; ?>
                    </td>
                    <td>
                        <?php 
                        echo $cash_request_id;
                        echo $cash_request_disabled;  
                        ?>
                    </td>
                </tr>
                <?php
                echo $employee_id;
                if ($employee_oid != 0){
                    echo '<tr>
                        <td>
                        '.$employee_disabled.'
                        </td>
                        <td>&nbsp;</td>
                    </tr>';
                }
                ?>
                
            </table>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="box box-default">
        <div class="box-header with-border">
            <h4>Detail of PP</h4>
            <?php
            if ($pp_status < 2){
            
            echo '<input type="hidden" name="pp_encrypt" value="">';
            echo '<input type="hidden" name="pp_detail_id" value="">';
            echo '<table class="table table-bordered">';
            echo '<tr>
                <th>#</th>
                <th><input class="form-control input-sm" type="text" placeholder="Auto" disabled></th>
                <th><input name="job_order" class="form-control input-sm" type="text" placeholder="Ref. No."></th>
                <th><input name="description" class="form-control input-sm" type="text" placeholder="Description.."></th>
                <th><input name="unit" class="form-control input-sm numberic" type="text" placeholder="Unit"></th>
                <th><input name="price" class="form-control input-sm currency" type="text" placeholder="Price"></th>
                <th><input class="form-control input-sm" type="text" placeholder="Auto" disabled></th>
                <th class="text-right"><input type="submit" class="btn btn-primary btn-sm" value="Save"></th>
            </tr>';
            echo '</table>';
            }
            ?>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Dept/Act</th>
                        <th>Ref. No</th>
                        <th>Description</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <?php
                if ($detail->num_rows()!=0){
                    $no = 1;
                    $total_all = 0;
                    echo '<tbody>';
                    foreach ($detail->result() as $val) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$val->act_title.'</td>';
                        echo '<td>'.$val->job_order.'</td>';
                        echo '<td>'.$val->description.'</td>';
                        echo '<td>'.$val->unit.'</td>';
                        echo '<td class="text-right">'.number_format($val->price).'</td>';
                        echo '<td class="text-right">'.number_format($val->total).'</td>';
                        echo '<td class="text-right">';
                        if ($pp_status < 2){
                        //echo '<button class="btn btn-success btn-sm" onclick="edit_data('.$val->pp_detail_id.')"><i class="fa fa-edit"></i></button> ';
                        echo '<a class="btn btn-danger btn-sm" href="'. site_url('paymentprocess/ppgeneral/20191231214301/'.$param_pp_id.'/0/2/'.$val->pp_detail_id).'"><i class="fa fa-trash"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                        $total_all += $val->total;
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>No</th>
                        <th>Dept/Act</th>
                        <th>Ref. No</th>
                        <th>Description</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th class="text-right">'. number_format($total_all).'</th>
                        <th class="text-right">Action</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
                
            </table>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="box box-default">
        <div class="box-header with-border">
            <?php
            if ($param_pp_id == 0){
                echo 'Footer';
            } else {
            ?>
            <div class="col-md-3">
                <h4>Prepared By</h4>
                <div><?php echo $this->ppdetail_model->get_user_by_id($prepare_by) ?></div>
            </div>
            <div class="col-md-3">
                <h4>Cross Check By</h4>
                <div>
                <?php
                if ($cross_check_by == 0 && $pp_status == 0){
                    if ($action_cross_check){
                        echo '<a href="'. site_url($cross_check_link).'" class="btn btn-success btn-sm">Cross Check</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Cross Check</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($cross_check_by);
                }
                ?>
                </div>
            </div>
            <div class="col-md-3">
                <h4>Checked By</h4>
                <div>
                <?php
                if ($checked_by == 0 && $pp_status == 1){
                    if ($action_checked){
                        echo '<a href="'. site_url($check_link).'" class="btn btn-success btn-sm">Check</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Check</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($checked_by);
                }
                ?>
                </div>
            </div>
            <div class="col-md-3">
                <h4>Approved By</h4>
                <div>
                <?php
                if ($approved_by == 0 && ($pp_status == 1 || $pp_status == 2)){
                    if ($action_approved){
                        echo '<a href="'.site_url($approve_link).'" class="btn btn-success btn-sm">Approve</a>';
                    } else {
                        echo '<button class="btn btn-default btn-sm">Approve</button>';
                    }
                } else {
                    echo $this->ppdetail_model->get_user_by_id($approved_by);
                }
                ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo form_close();  ?>