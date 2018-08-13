<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data(pp_id)
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $.ajax({
            url: "<?php echo site_url('trans/trans06_ajax_pp/') ?>/" + pp_id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="pp_id"]').val(pp_id);
                $('[name="description"]').val(data.description);
                $('[name="total"]').val(data.total);
                $('[name="branch_id"]').val(data.branch_id);
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Payment Voucher'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('trans/trans06_ajax_view/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {                        
                $('[name="trans_id"]').val(data.trans_id);
                $('[name="pv_id"]').val(data.pv_id);
                $('[name="pv_date"]').val(data.pv_date);
                $('[name="pv_number"]').val(data.pv_number);
                $('[name="pp_id"]').val(data.pp_id);
                $('[name="description"]').val(data.description);
                $('[name="admin_fee"]').val(data.admin_fee);
                $('[name="payment_mode"]').val(data.payment_mode);
                $('[name="received_by"]').val(data.received_by);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Payment Voucher'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('trans/trans06_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('trans/trans06_update') ?>";
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

    function delete_data(id, pp_id)
    {
        if (confirm('Are you sure delete this data?'))
        {
            // ajax delete data from database
            $.ajax({
                url: "<?php echo site_url('trans/trans06_delete') ?>/" + id + "/" + pp_id,
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
                <h4 class="modal-title">Payment Voucher Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <div class="form-body">

                        <?php
                        echo $trans_id;
                        echo $branch_id;
                        echo $pv_id;
                        echo $pv_date;
                        echo $pv_number;
                        echo $pp_id;
                        echo $description;
                        echo $admin_fee;
                        echo $payment_mode;
                        echo $received_by;
                        echo $total;
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