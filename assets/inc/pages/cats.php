<?php
$session = return_session();

if($session["status"] == "true" && $session["user_rank"] != "admin")
{
    redirect("index.php");
}


?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
       <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Administrare materii</li>
</ol>
        
<div class="container-fluid">
<div class="card">
<div class="card-body">
<div class="card">
        <div class="card-header bg-secondary text-white"><h5>Management Materii</h5></div>
        <div class="card-body">
                     
<?php
if(isset($_GET["action"]) && !empty($_GET["action"]))
{
    if($_GET["action"] == "view_all")
    {
        //all materies
        ?>
       <div class="col-md-10" style="margin:  auto;">
       <div class="card">
       <div class="card-header"><h6>Materii</h6> <small><span style='color: red'>(!)</span> = Lipsa profesori titulari</small></div>
       <div class="card-body">
       <?php
        $get_parents = mysqli_query($con, "SELECT * from materii WHERE cat_parent='0'");
        if(mysqli_num_rows($get_parents) > 0)
        {
            ?>
             <ul class="list-group">
            <?php
            while($a = mysqli_fetch_assoc($get_parents))
            {
                $parent_id = $a["cat_id"];
                $parent_name = $a["cat_name"];
                
                $get_children = mysqli_query($con, "SELECT * FROM materii WHERE cat_parent='$parent_id'");
                if(mysqli_num_rows($get_children) > 0)
                {
                    ?>
                     <li class = "list-group-item" style="font-size: 16px;">
                     <div class="d-flex justify-content-between">
                     <div><i class="fa fa-home" aria-hidden="true"></i> <a href = "#"><?php echo $parent_name;?></a> </i>
                      </div>
                      <!-- <div style="font-size: 14px;"><span style="font-size: 13px;"></span></div> -->
                      </div>
                     </li>
                     <ul class = "list-group list-group-item">
                    <?php
                    while($b = mysqli_fetch_assoc($get_children))
                    {
                        $child_name = $b["cat_name"];
                        
                        $child_id = $b["cat_id"];
                        
                        $teachers =  return_cat_teachers($child_id);
                        $is_entitled_alert = is_entitled_alert($child_id);   
                    ?>
                     <li class = "list-group-item">
                      <div class="d-flex justify-content-between">
                     <div><i class="fa fa-child" aria-hidden="true"></i> <b><?php echo $child_name;?></b> <small><?php echo $teachers;?></small> <?php echo $is_entitled_alert;?> </div> 
                     <div style="font-size: 14px;"><span style="font-size: 12px;"><a href="index.php?p=cats&action=edit&id=<?php echo $child_id;?>&section=edit" >[edit]</a></span></div>
                     </div>
                     </li>
                     <?php
                     $get_subs = mysqli_query($con, "SELECT * from materii WHERE cat_parent='$child_id'");
                     if(mysqli_num_rows($get_subs) > 0)
                     {
                        ?>
                         <ul class = "list-group list-group-item">
                         <?php
                         while($c = mysqli_fetch_array($get_subs))
                         {
                            $sub_id = $c["cat_id"];
                            $sub_name = $c["cat_name"];
                            ?>
                              <li class = "list-group-item">
                               <div class="d-flex justify-content-between">
                     <div><i class="fa fa-child" aria-hidden="true"></i> <b><?php echo $sub_name;?></b></div> 
                     <div style="font-size: 14px;"><span style="font-size: 12px;"><a href="index.php?p=cats&action=edit&id=<?php echo $sub_id;?>&section=edit" >[edit]</a></span></div>
                     </div>
                              </li>
                            <?php
                         }
                         ?>
                         </ul>
                        <?php
                     }
                     ?>
                    <?php
                    }
                    ?>
                    </ul>
                    <?php
                }
                else
                {
                    ?>
                     <li class = "list-group-item" style="font-size: 16px;">
                       <div class="d-flex justify-content-between">
                       <div> <i class="fa fa-home" aria-hidden="true"></i> <a  href = "#"><?php echo $parent_name;?></a> </div>
                       <div style="font-size: 14px;"><span style="font-size: 13px;"><a href="index.php?p=cats&action=edit&id=<?php echo $parent_id;?>&section=edit">[edit]</a></span> (<?php echo build_tree($parent_id);?>)</div>
                       </div>
                     </li>
                    <?php
                }
                ?>
         </ul>
                <?php
            }
            ?>
            </ul>
            <?php
        }
       ?>
       </div>
       </div>
       </div>
       
        <?php
    }
    elseif($_GET["action"] == "edit")
    {
        //edit materii
        ?>
        <div class="col-md-8" style="margin:  auto;">
        
        <div class="card">
        <div class="card-header"><h6>Editare materie</h6></div>
        <div class="card-body">
        <?php
        if(isset($_GET["id"]) && !empty($_GET["id"]))
        {
            $id = $_GET["id"];
            
          if(isset($_GET["section"]) && !empty($_GET["section"]))
          {
                if($_GET["section"] == "edit")
                {
                  
                    if(!isset($_POST["save_cat"]))
                    {
                    $get_cat = mysqli_query($con, "SELECT * FROM materii WHERE cat_id='$id' LIMIT 1");
                    
                    if(mysqli_num_rows($get_cat) == 1)
                    {
                        $a = mysqli_fetch_assoc($get_cat);
                        
                        $cat_id = $a["cat_id"];
                        $cat_name = $a["cat_name"];
                        
                        $cat_parent = $a["cat_parent"];
                        $cat_desc = $a["cat_desc"];
                        
                      if($cat_parent == "0")
                      {
                            ?>
                            <div class="alert alert-warning border border-dark" role="alert">
                            Atentie, aceasta materie este una <b>importanta</b>. Editarea sau <b>stergerea</b> ei poate duce la erori critice!
                            </div>
                            <?php
                            }
                            ?>
                            <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
                            <input type="hidden" name="cat_id" value="<?php echo $id;?>"/>
                            <div class="form-group">
                            <label>Edit nume</label>
                            <input type="text" class="form-control form-control-sm" value="<?php echo $cat_name;?>" placeholder="Introdu numele materiei..." required name="cat_name" id="cat_name"/>
                            </div>
                            <div class="form-group">
                            <label>Anul</label>
                            <?php
                              echo "<select class='form-control' name='parent_id' id='parent_id'>";
                            $get_parent = mysqli_query($con,"SELECT cat_id,cat_name FROM materii WHERE cat_id='$cat_parent' LIMIT 1");
                            if(mysqli_num_rows($get_parent)  == 1)
                            {
                                $c = mysqli_fetch_assoc($get_parent);
                                $parent_name = $c["cat_name"];
                                $parent_id = $c["cat_id"];
                                
                                echo "<option value='$parent_id' id='$parent_name'>[".build_tree($parent_id)."]</option>";
                            }
                            
                            // echo "<option value='0'>Niciunul</option>";
                             $get_cats = mysqli_query($con, "SELECT * FROM materii WHERE cat_id <> '$cat_id' AND cat_parent = '0' order by cat_parent ASC");
                                
                                if(mysqli_num_rows($get_cats) > 0)
                                    {       
                                     while($a = mysqli_fetch_assoc($get_cats))
                                      {
                                       $cats_id= $a["cat_id"];
                                       $cats_name = build_tree($cats_id);
            
                                         echo "<option value='$cats_id' id='$cats_name'>$cats_name</option>";     
                                     }
                                     }
        
                             echo "</select>";
                            ?>
                            <div id="result">
                    
                            </div>
                            </div>
                            <div class="form-group">
            <label>Descriere</label>
               <textarea name="desc" id="desc_body" placeholder="Introdu o descriere. Poti utiliza si diacritice!"><?php echo $cat_desc;?></textarea>
            </div>

                            <div class="modal fade" id="remove_cat_modal" tabindex="-1" role="dialog" aria-labelledby="remove_cat_modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Sterge materie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      Esti sigur ca vrei sa stergi materia selectata? <br>Atentie!<br />
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
        <a href="index.php?p=cats&action=edit&id=<?php echo $id;?>&section=remove_cat&do=yes"><button type="button" class="btn btn-danger">Confirma Stergerea</button></a>
      </div>
    </div>
  </div>
</div>
                            <div class="text-center">
                            <a href="index.php?p=cats&action=view_all"><button type="button" class="btn btn-secondary">Inapoi la materii</button></a>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#remove_cat_modal">Sterge</button>
                            <button type="reset" class="btn btn-info">Reset</button>
                            <button type="submit" class="btn btn-primary" name="save_cat">Salveaza</button>
                            </div>
                            </form>
                            <?php
                    }
                    else
                    {
                        echo "<div class='text-center'>materia nu exista! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la materii</a></div>";
                    }
                    }
                    else
                    {
                        //run save query
                        if(isset($_POST["cat_name"]) && isset($_POST["parent_id"]))
                        {
                            $id = $_POST["cat_id"];
                            $cat_name = $_POST["cat_name"];
                            $parent_id = $_POST["parent_id"];
                            $cat_desc = $_POST["desc"];
                            
                            $cat_slug = slug($cat_name);
                            
                            if(mysqli_num_rows(mysqli_query($con, "SELECT cat_id FROM materii WHERE cat_id='$id'")) == 1)
                            {
                               $update = mysqli_query($con, "UPDATE materii SET cat_name = '".addslashes($cat_name)."',cat_parent='$parent_id',slug='$cat_slug',cat_desc ='".addslashes($cat_desc)."' WHERE cat_id='$id'");
                               if($update)
                               {
                                    redirect("index.php?p=cats&action=edit&id=$id&section=edit");
                               }
                               else
                               {
                                    echo mysqli_error($con);
                               }
                            }
                            else
                            {
                                echo "<div class='text-center'>materia nu exista! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la materii</a></div>";
                            }
                        }
                        else
                        {
                           ?>
                            <div class='text-center'>Toate campurile sunt obligatorii! <hr> <a href='index.php?p=cats&action=edit&id=<?php echo $id;?>&section=edit'>Inapoi la editare</a></div>
                           <?php
                        }
                    }
                }
                elseif($_GET["section"] == "move_cats")
                {
                    
                }
                elseif($_GET["section"] == "remove_cat")
                {
                    if(isset($_GET["do"]) && $_GET["do"] == "yes")
                    {
                        $get_data = mysqli_query($con, "SELECT * from materii WHERE cat_id='$id'");
                        if(mysqli_num_rows($get_data) == 1)
                        {
                            //get all children and children of children
                            $get = mysqli_query($con,"SELECT cat_id FROM materii WHERE cat_parent='$id'");
                            if(mysqli_num_rows($get) > 0 )
                            {
                                while($a = mysqli_fetch_assoc($get))
                                {
                                    $child_id  = $a["cat_id"];
                                    
                                   $get_children_of = mysqli_query($con, "SELECT cat_id FROM materii WHERE cat_parent='$child_id'");
                                   if(mysqli_num_rows($get_children_of) > 0)
                                   {
                                        while($b = mysqli_fetch_assoc($get_children_of))
                                        {
                                            $sub_id = $b["cat_id"];
                                            
                                            //delete children of
                                            $delete_children_of = mysqli_query($con, "DELETE from materii WHERE cat_id='$sub_id'");
                                            if(!$delete_children_of)
                                            {
                                                die(mysqli_error($con));
                                            }
                                        }
                                   }
                                   $delete_children = mysqli_query($con, "DELETE from materii WHERE cat_id='$child_id'");
                                            if(!$delete_children)
                                            {
                                                die(mysqli_error($con));
                                            }
                                }
                            }
                            $delete_parent = mysqli_query($con, "DELETE from materii WHERE cat_id='$id'");
                            if(!$delete_parent)
                            {
                                die(mysqli_error($con));
                            }
                            
                            echo "<div class='text-center'>Query-urile de stergere au fost executate! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la lista de materii</a></div>";
                        }
                        else
                        {
                           echo "<div class='text-center'>materia selectata nu exista! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la lista de materii</a></div>";
                        }
                    }
                    else
                    {
                        redirect("index.php?p=cats&action=view_all");
                    }
                }
                else
                {
                    echo "<div class='text-center'>Sectiunea introdusa nu exista! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la materii</a></div>";
                }
            
          }
          else
          {
                echo "<div class='text-center'>Nu s-a introdus specificat nici o sectiune! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la materii</a></div>";
          }
        }
        else
        {
            echo "<div class='text-center'>Nu s-a introdus nici un ID de materie! <hr> <a href='index.php?p=cats&action=view_all'>Inapoi la materii</a></div>";
        }
        ?>
        </div>
        </div>
           </div>
        <?php
    }
    elseif($_GET["action"] == "add_new")
    {
        ?>
        <div class="col-md-8" style="margin:  auto;">
        
        <!-- ADD new  -->
        <div class="card">
        <div class="card-header"><h6>Adauga materie noua</h6></div>
        <div class="card-body">
        <?php
        if(!isset($_POST["add_cat"]))
        {
            ?>
            <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
            <div class="form-group">
            <label>Nume materie</label>
            <input type="text" class="form-control" placeholder="Introdu numele de materie aici" required="" name="cat_name"/>
            </div>
            <div class="form-group">
            <label>Anul</label>
            <select name="cat_parent" class="form-control">
            <!-- <option value="0">Niciunul</option> -->
            <?php
            $get_cats = mysqli_query($con, "SELECT * FROM materii WHERE cat_id <> '$cat_id' AND cat_parent = '0' order by cat_parent ASC");
                                
                                if(mysqli_num_rows($get_cats) > 0)
                                    {
                                         
                                     while($a = mysqli_fetch_assoc($get_cats))
                                      {
                                       $cats_id= $a["cat_id"];
                                       $cats_name = build_tree($cats_id);
            
                                         echo "<option value='$cats_id' id='$cats_name'>$cats_name</option>";
            
                                     }
                                     }
            ?>
            </select>
            </div>
            <div class="form-group">
            <label>Descriere</label>
               <textarea name="desc" id="desc_body" placeholder="Introdu o descriere. Poti utiliza si diacritice!"></textarea>
            </div>
            <hr />
            <div class="text-center">
            <a href="index.php?p=cats&action=view_all"><button type="button" class="btn btn-secondary">Inapoi</button></a>
            <button type="submit" class="btn btn-primary" name="add_cat">Adauga materie</button>
            </div>
            </form>
            <?php
        }
        else
        {
            //insert cat query
            if(isset($_POST["cat_name"]) && isset($_POST["cat_parent"]))
            {
                $cat_name = $_POST["cat_name"];
                $cat_parent  = $_POST["cat_parent"];
                $cat_slug = slug($cat_name);
                $cat_desc = $_POST["desc"];
                
                if(mysqli_num_rows(mysqli_query($con, "SELECT cat_id FROM materii WHERE cat_name='".addslashes($cat_name)."' AND cat_parent = '$cat_parent'")) < 1)
                {
                    $insert = mysqli_query($con, "INSERT into materii (cat_name,cat_desc,cat_parent,slug) VALUES ('".addslashes($cat_name)."','".addslashes($cat_desc)."','$cat_parent','$cat_slug')");
                    if($insert)
                    {
                        echo "<div class='text-center'>materia <b>$cat_name</b> a fost creata ! <hr> <a href='index.php?p=cats&action=view_all'>Lista materii</a></div>";
                    }
                    else
                    {
                        echo mysqli_error($con);
                    }
                }
                else
                {
                    echo "<div class='text-center'>Materia introdusa deja exista! <hr> <a href='index.php?p=cats&action=add_new'>Inapoi la formular</a></div>";
                }
                
            }
            else
            {
                echo "<div class='text-center'>Numele si parintele de materie sunt obligatorii! <hr> <a href='index.php?p=cats&action=add_new'>Inapoi la formular</a></div>";
            }
        }
        ?>
        </div>
        
        </div>
        </div>
        <?php
    }
    else
    {
        echo "<div class='text-center'>Sectiunea introdusa nu exista! <hr> <a href='index.php'>Acasa</a></div>";
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
       </div>
                        