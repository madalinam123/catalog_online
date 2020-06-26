<?php
$session = return_session();
$user_id = $session["user_id"];

if($session["user_rank"] == "student")
{
    redirect("index.php");
}
?>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="index.php">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Import CSV</li>
</ol>
<div class="card">
<div class="card-header">
Importa Fisier .CSV
</div>
<div class="card-body">
Utilizeaza sectiunea petru a importa studenti prin intermediul fisierelor .CSV<br />
Este foarte important sa se respecte urmatoarea structura: <br />
<ul>
<li>seria</li>
<li>grupa</li>
<li>nume</li>
<li>nr_mat</li>
<li>email</li>
</ul>
<hr />
<div class="alert alert-info">
Parolele se genereaza in functie de numar matricol sub structura: <br />
parola_<b>numar_matricol</b> -> ex: parola_3255440
</div>
<hr />

    <form class="form-control" action="<?php echo $_SERVER["REQUEST_URI"];?>" method="post" name="uploadCSV"
    enctype="multipart/form-data">

    <div class="form-group border p-2">
    <label>An Studiu</label>
    <select class="form-control form-control-sm" name="cat_id" required>
    <option value="">
    Selecteaza Anul
    </option>    
    <?php
if($session["user_rank"] == "admin")
{
    //get all
     $get_year = mysqli_query($con, "SELECT * FROM materii WHERE cat_parent = '0'");
    if(mysqli_num_rows($get_year) > 0)
    {
        while($y = mysqli_fetch_assoc($get_year))
        {
            $year_id = $y["cat_id"];
            
            echo "<option value='$year_id'>".build_tree($year_id)."</option>";
        }
    }
}
elseif($session["user_rank"] == "teacher")
{
    $teacher_years = return_years($user_id);
    
    $year_data = explode(",",$teacher_years);
    foreach($year_data as $year)
    {
        echo "<option value='$year'>".build_tree($year)."</option>";
    }
}
    ?>
    </select>
    </div>
    
    <div class="form-group border p-2">
    <label>Alege un fisier .CSV</label>
    <input type="file" name="file" id="file" accept=".csv" class="form-control form-control-sm" required>
    </div>
    
    <div class="form-group border p-2">
    <label>Selecteaza delimitator (,/;)</label>
     <select name="delimiter" class="form-control form-control-sm" required>
            <option value="" selected disabled>Selecteaza</option>
            <option value=";">; - Microsoft Excel CSV</option>
            <option value=",">, - CSV Normal</option>
            </select>
    </div>
    <hr>
    
    <div class="text-center">
     <button type="submit" id="submit" name="import"
            class="btn btn-primary">Import</button>
    </div>
    
    <div id="labelError"></div>
</form>
<hr />

<?php
$session = return_session();

if (isset($_POST["import"])) {
    
    ?>
<div class="card">
<div class="card-header">Resultat</div>
<div class="card-body alert alert-warning">
    <?php
    $fileName = $_FILES["file"]["tmp_name"];
    $delimiter = $_POST["delimiter"];
    $cat_id = $_POST["cat_id"];
    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = file_get_contents($fileName);
        
       $data =  csv_to_array($file,$delimiter);
       
       if(isset($data[0]["seria"]) && isset($data[0]["grupa"]) && isset($data[0]["nume"]) && isset($data[0]["nr_mat"]) && isset($data[0]["email"]))
       {
            foreach($data as $d)
            {
                $nr_mat = $d["nr_mat"];
                $nume = strtolower($d["nume"]);
                $nume_prenume_array = explode(" ",$nume);
                $nume_prenume_array = array_filter($nume_prenume_array);
                    //run foreach loops
                    foreach($nume_prenume_array as $names)
                    {
                        $names = strtolower($names);
                        $names = ucfirst($names);
                        
                        $name_arr[] = $names;
                    }
                
                    if(count($name_arr) > 1)
                    {
                        $nume_f = $name_arr[0];
                        $nume_p = $name_arr[1];
                    }
                    else
                    {
                        $nume_f = $name_arr[0];
                        $nume_p  = "";
                    }
                    
                $email = $d["email"];
                $serie = $d["seria"];
                $grupa = $d["grupa"];
                
                $group_id = generate_group($grupa);
                $series_id = generate_series($serie);
                
                $import = insert_csv_record($nr_mat,$nume_f,$nume_p,$email,$group_id,$series_id,$cat_id,$session["user_id"]);
                
                echo "<div class='text-center'>Import: nr_mat - $nr_mat  - status: ".$import."</div>";
            }
       }
       else
       {
           echo "<div class='text-center'>Fisier invalid/Delimitator incorect/Campuri negasite!</div>";
       }
    }
?>
</div>
</div>
<?php
}
?>
</div>
</div>
<script type="text/javascript">
	$(document).ready(
	function() {
		$("#frmCSVImport").on(
		"submit",
		function() {

			$("#response").attr("class", "");
			$("#response").html("");
			var fileType = ".csv";
			var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+("
					+ fileType + ")$");
			if (!regex.test($("#file").val().toLowerCase())) {
				$("#response").addClass("error");
				$("#response").addClass("display-block");
				$("#response").html(
						"Invalid File. Upload : <b>" + fileType
								+ "</b> Files.");
				return false;
			}
			return true;
		});
	});
</script>