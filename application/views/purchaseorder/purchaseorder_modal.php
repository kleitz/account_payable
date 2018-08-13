<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Credit Invoice'); // Set Title to Bootstrap modal title
    }
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="po_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('purchaseorder/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="po_id"]').val(data.po_id);
                $('[name="supplier_id"]').val(data.supplier_id);
                $('[name="po_number"]').val(data.po_number);
                $('[name="po_date"]').val(data.po_date);
                $('[name="invoice"]').val(data.invoice);
                $('[name="invoice_date"]').val(data.invoice_date);
                $('[name="receive_no"]').val(data.receive_no);
                $('[name="receive_date"]').val(data.receive_date);
                $('[name="branch_id"]').val(data.branch_id);
                $('[name="description"]').val(data.description);
                
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Credit Invoice'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('purchaseorder/purchaseorder_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('purchaseorder/purchaseorder_update') ?>";
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
                window.location = "<?php echo site_url('purchaseorder/go/'.$pagecode.'/3/') ?>";
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
                url: "<?php echo site_url('purchaseorder/purchaseorder_delete') ?>/" + id,
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
                <h4 class="modal-title">Credit Invoice Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <div class="form-body">
                        
                        <?php
                        echo $po_id;
                        echo $supplier_id;
                        echo $po_number;
                        echo $po_date;
                        echo $invoice;
                        echo $invoice_date;
                        echo $receive_no;
                        echo $receive_date;
                        echo $branch_id;
                        echo $description;
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
        echo form_open_multipart('purchaseorder/do_upload', $attribut); 
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
                <input type="hidden" name="po_id" value="">
                
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
<!-- End Bootstrap modal -->