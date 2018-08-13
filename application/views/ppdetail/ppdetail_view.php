<?php
if ($info->num_rows()!=0){
    $row = $info->row();
?>
<div class="col-xs-12">
    <div class="panel panel-default">
        <!-- <div class="panel-heading">
            <h4>Invoice</h4>
        </div> -->
        <div class="panel-body">
            <div class="clearfix">
                <div class="pull-left">
                    <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
                </div>
                <div class="pull-right" style="text-align: right">
                    <div>No.PP : <?php echo $row->pp_number ?></div>
                    <?php
                    $h3 = 'PROSES PEMBAYARAN';
                    $h5 = 'PAYMENT PROCESS';
                    if($row->pp_type == 2){
                        $h3 = 'PROSES PEMBAYARAN KREDIT SUPPLIER';
                        $h5 = 'PAYMENT PROCESS FOR SUPPLIER INSTALLMENT';
                    }
                    ?>
                    <h3><?php echo $h3 ?></h3>
                    <h5>(<i><?php echo $h5 ?></i>)</h5>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">

                    <div class="pull-left m-t-30">
                        <address>
                            <strong>Tanggal (Date) : <?php echo $row->pp_date ?><br>
                            <strong>Jatuh tempo (Due date) : <?php echo $row->pp_due_date ?><br>
                        </address>
                    </div>
                    <div class="pull-right m-t-30">
                        <p><strong>Kwitansi No: </strong> <?php echo $row->pv_number ?></p>
                        <p><strong><?php echo $row->supplier_name ?></strong></p>
                    </div>
                </div><!-- end col -->
            </div>
            <!-- end row -->

            <div class="m-h-50"></div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    if ($row->pp_status < 2){
                    ?>
                    <a href="<?php echo site_url($add_item); ?>" class="btn btn-primary btn-sm">New Item</a>
                    <?php } ?>
                    <div>&nbsp;</div>
                    <div class="box-body table-responsive no-padding" style="padding-top: 5px;">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dept/Act</th>
                                    <th>Job Order</th>
                                    <th>Description</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <?php
                            $subtotal = 0;
                            if ($detail->num_rows()!=0){
                                echo '<tbody>';
                                $no = 1;
                                foreach ($detail->result() as $val) {
                                    $enc_ppd_id = $this->general_model->encrypt_value($val->pp_detail_id);
                                    $delete = 'delitem/'.$enc_ppd_id;
                                    $subtotal = $subtotal + $val->total;
                                    echo '<tr>';
                                    echo '<td>'.$no.'</td>';
                                    echo '<td>'.$row->branch_name.'</td>';
                                    echo '<td>'.$val->po_number.'</td>';
                                    echo '<td>'.$val->description.'</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '<td>'.number_format($val->total).'</td>';
                                    echo '<td>';
                                    if ($row->pp_status < 2){
                                        echo '<a href="'. site_url('ppdetail/'.$delete).'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                    $no++;
                                }
                                echo '</tbody>';
                            }
                            ?>                               
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <?php
                    $checklink = 'ppdetail/action_checked/' . $encrypt_id;
                    $approvelink = 'ppdetail/action_approved/' . $encrypt_id;
                    ?>
                    <table class="table">
                        <tr>
                            <td>Prepared by</td>
                            <td>Checked by</td>
                            <td>Approved by</td>
                            <td>Paid by</td>
                        </tr>
                        <tr>
                            <td><?php echo $this->ppdetail_model->get_user_by_id($row->prepare_by) ?></td>
                            <td>
                                <?php
                                if ($row->checked_by == 0){
                                    if ($action_checked){
                                        echo '<a href="'. site_url($checklink).'" class="btn btn-success btn-sm">Check</a>';
                                    } else {
                                        echo '<button class="btn btn-default btn-sm">Check</button>';
                                    }
                                } else {
                                    echo $this->ppdetail_model->get_user_by_id($row->checked_by);
                                }
                                ?>
                                
                            </td>
                            <td>
                                <?php
                                if ($row->approved_by == 0){
                                    if ($action_approved){
                                        echo '<a href="'.site_url($approvelink).'" class="btn btn-success btn-sm">Approve</a>';
                                    } else {
                                        echo '<button class="btn btn-default btn-sm">Approve</button>';
                                    }
                                } else {
                                    echo $this->ppdetail_model->get_user_by_id($row->approved_by);
                                }
                                ?>
                                
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-6 col-md-offset-3">
                    <h3 class="text-right">Sub-total: <?php echo number_format($subtotal) ?></h3>
                </div>
            </div>
            <hr>
            <?php
            $link = 'paymentprocess/go/' . $this->asik_model->category_configuration;
            $link .= $this->asik_model->config_03 . '/';
            ?>
            <div class="hidden-print">
                <div class="pull-right">
                    <a href="<?php echo site_url($link) ?>" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php }
