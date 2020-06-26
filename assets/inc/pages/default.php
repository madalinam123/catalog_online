<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="index.php">Dashboard</a>
  </li>
</ol>   
<div class="card">
<div class="card-header">
Home
</div>
<div class="card-body">

<?php

$session = return_session();

if($session["user_rank"] == "admin")
{
    include "default_content/admin.php";
}
elseif($session["user_rank"] == "teacher")
{
    include "default_content/teacher.php";
}
elseif($session["user_rank"] == "student")
{
    include "default_content/user.php";
}
?>
</div>
</div>  