<?php
    
    if (validation_errors() != ""){ ?>
    <div class="col-md-8">
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Peringatan!</h4>
            <?php echo validation_errors(); ?>
        </div>
    </div>
    <?php }    

    echo form_open('paymentvoucher/get_validation/'.$pagecode.'/'.$encrypt_id.'/'.$pvnumber); 
    echo $pv_id;
    echo $pv_number;
    echo $pp_id;
    echo $cash_request_id;
    echo $cash_from_cashrequest;
    echo $branch_id;
    echo $tipe;
    
    if ($confirm != 0){
?>
<div class="col-md-8">
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Peringatan!</h4>
        <div><?php echo $confirm_text ?></div>
        <div>
            <input type="hidden" name="confirm_go" value="1">
            <button type="submit" name="submit" class="btn btn-warning btn-sm">Yes</button>&nbsp;
            <button type="button" class="btn btn-warning btn-sm" data-dismiss="alert" aria-hidden="true">No</button>
        </div>
    </div>
</div>
    <?php } ?>
<div class="col-md-8">
    <div class="box <?= $box ?>">
        <div class="box-header with-border">
            <div class="col-sm-6">
                <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
            </div>
            <div class="col-sm-6">
                <table>
                    <tr>
                        <td>No. Kwitansi </td>
                        <td> : <?php echo $pvnumber; ?></td>
                    </tr>
                    <tr>
                        <td>No. <?php echo $caption_number ?> </td>
                        <td> : <?php echo $nota_number; ?></td>
                    </tr>
                    <tr>
                        <td>Outlet</td>
                        <td>: <?php echo $branch_opt[$outlet] ?></td>
                    </tr>
                </table>
                <h3>KWITANSI <i>(Payment Voucher)</i></h3>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <td colspan="2">
                        Bayar ke (Pay To): <strong style="font-size: 18px"><?php echo $pv_title ?></strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $description ?>
                    </td>
                    <td>
                        <?php
                        echo '<div class="form-group">';
                        echo '<div class="row">';
                        echo '<div class="col-md-12">';
                        echo '<label style="color:#575757">Amount</label>';
                        echo '<input type="text" class="form-control" value="'.number_format($total).'" disabled>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo $admin_fee;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php 
                        //if ($val_cash_from_cashrequest == 0){
                            echo '<div class="form-group">';
                            echo '<div class="row">';
                            echo '<div class="col-md-12">';
                            echo '<label style="color:#575757">Payment mode</label>';
                            echo '<input type="text" class="form-control" value="'.$mode_opt[$payment_mode].'" disabled>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        //}
                        //if ($val_cash_from_cashrequest == 0){
                            switch ($payment_mode) {
                                case 0:
                                    echo $bank_id;
                                    ///echo $account_id;
                                    break;
                                case 1:
                                    echo $bank_id;
                                    break;
                                case 2:
                                    echo $bank_id;
                                    break;
                                case 3:
                                    echo $bank_id;
                                    echo $bank_cek_from;
                                    break;
                                case 4:
                                    echo $bank_id;
                                    echo $bank_bg_from;
                                    break;
                            }
                        //}
                        if ($val_cash_from_cashrequest != 0){
                            if ($val_employee_id == 0){
                                echo '<span class="label label-success">Nota Come</span>';
                            } else {
                                echo '<span class="label label-success">Return Cash to Employee</span>';
                            }
                        }
                        ?>
                       
                    </td>
                    <td>
                        <?php
                        if ($payment_mode != 0 && $val_cash_request_id == 0){
                            echo $bank_name_to;
                            echo $bank_account_name_to;
                            echo $bank_account_num_to;
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="box-body with-border">
            <div class="col-sm-6">
                <!--
                <div><strong>Disiapkan Oleh (Paid by)</strong></div>
                <span class="label label-warning">To be Paid</span>
                -->
                <?php echo $pv_date; ?>
            </div>
            <div class="col-sm-6">
                <?= $received_name ?>
            </div>
        </div>
        
        <div class="box-footer">
            <button type="submit" name="submit" class="btn btn-primary">Save</button>
            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode) ?>" class="btn btn-default">Back</a>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file()">Upload file</button>
        </div>
        <div class="box-body">
            Hanya bisa dilakukan setelah proses simpan
        </div>
    </div>
</div>
<!---------->