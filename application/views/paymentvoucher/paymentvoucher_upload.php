<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="pv_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals  

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('paymentvoucher/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="input_pv_id"]').val(data.pv_id);
                $('[name="input_pv_date"]').val(data.pv_date);
                $('[name="input_pv_number"]').val(data.pv_number);
                $('[name="input_description"]').val(data.description);
                $('[name="input_total"]').val(data.total);
                $('[name="input_admin_fee_org"]').val(data.admin_fee);
                if (data.trans_id == 0){
                    $('[name="input_admin_fee"]').val(data.admin_fee);
                    $('[name="input_admin_fee"]').prop('disabled', true);
                } else {
                    $('[name="input_admin_fee"]').val(data.admin_fee);
                }
                
                $('[name="input_bank_name_to"]').val(data.bank_name_to);
                $('[name="input_bank_account_name_to"]').val(data.bank_account_name_to);
                $('[name="input_bank_account_num_to"]').val(data.bank_account_num_to);
                $('[name="input_received_name"]').val(data.received_name);
                $('[name="input_trans_id"]').val(data.trans_id);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Data PV'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function save()
    {
        var url;
        url = "<?php echo site_url('paymentvoucher/update_pv_edit') ?>";

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }

</script>

<!-- Bootstrap modal -->


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart('paymentvoucher/do_upload', $attribut); 
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
                <input type="hidden" name="pv_id" value="">
                <input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id; ?>">
                
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


<!---- Payment voucher Edit --->
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Payment Voucher Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php
                        echo $input_pv_id;
                        echo $input_pv_date;
                        echo $input_pv_number;
                        echo $input_description;
                        echo $input_total;
                        echo $input_admin_fee_org;
                        echo $input_admin_fee;
                        echo $input_bank_name_to;
                        echo $input_bank_account_name_to;
                        echo $input_bank_account_num_to;
                        echo $input_received_name;   
                        echo $input_trans_id;
                        ?>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->