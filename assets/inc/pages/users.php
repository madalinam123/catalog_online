<?php
$session = return_session();

if ($session["user_rank"] != "admin") {
    redirect("index.php");
}

?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Administrare useri</li>
</ol>
                        
                        
              <div class="card">
                  <div class="card-body">
                  <div style="width: 100%; display: block;">

    <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="GET">
	<input type="hidden" name="p" value="users">
  
<div class="row">

  <div class="col-lg">
  <label>Username</label>
  <input type="text" class="form-control form-control-sm" placeholder="Username/Nr matricol" name="username" value="<?php echo isset($_GET['username']) ? $_GET['username'] : '' ?>">
  </div>
  
   <div class="col-lg">
   <label>Status cont</label>
   <select name="active" class="form-control form-control-sm">
  <option value="">Toate</option>
  <option value="1" <?php if(isset($_GET["active"]) && $_GET["active"] == "1") {echo "selected";}?>>Activ</option>
  <option value="0" <?php if(isset($_GET["active"]) && $_GET["active"] == "0") {echo "selected";}?>>Banat</option>
  
   </select>
   </div>
  
  <div class="col-lg">
  <label>Rank</label>
  <select name="rank" class="form-control form-control-sm">

  <option value="">Toate</option>
  <option value="student" <?php if(isset($_GET["rank"]) && $_GET["rank"] == "student") {echo "selected";}?>>Student</option>
  <option value="teacher" <?php if(isset($_GET["rank"]) && $_GET["rank"] == "teacher") {echo "selected";}?>>Profesor</option>
  <option value="admin" <?php if(isset($_GET["rank"]) && $_GET["rank"] == "admin") {echo "selected";}?>>Admin</option>
  
</select>
  </div>

    <div class="col-lg">
  <label>Grupa</label>
  <select name="group_id" class="form-control form-control-sm">
  <option value="">Toate</option>
    <?php
$get_groups = mysqli_query($con,"SELECT * FROM groups  order by group_name ASC");
if(mysqli_num_rows($get_groups) > 0)
{
    while($a = mysqli_fetch_assoc($get_groups))
    {
        if(isset($_GET["group_id"]) && $_GET["group_id"] == $a["id"])
        {
            $selected = "selected";
        }
        else
        {
            $selected = "";
        }
        
        echo "<option value='".$a["id"]."' $selected>".$a["group_name"]."</option>";
    }
       
}
?>
  
</select>
  </div>
   <div class="col-lg">
  <label>Serie</label>
  <select  name="series_id" class="form-control form-control-sm">
  <option value="">Toate</option>
<?php
$get_series = mysqli_query($con,"SELECT * FROM series order by group_name ASC");
if(mysqli_num_rows($get_series) > 0)
{
    while($b = mysqli_fetch_assoc($get_series))
    {
       if(isset($_GET["series_id"]) && $_GET["series_id"] == $b["id"])
        {
            $selected = "selected";
        }
        else
        {
            $selected = "";
        }
        
        echo "<option value='".$b["id"]."' $selected>".$b["group_name"]."</option>";
    }
       
}
?>
</select>
  </div>
  
  <div class="col-lg">
  <label>Sortare</label>
<select name="sort" class="form-control form-control-sm">
<option value="">Toate</option>
<option value="name_asc" <?php if(isset($_GET["sort"]) && $_GET["sort"] == "name_asc") {echo "selected";}?>>Nume Aa-Zz</option>
<option value="name_desc" <?php if(isset($_GET["sort"]) && $_GET["sort"] == "name_desc") {echo "selected";}?>>Nume Zz-Aa</option>
<option value="date_asc" <?php if(isset($_GET["sort"]) && $_GET["sort"] == "date_asc") {echo "selected";}?>>Cele mai vechi</option>
<option value="date_desc" <?php if(isset($_GET["sort"]) && $_GET["sort"] == "date_desc") {echo "selected";}?>>Cele mai noi</option>
   </select>
  </div>
  
  <div class="col-lg">
   <label>&nbsp;</label>
   <button type="submit" class="form-control form-control-sm  btn-sm" name="filter">Filtreaza</button>
  </div>
  
</div>
    </div>
  
   </form>

   </div>
   <hr>
                  <?php

$limit = "1000";

if (isset($_GET["page"])) {
    $page = $_GET["page"];
    if ($page < 1) {
        $page = 1;
    } else {
        $page = $page;
    }
} else {
    $page = 1;
}
$offset = ($page - 1) * $limit;


