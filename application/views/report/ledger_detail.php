<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="form-group">
                <div class="input-group">
                    <div class="btn-group">
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/1/') ?>" class="btn btn-default">Today</a>
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/2/') ?>" class="btn btn-default">Yesterday</a>
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/3/') ?>" class="btn btn-default">This Week</a>
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/4/') ?>" class="btn btn-default">This Month</a>
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/5/') ?>" class="btn btn-default">Last Month</a>
                        <a href="<?php echo site_url('report/ledger_detail/'.$account_id.'/6/') ?>" class="btn btn-default">Up to Today</a>
                    </div>
                </div>  
            </div>
        </div>
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('report/ledger_detail/'.$account_id) ?>">
                <div class="form-group">

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?php echo $start_date ?>" placeholder="Date from">
                    </div>

                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?php echo $end_date ?>" placeholder="Date to">
                    </div>
                </div>
                <div class="form-group">
                    
                        <select name="pp_type" class="form-control">
                            <?php
                            foreach ($pp_type_arr as $key => $value) {
                                if ($pp_type == $key){
                                    echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                                } else {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                
                            }
                            ?>
                        </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
            </form>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->general_model->get_string_date_ver2($previous_date) ?></th>
                        <th colspan="5">This is previous balance </th>
                        <th class="text-right"><?php echo number_format($previous_balance, 2) ?></th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Remark</th>
                        <th>PP Number</th>
                        <th>Title</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    $debit_total = 0;
                    $credit_total = 0;
                    $balance = $previous_balance;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $remark = '-';
                        if (isset($val->remark)){
                            $remark = $val->remark;
                        }
                        $debcre = $val->debit + $val->credit;
                        if ($val->debit == 0){
                            $balance = $balance - $debcre;
                        } else {
                            $balance = $balance + $debcre;
                        }
                        /** LINK **/
                        $link = $val->description;
                        if (isset($val->pv_number)){
                            $substr = substr($val->pv_number, 0, 2);
                             switch ($substr) {
                                case 'PV':
                                    $pv_enc = $this->report_model->get_pv_id($val->pv_number);
                                    $link = '<a href="'. site_url('paymentvoucher/detail/20191231214302/').$pv_enc.'" target="_blank">'.$val->description.'</a>';
                                    break;
                                case 'OP':
                                    //$pp_type = 1;
                                    break;
                                case 'RB':
                                    $rb_id = $this->report_model->get_rb_id($val->pv_number);
                                    $link = '<a href="'. site_url('receiveinbank/detail/20191121214305/'.$rb_id).'" target="_blank">'.$val->description. '</a>';
                                    break;
                                case 'RC':
                                    $rc_id = $this->report_model->get_rc_id($val->pv_number);
                                    $link = '<a href="'. site_url('cashreceived/detail/20191121214303/'.$rc_id).'" target="_blank">'.$val->description.'</a>';
                                    break;
                                case 'RT':
                                    $rt_id = $this->report_model->get_rt_id($val->pv_number);
                                    $link = '<a href="'. site_url('cashreturned/detail/20191121214304/'.$rt_id).'" target="_blank">'.$val->description.'</a>';
                                    break;
                            }
                            
                        }
                        ////////////////////////////////////////////////////////
                        $ppnumber = '-';
                        if(isset($pp_numbers[$val->pv_number])){
                            $ppnumber = $pp_numbers[$val->pv_number];
                        } else {
                            $ppnumber = $val->pv_number;
                        }
                        if ($pp_type == 'All'){
                            echo '<tr>';
                            echo '<td>'.$no.'</td>';
                            echo '<td>'.$this->general_model->get_string_date_ver2($val->trans_date).'</td>';
                            echo '<td>'.$remark.'</td>';
                            echo '<td>'.$ppnumber.'</td>';
                            echo '<td>'.$link.'</td>';
                            echo '<td class="text-right">'.number_format($val->debit, 2).'</td>';
                            echo '<td class="text-right">'.number_format($val->credit, 2).'</td>';
                            echo '<td class="text-right">'.number_format($balance, 2).'</td>';
                            echo '</tr>';
                            $no++;
                        } else {
                            $pp_num = substr($ppnumber, 0, 2);
                            if ($pp_type == $pp_num){
                                echo '<tr>';
                                echo '<td>'.$no.'</td>';
                                echo '<td>'.$this->general_model->get_string_date_ver2($val->trans_date).'</td>';
                                echo '<td>'.$remark.'</td>';
                                echo '<td>'.$ppnumber.'</td>';
                                echo '<td>'.$link.'</td>';
                                echo '<td class="text-right">'.number_format($val->debit, 2).'</td>';
                                echo '<td class="text-right">'.number_format($val->credit, 2).'</td>';
                                echo '<td class="text-right">'.number_format($balance, 2).'</td>';
                                echo '</tr>';
                                $no++;
                            }
                        }
                        ////////////////////////////////////////////////////////
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>'.$this->general_model->get_string_date_ver2($end_date).'</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Next balance without filter pp type</th>
                        <th class="text-right">&nbsp;</th>
                        <th class="text-right">&nbsp;</th>
                        <th class="text-right">'. number_format($next_balance, 2).'</th>
                    </tr>';
                    echo '</tfoot>';
                }
                ?>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="col-xs-4">
                <p><strong>Checked By</strong></p>
                <?php
                if ($checked_name == '0'){
                    if ($start_date == ''){
                        echo '<div><a href="#" class="btn btn-sm btn-default">Check</a></div>';
                    } else {
                        echo '<div><a href="'. site_url($url_module.'/action_checked/'.$start_date.'/'.$end_date.'/'.$report_type.'/0/'.$account_id).'" class="btn btn-sm btn-warning">Check</a></div>';
                    }
                    
                } else {
                    echo '<div>'.$checked_name.'</div>';
                }
                ?>                            
            </div>
            <div class="col-xs-4">
                <p><strong>Approved By</strong></p>
                <?php
                if ($approved_name == '0'){
                    if ($start_date == ''){
                        echo '<div><a href="#" class="btn btn-sm btn-default">Approve</a></div>';
                    } else {
                        if ($checked_name == '0'){
                            echo '<div><a href="#" class="btn btn-sm btn-default">Approve</a></div>';
                        } else {
                            echo '<div><a href="'. site_url($url_module.'/action_approved/'.$report_id.'/'.$start_date.'/'.$end_date.'/0/'.$account_id).'" class="btn btn-sm btn-primary">Approve</a></div>';
                        }
                    }
                } else {
                    echo '<div>'.$approved_name.'</div>';
                }
                ?>
            </div>
            <div class="col-xs-4">
                <p><strong>File upload</strong></p>
                <?php
                if ($report_id != 0){
                    echo '<div>';
                    echo '<a href="#" onclick="upload_file('.$report_id.')" class="btn btn-sm btn-default">Upload</a> ';
                    echo '<a href="'. site_url('report/upload_more/'.$start_date.'/'.$end_date.'/'.$account_id).'" class="btn btn-sm btn-success">Upload more</a> ';
                    if ($report_file != ''){
                        echo '<a href="'. base_url().'assets/reportfile/'.$report_file.'" class="btn btn-sm btn-default" target="_blank">'. $report_file . '</a>';
                        
                    }
                    echo '</div>';
                }
                ?>
            </div>
            <div class="col-xs-12">&nbsp;</div>
        </div>
    </div>
    <!-- /.box -->
</div>
<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="report_file_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart($url_module.'/do_upload/0/'.$account_id, $attribut); 
        ?>
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-up">Upload Form</h4>
            </div>
            <div class="modal-body form">
                <div class="form-body">
                <input type="hidden" name="report_file_id" value="">
                
                <div class="form-group">
                    <div class="col-md-12">
                    <label class="control-label">File image</label>
                    <input type="file" name="userfile" class="filestyle" data-icon="false" data-buttonname="btn-default">
                    </div>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <input class="btn btn-success" type="submit" value="Upload" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            
        
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->