<style>
body
{
    background-color: #3a3d66;
}
</style>

<div class="card card-login mx-auto mt-5 col-md-4 login-form" style="">
      <div class="card-header">Introdu datele de logare</div>
      <div class="card-body">
        <form action="<?php $_SERVER["PHP_SELF"];?>" method="POST">
          <div class="form-group">
            <div class="form-label-group">
              <input type="text" id="inputEmail" class="form-control" placeholder="Numar matricol/Username" required="required" autofocus="autofocus" name="username">
              
            </div>
          </div>
          <div class="form-group">
            <div class="form-label-group">
              <input type="password" id="inputPassword" class="form-control" placeholder="Parola aici" required name="password">
              
            </div>
          </div>
          
        <button class="btn btn-primary btn-block" type="submit" name="login">Logare</button>
        </form>
        <hr />
        
        <div class="text-center">
        <?php
        if(isset($_POST["login"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
            
            $exec = login($username,$password);
            
            if($exec["status"] == "true")
            {
                redirect("index.php");
            }
            else
            {
                ?>
                <div class="alert alert-danger"><?php echo $exec["error"];?></div>
                <?php
            }
        }
        ?>
         <span class="d-block small"></span>
        </div>
      </div>
    </div>