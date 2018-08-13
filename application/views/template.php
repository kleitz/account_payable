<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Account Payable</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/ionicons.min.css">
  <!-- Favicons -->
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url(); ?>assets/ico/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url(); ?>assets/ico/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url(); ?>assets/ico/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url(); ?>assets/ico/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url(); ?>assets/ico/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url(); ?>assets/ico/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url(); ?>assets/ico/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url(); ?>assets/ico/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/ico/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo base_url(); ?>assets/ico/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo base_url(); ?>assets/ico/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url(); ?>assets/ico/favicon-16x16.png">
<link rel="manifest" href="<?php echo base_url(); ?>assets/ico/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
  <!-- iCheck 
  <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
  -->
  <!-- jvectormap 
  <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  -->
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datepicker/datepicker3.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/select2.min.css">

  
  <!-- DataTables -->
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/dist/table_style.css" rel="stylesheet" type="text/css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/skin-blue.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">AP</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Account Payable</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
        <?php
        $nav_photo = 'avatar.png';
        if ($this->session->userdata('photo')!=''){
            $nav_photo = $this->session->userdata('photo');
        }
        ?>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">          
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo base_url(); ?>assets/img_profile/<?php echo $nav_photo ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $this->session->userdata('fullname'); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo base_url(); ?>assets/img_profile/<?php echo $nav_photo ?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo $this->session->userdata('fullname'); ?>
                </p>
              </li>
              <?php
                $profil_link = 'users/profile/'.$this->session->userdata('username').'/';
              ?>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo site_url($profil_link); ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo site_url('auth/logout/'); ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <?php require_once 'navigation.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?php echo $content_header; ?>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      
      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->

          <?php require_once $halaman; ?>
        
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 9.5
    </div>
    <strong>Copyright &copy; 2017 <a href="https://www.mchensolutions.com">Mchensolutions</a>.</strong> All rights
    reserved.
  </footer>

</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables JavaScript -->
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.colVis.js"></script>
<!-- numeral -->
<script src="<?php echo base_url(); ?>assets/plugins/numeral.js" type="text/javascript"></script>

<!-- Select2 -->
<script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
<!-- InputMask -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery.maskMoney.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<!-- bootstrap datepicker -->
<script src="<?php echo base_url(); ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<!-- page script -->
<script>
$(document).ready(function() {

    //Date picker
    $('.datepicker').datepicker({
      format: "yyyy-mm-dd",
      todayHighlight: true,
      autoclose: true
    });
    
    $(".textarea").wysihtml5();
    
    //Initialize Select2 Elements
    $(".select2").select2();
    /////
    //Money
    $('.currency').maskMoney({allowZero:true, prefix: 'Rp. '});
    $('.numberic').maskMoney({allowZero:true, prefix: '', thousands:'', precision:0});
    $('.float').maskMoney({allowZero:true, thousands:''});

    // datatables
    $("#datatable").DataTable({
        dom: 'Bfrtip',
       
        lengthMenu: [
            [ 10, 25, 50, 100, -1 ],
            [ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
        buttons: [
            //'copy', 'csv', 'excel', 'pdf', 'print'
            
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'copy',
		footer: true,
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'csv',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'excel',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'A4',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'print',
                text: 'Print all',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },

            {
                extend: 'pageLength'
            }
            
        ],
                    
        select: true,
        scrollX: true,
        scrollY: false,
        //sScrollX: "100%",
        sScrollXInner: "110%",
        autoWidth: false,
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        <?php echo $footer_total ?>
    });
    /* ======= Datatable2 ======= */    
    $("#datatable2").DataTable({
        dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, 100, -1 ],
            [ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
        buttons: [            
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'copy',
		footer: true,
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'csv',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'excel',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'A4',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'print',
                text: 'Print all',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pageLength'
            }
            
        ],
                    
        select: true,

        <?php echo $footer_total ?>
    });

    $(".datatable3").DataTable({
        <?php echo $footer_total ?>
    });
    
    // datatables4
    $("#datatable4").DataTable({
        dom: 'Bfrtip',
       
        lengthMenu: [
            [ 10, 25, 50, 100, -1 ],
            [ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
        buttons: [
            //'copy', 'csv', 'excel', 'pdf', 'print'
            
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'copy',
		footer: true,
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'csv',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'excel',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'A4',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'print',
                text: 'Print all',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },

            {
                extend: 'pageLength'
            }
            
        ],
                    
        select: true,
        scrollX: true,
        scrollY: false,
        //sScrollX: "100%",
        sScrollXInner: "110%",
        autoWidth: false,
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 2
        },
        <?php echo $footer_total ?>
    });
    
    // datatables
    $("#datatable5").DataTable({
        dom: 'Bfrtip',
       
        lengthMenu: [
            [25],
            ['25 rows']
        ],
        buttons: [
            
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'copy',
		footer: true,
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'csv',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'excel',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'A4',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                title: '<?php echo $datatable_title ?>',
                extend: 'print',
                text: 'Print all',
		footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }
            
        ],

        <?php echo $footer_total ?>
    });

    //select all checkboxes
    $("#select_all").change(function(){  //"select all" change 
        var status = this.checked; // "select all" checked status
        $('.checkbox').each(function(){ //iterate all listed checkbox items
            this.checked = status; //change ".checkbox" checked status
        });
    });

    $('.checkbox').change(function(){ //".checkbox" change 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(this.checked == false){ //if this item is unchecked
            $("#select_all")[0].checked = false; //change "select all" checked status to false
        }

        //check "select all" if all checkbox items are checked
        if ($('.checkbox:checked').length == $('.checkbox').length ){ 
            $("#select_all")[0].checked = true; //change "select all" checked status to true
        }
    });
    
    
});
function confirmTest(val){
    document.getElementById("conf").innerHTML="Anda memilih Petty Cash "+val ;
}

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

  

</script>

<?php 
    if (isset($show_modal)){
        require_once $show_modal;
    }
?>

</body>
</html>