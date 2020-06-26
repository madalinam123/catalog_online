<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Administrare Serii</li>     
</ol>
<div class="card" style="font-size: 14px;">
<div class="card-header">
    <h5>Adauga Serie</h5>
</div>
<div class="card-body">
<?php
if(!isset($_POST["add_group"]))
{
    ?>
    <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
        <div class="form-row">
        <div class="col-lg">
            <label>Nume Serie</label>
            <input type="text" min="4" maxlength="20" name="group_name" class="form-control form-control-sm" placeholder="Seria A" required/>
        </div>
        </div>
        <hr />
        <div class="form-row">
        <div class="col-lg">
        <div class="text-center">
            <a href="index.php?p=series&action=view_all" class="btn btn-secondary btn-sm">Inapoi</a>
            <button type="submit" name="add_group" class="btn btn-success btn-sm">Adauga Serie</button>
        </div>
        </div>
        </div>
    </form>
    <?php
}
else
{
    //run save quiery
    $group_name = $_POST["group_name"];
    
    if(mysqli_num_rows(mysqli_query($con, "SELECT group_name FROM series WHERE group_name = '".addslashes($group_name)."'")) < 1)
    {
        $insert = mysqli_query($con, "INSERT into series VALUES (DEFAULT,'".addslashes($group_name)."','".date("Y-m-d H:i:s")."')");
        if($insert)
        {
            echo "<div class='text-center'>Serie adaugata cu succes!!<hr><a href='index.php?p=add_series'>Inapoi</a></div>";
        }
        else
        {
            echo mysqli_error($con);
        }
    }
    else
    {
        echo "<div class='text-center'>Exista deja o Serie cu numele dat! Incearca din nou!<hr><a href='index.php?p=add_series'>Inapoi</a></div>";
    }
}
?>
</div>
</div>