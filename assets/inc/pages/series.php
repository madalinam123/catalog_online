<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Administrare serii</li>     
</ol>
<div class="card" style="font-size: 14px;">
<div class="card-header">
<div class="row justify-content-between">
<div><h5>Administrare serii</h5></div>
<div><a href="index.php?p=add_series" class="btn btn-outline-success btn-sm">Adauga serie</a></div>
</div>
</div>
<div class="card-body">
<?php
if(isset($_GET["action"]) && !empty($_GET["action"]))
{
    if($_GET["action"] == "view_all")
    {
        ?>
<div class="card" style="font-size: 14px;">
<div class="card-header">
<h6>Toate seriie</h6>
</div>
<div class="card-body">
<?php
$get_series = mysqli_query($con,"SELECT * FROM series  ORDER by date DESC");
if(mysqli_num_rows($get_series) > 0)
{
    ?>
    <table class="data-view table table-bordered table-hover" style="font-size: 14px;width: 100%;">
    <thead class="thead-dark">
    <tr>
    <th>#</th>
    <th>Nume</th>
    <th>Data Adaugare</th>
    <th>#</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 1;
    while($a = mysqli_fetch_assoc($get_series))
    {
        $id = $a["id"];
        $name = $a["group_name"];
        $date = $a["date"];
        
        ?>
        
    <div class="modal fade" id="remove_<?php echo $id;?>_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sterge serie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Esti sigur ca vrei sa stergi seria?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Nu</button>
        <a class="btn btn-danger" href="index.php?p=series&action=remove&id=<?php echo $id;?>&confirm=yes">Sterge</a>
      </div>
    </div>
  </div>
</div>
        
        <tr>
        <td><?php echo $i;?></td>
        <td><a href="index.php?p=series&action=edit&id=<?php echo $id;?>"><?php echo $name;?></a></td>
        <td><?php echo $date;?></td>
        <td><a href="#" data-toggle="modal" data-target="#remove_<?php echo $id;?>_modal" class="btn btn-outline-danger btn-sm">Sterge</a></td>
        </tr>
        <?php
        $i++;
    }
    ?>
    </tbody>
    </table>
    <?php
}
else
{
    echo "<div class='text-center'>Nu s-au gasit serii in baza de date!</div>";
}
?>
</div>
</div>
        <?php
    }
    elseif($_GET["action"] == "edit")
    {
        ?>
<div class="card" style="font-size: 14px;">
<div class="card-header">
<h6>Editare serie</h6>
</div>
<div class="card-body">
<?php
if(isset($_GET["id"]) && !empty($_GET["id"]))
{
    $id = $_GET["id"];
    
    $get_data = mysqli_query($con,"SELECT * FROM series WHERE id='$id' LIMIT 1");
    if(mysqli_num_rows($get_data) == 1)
    {
        $a = mysqli_fetch_assoc($get_data);
        $name = $a["group_name"];
        
        if(!isset($_POST["save"]))
        {
            ?>
    <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
    <div class="form-row">
    <div class="col-lg">
    <label>Nume serie</label>
    <input type="text" min="4" maxlength="20" name="group_name" value="<?php echo $name;?>" class="form-control form-control-sm" placeholder="serie A" required/>
    </div>
    </div>
    <hr />
    <div class="form-row">
    <div class="col-lg">
    <div class="text-center">
    <a href="index.php?p=series&action=view_all" class="btn btn-secondary btn-sm">
    Inapoi
    </a>
    <button type="submit" name="save" class="btn btn-success btn-sm">
    Modifica
    </button>
    </div>
    </div>
    </div>
    </form>
            <?php
        }
        else
        {
            //run update query
            $name = $_POST["group_name"];
            $update = mysqli_query($con, "UPDATE series SET group_name = '".addslashes($name)."' WHERE id='$id'");
            if($update)
            {
                redirect("index.php?p=series&action=view_all");
            }
            else
            {
                echo mysqli_error($con);
            }
        }
    }
    else
    {
         echo "<div class='text-center'>Nu s-a gasit nici o serie cu id-ul specificat!</div>";
    }
}
else
{
    
    echo "<div class='text-center'>Nu a fost specificat nici un ID de serie!</div>";
    
}
?>
</div>
</div>
        <?php
    }
    elseif($_GET["action"] == "remove")
    {
        if(isset($_GET["id"]) && !empty($_GET["id"]))
        {
            if(isset($_GET["confirm"]) && $_GET["confirm"] == "yes")
            {
                $serie_id = $_GET["id"];
                
                $del = mysqli_query($con, "DELETE from series WHERE id='$serie_id' LIMIT 1");
                if($del)
                {
                    redirect("index.php?p=series&action=view_all");
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
    }
    else
    {
        echo "Actiunea specificata nu exista!";
    }
}
else
{
    echo "Nici o actiune specificata!";
}
?>
</div>
</div>