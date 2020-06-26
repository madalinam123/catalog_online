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

$teacher_years = return_years($teacher_id);


?>

<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Adauga student</li>
</ol>          
          
              <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Informatii Student</h3>
                  </div>
                  <div class="card-body">
                  <?php
                  if(!isset($_POST["save"]))
                  {
                    //show form
                    
                    ?>
                    <script>
function checkPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field 
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match. 
        //Set the color to the good color and inform
        //the user that they have entered the correct password 
        pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Parolele se potrivesc!"
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Parolele nu se potrivesc!"
    }
}  

                    </script>
                     <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
                     <table class="table card-table table-vcenter text-nowrap table-striped table-bordered table-hover" style="font-size: 14px;">
                     <tr>
                     <td>Nume</td>
                     <td><input type="text" class="form-control form-control-sm" name="nume" required/></td>
                     </tr>
                     <tr>
                     <td>Prenume</td>
                     <td><input type="text" class="form-control form-control-sm" name="prenume" required/></td>
                     </tr>
                     <tr>
                     <td>Email</td>
                     <td><input type="email" class="form-control form-control-sm" name="email" required/></td>
                     </tr>
                     <tr>
                     <td>Nr. Matricol</td>
                     <td><input type="text" class="form-control form-control-sm" name="username" required/></td>
                     </tr>
                     <tr>
                     <td>Parola</td>
                     <td><input type="password" class="form-control form-control-sm" name="password" id="pass1" required/></td>
                     </tr>
                     <tr>
                     <td>Repeta Parola</td>
                     <td><input type="password" class="form-control form-control-sm" name="password2" id="pass2" onkeyup="checkPass(); return false;" required/>
                    <span id="confirmMessage" class="confirmMessage"></span>
                     </td>
                     </tr>
                    <tr>
                     <td>An Studiu</td>
                     <td>
                     <select  name="cat_id" class="form-control form-control-sm" required>
                     <option>Selecteaza</option>
<?php
$years = explode(",",$teacher_years);
foreach($years as $year)
{
    echo "<option value='$year'>".build_tree($year)."</option>";
}
?>
</select>
                     </td>
                     </tr>
                    <tr>
                     <td>Grupa</td>
                     <td>
                    
                     <select  name="group_id" class="form-control form-control-sm" required>
                      <option>Selecteaza</option>
<?php
$get_groups = mysqli_query($con,"SELECT * FROM groups order by group_name ASC");
if(mysqli_num_rows($get_groups) > 0)
{
    while($a = mysqli_fetch_assoc($get_groups))
    {
        echo "<option value='".$a["id"]."'>".$a["group_name"]."</option>";
    }
       
}
?>
</select>
                     
                     </td>
                     </tr>
                     
                     <tr>
                     <td>Serie</td>
                     <td>
                     <select  name="series_id" class="form-control form-control-sm" required>
<?php
$get_series = mysqli_query($con,"SELECT * FROM series order by group_name ASC");
if(mysqli_num_rows($get_series) > 0)
{
    while($b = mysqli_fetch_assoc($get_series))
    {
        echo "<option value='".$b["id"]."'>".$b["group_name"]."</option>";
    }
       
}
?>
</select>
                     </td>
                     </tr>
                     
                     </table>     
                     <hr  style="width: 75%;"/>
                     <div class="text-center">
                     <a href="index.php"><button type="button" class="btn btn-secondary">Inapoi</button></a>
                    <button type="submit" class="btn btn-success" name="save">Adauga</button>
                     </div>
                       </form>
                    <?php
                    
                  }
                  else
                  {
                    $username = $_POST["username"];
    $nume = $_POST["nume"];
    $prenume = $_POST["prenume"];
    
    
                                   
    
    if(isset($_POST["group_id"]))
    {
        $group_id = $_POST["group_id"];
    }
    else
    {
        $group_id = '0';
    }
    
    if(isset($_POST["series_id"]))
    {
        $series_id = $_POST["series_id"];
    }
    else
    {
        $series_id = 0;
    }
    
    $email = $_POST["email"];
    $password = md5($_POST["password"]);
  
    
    
    if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE username='".addslashes($email)."' OR email='".addslashes($email)."' LIMIT 1")) < 1)
    {
    $cols = array("username","password","email","first_name","last_name","user_rank","group_id","series_id","status","created_at");
    $vals = array($username,$password,$email,$prenume,$nume,"student",$group_id,$series_id,"1",date("Y-m-d H:i:s"));
    
    $add_user = PushData("users",$cols,$vals);
    
    if($add_user["result"] == "true")
    {
        $user_id = $add_user["query_id"];
        
                                    if(isset($_POST["cat_id"]))
                                    {
                                       $cat_id = $_POST["cat_id"];
                                       
                                       
                                       
                                       $insert  = "INSERT into user_meta VALUES (DEFAULT,'$user_id','$cat_id','student','0');";
                                        
                                       
                                        $insert_query = mysqli_multi_query($con,$insert);
                                        if(!$insert_query)
                                        {
                                            echo mysqli_error($con);
                                        }
                                   }
        
        echo "<div class='text-center'>Utilizatorul: <b>$username</b> cu rank-ul de student a fost adaugat in sistem!<hr><a href='index.php'>Inapoi acasa</a></div>";
    }
    else
    {
        echo $add_user["error"];
    }
    }
    else
    {
        echo "<div class='text-center'>Exista deja un utilizator cu numarul matricol/username-ul/email-ul utilizat! <hr><a href='index.php?p=new_user'>Incearca din nou</a></div>";
    }

                 
                  }
                  ?>
                  </div>
                  </div>