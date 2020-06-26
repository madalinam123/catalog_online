<?php
require "inc/helpers.php";
$session = return_session();
if($session["status"] == "true")
{
    $user  = return_user($session["user_id"]);
    $name = ucfirst(strtolower($user["last_name"]))." ".ucfirst(strtolower($user["first_name"]));
}
else
{
    $name = "";
}
$rank = user_type_rank();
?>
 
 <!doctype html>
    <html lang="en">
      <head>
        <title><?php echo $app_name;?> -  <?php echo return_title();?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    
        <link rel="stylesheet" href="css/bootstrap.css">
      <link rel="stylesheet" href="css/fontawesome-free/css/all.min.css">
     
      <link rel="stylesheet" href="css/main.css">
      <link rel="stylesheet" href="css/bootstrap-multiselect.css">
      <link rel="stylesheet" type="text/css" href="css/data-tables/datatables.css"/>
      <style>
      
      .multiselect-container {
        width: 100% !important;
    }
    .checkbox
    {
        color: black;
    }
     .dt-button-collection
     {
        width: 200px;
     }
     
      </style>
      </head>
      <body class="d-flex flex-column">
    <nav class="navbar navbar-expand-lg navbar-light" style=" border-bottom: 1px solid #50BDB8; background-color: #3a3d66;">
      <a class="navbar-brand" href="index.php" style="color:#c8a900;"><i class="fas fa-user-graduate"></i> <?php echo $app_name;?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto btn-group justify-content-center ">
          <li class="nav-item dropdown btn btn text-white nav-red">
            <a class="nav-link" href="../index.html" style="color: white;">Acasa <span class="sr-only"></span></a>
          </li>
          <li class="nav-item dropdown btn btn text-white nav-red">
            <a class="nav-link" href="../assets/common/history.html" style="color: white;">Istoric</span></a>
          </li>
          <li class="nav-item dropdown btn btn text-white nav-red">
            <a class="nav-link" href="../assets/common/plan.html" style="color: white;">Plan</span></a>
          </li>
           <li class="nav-item dropdown btn btn text-white nav-red">
            <a class="nav-link" href="../assets/common/contact.html" style="color: white;">Contact</span></a>
          </li>
        </ul>
        
       <ul class="navbar-nav ml-auto btn-group">
       <li class="nav-item dropdown btn nav-red text-white" style="height: 100%;">
            <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left: 5px; color: white;">
              <i class="far fa-id-badge"></i> <?php if(!empty($name)) {echo "$rank - $name";}else{echo "Contul meu";}?> <span class="badge badge-danger"></span> 
            </a>
            
            <div class="dropdown-menu" style="min-width: 300px; border-radius: 0px;">
			<div class="container">
            <form>
            <div class='row' style="padding: 10px; width: 100%;">
            <?php
            if(!empty($name))
            {
                ?>
            <a class="dropdown-item" href="index.php?p=settings"><i class="fas fa-cogs"></i> Setari Cont</a>
            <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>    
                <?php
            }
            else
            {
                ?>
            <a class="dropdown-item" href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a>        
                <?php
            }
            ?>               
            </div>
            </form>
            </li>
       </ul>
      </div>
      </div>
    </nav>
 <?php
 if($session["status"] == "true")
 {
    ?>
      <div class="container-fluid" style="width:100%;min-height: 100%; height: 100%;">
   <div class="row h-100" style="min-height: 100%; height: 100%;">
<div class="col-xs-12 col-sm-12  col-md-2 col-lg-2  p-0" style="background-color: #3a3d66; ">

<div class="nav-side-menu" >
    <div class="brand" style="color:#c8a900">Navigare</div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
  
        <div class="menu-list">
  
            <ul id="menu-content" class="menu-content collapse out">
               

                <?php
                if($session["status"] == "true")
                {
                    if($session["user_rank"] == "admin")
                    {
                        ?>
                    <li  data-toggle="collapse" data-target="#materii" class="collapsed active nav-red">
                    <a href="#"><i class="fas fa-atlas"></i> Materii <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="materii">
                    <li><a href="index.php?p=cats&action=view_all"><i class="fas fa-globe"></i> Toate materiile</a></li>
                    <li><a href="index.php?p=cats&action=add_new"><i class="fas fa-plus"></i> Adauga materie</a></li>
                    
                    </ul>
                    <li  data-toggle="collapse" data-target="#useri" class="collapsed active">
                    <a href="#"><i class="fas fa-users"></i> Useri <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="useri">
                    
                    <li><a href="index.php?p=users"><i class="fas fa-globe"></i> Toti userii</a></li>
                    <li><a href="index.php?p=new_user"><i class="fas fa-plus"></i> Adauga user</a></li>
                    
                    </ul>
                    
                    <li  data-toggle="collapse" data-target="#groups" class="collapsed active">
                    <a href="#"><i class="fas fa-user-friends"></i> Grupe <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="groups">
                    
                    <li><a href="index.php?p=groups&action=view_all"><i class="fas fa-globe"></i> Toate grupele</a></li>
                    <li><a href="index.php?p=add_group"><i class="fas fa-plus"></i> Adauga grupe</a></li>
                    
                    </ul>
                    
                    <li  data-toggle="collapse" data-target="#series" class="collapsed active">
                    <a href="#"><i class="fas fa-user-friends"></i> Serii <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="series">
                    
                    <li><a href="index.php?p=series&action=view_all"><i class="fas fa-globe"></i> Toate seriile</a></li>
                    <li><a href="index.php?p=add_series"><i class="fas fa-plus"></i> Adauga serie</a></li>
                    
                    </ul>
                    
                    <li class="collapsed active">
                    <a href="index.php?p=import_csv"><i class="fas fa-file-import"></i> Import .CSV</a>
                    </li>
                    
                        <?php
                    }
                    elseif($session["user_rank"] == "teacher")
                    {
                      $get_cats = mysqli_query($con, "SELECT cat_id,is_entitled FROM user_meta WHERE user_id = '".$session["user_id"]."' AND meta_type='teacher'");
                      if(mysqli_num_rows($get_cats) > 0)
                      {
                            while($a = mysqli_fetch_assoc($get_cats))
                            {
                                $teacher_cat = $a["cat_id"];
                                $teacher_cat_name = basename(build_tree($teacher_cat));
                                $teacher_cat_full = build_tree($teacher_cat);
                                $is_entitled = $a["is_entitled"];
                                ?>
                    <li  data-toggle="collapse" data-target="#catalog_<?php echo $teacher_cat;?>" class="collapsed active">
                    <a href="#"><i class="fas fa-database"></i> <?php echo $teacher_cat_full;?> <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="catalog_<?php echo $teacher_cat;?>">
                    <?php
                    if($is_entitled == 1)
                    {
                        ?>
                       <li><a href="index.php?p=grade_components&cat_id=<?php echo $teacher_cat;?>"><i class="fas fa-globe"></i> Formula de calcul *</a></li> 
                        <?php
                    }
                    ?>
                    <li><a href="index.php?p=admin_catalog&cat_id=<?php echo $teacher_cat;?>&action=view_all"><i class="fas fa-globe"></i> Catalog Componente</a></li>
                    <li><a href="index.php?p=admin_catalog&cat_id=<?php echo $teacher_cat;?>&action=finals"><i class="fas fa-plus"></i> Catalog Final</a></li>
                    <li><a href="index.php?p=export_pdf&cat_id=<?php echo $teacher_cat;?>"><i class="fas fa-file-pdf"></i> Export .pdf</a></li>
                    </ul>
                                <?php
                            }
                      }
                        ?>
                    <li  data-toggle="collapse" data-target="#useri" class="collapsed active">
                    <a href="#"><i class="fas fa-users"></i> Studenti <i class="fas fa-caret-down"></i></a>
                    </li>
                    <ul class="sub-menu collapse" id="useri">
                    
                    <li><a href="index.php?p=students"><i class="fas fa-globe"></i> Toti studentii</a></li>
                    <li><a href="index.php?p=new_student"><i class="fas fa-plus"></i> Adauga student</a></li>
                    
                    </ul>
                    <li class="collapsed active">
                    <a href="index.php?p=import_csv"><i class="fas fa-file-import"></i> Import .CSV</a>
                    </li>
                                    
                    <?php
                    }
                    elseif($session["user_rank"] == "student")
                    {       
                    
                        ?>
                        <li class="collapsed active">
                  <a href="index.php?p=user_catalog&action=view_all"><i class="fas fa-database"></i> Catalog</a>
                </li>
                        <?php
                    }
                }
                ?>
                
            </ul>
     </div>
</div>
</div>
<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 p-0" style="min-height: 750px;overflow-y: scroll;">

<?php 
    get_page();
    ?>
</div>
</div>
</div>
    <?php
 }
 else
 {
    include "inc/login_form.php";
    echo "<br><div class='text-center text-white' style=' font-size: 13px;'>Copyright &copy ".date("Y")." | ".$app_name." | All rights reserved.</div>";
 }
 ?> 
        <script src="js/jquery-3.4.1.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/bootstrap-multiselect.js"></script>
        <script src="js/ajax_callbacks.js"></script>
 
