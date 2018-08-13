<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data(cash_receive_id)
    {        
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('cashreturned/ajax_get_cash_receive/') ?>/" + cash_receive_id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                var amount = 0;
                $('[name="cash_receive_id"]').val(cash_receive_id);
                //$('[name="cash_return_date"]').val(data.cash_receive_date); 
                $('[name="account_from"]').val(data.account_to); // received_from ditukar dg received_by
                $('[name="account_to"]').val(data.account_from);
                if (data.paid_off != 0){
                    amount = data.amount - data.paid_off;
                } else {
                    amount = data.amount;
                }
                $('[name="amount"]').val(amount);
                $('[name="branch_id"]').val(data.branch_id);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded

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
            url: "<?php echo site_url('cashreturned/ajax_view/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="cash_receive_id"]').val(data.cash_receive_id);
                $('[name="cash_return_date"]').val(data.cash_return_date);
                $('[name="account_from"]').val(data.account_from); // received_from ditukar dg received_by
                $('[name="account_to"]').val(data.account_to);
                $('[name="amount"]').val(data.amount);
                $('[name="return_mode"]').val(data.return_mode);
                $('[name="remark"]').val(data.remark);
                $('[name="branch_id"]').val(data.branch_id);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Return Cash'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function edit_date(id)
    {
        save_method = 'update_date';
        $('#form_date')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('cashreturned/ajax_view/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="cash_return_id"]').val(data.cash_return_id);
                $('[name="trans_id"]').val(data.trans_id);
                $('[name="cash_return_date"]').val(data.cash_return_date);

                $('#modal_form_date').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Date'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('cashreturned/insert_data') ?>";
        }
        else
        {
            url = "<?php echo site_url('cashreturned/update_data') ?>";
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
    
    function save_date()
    {
        var url;
        if (save_method == 'update_date')
        {
            url = "<?php echo site_url('cashreturned/update_date') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form_date').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form_date').modal('hide');
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
                url: "<?php echo site_url('cashreturned/delete_data') ?>/" + id,
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
                <h4 class="modal-title">Cash Returned Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php                        
                        echo $cash_return_id;
                        echo $cash_receive_id;
                        echo $cash_return_date;
                        echo $account_from;
                        echo $account_to;
                        echo $amount;
                        echo $return_mode;
                        echo $remark;
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
<div class="modal fade" id="modal_form_date" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Cash Returned Date Edit</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_date">
                    <div class="form-body">

                        <?php                        
                        echo $cash_return_id;
                        echo $trans_id;
                        echo $cash_return_date;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_date()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->