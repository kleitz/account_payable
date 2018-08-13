<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment Voucher</title>
        <script type="text/javascript">
            function printDiv() {
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = originalContents;

                window.print();
            }
        </script>
        <style type="text/css">
            body {
                font-family: Arial, Helvetica, sans-serif;
            }
            .tbl_noborder {}
            .tbl_noborder td {
                padding: 12px;
                border: 0;
            }
            
            .tbl_noborder1 {}
            .tbl_noborder1 td {
                padding: 3px;
                border: 0;
            }
            .tbl {
                border-spacing: 0;
                border-collapse: collapse;
            }
            
            .tbl th, td {
                border:1px solid #474747;
                padding: 12px;
            }
            #info {
                padding: 3px 0px;
            }
        </style>
    </head>
    <body onload="printDiv()">
        <div class="box box-primary">
            <div class="box-header with-border">
                <table class="tbl_noborder" width="100%">
                    <tr>
                        <td>
                            <div class="col-sm-6">
                                <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
                            </div>
                        </td>
                        <td align="right">
                            <div class="col-sm-6">
                                <div id="info">No. Kwitansi : <?php echo $pv_number; ?></div>
                                <div id="info">No. <?php echo $caption_number ?> : <?php echo $nota_from; ?></div>
                                <div id="info">Outlet : <?php echo $outlet; ?></div>
                                
                                <h3>KWITANSI <i>(Payment Voucher)</i></h3>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="box-body">
                <table class="tbl" width="100%">
                    <tr>
                        <td colspan="2">
                            Bayar ke (<i>Pay To</i>): <strong style="font-size: 18px"><?php echo $pv_title ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div id="info">Deskripsi (<i>Description</i>)</div>
                            <?= $description ?>
                        </td>
                        <td valign="top">
                            <?php
                            $totalall = $total + $admin_fee;
                            ?>
                            <table class="tbl_noborder1">
                                <tr>
                                    <td style="padding-right: 21px">Amount</td>
                                    <td align='right'><?php echo number_format($total) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-right: 21px">Admin Fee</td>
                                    <td align='right'><?php echo number_format($admin_fee) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 3px; border-bottom: 1px solid #575757;"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align='right'  style="padding-top: 3px;"><?php echo number_format($totalall) ?></td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <?php 
                            echo '<div id="info">Payment mode : '.$payment_mode_text.'</div>';
                            switch ($payment_mode) {
                                case 0:
                                    echo $account_name;
                                    break;
                                case 1:
                                    echo $bank_outlet;
                                    break;
                                case 2:
                                    echo $bank_outlet;
                                    break;
                                case 3:
                                    echo $bank_cek_from;
                                    break;
                                case 4:
                                    echo $bank_bg_from;
                                    break;
                            }
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                            if ($payment_mode != 0){
                                echo '<div id="info">BANK : '.$bank_name_to.'</div>';
                                echo '<div id="info">Nama Rekening : '.$bank_account_name_to.'</div>';
                                echo '<div id="info">No. Rekening : '.$bank_account_num_to.'</div>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="box-body with-border">
                <p>&nbsp;</p>
                <table class="tbl_noborder" width="100%">
                    <tr>
                        <td align="center">
                            <div class="col-sm-6">
                                <div><strong>Disiapkan Oleh (<i>Paid by</i>)</strong></div>
                                <?php
                                if ($paid_by != 0){
                                    echo $this->payment_voucher_model->get_fullname_by_id($paid_by);
                                }
                                ?>

                                <div><?= $this->general_model->get_string_date($pv_date) ?></div>
                            </div>
                        </td>
                        <td align="center">
                            <div class="col-sm-6">
                                <div><strong>Diterima Oleh (<i>Received by</i>)</strong></div>
                                <?= $received_name ?>
                                <div><?= $this->general_model->get_string_date($pv_date) ?></div>
                            </div>
                        </td>
                    </tr>
                </table>
                
                
            </div>
        </div>
    </body>
</html>