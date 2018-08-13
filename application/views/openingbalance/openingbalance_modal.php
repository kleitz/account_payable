<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Receive in Bank'); // Set Title to Bootstrap modal title
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('openingbalance/ajax_view/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {                        
                $('[name="opening_balance_id"]').val(data.opening_balance_id);
                $('[name="opening_balance_number"]').val(data.opening_balance_number);
                $('[name="number_disable"]').val(data.opening_balance_number);
                $('[name="opening_balance_date"]').val(data.opening_balance_date);
                $('[name="description"]').val(data.description);
                $('[name="amount"]').val(data.amount);
                $('[name="branch_id"]').val(data.branch_id);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Receive in Bank'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('openingbalance/opening_balance_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('openingbalance/opening_balance_update') ?>";
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
                alert('Error adding / update data. Check: Trans date or value of field');
            }
        });
    }

    function delete_data(id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('openingbalance/opening_balance_delete') ?>/" + id,
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
    
    function upload_file(id)
    {
        save_method = 'upload';
        $('#form_up')[0].reset(); // reset form on modals
        $('#modal_form_up').modal('show'); // show bootstrap modal
        $('[name="opening_balance_id"]').val(id);
        $('.modal-title-up').text('File Upload'); // Set Title to Bootstrap modal title
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
                <h4 class="modal-title">Receive in Bank Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php
                        echo $opening_balance_id;
                        echo $opening_balance_number;
                        echo $number_disable;
                        echo $opening_balance_date;
                        echo $description;
                        echo $amount;
                        echo $branch_id;
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_up" role="dialog">
    <div class="modal-dialog">
        <?php 
        $attribut = array('id'=>'form_up', 'class'=>'form-horizontal');
        echo form_open_multipart('openingbalance/do_upload', $attribut); 
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
                <input type="hidden" name="opening_balance_id" value="">
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