if (isset($_GET["sort"])) {
    $params_users = array(
        "sort" => array(
            1 => array(
                "GET" => "name_asc",
                "sql_field" => "last_name",
                "order_sql" => "ASC",
                ),
            2 => array(
                "GET" => "name_desc",
                "sql_field" => "last_name",
                "order_sql" => "DESC",
                ),
            3 => array(
                "GET" => "date_desc",
                "sql_field" => "created_at",
                "order_sql" => "DESC",
                ),
            4 => array(
                "GET" => "date_asc",
                "sql_field" => "created_at",
                "order_sql" => "ASC",
                ),
            ),

        "criteria" => array(
            1 => array(
                "GET" => "rank",
                "sql_field" => "user_rank",

                ),
            2 => array(
                "GET" => "active",
                "sql_field" => "status",

                ),

            3 => array(
                "GET" => "series_id",
                "sql_field" => "series_id",

                ),
            4 => array(
                "GET" => "username",
                "sql_field" => "username",

                ),
             5 => array(
                "GET" => "group_id",
                "sql_field" => "group_id",

                ),

            ),


        );

    $query_pure = gen_query($params_users, "users");


} else {
    $query_pure = "SELECT * FROM users ORDER BY created_at DESC";
}



$total_pages = mysqli_query($con, $query_pure);

$cp = mysqli_num_rows($total_pages);
$tp = ceil($cp / $limit);

$get_users = mysqli_query($con, "$query_pure  LIMIT $offset, $limit");

if (mysqli_num_rows($get_users) > 0) {
?>
                      <div class="table-responsive">
                    <table class="table card-table table-hover data-view" style="font-size: 14px;width:100%;">
                    
                      <thead class="thead-dark">
                        <tr>
                          <th>#</th>
                      
                          <th>Username/Nr.Mat</th>
                          <th>Nume complet</th>
                          <th>Data creare</th>
                          <th>Rank</th>
                          <th>Materie</th>
                          <th>An Studiu</th>
                          <th>Grupa</th>
                          <th>Seria</th>
                          <th>Status</th>
                         
                        </tr>
                      </thead>
                      <tbody>
                                                  <?php
    $i = 1 + $offset;
    while ($a = mysqli_fetch_array($get_users)) {
        $user_id = $a["id"];
        $username = $a["username"];
        $name = $a["last_name"] . " " . $a["first_name"];

        $created_at = $a["created_at"];
        $user_rank = $a["user_rank"];
        //get_status

        if ($a["status"] == "0") {
            $status = '<span class="badge badge-pill badge-danger">Banat</span>';

        } else {
            $status = '<span class="badge badge-pill badge-success">Activ</span>';
        }
        
        $year = return_user_cat($user_id);
        $materie = return_teacher_cat($user_id);
        $group_id = $a["group_id"];
        
        $series_id = $a["series_id"];
        
        $series_name = return_series_name($series_id);
        $group_name = return_group_name($group_id);



        if ($user_rank == "admin") {
            $user_icon = 'Admin - <i class="fas fa-award"></i>';
        } elseif ($user_rank == "teacher") {
            $user_icon = 'Profesor - <i class="fas fa-school"></i>';
        } elseif ($user_rank == "student") {
            $user_icon = 'Student - <i class="fas fa-user-graduate"></i>';
        }

    
    $alert = group_alert($user_id);
    if(!empty($alert))
    {
        $alert = "<span style='color:red'>(!)</span>";
    }
    else
    {
        $alert = "";
    }
    
?>
                                <tr>
                                <td><?php echo $i; ?></td>
                              
                                <td><a href="index.php?p=edit_user&user_id=<?php echo $user_id; ?>&action=edit"><?php echo $username; ?></a></td>
                                <td><?php echo $alert;?> <?php echo $name; ?></td>
                                <td><?php echo $created_at; ?></td>
                                <td><?php echo $user_icon; ?></td>
                                <td><?php echo $materie; ?></td>
                                <td><?php echo $year; ?></td>
                                <td><?php echo $group_name;?></td>
                                <td><?php echo $series_name;?></td>
                                <td><?php echo $status; ?></td>
                                </tr>
                                <?php
        $i++;
    }

?>
                      
                      </tbody>
                      
                      </table>
                      </div>
                      <?php
    foreach ($_GET as $key => $value) {
        $links[] = "$key=$value";
    }

    $link = implode("&", $links);

?>
                      <hr>
      <?php
    if ($tp > 1) {

?>
<nav>
  <ul class="pagination justify-content-center">
  <li class="page-item"><a class="page-link" href='<?php echo "index.php?$link&page=1"; ?>'>Prima pagina</a></li>
    <li class="page-item">
      <a class="page-link" href="<?php if ($page <= 1) {
            echo '#';
        } else {
            echo "index.php?$link&page=" . ($page - 1);
        } ?>">Inapoi</a>
    </li>
    <li class="page-item">
      <a class="page-link" href="<?php if ($page >= $tp) {
            echo '#';
        } else {
            echo "index.php?$link&page=" . ($page + 1);
        } ?>">Inainte</a>
    </li>
   
 
    
  </ul>
</nav>
  <?php
    }
?>
                    <?php
} else {
?>
                    <div class="text-center"><i>Nici un user gasit pentru criteriul definit!</i></div>
                    <?php
}


?>               
                  </div>          