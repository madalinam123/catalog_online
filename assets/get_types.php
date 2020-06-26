<?php
require "inc/helpers.php";
$cat_id = $_POST["cat_id"];
$get_data = mysqli_query($con, "
SELECT * FROM grade_types WHERE cat_id = '$cat_id'
");
if(mysqli_num_rows($get_data) > 0)
{
    
    while($a = mysqli_fetch_assoc($get_data))
    {
        $arr[]  = array("type_id" => $a["id"],"type_name" => $a["type_name"]);
    }
    
    
}
else
{
    $arr[]  = array("type_id" => "","type_name" => "Nu s-au gasit tipologii de note pentru materia selectata!");
    
    
}

echo json_encode($arr);
?>