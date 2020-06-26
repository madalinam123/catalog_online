<?php
$session = return_session();
if($session["status"] == "false")
{
    redirect("index.php");
}
else
{
    if($session["user_rank"] != "teacher")
    {
        redirect("index.php");
    }
}

$teacher_id = $session["user_id"];
$db_cat = mysqli_query($con, "SELECT cat_id,is_entitled FROM user_meta WHERE user_id = '$teacher_id' AND meta_type = 'teacher' AND cat_id='".$_GET["cat_id"]."' LIMIT 1");
if(mysqli_num_rows($db_cat) == 1)
{
    $a = mysqli_fetch_assoc($db_cat);
    
    $cat_id = $a["cat_id"];
    $is_entitled = $a["is_entitled"];
    
}
else
{
    $cat_id = "";
    $is_entitled = "";
}
//redirect if params do not work
 if(isset($_GET["cat_id"]) && !empty($_GET["cat_id"]))
    {
        $query_cat = $_GET["cat_id"];
        
        if($query_cat != $cat_id)
        {
            redirect("index.php");
        }
    }
    else
    {
        redirect("index.php");
    }   
?>

 <ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Export PDF Note Studenti</li>
</ol>    
        
 <div class="card">
 <div class="card-header"><h6>Export Date -> <?php echo basename(build_tree($query_cat));?></h6></div>
 <div class="card-body">
 <div class="alert alert-info text-center">Apasa pe buton pentru a genera fisierul .pdf<hr />
 <a href="generate_export.php?cat_id=<?php echo $query_cat;?>" target="_new" class="btn btn-success">Genereaza</a>
 </div>
 </div>
 </div>