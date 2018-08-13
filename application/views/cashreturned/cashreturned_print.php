<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cash Return</title>
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
                padding: 12px;
            }
            #info {
                padding: 3px 0px;
            }
        </style>
    </head>
    <body onload="printDiv()">
        <?php
        echo '<table class="tbl_noborder">';
        echo '<tr>';
            echo '<td>Cash Return ID</td>';
            echo '<td>' . $return_number . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Date</td>';
            echo '<td>' . $return_date . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Cash From</td>';
            echo '<td>' . $account_from . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Received By</td>';
            echo '<td>' . $account_to . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Amount</td>';
            echo '<td>' . $amount . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Return Mode</td>';
            echo '<td>' . $return_mode . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Outlet</td>';
            echo '<td>' . $branch_name . '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>Remark</td>';
            echo '<td>' . $remark . '</td>';
        echo '</tr>';
        echo '</table>';
        ?>
    </body>
</html>