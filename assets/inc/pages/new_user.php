<?php
$session = return_session();
if($session["status"] == "false")
{
    redirect("index.php");
}
else
{
    if($session["user_rank"] != "admin")
    {
        redirect("index.php");
    }
}
?>
<script>
function UsercheckPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('user_pass1');
    var pass2 = document.getElementById('user_pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('user_confirmMessage');
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
        message.innerHTML = "Parolele se potrivesc!";
        $('#user-submit-button').prop('disabled', false);
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Parolele nu se potrivesc!";
        $('#user-submit-button').prop('disabled',true);
    }
}  
function TeachercheckPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('teacher_pass1');
    var pass2 = document.getElementById('teacher_pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('teacher_confirmMessage');
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
        message.innerHTML = "Parolele se potrivesc!";
        $('#teacher-submit-button').prop('disabled', false);
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Parolele nu se potrivesc!";
        $('#teacher-submit-button').prop('disabled',true);
    }
} 
function AdmincheckPass()
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('admin_pass1');
    var pass2 = document.getElementById('admin_pass2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('admin_confirmMessage');
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
        message.innerHTML = "Parolele se potrivesc!";
        $('#admin-submit-button').prop('disabled', false);
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Parolele nu se potrivesc!";
        $('#admin-submit-button').prop('disabled',true);
    }
} 
</script>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Adauga user</li>
</ol>          
          
              <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Informatii Utilizator</h3>
                  </div>
                  <div class="card-body">
