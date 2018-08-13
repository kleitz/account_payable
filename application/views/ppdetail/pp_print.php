<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment Process</title>
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
            .tbl {
                border-spacing: 0;
                border-collapse: collapse;
            }
            
            .tbl th, td {
                border:1px solid #474747;
                padding: 5px 9px;
            }
            #info {
                padding: 3px 0px;
            }
        </style>
    </head>
    <body onload="printDiv()">
        <div class="col-md-9" id="printableArea">
            <div class="box box-primary">
                <div>
                    <table class="tbl_noborder" width="100%">
                        <tr>
                            <td>
                                <div class="pull-left">
                                    <img src="<?php echo base_url(); ?>assets/dist/img/queens-new.png" alt="">
                                    <div id="info">Tanggal (<i>Date</i>) : <?php echo $pp_date ?></div>
                                    <div id="info">Jatuh Tempo (<i>Due Date</i>) : <?php echo $pp_due_date ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="pull-right" style="text-align: right">
                                    <div id="info">No. PP : <?php echo $pp_number ?></div>
                                    <div id="info"><?php echo '<strong>' . $header1 . '</strong>'; ?></div>
                                    <div id="info">(<i><?php echo $header2; ?></i>)</div>
                                    <div id="info">No. Kwitansi (<i>Payment Voucher</i>) : <?php echo $pv_number ?></div>
                                    <div id="info"><?php echo $pp_title ?></div>
                                </div>
                            </td>
                        </tr>
                    </table>                    
                </div>
                <div>
                    <table class="tbl" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Dept/Act</th>
                                <th>Pesanan (<i>Job Order</i>)</th>
                                <th>Deskripsi (<i>Description</i>)</th>
                                <th>Unit</th>
                                <th>Harga (<i>Price</i>)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <?php
                        if ($detail->num_rows()!=0){
                            $no = 1;
                            $total_all = 0;
                            echo '<tbody>';
                            foreach ($detail->result() as $val) {
                                $re_desc = str_replace("\n", '<br>', $val->description);
                                echo '<tr>';
                                echo '<td>'.$no.'</td>';
                                echo '<td>'.$val->act_title.'</td>';
                                echo '<td>'.$val->job_order.'</td>';
                                echo '<td>'.$re_desc.'</td>';
                                echo '<td>'.$val->unit.'</td>';
                                echo '<td align="right">'.number_format($val->price).'</td>';
                                echo '<td align="right">'.number_format($val->total).'</td>';
                                echo '</tr>';
                                $total_all += $val->total;
                                $no++;
                            }
                            echo '<tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>Total</th>
                                <th align="right">'. number_format($total_all).'</th>';
                            echo '</tr>';
                            
                            echo '</tbody>';
                            
                        }
                        ?>
                    </table>
                </div>
                <p>&nbsp;</p>
                <div>
                    <table class="tbl_noborder" width="100%">
                        <tr>
                            <th>Prepared By</th>
                            <th>Checked By</th>
                            <th>Approved By</th>
                        </tr>
                        <tr>
                            <td align="center"><?php echo $prepared; ?></td>
                            <td align="center"><?php echo $checked; ?></td>
                            <td align="center"><?php echo $approved; ?></td>
                        </tr>
                    </table>                    
                </div>
            </div>
        </div>
    </body>
</html>
