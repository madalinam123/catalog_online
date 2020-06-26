<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Setari cont</li>
</ol>
<?php
$session = return_session();

if($session["status"] == "false")
{
    redirect("index.php");
}
?>
              <div class="card" style="font-size: 14px;">
                  <div class="card-header">
                    <h5 class="card-title">Setari cont</h5>
                  </div>
                  <div class="card-body">    
    
                    <div class="card">
                  <div class="card-header">
                    <h6 class="card-title">Schimba parola</h6>
                  </div>
                  <div class="card-body">
                  <div style="width: 100%; display: block;">
                  
                  <?php
                  $logged = $session["user_id"];
                  
                  if(!isset($_POST["change_pass"]))
                  {
                    ?>
                    <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
                          <div class="table-responsive">
                     <table class="table card-table table-vcenter text-nowrap table-striped table-bordered table-hover" style="width: 90%; margin: auto; font-size: 14px;">
                     
                     <tr>
                     <td>Parola</td>
                     <td><input type="password" class="form-control" name="pass" style="width: 100%;" placeholder="Introdu o parola. Minim 6 caractere..." required=""/></td>
                     </tr>
                     <tr>
                     <td>Confirma Parola</td>
                     <td><input type="password" class="form-control" name="pass2" placeholder="Confirma Parola..." required="" style="width: 100%;"/></td>
                     </tr>
              
                     </table>
                     <hr />
                     <div class="text-center">
                     <a href="index.php?sb=profile"><button type="button" class="btn btn-secondary">Inapoi</button></a>
                     <button type="submit" name="change_pass" class="btn btn-success">Schimba Parola</button>
                     </div>
                     
                     </div>
                     </form>
                    
                    
                    <?php
                    
                  }
                  else
                  {
                        $pass = $_POST["pass"];
                        $pass2  = $_POST["pass2"];
                        
                        $date = date("Y-m-d h:i:s");
                        
                        if(strlen($pass) < 6)
                        {
                            echo "<div class='text-center'>Parola trebuie sa contina minim <b>6</b> caractere! <hr> <a href='".$_SERVER["REQUEST_URI"]."'>Inapoi</a></div>";
                        }
                        else
                        {
                            if($pass == $pass2)
                            {
                                $update_pass = mysqli_query($con, "UPDATE users SET password = '".md5($pass)."' WHERE id = '$logged'");
                                if($update_pass)
                                {
                                    echo "<div class='text-center'>Parola a fost schimbata cu <b>$pass</b>. Perfect! <hr> <a href='index.php?sb=profile'>Inapoi</a></div>";
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
                  </div>
                  </div>