<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Cash Request'); // Set Title to Bootstrap modal title
    }
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="note_id"]').val(id);
        $('.modal-title-up').text('File Upload Cash Request'); // Set Title to Bootstrap modal title
    }
    
    function upload_pr(id)
    {
        save_method = 'upload';
        $('#form_pr')[0].reset(); // reset form on modals
        $('#modal_form_pr').modal('show'); // show bootstrap modal
        $('[name="note_id"]').val(id);
        $('.modal-title-pr').text('File Upload Purchase Request (PR)'); // Set Title to Bootstrap modal title
    }
    
    function pay(id)
    {
        save_method = 'pay';
        $('#form_tr')[0].reset(); // reset form on modals
        $('#modal_form_tr').modal('show'); // show bootstrap modal
        $('[name="note_id"]').val(id);
        $('.modal-title-up').text('Pay Cash Request'); // Set Title to Bootstrap modal title
    }
    
    function cash_return(id)
    {
        save_method = 'cash_return';
        $('#form_cash_return')[0].reset(); // reset form on modals
        $('#modal_form_cash_return').modal('show'); // show bootstrap modal
        $('[name="note_id"]').val(id);
        $('.modal-title_cash_return').text('Cash Return Form'); // Set Title to Bootstrap modal title
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('cashreqnote/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="note_id"]').val(data.note_id);
                $('[name="note_number"]').val(data.note_number);
                $('[name="note_date"]').val(data.note_date);
                $('[name="employee_id"]').val(data.employee_id);
                $('[name="branch_id"]').val(data.branch_id);
                $('[name="description"]').val(data.description);
                $('[name="amount"]').val(data.amount);
                $('[name="payment_mode"]').val(data.payment_mode);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Cash Request'); // Set title to Bootstrap modal title

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
        if (save_method == 'add')
        {
            url = "<?php echo site_url('cashreqnote/cashreqnote_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('cashreqnote/cashreqnote_update') ?>";
        }

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
    
    function save_tr()
    {
        var url;
        if (save_method == 'pay')
        {
            url = "<?php echo site_url('cashreqnote/trans_pay') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_tr').serialize(),
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
    
    function save_cash_return()
    {
        var url;
        if (save_method == 'cash_return')
        {
            url = "<?php echo site_url('cashreqnote/trans_cash_return') ?>";
        }
        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_cash_return').serialize(),
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

    function delete_data(id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('cashreqnote/cashreqnote_delete') ?>/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (data)
                {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });

        }
    }

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Cash Request Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <div class="form-body">

                        <?php
                        echo $note_id;
                        echo $note_number;
                        echo $note_date;
                        echo $employee_id;
                        echo $branch_id;
                        echo $description;
                        echo $amount;
                        echo $payment_mode;
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart('cashreqnote/do_upload', $attribut); 
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
                <input type="hidden" name="note_id" value="">
                
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_pr" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_pr', 'class'=>'form-horizontal');
        echo form_open_multipart('cashreqnote/do_upload_pr', $attribut); 
        ?>
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title-pr">Upload Form</h4>
            </div>
            <div class="modal-body form">
                <div class="form-body">
                <input type="hidden" name="note_id" value="">
                
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
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_tr" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_tr">Payment Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_tr" class="form-horizontal">
                    <div class="form-body">
                        <input type="hidden" name="note_id" value="" />
                        <?php
                        echo $account_id;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_tr()" class="btn btn-primary">Process</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modal_form_cash_return" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title_cash_return">Payment Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_cash_return" class="form-horizontal">
                    <div class="form-body">
                        <input type="hidden" name="note_id" value="" />
                        <?php
                        echo $cash_return;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_cash_return()" class="btn btn-primary">Return</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- End Bootstrap modal -->