<script type="text/javascript" src="css/data-tables/datatables.js"></script>


<script src="js/tinymce/tinymce.min.js"></script>
<script>
  tinymce.init({
    selector: '#desc_body',
    height: 300,
    
    theme: 'modern',
    plugins: [
      'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern imagetools'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent |',
    toolbar2: 'print preview media | forecolor backcolor emoticons',
    image_advtab: false
});

  </script>
  
  <script>
   $(document).ready(function() {
        $('#multi-select').multiselect(
        {
            buttonWidth: '100%',
            nonSelectedText: 'Selecteaza o optiune',
            buttonClass: 'form-control form-control-sm',
            optionClass: function(element) {
            return 'list-group-item';
            }
            
        }
        );
                $('#multi-select-teacher').multiselect(
        {
            buttonWidth: '100%',
            nonSelectedText: 'Selecteaza o optiune',
            buttonClass: 'form-control form-control-sm',
            optionClass: function(element) {
            return 'list-group-item';
            }
            
        }
        );
        
    });
$('#user_type_select').change(function(){
    $('form').hide();
    $('form#'+$(this).val()).show();
});

function get_grade_types(obj)
  { //dropdown
    $('#type_id').empty()
    var cat_id = $('#cat_id').val();
    $.ajax({
            type: "POST",
            url: "get_types.php",
            data: { 'cat_id': cat_id  },
            success: function(data){
                // Parse the returned json data
                var opts = $.parseJSON(data);
                // Use jQuery's each to iterate over the opts value
                $.each(opts, function(i, d) {
                    // You will need to alter the below to get the right values from your json object.  Guessing that d.id / d.modelName are columns in your carModels data
                    $('#type_id').append('<option value="' + d.type_id + '">' + d.type_name + '</option>');
                });
            }
        });
  }
$(document).ready(function() {
    var table = $('table.data-view').DataTable( {
        responsive: true,
        "autoWidth": false,
        lengthChange: false,
        pageLength: 50,
       
        dom: 'Bflrtip',
        buttons: true,
        buttons: [ 
         { extend: 'copy', text: 'Copiaza' },
         { extend: 'csv', text: 'Exporta CSV', title: 'data_export_<?php echo time();?>',footer: true,exportOptions: {
                    columns: ':visible'
                }},
         { extend: 'colvis', text: 'Ascunde Coloane' }
        ]  
    } );   
} );
   $(".percentage").on("keyup", function() {
    var sum = 0;
    $(".percentage").each(function(){
        sum += +$(this).val();
    });
    if(sum < 100)
    {
        $("#percent_result").html("<div class='alert-danger'>Procentaj actual: "+sum+"%</div>");
        $("#submit_button").attr("disabled", true);
    }
    else if(sum > 100)
    {
        $("#percent_result").html("<div class='alert-danger'>Atentie, procentajul actual depasete 100%</div>");
        $("#submit_button").attr("disabled", true);
    }
    else
    {
        $("#percent_result").html("<div class='alert-success'>Procentaj actual: "+sum+"%</div>");
        $("#submit_button").attr("disabled", false);
    }
    
});
</script>
</body>
</html>