<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="col-sm-6">
                <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
            </div>
            <div class="col-sm-6">
                <?php
                $substr = substr($nota_from, 0, 2);
                
                if ($substr == 'CR'){
                    $ppcode = $this->asik_model->category_transaction.$this->asik_model->trans_02;
                    $enc_id = $this->general_model->encrypt_value($cash_request_id);
                    $detail = 'cashrequest/cdetail/' . $ppcode.'/'.$enc_id.'/';
                } else {
                    $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    $enc_id = $this->general_model->encrypt_value($pp_id);
                    $pp_type = 0;
                    switch ($substr) {
                        case 'GN':
                            $pp_type = 0;
                            break;
                        case 'SC':
                            $pp_type = 1;
                            break;
                        case 'CE':
                            $pp_type = 2;
                            break;
                        case 'OT':
                            $pp_type = 3;
                            break;
                        case 'PR':
                            $pp_type = 4;
                            break;
                    }
                    $detail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/'.$pp_type;
                }
                $link = '<a  href="'. site_url($detail).'" target="_blank">'.$nota_from.'</a>';
                ?>
                <table>
                    <tr>
                        <td>No. Kwitansi </td>
                        <td> : <?php echo $pv_number; ?></td>
                    </tr>
                    <tr>
                        <td>No. <?php echo $caption_number ?> </td>
                        <td> : <?php echo $link; ?></td>
                    </tr>
                    <tr>
                        <td>Outlet </td>
                        <td> : <?php echo $outlet; ?></td>
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
                        <h4>Description</h4>
                        <?= $description ?>
                    </td>
                    <td>
                        <?php
                        $totalall = $total + $admin_fee;
                        ?>
                        <table>
                            <tr>
                                <td style="padding-right: 21px">Amount</td>
                                <td class="text-right"><?php echo number_format($total) ?></td>
                            </tr>
                            <tr>
                                <td style="padding-right: 21px">Admin Fee</td>
                                <td class="text-right"><?php echo number_format($admin_fee) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 3px 0px; border-bottom: 1px solid #575757;"></td>
                            </tr>
                            <tr>
                                <td colspan="2"  class="text-right" style="padding: 3px 0px;"><?php echo number_format($totalall) ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php 
                        echo '<h4>Payment mode : '.$payment_mode_text.'</h4>';
                        switch ($payment_mode) {
                            case 0:
                                echo $bank_outlet;
                                break;
                            case 1:
                                echo $bank_outlet;
                                break;
                            case 2:
                                echo $bank_outlet;
                                break;
                            case 3:
                                echo $bank_outlet.'<br>';
                                echo $bank_cek_from;
                                break;
                            case 4:
                                echo $bank_outlet.'<br>';
                                echo $bank_bg_from;
                                break;
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($payment_mode != 0){
                            echo '<table>';
                            echo '<tr>';
                            echo '<td style="padding-right:9px">BANK</td>';
                            echo '<td> : '.$bank_name_to.'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td style="padding-right:9px">Nama Rekening</td>';
                            echo '<td> : '.$bank_account_name_to.'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td style="padding-right:9px">No. Rekening</td>';
                            echo '<td> : '.$bank_account_num_to.'</td>';
                            echo '</tr>';
                            echo '</table>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="box-body with-border">
            <div class="col-sm-6">
                <div><strong>Disiapkan Oleh (Paid by)</strong></div>
                <?php
                if ($paid_by == 0){
                    if ($action_paid){
                        echo '<a class="btn btn-success btn-sm" href="'. site_url('paymentvoucher/check_paid/'.$pv_id.'/'.$encrypt_id) .'">Paid</a>';
                    } else {
                        echo '<span class="label label-default">Paid</span>';
                    }
                } else {
                    echo $this->payment_voucher_model->get_fullname_by_id($paid_by);
                }
                ?>
                
                <div><?= $this->general_model->get_string_date($pv_date) ?></div>
            </div>
            <div class="col-sm-6">
                <div><strong>Diterima Oleh (Received by)</strong></div>
                <?= $received_name ?>
                <div><?= $this->general_model->get_string_date($pv_date) ?></div>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('paymentvoucher/go/'.$pagecode) ?>" class="btn btn-default btn-sm">Back</a>
            <button onclick="edit_data(<?php echo $pv_id ?>)" type="button" name="submit" class="btn btn-default btn-sm">Edit</button>
            <?php
            if ($paid_by == 0){
                echo '<button type="submit" name="submit" class="btn btn-default btn-sm">Delete</button>';
            }
            ?>
            <a target="_blank" href="<?php echo site_url($print_link) ?>" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">
            <button class="btn btn-default btn-sm" onclick="upload_file(<?php echo $pv_id ?>)">Upload file</button>
        </div>
        <div class="box-body">
            <?php
            
            $iconfile = array(
                '.jpg' => 'fa-file-image-o',
                '.png' => 'fa-file-image-o',
                '.jpeg' => 'fa-file-image-o',
                '.pdf' => 'fa-file-pdf-o',
                '.doc' => 'fa-file-word-o',
                '.docx' => 'fa-file-word-o',
                '.xls' => 'fa-file-excel-o',
                '.xlsx' => 'fa-file-excel-o'
            );
            
            if ($paymentvoucher_file->num_rows()!=0){
                echo '<table class="table">';
                foreach ($paymentvoucher_file->result() as $val) {
                    echo '<tr>
                        <td><i class="fa '.$iconfile[$val->file_type].'"></i> | '.$val->file_name.'</td>
                        <td class="text-right">
                            <a href="'. base_url().'assets/paymentvoucher/'.$val->file_name.'" target="_blank">
                            <i class="fa fa-download"></i></a>&nbsp;|&nbsp;
                            <a href="'. site_url('paymentvoucher/delete_file/'.$val->pv_file_id.'/'.$val->file_name.'/'.$encrypt_id) .'">
                            <i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>';
                }
                echo '</table>';
            } else {
                echo 'Belum ada file.';
            }
            ?>
        </div>
    </div>
</div>