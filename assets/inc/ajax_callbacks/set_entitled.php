<?php
require "../helpers.php";

if($_POST)
{
    $is_entitled = $_POST["is_entitled"];
    $cat_id = $_POST["cat_id"];
    $teacher_id = $_POST["user_id"];
    
    
    //remove previous data if exists
    
    $remove_meta = mysqli_query($con, "DELETE from user_meta WHERE user_id = '$teacher_id' AND cat_id = '$cat_id' AND meta_type = 'teacher'");
    
    if(!$remove_meta)
    {
        die(mysqli_error($con));
    }
        
        $insert = mysqli_query($con, "INSERT into user_meta VALUES (DEFAULT,'$teacher_id','$cat_id','teacher','$is_entitled')");
        
        if(!$insert)
        {
            echo mysqli_error($con);
        }
}

?>