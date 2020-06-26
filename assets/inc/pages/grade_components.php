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
$db_cat = mysqli_query($con, "SELECT cat_id,is_entitled FROM user_meta WHERE user_id = '$teacher_id' AND meta_type = 'teacher' and cat_id ='".$_GET["cat_id"]."' LIMIT 1");
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
        
        if($query_cat != $cat_id OR $is_entitled == 0)
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
    <li class="breadcrumb-item active">Formula de calcul</li>
</ol>          

<?php
$get_grade_types = mysqli_query($con, "SELECT * FROM grade_types WHERE cat_id = '$cat_id'");
if(mysqli_num_rows($get_grade_types) > 0)
{
    while($a = mysqli_fetch_assoc($get_grade_types))
    {
        $type_name = $a["type_name"];
        $type_percentage = $a["type_percentage"];
        
        $type_data[] = array("type_name" => $type_name,"type_percentage" => $type_percentage);
    }
}
else
{
     $type_data = array(
        1 => array("type_name" => "Nota 1" ,"type_percentage" => 50),
        2 => array("type_name" => "Nota 2" ,"type_percentage" => 50),
        3 => array("type_name" => "Nota 3" ,"type_percentage" => 0),
        4 => array("type_name" => "Nota 4" ,"type_percentage" => 0),
        5 => array("type_name" => "Nota 5" ,"type_percentage" => 0),
        6 => array("type_name" => "Nota 6" ,"type_percentage" => 0),
        7 => array("type_name" => "Nota 7" ,"type_percentage" => 0),
        8 => array("type_name" => "Nota 8" ,"type_percentage" => 0),
     );
}

?>
<div class="card">
<div class="card-header"><h6>Formula de calcul</h6></div>
<div class="card-body">
<table class="table table-bordered table-hover">
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<?php
foreach($type_data as $type)
{
    $name = $type["type_name"];
    $percentage = $type["type_percentage"];
    
    ?>
    <tr>
    <td><?php echo $name;?></td>
    <td><input type="hidden" name="type_name[]" value="<?php echo $name;?>" required/><input type="number" step="0.01" class="form-control form-control-sm percentage" placeholder="Procentaj" name="percentage[]" value="<?php echo $percentage;?>" required/></td>
    </tr>
    <?php
}
?>
<tr><td colspan="2" class="text-center"><a href="index.php" class="btn btn-secondary btn-sm">Inapoi</a> <button type="submit" name="save" id="submit_button" class="btn btn-primary btn-sm">Salveaza</button></td></tr>
</form>

</table>
<div id="percent_result">

</div>

<?php
if(isset($_POST["save"]))
{
    if(isset($_POST["percentage"]) && isset($_POST["type_name"]))
    {
        $name = $_POST["type_name"];
        $percentage = $_POST["percentage"];
        
      
        $insert = "";
        foreach($name as $key=>$val)
        {
        
                $grade_percentage = $percentage[$key];
                
                $grade_type = $val;
                
                if(mysqli_num_rows(mysqli_query($con,"SELECT type_name FROM grade_types WHERE type_name = '$grade_type' AND cat_id = '$cat_id'")) == 1)
                {
                   
                    $insert .= "UPDATE grade_types SET type_percentage = '$grade_percentage' WHERE type_name='$grade_type' AND cat_id = '$cat_id';";
                }
                else
                {
                    $insert .= "INSERT into grade_types VALUES (DEFAULT,'$grade_type','$grade_percentage','$cat_id','".date("Y-m-d H:i:s")."'); ";
                }
                
        }
        
        $insert_data = mysqli_multi_query($con,$insert);
        if($insert_data)
        {
            redirect($_SERVER["REQUEST_URI"]);
        }
        else
        {
            echo mysqli_error($con);
        }
    }
}
?>
</div>
</div>

<?php
?>          