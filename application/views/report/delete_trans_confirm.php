<script type="text/javascript">
    var save_method; //for save method string
    var table;
    
    function add_confirm(id)
    {
        save_method = 'add_confirm';
        $('#remark_form')[0].reset(); // reset form on modals
        $('[name="trans_id"]').val(id);
        $('#modal_remark_form').modal('show'); // show bootstrap modal
        
    }

    function save_confirm()
    {
        var url;
        if (save_method == 'add_confirm')
        {
            url = "<?php echo site_url('report/delete_confirm') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#remark_form').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_remark_form').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }

</script>
<!-- ADD REMARK -->
<div class="modal fade" id="modal_remark_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Confirm</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="remark_form">
                    <div class="form-body">

                        <?php
                        echo $trans_id;
                        echo $remark;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_confirm()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->