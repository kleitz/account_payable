<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Purchase Order'); // Set Title to Bootstrap modal title
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
                $('[name="po_date"]').val(data.po_date);
                $('[name="po_number"]').val(data.po_number);
                $('[name="branch_id"]').val(data.branch_id);
                $('[name="supplier_id"]').val(data.supplier_id);
                $('[name="invoice"]').val(data.invoice);
                $('[name="description"]').val(data.description);
                $('[name="due_date"]').val(data.due_date);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Purchase Order'); // Set title to Bootstrap modal title

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
        <?php echo form_open_multipart('specification/do_upload'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Purchase Order Form</h4>
            </div>
            <div class="modal-body form">
                <?php 
                
                echo '<input type="hidden" name="po_id" value="">';
                ?>
                <div class="form-group">
                    <label class="control-label">File image</label>
                    <input type="file" name="userfile" class="filestyle" data-icon="false" data-buttonname="btn-default">
                </div>

                

                
            </div>
            <div class="modal-footer">
                <input class="btn btn-success" type="submit" value="Upload" />
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->