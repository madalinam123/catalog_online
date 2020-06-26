<?php
$session = return_session();

if ($session["user_rank"] != "teacher") {
    redirect("index.php");
}
$years = return_years($session["user_id"]);


?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Administrare studenti</li>
</ol>
                        
                        
              <div class="card">
                  <div class="card-body">
                  <div style="width: 100%; display: block;">

   
   
    <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="GET">
	<input type="hidden" name="p" value="students">
    <input type="hidden" name="user_rank" value="student"/>
    
<div class="row">


  <div class="col-lg">
  <label>Nr Matricol</label>
  <input type="text" class="form-control form-control-sm" placeholder="ex: 1234533" name="username" value="<?php echo isset($_GET['username']) ? $_GET['username'] : '' ?>">
  </div>
  
      <div class="col-lg">
  <label>An Studiu</label>
  <select name="cat_id" class="form-control form-control-sm">
  <option value="">Toate</option>
    <?php
    
    if(!empty($years))
    $years_ar = explode(",",$years);
     
    {
        
        foreach($years_ar as $y)
        {
            if(isset($_GET["cat_id"]) && $_GET["cat_id"] == $y)
        {
            $selected = "selected";
        }
        else
        {
            $selected = "";
        }
            echo "<option value='$y' $selected>".build_tree($y)."</option>";
        }
    }
    ?>
  
</select>
  </div>
  
  <div class="col-lg">
  <label>Seria</label>
  <select name="series_id" class="form-control form-control-sm">
  <option value="">Toate</option>
    <?php
$get_series = mysqli_query($con,"SELECT * FROM series  order by group_name ASC");
if(mysqli_num_rows($get_series) > 0)
{
    while($a = mysqli_fetch_assoc($get_series))
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
   <label>&nbsp;</label>
   <button type="submit" class="form-control form-control-sm  btn-sm" name="filter">Filtreaza</button>
  </div>
</div>
</div>
</form>
</div>
   <hr>
<?php
$_GET["user_rank"] = "student";

if(isset($_GET["cat_id"]) OR !empty($_GET["cat_id"]))
{
    $year_ids = $_GET["cat_id"];
}
else
{
    
    $year_ids  = $years;
}

if(empty($_GET["cat_id"]))
{
    $year_ids = $years;
}

$limit = "10000";
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

if (isset($_GET["filter"])) {
    $params_users = array(
        
        "criteria" => array(
             2 => array(
                "GET" => "user_rank",
                "sql_field" => "user_rank",

                ),
            4 => array(
                "GET" => "username",
                "sql_field" => "username",

                ),
             5 => array(
                "GET" => "group_id",
                "sql_field" => "group_id",

                ),
             6 => array
             (
                "GET" => "series_id",
                "sql_field" => "series_id",
             ),
            
            ),
        );

    $query_pure = gen_query($params_users, "users");

} else {
    $query_pure = "SELECT * FROM users WHERE user_rank = 'student' ";
}

$total_pages = mysqli_query($con, $query_pure);

$cp = mysqli_num_rows($total_pages);
$tp = ceil($cp / $limit);

$get_users = mysqli_query($con, "$query_pure AND id IN (SELECT user_id FROM user_meta WHERE cat_id IN ($year_ids))");

if (mysqli_num_rows($get_users) > 0) {
?>
                      <div class="table-responsive">
                    <table class="data-view table card-table table-hover" style="font-size: 14px;">
                    
                      <thead class="thead-dark">
                        <tr>
                          <th>#</th>
                      
                          <th>Numar Matricol</th>
                          <th>Nume Complet</th>
                          <th>An Studiu</th>
                          <th>Grupa</th>
                          <th>Serie</th>
                          <th>Data creare</th>
                        </tr>
                      </thead>
                      <tbody>
                                                  <?php
    $i = 1+$offset;
    while ($a = mysqli_fetch_array($get_users)) {
        $user_id = $a["id"];
        $username = $a["username"];
        $name = ucfirst($a["last_name"]) . " " . ucfirst($a["first_name"]);
     
        $created_at = $a["created_at"];
        $user_rank = ucfirst(rank($a["user_rank"]));
        //get_status
        
        $year = return_user_cat($user_id);

        $group_id = $a["group_id"];
        
        $series_id = $a["series_id"];
        
        $series_name = return_series_name($series_id);
        $group_name = return_group_name($group_id);
?>
                                <tr>
                                <td><?php echo $i; ?></td>
                                
                                <td><a href="#"><?php echo $username; ?></a></td>
                                <td><?php echo $name; ?></td>
                                <td><?php echo $year; ?></td>
                                <td><?php echo $group_name;?></td>
                                <td><?php echo $series_name;?></td>
                                <td><?php echo $created_at; ?></td>
                               
                                </tr>
                                <?php
        $i++;
    }
?>
                      
                      </tbody>               
                      </table>
                      </div>
                      <hr>
      <?php
    if ($tp > 1) {
            
?>
<nav>
  <ul class="pagination justify-content-center">
  <li class="page-item"><a class="page-link" href='<?php echo
        "index.php?$link&page=1"; ?>'>Prima pagina</a></li>
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