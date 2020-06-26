<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active"><a href="index.php?p=users">Administrare useri</a></li>
    <li class="breadcrumb-item active">Editare user</li>
</ol>
           
           
            <div class="row row-cards row-deck">
              <div class="col-12">
              <div class="card">
              <div class="card-body">   
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
            $logged = $session["user_id"];
            
            if(isset($_GET["user_id"]) && $_GET["user_id"] != "")
            {
                $user_id = $_GET["user_id"];
                
                if(isset($_GET["action"]) && !empty($_GET["action"]))
                {
                    if($_GET["action"] == "edit")
                    {
                        ?>
                          <div class="card">
                  <div class="card-header">
                    <h6 class="card-title">Detalii cont</h6>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block;">
                  
                  <?php
                  if(!isset($_POST["save"]))
                  {
                  $get_user_info = mysqli_query($con, "SELECT * FROM users WHERE id='$user_id'");
                  if(mysqli_num_rows($get_user_info) == 1)
                  {
                        $a = mysqli_fetch_assoc($get_user_info);
                        
                        $username = $a["username"];
                        $nume = $a["last_name"];
                        $prenume = $a["first_name"];
                        $created_at = $a["created_at"];
                        $user_rank = $a["user_rank"];
                        $status = $a["status"];
                        $email = $a["email"];
                        $group_id = $a["group_id"];
                        $series_id = $a["series_id"];  
                        
                         if ($user_rank == "admin") {
            $user_icon = 'Admin - <i class="fas fa-award"></i>';
        } elseif ($user_rank == "teacher") {
            $user_icon = 'Profesor - <i class="fas fa-school"></i>';
        } elseif ($user_rank == "student") {
            $user_icon = 'Student - <i class="fas fa-user-graduate"></i>';
        }
                        
                        ?>
                        <div class="modal fade" id="remove_modal" tabindex="-1" role="dialog" aria-labelledby="remove_modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="remove_modal">Sterge user</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Esti sigur ca vrei sa stergi acest user?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Nu</button>
        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=remove&act=yes"><button type="button" class="btn btn-danger">Da</button></a>
      </div>
    </div>
  </div>
</div>
                <?php
                if($user_rank  == "student")    //STUDENT
                {
                   
                   $cat_ids =  return_cats("student",$user_id);
                    ?>
                    <form id="form_student" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="student"/>
<div class="form-row">
<div class="col-lg">
<label>Numar Matricol</label>
<input type="number" class="form-control form-control-sm" placeholder="ex:1001001" name="username" value="<?php echo $username;?>" <?php if($user_rank == "student") {echo "readonly";}?> required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="student@email.com" name="email" value="<?php echo $email;?>"<?php if($user_rank == "student") {echo "readonly";}?> required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popovici" name="nume" value="<?php echo $nume;?>" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Ion" value="<?php echo $prenume;?>" name="prenume" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Ani Studiu</label><br />
<select class="form-control form-control-sm" name="cat_ids[]" required>

<?php

if($cat_ids != false)
{
    foreach($cat_ids as $cat)
    {
        
        echo "<option value='".$cat["cat_id"]."' selected>".$cat["cat_name"]."</option>";
        $cats[] = " '".$cat["cat_id"]."' ";
    }  
    $cats = implode(",",$cats);
  
}
else
{
    $cats = "''";
}

$get_cats = mysqli_query($con,"SELECT * FROM materii WHERE cat_parent='0' AND cat_id NOT IN($cats)");
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
<select class="form-control form-control-sm" name="group_id" <?php if($user_rank == "student")?> required>
<?php

$get_group_name = mysqli_query($con, "SELECT group_name FROM groups WHERE id='$group_id' LIMIT 1");
if(mysqli_num_rows($get_group_name) == 1)
{
    $g = mysqli_fetch_assoc($get_group_name);
    
    echo "<option value='$group_id' selected>".$g["group_name"]."</option>";
}
if($user_rank == "student")
{
    
}
else
{

$get_groups = mysqli_query($con,"SELECT * FROM groups WHERE id <> '$group_id' order by group_name ASC");
if(mysqli_num_rows($get_groups) > 0)
{
    while($a = mysqli_fetch_assoc($get_groups))
    {
        echo "<option value='".$a["id"]."'>".$a["group_name"]."</option>";
    }
       
}

}
?>
</select>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Seria</label><br />
 <select class="form-control form-control-sm"  <?php if($user_rank == "student")?> name="series_id" required> 
<?php

$get_series_name = mysqli_query($con, "SELECT group_name FROM series WHERE id='$series_id' LIMIT 1");
if(mysqli_num_rows($get_series_name) == 1)
{
    $g = mysqli_fetch_assoc($get_series_name);
    
    echo "<option value='$series_id' selected>".$g["group_name"]."</option>";
}

if($user_rank == "student")
{
    
}
else
{
$get_seriess = mysqli_query($con,"SELECT * FROM series WHERE id <> '$series_id' order by group_name ASC");
if(mysqli_num_rows($get_seriess) > 0)
{
    while($a = mysqli_fetch_assoc($get_seriess))
    {
        echo "<option value='".$a["id"]."'>".$a["group_name"]."</option>";
    }
       
}
}

?>
</select>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Tip user:</label>
<?php
echo $user_icon;
?>
</div>
</div>
<hr/>
<div class="form-row">
<div class="col-lg">
<label>Status Cont: </label>
<?php
                     if($status == "1")
                     {
                        ?>
                        <span class="badge badge-pill badge-success">Activ</span>
                        <?php
                     }
                     else
                     {
                        ?>
                        <span class="badge badge-pill badge-danger">Banat</span>
                       <?php
                     }
                     ?>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Data creare: </label>
<?php echo $created_at;?>
</div>
</div>
<hr />

      <div class="text-center">
                     <a href="index.php?p=users"><button type="button" class="btn btn-secondary btn-sm">Inapoi</button></a>
                     <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=change_pass"><button type="button" class="btn btn-warning btn-sm">Schimba parola</button></a>
                     <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#remove_modal">Elimina</button></a>
                     
                    <?php
                     if($status == "1")
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=ban"> <button type="button" class="btn btn-outline-danger btn-sm">Baneaza</button></a>
                        <?php
                     }
                     else
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=unban"> <button type="button" class="btn btn-outline-success btn-sm">Debaneaza</button></a>
                       <?php
                     }
                     ?>
                     

                     <button type="submit" name="save" class="btn btn-success btn-sm">Salveaza</button>
                     
                     </div>
</form>
                    <?php
                }
                elseif($user_rank == "teacher")   //TEACHER
                {
                     $cat_ids =  return_cats("teacher",$user_id);
                    ?>
                    <form  id="form_teacher" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="teacher"/>
<div class="form-row">
<div class="col-lg">
<label>Username</label>
<input type="text" class="form-control form-control-sm" placeholder="ex:user2345" name="username" value="<?php echo $username;?>"<?php if($user_rank == "teacher") {echo "readonly";}?> required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="teacher@email.com" name="email" value="<?php echo $email;?>"<?php if($user_rank == "teacher") {echo "readonly";}?> required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popescu" name="nume" value="<?php echo $nume;?>" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Marius" name="prenume" value="<?php echo $prenume;?>" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Materii Predate</label><br />
<select id="multi-select" name="cat_ids[]" multiple="multiple" required>
<?php
if($cat_ids != false)
{
    foreach($cat_ids as $cat)
    {
        
        echo "<option value='".$cat["cat_id"]."' selected>".$cat["cat_name"]."</option>";
        $cats[] = " '".$cat["cat_id"]."' ";
    }  
    $cats = implode(",",$cats);
  
}
else
{
    $cats = "''";
}

$get_cats = mysqli_query($con,"SELECT * FROM materii WHERE  cat_id NOT IN ($cats)");
if(mysqli_num_rows($get_cats) > 0)
{
    
    while($a = mysqli_fetch_assoc($get_cats))
    {
        if($a["cat_parent"] != "0")
        {
        echo "<option value='".$a["cat_id"]."'>".build_tree($a["cat_id"])."</option>";
        }
    }
}
?>
</select>
</div>
</div>
<?php
if($cat_ids != false)
{
    ?>
    <hr />
<div class="form-row">
<div class="col-lg">
<label>Titilarizare materii</label><br />
<table class="table table-bordered table-hover">
<tr>
<th>Materie</th>
<th>Titular</th>
</tr>

    <?php
    $i = 1;
    foreach($cat_ids as $opts)
    {
        //$disable = entitled_teacher($opts["cat_id"]);
        
        //if($disable["user_id"] == $user_id && $disable["is_entitled"] == "1")
        //{
           // $disable = "";
        //}
       // elseif($disable["user_id"] != $user_id && $disable["is_entitled"] == "1")
       // {
           // $disable = "disabled";
       // }
        //else
       // {
            //$disable = "";
       // }
        ?>
        <tr><td><?php echo $opts["cat_name"];?></td>
        <td>
        <table class="table table-bordered table-sm">
        <tr>
        <td><input class="entitled_do" data-cat-id="<?php echo $opts["cat_id"];?>" data-user-id="<?php echo $user_id;?>" name="is_entitled_<?php echo $opts["cat_id"];?>" type="radio" id="<?php echo $i;?>" value="1" <?php if($opts["is_entitled"] == "1") {echo "checked";}?> <?php echo $disable;?>/> da</td>
        <td><input  class="entitled_do" data-cat-id="<?php echo $opts["cat_id"];?>" data-user-id="<?php echo $user_id;?>" name="is_entitled_<?php echo $opts["cat_id"];?>"  type="radio"  value="0" id="<?php echo $i;?>" <?php if($opts["is_entitled"] == "0") {echo "checked";}?> <?php echo $disable;?>/> nu</td>
        </tr>
       
        </table>
        </td>
        
        </tr>
        <?php
        $i++;
    }
}
?>
</table>
</div>
</div>
<?php
?>

<hr />
<div class="form-row">
<div class="col-lg">
<label>Tip user:</label>
<?php
echo $user_icon;
?>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Status Cont: </label>
<?php
                     if($status == "1")
                     {
                        ?>
                        <span class="badge badge-pill badge-success">Activ</span>
                        <?php
                     }
                     else
                     {
                        ?>
                        <span class="badge badge-pill badge-danger">Banat</span>
                       <?php
                     }
                     ?>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Data creare: </label>
<?php echo $created_at;?>
</div>
</div>
      <div class="text-center">
                     <a href="index.php?p=users"><button type="button" class="btn btn-secondary btn-sm">Inapoi</button></a>
                     <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=change_pass"><button type="button" class="btn btn-warning btn-sm">Schimba parola</button></a>
                     <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#remove_modal">Elimina</button></a>
                     
                    <?php
                     if($status == "1")
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=ban"> <button type="button" class="btn btn-outline-danger btn-sm">Baneaza</button></a>
                        <?php
                     }
                     else
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=unban"> <button type="button" class="btn btn-outline-success btn-sm">Debaneaza</button></a>
                       <?php
                     }
                     ?>
                     

                     <button type="submit" name="save" class="btn btn-success btn-sm">Salveaza</button>
                     
                     </div>
</form>
                    <?php
                }
                elseif($user_rank == "admin")    //ADMIN
                {
                    ?>
                    <form  id="form_admin" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="user_rank" value="admin"/>
<div class="form-row">
<div class="col-lg">
<label>Username</label>
<input type="text" class="form-control form-control-sm" placeholder="ex:admin" name="username" value="<?php echo $username;?>" required>
</div>

<div class="col-lg">
<label>Email</label>
<input type="email" class="form-control form-control-sm" placeholder="admin@email.com" name="email" value="<?php echo $email;?>" required>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Nume</label>
<input type="text" class="form-control form-control-sm" placeholder="Popovici" name="nume" value="<?php echo $nume;?>" required/>
</div>

<div class="col-lg">
<label>Prenume</label>
<input type="text" class="form-control form-control-sm" placeholder="Ion" name="prenume" value="<?php echo $prenume;?>" required/>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Tip user:</label>
<?php
echo $user_icon;
?>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Status Cont: </label>
<?php
                     if($status == "1")
                     {
                        ?>
                        <span class="badge badge-pill badge-success">Activ</span>
                        <?php
                     }
                     else
                     {
                        ?>
                        <span class="badge badge-pill badge-danger">Banat</span>
                       <?php
                     }
                     ?>
</div>
</div>
<hr />
<div class="form-row">
<div class="col-lg">
<label>Data creare: </label>
<?php echo $created_at;?>
</div>
</div>

      <div class="text-center">
                     <a href="index.php?p=users"><button type="button" class="btn btn-secondary btn-sm">Inapoi</button></a>
                     <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=change_pass"><button type="button" class="btn btn-warning btn-sm">Schimba parola</button></a>
                     <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#remove_modal">Elimina</button></a>
                     
                    <?php
                     if($status == "1")
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=ban"> <button type="button" class="btn btn-outline-danger btn-sm">Baneaza</button></a>
                        <?php
                     }
                     else
                     {
                        ?>
                        <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=unban"> <button type="button" class="btn btn-outline-success btn-sm">Debaneaza</button></a>
                       <?php
                     }
                     ?>
                     

                     <button type="submit" name="save" class="btn btn-success btn-sm">Salveaza</button>
                     
                     </div>
</form>
                    <?php
                }
                else
                {
                    
                }
                ?>
                  
                        <?php
                        
                  }
                  
                  else
                  {
                    echo "<div class='text-center'>Userul nu exista! <hr> <a href='index.php'>Inapoi</a></div>";
                  }
                  }
                  else
                  {
                        
                        $nume = $_POST["nume"];
                        $prenume = $_POST["prenume"];
                        $username = $_POST["username"];
                        $user_rank  = $_POST["user_rank"];
                        $email = $_POST["email"];
                        //$group_id = $_POST["group_id"];
              
                       
                        
                        if(isset($_POST["group_id"]))
                        {
                            $group_id = $_POST["group_id"];
                        }
                        else
                        {
                            $group_id = "0";
                        }
                        
                        if(isset($_POST["series_id"]))
                        {
                            $series_id = $_POST["series_id"];
                        }
                        else
                        {
                            $series_id = "0";
                        }
                        
                        $date = date("Y-m-d h:i:s");
                        
                        if(!empty($nume) && !empty($prenume) && !empty($username) && !empty($user_rank))
                        {
                            //run update query
                            
                            if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE id='$user_id'")) == 1)
                            {
                                //run query
                                
                                $update_user = mysqli_query($con, "
                                UPDATE users
                                SET
                                first_name='".addslashes($prenume)."', last_name='".addslashes($nume)."',email = '".addslashes($email)."', username='".addslashes($username)."',
                                user_rank = '$user_rank',group_id = '$group_id',series_id = '$series_id' WHERE id = '$user_id'
                                ");
                                
                              
                                if($update_user)
                                {
                                   if(isset($_POST["cat_ids"]))
                                   {
                                        $remove = mysqli_query($con,"DELETE from user_meta WHERE user_id = '$user_id'");
                                        $insert = "";
                                        foreach($_POST["cat_ids"] as $cat_id)
                                        {
                                            if(isset($_POST["is_entitled_$cat_id"]))
                                            {
                                                $is_entitled  = $_POST["is_entitled_$cat_id"];
                                            }
                                            else
                                            {
                                                $is_entitled = 0;
                                                
                                            }
                                            
                                            
                                            
                                            $insert .= "INSERT into user_meta VALUES (DEFAULT,'$user_id','$cat_id','$user_rank','$is_entitled');";
                                        }
                                        
                                        $insert_query = mysqli_multi_query($con,$insert);
                                        if(!$insert_query)
                                        {
                                            echo mysqli_error($con);
                                        }
                                   }
                                   
                                    redirect($_SERVER["REQUEST_URI"]);
                                }
                                else
                                {
                                    echo mysqli_error($con);
                                }
                            }
                            else
                            {
                                 echo "<div class='text-center'>Userul nu exista in sistem! <hr> <a href='index.php?p=admin_users'>Inapoi</a></div>";
                            }
                        }
                        else
                        {
                            echo "<div class='text-center'>Ai ca campuri obligatorii necompletate! <hr> <a href='".$_SERVER["REQUEST_URI"]."'>Inapoi</a></div>";
                        }
                  }
                  ?>
                  
                  </div>
                  </div>
                  </div>
                        <?php
                    }
                    elseif($_GET["action"] == "remove")
                    {
                        ?>
                          <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Sterge user</h3>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block; text-align: center;">
                 <?php
                 if(isset($_GET["act"]) && $_GET["act"] == "yes")
                 {
                                       //check for user data
                    
                    $get_user = mysqli_query($con, "SELECT id FROM users WHERE id='$user_id' LIMIT 1");
                    if(mysqli_num_rows($get_user) == 1)
                    {
                       $a = mysqli_fetch_assoc($get_user);
                    
                    $del_cmd = 
                    array(
                    "DELETE from users WHERE id='$user_id'",
                    "DELETE FROM import_data WHERE user_id='$user_id'",
                    "DELETE FROM grades WHERE student_id='$user_id' OR teacher_id='$user_id'",
                    "DELETE from user_meta WHERE user_id='$user_id'",
                    );
                   
                    $aff_rows = 0;
                    foreach($del_cmd as $current_sql)
                    {
                    $deleteContacts = mysqli_query($con, $current_sql); 
                    $aff_rows = $aff_rows + mysqli_affected_rows($con);
                    }
                    
                        ?>
                        <div class="alert alert-success text-center">Actiune de stergere finalizata!</div>
                        <?php                                      
                    }
                    else
                    {
                        ?>
                        <div class="alert alert-danger text-center">User negasit in DB!</div>
                        <?php
                    }
                 }
                 else
                 {
                    redirect("index.php?p=edit_user&user_id=$user_id&action=edit");
                 }
                
                 ?>
                  </div>
                  </div>
                  </div>
                        <?php
                    }
                 
                    elseif($_GET["action"] == "change_pass")
                    {
                        ?>
                    <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Schimba parola userului</h3>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block;">
                  
                  <?php
                  if(!isset($_POST["change_pass"]))
                  {
                    ?>
                    <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
                          <div class="table-responsive">
                     <table class="table card-table table-vcenter text-nowrap table-striped table-bordered table-hover" style="width: 90%; margin: auto;">
                     
                     <tr>
                     <td>Parola</td>
                     <td><input class="form-control form-control-sm" type="password" name="pass" style="width: 100%;" placeholder="Parola. minim 6 caractere" required=""/></td>
                     </tr>
                     <tr>
                     <td>Confirmare</td>
                     <td><input class="form-control form-control-sm" type="password" name="pass2" placeholder="Confirma parola" required="" style="width: 100%;"/></td>
                     </tr>
              
                     </table>
                     <hr />
                     <div class="text-center">
                     <a href="index.php?p=edit_user&user_id=<?php echo $user_id;?>&action=edit"><button type="button" class="btn btn-secondary">Inapoi</button></a>
                     <button type="submit" name="change_pass" class="btn btn-success">Schimba parola</button>
                     </div>
                     
                     </div>
                     </form>
                    
                    <?php
                    
                  }
                  else
                  {
                        $pass = $_POST["pass"];
                        $pass2  = $_POST["pass2"];
                        
                        if(strlen($pass) < 6)
                        {
                            echo "<div class='text-center'>Parola trebuie sa contina maxim <b>6</b> caractere! <hr> <a href='".$_SERVER["REQUEST_URI"]."'>Inapoi</a></div>";
                        }
                        else
                        {
                            if($pass == $pass2)
                            {
                                $update_pass = mysqli_query($con, "UPDATE users SET password = '".md5($pass)."' WHERE id = '$user_id'");
                                if($update_pass)
                                {
                                    echo "<div class='text-center'>Parola inlocuita cu: <b>$pass</b>. Perfect! <hr> <a href='index.php?p=edit_user&user_id=$user_id&action=edit'>Inapoi</a></div>";
                                }
                                else
                                {
                                    echo mysqli_error($con);
                                }
                            }
                            else
                            {
                                echo "<div class='text-center'>Parolele nu se potrivesc! <hr> <a href='".$_SERVER["REQUEST_URI"]."'>Inapoi</a></div>";
                            }
                  
                        }
                  }
                  ?>
                  
                  </div>
                  </div>
                  </div>    
                        <?php
                    }
                   
                    elseif($_GET["action"] == "ban")
                    {
                        ?>
                        <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Banare utilizator</h3>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block; text-align: center;">
                  
                  <?php
                 if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE id='$user_id'")) == 1)
                            {
                               $ban_user = mysqli_query($con, "UPDATE users SET status='0' WHERE id='$user_id'");
                               if($ban_user)
                               {
                                    redirect("index.php?p=edit_user&user_id=$user_id&action=edit");
                               }
                               else
                               {
                                echo mysqli_error($con);
                               }
                            }
                            else
                            {
                                 echo "<div class='text-center'>Userul nu exista in DB! <hr> <a href='index.php?p=edit_user&user_id=$user_id&action=edit'>Inapoi</a></div>";
                            }
                  ?>
                  
                  </div>
                  </div>
                  </div>
                        <?php
                    }
                    elseif($_GET["action"] == "unban")
                    {
                        ?>
                     <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Debaneaza user</h3>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block; text-align: center;">
                  
                  <?php
                 if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE id='$user_id'")) == 1)
                            {
                               $ban_user = mysqli_query($con, "UPDATE users SET status='1' WHERE id='$user_id'");
                               if($ban_user)
                               {
                                    redirect("index.php?p=edit_user&user_id=".$user_id."&action=edit");
                               }
                               else
                               {
                                echo mysqli_error($con);
                               }
                            }
                            else
                            {
                                 echo "<div class='text-center'>Userul nu exista in DB! <hr> <a href='index.php?p=admin_users'>Inapoi</a></div>";
                            }
                  ?>
                  
                  </div>
                  </div>
                  </div>   
                        <?php
                    }
                    else
                    {
                        echo "<div class='text-center'>Selected action does not exist or has not been defined! <hr> <a href='index.php'>Inapoi</a></div>";
                    }
                }
                else
                {
                    redirect("index.php");
                }
            }
            else
            {
                redirect("index.php");
            }
            
            ?>
                </div>
                </div>             
                </div>
                </div>