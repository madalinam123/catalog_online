<?php
$session = return_session();

if ($session["user_rank"] != "student") {
    redirect("index.php");
}

$student_id = $session["user_id"];
$year_id = return_student_year($student_id);

//year name
$student_year = build_tree($year_id);
?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Catalog Student</li>     
</ol>
<div class="card" style="font-size: 14px;">
<div class="card-header">
<h5>Sectiune Catalog </h5>  <?php echo $student_year;?>
</div>
<div class="card-body">
<?php

if(isset($_GET["action"]) && !empty($_GET["action"]))
{
    if($_GET["action"] == "view_all")
    {
        ?>
        <div class="card">
        <div class="card-header">
        <h6>Catalog General</h6>
        </div>
        <div class="card-body">
        
        <?php
        $get_materii = mysqli_query($con, "SELECT cat_id,cat_name FROM materii WHERE cat_parent = '$year_id'");
        if(mysqli_num_rows($get_materii) > 0)
        {
            ?>
            <table class="data-view table table-bordered table-hover" style="font-size: 14px; width: 100%">
            <thead class="thead-dark">
            <tr>
           
            <th>Materie</th>
            <th>Formula</th>
            <th>Medie</th>
            
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            while($a = mysqli_fetch_assoc($get_materii) )
            {
                
                $cat_id = $a["cat_id"];
                $cat_name = basename(build_tree($cat_id));
                $percentage_total = grades_percentage_total($cat_id,$student_id);
                if($percentage_total == 100)
                {
                     $final_grade  = return_final_grade($cat_id,$student_id);
                     $final_grade = number_format($final_grade,"2",".",",");
                }
                else
                {
                    $final_grade = "0";
                }
               $cats[] = $cat_id;
               ?>
               <tr>
               
               <td>
               <?php
               if($final_grade == "0")
               {
                    $link = "#";
               }
               else
               {
                    $link = "index.php?p=user_catalog&action=view_grades&cat_id=$cat_id";
               }
               ?>
               <a href="<?php echo $link;?>"><?php echo $cat_name;?></a>
               </td>
               <td><?php echo render_formula($cat_id);?></td>
               <td><?php echo $final_grade;?></td>
               </tr>
               <?php
               $i++;
            }
            $cats = implode(",",$cats);
            $total_avg = return_final_average($cats,$student_id);
            ?>
           
            </tbody>
            <tfoot>
             <tr>         
                                <td></td>
                                <td class="text-right">
                      
                                <b>Media finala</b>
                                </td>
                                
                                <td><span class="badge badge-primary" style="font-size:14px;"><?php echo $total_avg;?></span></td>
                                
                                </tr>
            </tfoot>
            </table>
            <?php
        }
        else
        {
            echo "<div class='text-center'>Nu s-au gasit materii!</div>";
        }
        ?>
        </div>
        </div>
        <?php
    }
    elseif($_GET["action"] == "view_grades")
    {
        ?>
        <div class="card">
        <div class="card-header">
        <h6>Catalog Materie</h6>
        </div>
        <div class="card-body">
        <?php
        if(isset($_GET["cat_id"]) && !empty($_GET["cat_id"]))
        {
            $cat_id = $_GET["cat_id"];
            
            $get_grades =  get_grades_by($session["user_id"],$cat_id,$method = "normal");
            
                       if(count($get_grades) > 0)
                        {
                              $percentage_total = grades_percentage_total($cat_id,$student_id);
                            if($percentage_total == 100)
                                {
                                    $final_grade  = return_final_grade($cat_id,$student_id);
                                     $final_grade = number_format($final_grade,"2",".",",");
                                  }
                                 else
                                 {
                                 $final_grade = "0";
                                }
                            ?>
                            <div class="card">
                            <div class="card-header">
                            <div class="row justify-content-between">
                            <div><?php echo basename(build_tree($cat_id));?></div>
                            <div><td>Formula: <?php echo render_formula($cat_id);?></td></div>
                            </div>
                            </div>
                            <div class="card-body">
                            
                            <div class="table-responsive">
                            
                            <table class="data-view table table-bordered table-hover" style="width: 100%; font-size: 14px;">
                            <thead class="thead-dark">
                            <tr>
                            <th>Tip Nota</th>
                                  
                            <th>Nota</th>
                            </tr>
                            </thead>
                            <tbody> 
                            <?php

                            foreach($get_grades as $a)
                            {
                            
                                $grade_type = $a["type_name"];
                                $type_id = $a["type_id"];
                               
                                $date = $a["date"];
                                $grade = $a["grade"];
                                $u = return_user($teacher_id);         
                                
                                ?>
                                <tr>
                                <td><?php echo $grade_type;?></td>                   
                               
                                <td><?php echo $grade;?></td>
                                </tr>
                                <?php
                            }
                            ?>                       
                            </tbody>
                                <tfoot>  
                                <tr>
                                     
                                <td class="text-right">
                                <b>Media finala</b>
                                </td>
                                <td>
                                <span class="badge badge-primary" style="font-size:14px;"><?php echo $final_grade;?></span>
                                </td>
                                </tr>
                                </tfoot>
                             </table> 
                             </div>
                            </div>
                            </div>
                            <?php
                        }
                        else
                        {
                            echo "<div class='text-center'>Nu s-au gasit note la <b>$materie</b>!</div>";
                        }
        }
        else
        {
            echo "Nu s-a specificat nici o materie!";
        }
        ?>
        </div>
        </div>
        <?php
    }
    elseif($_GET["action"] == "export_all_csv")
    {
       
    }
    elseif($_GET["acition"] == "export_csv")
    {
        
    }
    else
    {
        echo "Sectiunea specificata nu exista!";
    }
}
else
{
    echo "Sectiunea specificata nu exista!";
}
?>
</div>
</div>