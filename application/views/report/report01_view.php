<?php
if ($trans_notif->num_rows()!=0){
?>
<div class="col-xs-12">
    <div class="box box-danger">
        <div class="box-header with-border">
            <div class="text-red"><strong>Notification Delete</strong></div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Date Request</th>
                        <th>User</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    foreach ($trans_notif->result() as $value) {
                        $id = $value->trans_notif_id;
                        $transid = $value->trans_id;
                        $no++;
                        echo '<tr>';
                        echo '<td>'.$no.'</td>
                        <td>'.$value->pv_number.'</td>
                        <td>'.$value->request_date.'</td>
                        <td>'.$value->username.'</td>
                        <td>'.$value->remark.'</td>
                        <td>
                            <a href="'. site_url('report/delete_approve/'.$id.'/'.$transid).'" class="btn btn-success btn-sm">Approve</a>
                            <a href="'. site_url('report/delete_cancel/'.$id).'" class="btn btn-default btn-sm">Cancel</a>
                        </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>
<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('report/go/'.$pagecode) ?>">
                <div class="form-group">

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?= $start_date ?>" placeholder="Date from">
                    </div>

                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?= $end_date ?>" placeholder="Date to">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
                <div class="form-group">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                            <a href="<?php echo site_url('report/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                        </div>
                    </div>  
                </div>
                
            </form>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>PV No.</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">&nbsp;</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    echo '<tbody>';
                    foreach ($list->result() as $val) {
                        $link = '-';
                        if (isset($val->pv_number)){
                            $substr = substr($val->pv_number, 0, 2);
                             switch ($substr) {
                                case 'PV':
                                    $pv_enc = $this->report_model->get_pv_id($val->pv_number);
                                    $link = '<a href="'. site_url('paymentvoucher/detail/20191231214302/').$pv_enc.'" target="_blank">'.$val->pv_number.'</a>';
                                    break;
                                case 'OP':
                                    $op_id = $this->report_model->get_op_id($val->pv_number);
                                    $link = '<a href="'. site_url('openingbalance/detail/20191121214306/'.$op_id).'" target="_blank">'.$val->pv_number. '</a>';
                                    break;
                                case 'RB':
                                    $rb_id = $this->report_model->get_rb_id($val->pv_number);
                                    $link = '<a href="'. site_url('receiveinbank/detail/20191121214305/'.$rb_id).'" target="_blank">'.$val->pv_number. '</a>';
                                    break;
                                case 'RC':
                                    $rc_id = $this->report_model->get_rc_id($val->pv_number);
                                    $link = '<a href="'. site_url('cashreceived/detail/20191121214303/'.$rc_id).'" target="_blank">'.$val->pv_number.'</a>';
                                    break;
                                case 'RT':
                                    $rt_id = $this->report_model->get_rt_id($val->pv_number);
                                    $link = '<a href="'. site_url('cashreturned/detail/20191121214304/'.$rt_id).'" target="_blank">'.$val->pv_number.'</a>';
                                    break;
                            }
                            
                        }
                        
                        echo '<tr>';
                        echo '<td><div style="font-size:12px;">'.$no.'</div></td>';
                        echo '<td><div style="font-size:12px;">'.$this->general_model->get_string_date_ver2($val->trans_date).'</div></td>';
                        echo '<td><div style="font-size:12px;">'.$link.'</div></td>';
                        echo '<td><div style="font-size:12px;">'.$val->description.'</div></td>';
                        echo '<td class="text-right">'.number_format($val->amount, 2).'</td>';
                        echo '<td class="text-right"><a href="#" onclick="add_confirm('.$val->trans_id.')"><i class="fa fa-trash-o"></i></a></td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                    echo '<tfoot>';
                    echo '<tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>PV No.</th>
                        <th>Description</th>
                        <th class="text-right">amount</th>
                        <th class="text-right">&nbsp;</th>
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
                        echo '<div><a href="'. site_url($url_module.'/action_checked/'.$start_date.'/'.$end_date.'/'.$report_type.'/'.$link_code).'" class="btn btn-sm btn-warning">Check</a></div>';
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
                            echo '<div><a href="'. site_url($url_module.'/action_approved/'.$report_id.'/'.$start_date.'/'.$end_date.'/'.$link_code).'" class="btn btn-sm btn-primary">Approve</a></div>';
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
        echo form_open_multipart($url_module.'/do_upload/'.$link_code, $attribut); 
        ?>
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-up">Upload Formzz</h4>
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