<?php
if(!isset($_POST["add_user"]) && !isset($_POST["add_admin"]) && !isset($_POST["add_teacher"]))
{
?>  
            
<div class="form-row">
<div class="col-lg">
<label>Tip Utilizator</label>
<select id="user_type_select" class="form-control form-control-sm">
<option value="" selected disabled>Selecteaza</option>
<option value="form_student">Student</option>
<option value="form_teacher">Profesor</option>
<option value="form_admin">Administrator</option>
</select>
</div>
</div>
<hr />
<!-- STUDENT -->
<form id="form_student" style="display:none" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="student"/>    
<div class="form-row">
<div class="col-lg">
<label>Numar Matricol</label>
<input type="number" class="form-control form-control-sm" placeholder="ex:5444155" name="username" required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="student@email.com" name="email" required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popescu" name="nume" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Marius" name="prenume" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Parola aici..." name="password" id="user_pass1" required>
</div>

<div class="col-lg">
<label>Repeta parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Confirmare parola..." name="password2" id="user_pass2"  onkeyup="UsercheckPass(); return false;" required>
<span id="user_confirmMessage" class="confirmMessage"></span>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Ani Studiu</label><br />
<select name="cat_ids[]" class="form-control form-control-sm" required>
<?php
$get_cats = mysqli_query($con,"SELECT * FROM materii WHERE cat_parent='0'");
if(mysqli_num_rows($get_cats) > 0)
{
    while($a = mysqli_fetch_assoc($get_cats))
    {
        echo "<option value='".$a["cat_id"]."'>".$a["cat_name"]."</option>";
    }
       
}
?>
</select>
</div>
</div>

<hr />
<div class="form-row">
<div class="col-lg">
<label>Grupa</label><br />
<select  name="group_id" class="form-control form-control-sm" required>
<?php
$get_groups = mysqli_query($con,"SELECT * FROM groups order by group_name ASC");
if(mysqli_num_rows($get_groups) > 0)
{
    while($d = mysqli_fetch_assoc($get_groups))
    {
        echo "<option value='".$d["id"]."'>".$d["group_name"]."</option>";
    }
       
}
?>
</select>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Serie</label><br />
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
</div>
</div>
<hr />

<div class="text-center">
<a href="index.php" class="btn btn-secondary btn-sm">Inapoi</a>
<button type="submit" id="user-submit-button" name="add_user" class="btn btn-success btn-sm">Adauga Student</button>
</div>
</form>
   <!-- TEACHER -->
<form  id="form_teacher" style="display:none" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="teacher"/>
<div class="form-row">
<div class="col-lg">
<label>Username</label>
<input type="text" class="form-control form-control-sm" placeholder="ex:user2345" name="username" required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="teacher@email.com" name="email" required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popescu" name="nume" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Marius" name="prenume" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Parola aici..." name="password" id="teacher_pass1" required>
</div>

<div class="col-lg">
<label>Repeta parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Confirmare parola..." name="password2" id="teacher_pass2"  onkeyup="TeachercheckPass(); return false;" required>
<span id="teacher_confirmMessage" class="confirmMessage"></span>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Materii Predate</label><br />
<select id="multi-select-teacher" name="cat_ids[]" multiple="multiple" required>
<?php
$get_cats = mysqli_query($con,"SELECT * FROM materii WHERE cat_parent <> '0'");
if(mysqli_num_rows($get_cats) > 0)
{
    while($a = mysqli_fetch_assoc($get_cats))
    {
        $cat_id = $a["cat_id"];
        $cat_name = build_tree($cat_id);
        
        
        echo "<option value='".$cat_id."'>".$cat_name."</option>";
    }
       
}
?>
</select>
</div>
</div>
<hr />

<div class="text-center">
<a href="index.php" class="btn btn-secondary btn-sm">Inapoi</a>
<button type="submit" id="teacher-submit-button" name="add_teacher" class="btn btn-success btn-sm">Adauga Profesor</button>
</div>
</form>
  <!-- ADMIN -->
<form  id="form_admin" style="display:none" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="admin"/>
<div class="form-row">
<div class="col-lg">
<label>Username</label>
<input type="text" class="form-control form-control-sm" placeholder="ex:admin" name="username" required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="student@email.com" name="email" required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popescu" name="nume" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Marius" name="prenume" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Parola aici..." name="password" id="admin_pass1" required>
</div>

<div class="col-lg">
<label>Repeta parola</label>
<input type="password" class="form-control form-control-sm" placeholder="Confirmare parola..." name="password2" id="admin_pass2"  onkeyup="AdmincheckPass(); return false;" required>
<span id="admin_confirmMessage" class="confirmMessage"></span>
</div>
</div>
<hr />
<div class="text-center">
<a href="index.php" class="btn btn-secondary btn-sm">Inapoi</a>
<button type="submit" id="admin-submit-button" name="add_admin" class="btn btn-success btn-sm">Adauga Admin</button>
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
    $user_rank = $_POST["user_rank"];
    
    
    if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE username='".addslashes($email)."' OR email='".addslashes($email)."' LIMIT 1")) < 1)
    {
    $cols = array("username","password","email","first_name","last_name","user_rank","group_id","series_id","status","created_at");
    $vals = array($username,$password,$email,$prenume,$nume,$user_rank,$group_id,$series_id,"1",date("Y-m-d H:i:s"));
    
    $add_user = PushData("users",$cols,$vals);
    
    if($add_user["result"] == "true")
    {
        $user_id = $add_user["query_id"];
        
                                    if(isset($_POST["cat_ids"]))
                                    {
                                        $remove = mysqli_query($con,"DELETE from user_meta WHERE user_id = '$user_id'");
                                        $insert = "";
                                        foreach($_POST["cat_ids"] as $cat_id)
                                        {
                                            $insert .= "INSERT into user_meta VALUES (DEFAULT,'$user_id','$cat_id','$user_rank','0');";
                                        }
                                        
                                        $insert_query = mysqli_multi_query($con,$insert);
                                        if(!$insert_query)
                                        {
                                            echo mysqli_error($con);
                                        }
                                   }
        
        echo "<div class='text-center'>Utilizatorul: <b>$username</b> cu rank-ul de $user_rank a fost adaugat in sistem!<hr><a href='index.php'>Inapoi acasa</a></div>";
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