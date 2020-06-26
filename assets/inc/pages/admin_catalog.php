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
    
    $formula = render_formula($query_cat);
?>

 <ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Catalog Componente</li>
</ol>    
        
 <div class="card">
 <div class="card-header"><h6>Catalogul profesorului</h6></div>
 <div class="card-body">
<?php
if(empty($formula))
{
    ?>
    <div class="alert alert-danger text-center">Parametrii formulei de calcul pentru
    <b><?php echo basename(build_tree($query_cat))?></b> nu au fost setati!</div>
    <?php
}
else
{
    ?>
    <div class="card">
    <div class="card-header">
    <div class="container-fluid">
    <div class="row justify-content-between">
    <div class="col-lg">Catalog</div>
    <div class="col-lg text-right">Materie: <?php echo basename(build_tree($query_cat));?><br /><?php echo $formula;?></div>
    </div>
    </div>
    </div>
    <div class="card-body">
    <?php
    if(isset($_GET["action"]) && !empty($_GET["action"]))
    {
        if($_GET["action"] == "view_all")
        {
            //view all grades
            //get student entries
            $student_grades = return_students($query_cat);
            
           if(is_array($student_grades))
           {
                ?>
                <table class="table table-bordered table-hover data-view" style="font-size: 14px;">
                <thead class="thead-dark">
                <tr>
                <th>Count</th>
                <?php
                //generate table head
                foreach($student_grades[0] as $key=>$val)
                {
                    if($key != "student_id")
                    {
                         echo "<th>$key</th>";
                    }
                }
                ?>
                <th>Edit</th>
                </tr>
                 </thead>
                 <tbody>
                 
                 <?php
                 $i  = 1;
                 foreach($student_grades as $g)
                 {
                    echo "<tr>";
                    echo "<td>$i</td>";
                    foreach($g as $key=>$val)
                    {
                        if($key != "student_id")
                        {
                            echo "<td>$val</td>";
                        }
                    }
                    echo "<td><a href='index.php?p=admin_catalog&cat_id=$query_cat&action=edit_grades&student_id=".$g["student_id"]."'>Edit</a></td>";
                    echo "</tr>";
                    $i++;
                 }
                 ?>
                 </tbody>
                </table>
                <?php
           }
           else
           {
                echo "Nu sunt studenti in baza de date!";
           }
        }
        elseif($_GET["action"] == "edit_grades")
        {
            if(isset($_GET["student_id"]) && !empty($_GET["student_id"]))
            {
                $student_id = $_GET["student_id"];
                $u = return_user($student_id);
                $name = $u["last_name"]." ".$u["first_name"];
                $year_id = return_year($cat_id);
                
                if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE id = '$student_id' AND id IN (SELECT user_id from user_meta WHERE cat_id = '$year_id') LIMIT 1")) == 1)
                {
                    $get_grades = get_grades_by($student_id,$cat_id);
                    
                    ?>
                    <div class="card" style="font-size: 14px;">
                    <div class="card-header">
                    <div class="container-fluid">
                    <div class="row justify-content-between">
                    <div>Materie: <b><?php echo basename(build_tree($cat_id));?></b></div>
                    <div class="text-right">Student: <b><?php echo $name;?></b></div>
                    </div>
                    </div>
                    </div>
                    <div class="card-body">
                    <table class="table table-hover table-bordered">
                    <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
                    
                    <?php
                    if(is_array($get_grades))
                    {
                        foreach($get_grades as $g)
                        {
                            $type = $g["type_name"];
                            $value = $g["grade"];
                            ?>
                            <tr>
                            <td><?php echo $type;?></td>
                            <td><input type="hidden" name="type_name[]" value="<?php echo $type;?>" required/><input type="number" min="0" max="10" step="0.01" class="form-control form-control-sm percentage" placeholder="Nota" name="grade[]" value="<?php echo $value;?>" required/></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tr>
                    <td colspan="2" class="text-center">
                    <a href="index.php?p=admin_catalog&cat_id=<?php echo $cat_id;?>&action=view_all" class="btn btn-secondary btn-sm">Inapoi</a>
                    <button type="submit" class="btn btn-primary btn-sm" name="update">Modifica</button>
                    </td>
                    </tr>
                     </form>
                    </table>
                    </div>
                    </div>
    
                    <?php
                    //prepare the magin
                    if(isset($_POST["update"]))
                    {
   if(isset($_POST["grade"]) && isset($_POST["type_name"]))
    {
        $name = $_POST["type_name"];
        $grade = $_POST["grade"];
        
        $reset = mysqli_query($con, "DELETE from grades WHERE cat_id = '$cat_id' AND student_id = '$student_id'");
        if(!$reset)
        {
            die(mysqli_error($con));
        }
        $insert = "";
        foreach($name as $key=>$val)
        {
                $grade_value = $grade[$key];
                
                $grade_type = $val;
                
                $get_id = mysqli_query($con, "SELECT id FROM grade_types WHERE type_name = '$grade_type' AND cat_id = '$cat_id' LIMIT 1");
                if(mysqli_num_rows($get_id) == 1)
                {
                    $g = mysqli_fetch_assoc($get_id);
                    $type_id = $g["id"];
                }
                else
                {
                    die("Tipologie de nota inexistenta!");
                }
                
               $insert .= "INSERT into grades VALUES (DEFAULT,'$teacher_id','$student_id','$type_id','$cat_id','$grade_value','".date("Y-m-d H:i:s")."');";
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
                }
                else
                {
                    echo "Studentul nu exista sau nu este asociat acestei materii!";
                }
            }
            else
            {
                echo "Nu s-a specificat nici un id de student.";
            }
        }
        elseif($_GET["action"] == "finals")
        {
        $student_grades = return_finals_students($query_cat);
            
           if(is_array($student_grades))
           {
                ?>
                <table class="table table-bordered table-hover data-view" style="font-size: 14px;">
                <thead class="thead-dark">
                <tr>
                <th>Count</th>
                <?php
                //generate table head
                foreach($student_grades[0] as $key=>$val)
                {
                    if($key != "student_id")
                    {
                         echo "<th>$key</th>";
                    }
                }
                ?>
                </tr>
                 </thead>
                 <tbody>
                 
                 <?php
                 $i  = 1;
                 foreach($student_grades as $g)
                 {
                    echo "<tr>";
                    echo "<td>$i</td>";
                    foreach($g as $key=>$val)
                    {
                        if($key != "student_id")
                        {
                            echo "<td>$val</td>";
                        }
                    }
                    echo "</tr>";
                    $i++;
                 }
                 ?>
                 </tbody>
                </table>
                <?php
           }
           else
           {
                echo "Nu sunt studenti in baza de date!";
           }
        }
        else
        {
            echo "<div class='text-center'>Actiunea nu exista in sistem!</div>";
        }
    }
    else
    {
        echo "<div class='text-center'>Actiunea selectata nu exista!</div>";
    }
    ?>
    </div>
    </div>
    <?php
}
?>
 </div>
 </div>             