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
                text-align: left;
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
                <?php echo '
                <div>
                    <table class="tbl">
                        <tr>
                        <th>Number</th>
                        <td>'.$cash_request_number.'</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>'.$cash_request_date.'</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>'.$employee_name.'</td>
                    </tr>
                    <tr>
                        <th>Outlet (Branch)</th>
                        <td>'.$branch_name.'</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <div><strong>Description</strong></div>'.
                        $description.'
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <div><strong>Remark</strong></div>'.
                        $remark.'
                        </td>
                    </tr>
                    <tr>
                        <th>Payment mode</th>
                        <td>'.$payment_mode.'</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>'.$amount.'</td>
                    </tr>';
                 echo '<tr>
                        <th>Status</th>
                        <td>'.$cash_request_status.'</td>
                    </tr>
                    </table>
                </div>';
                ?>
                <p>&nbsp;</p>
                
            </div>
            <div>
                <table class="tbl">
                    <tr>
                        <th>Prepared by</th>
                        <td><?= $prepared ?></td>
                    </tr>
                    <tr>
                        <th>Checked by</th>
                        <td><?= $checked ?></td>
                    </tr>
                    <tr>
                        <th>Approved by</th>
                        <td><?= $approved ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
