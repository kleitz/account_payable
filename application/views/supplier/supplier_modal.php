<script type="text/javascript">
    var save_method; //for save method string
    var table;

    function add_data()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Supplier'); // Set Title to Bootstrap modal title
    }

    function edit_data(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('supplier/ajax_edit/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="supplier_id"]').val(data.supplier_id);
                $('[name="supplier_name"]').val(data.supplier_name);
                $('[name="account_name"]').val(data.account_name);
                $('[name="account_number"]').val(data.account_number);
                $('[name="bank_name"]').val(data.bank_name);
                $('[name="supplier_type"]').val(data.supplier_type);
                $('[name="address"]').val(data.address);
                $('[name="email"]').val(data.email);
                $('[name="phone"]').val(data.phone);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Supplier'); // Set title to Bootstrap modal title

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
            url = "<?php echo site_url('supplier/supplier_add') ?>";
        }
        else
        {
            url = "<?php echo site_url('supplier/supplier_update') ?>";
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
                url: "<?php echo site_url('supplier/supplier_delete') ?>/" + id,
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
                <h4 class="modal-title">Supplier Form</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php
                        echo $supplier_id;
                        echo $supplier_name;
                        echo $account_name;
                        echo $account_number;
                        echo $bank_name;
                        echo $supplier_type;
                        echo $address;
                        echo $email;
                        echo $phone;
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