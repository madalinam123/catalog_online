<?php
require_once 'inc/lib/mpdf/vendor/autoload.php';
require "inc/helpers.php";


if (isset($_GET["cat_id"]) && !empty($_GET["cat_id"])) {

    $cat_id = $_GET["cat_id"];
    $cat_name = basename(build_tree($cat_id));
    $cat_year = return_year($cat_id);
    $cat_year = build_tree($cat_year);
    $get_entitled  = mysqli_query($con, "SELECT user_id FROM user_meta WHERE cat_id = '$cat_id' AND is_entitled='1' and meta_type = 'teacher'");
    if(mysqli_num_rows($get_entitled) == 1)
    {
        $a = mysqli_fetch_assoc($get_entitled);
        $user_id = $a["user_id"];
        $u = return_user($user_id);
        $teacher = $u["last_name"]." ".$u["first_name"];
        
    }
    else
    {
        $teacher = "";
    }
        $data_html = "<style type='text/css'>";
        $data_html .= "
body,html
{
    font-size: 11px;
    
}
.header
{
    width: 100%;
    display: block;
    text-align: center;
}
.detalii
{
    
    width: 100%;
    display:block;
    padding: 0;
    font-size: 10px;
  
}
.our_data
{
    margin-left: 6px;
    mergin-right: 6px;
    
    width: 48%;
    display:inline-block;
    float:left;
   
    style: object-fit:contain;
}
.their_data
{
    
    width: 48%;
    display:inline-block;
    float:right;
    text-align: right;
    style: object-fit:contain;
    
    margin-left: 6px;
    mergin-right: 6px;
    
}
.info-table
{
    border: 0px;
    width: 100%;
    border-collapse: collapse-all
    font-size: 10px;
}
.info-table td
{
    
     padding: 2px; 
    
}
.ship-table
{
    border: 0px;
    width: 100%;
    border-collapse: collapse-all
    font-size: 10px;
}

.gray
{
    
    background-color: #d9d9d9;
    
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  font-size: 10x;
}

td, th {
  border: 1px solid #dddddd;
  padding: 2px;
  width: 16.6%;
  text-align: center;
}

tr:nth-child(even) {
  
}

";
        $data_html .= "</style>";
        $data_html .= "<div class='detalii'>";
        $data_html .= "<div class='our_data'>";
        $data_html .= 'Universitatea "Alexandru Ioan-Cuza" din Iasi<br>';
        $data_html .= "Facultatea de Informatica";
        $data_html .= "Studii Universitare de tip: Licenta";
        $data_html .= "Specializare: Informatica(in limba engleza) -  Cu frecventa<br/>";
        $data_html .= "Disciplina: $cat_name<br>";
        $data_html .= "Numar de credite: ____<br>";
        $data_html .= "Cadru didactic titular: $teacher<br>";
        $data_html .= "Forma de examinare: Evaluare pe parcurs<br>";
        $data_html .= "
";
        $data_html .= "</div>";
        $data_html .= "<div class='their_data'>";
        $data_html .= "An Universitar: ______<br><br>";
        $data_html .= "Semestru: _______<br><br>";
        $data_html .= "An studiu:  $cat_year<br><br>";
        $data_html .= "Data examen sesiune: ___/___/____<br><br>";
        $data_html .= "</div>";
        $data_html .= "</div><hr/>";
               $student_grades = return_finals_students($cat_id);
            
           if(is_array($student_grades))
           {
                
                $data_html .='<table class="info-table" style="font-size: 10px;">';
                $data_html .='<thead class="thead-dark">';
                $data_html .='<tr>';
                $data_html .='<th style="width: 50px;">NrCrt.</th>';
               
                //generate table head
                foreach($student_grades[0] as $key=>$val)
                {
                    if($key != "student_id")
                    {
                         $data_html .="<th>$key</th>";
                    }
                   
                }
                
                $data_html .= "<th>Nota Sesiune Reexaminari<hr/><i>Semnatura si Data</i></th>";
                $data_html .= "<th>Nota Sesiune Reexaminari<hr/><i>Semnatura si Data</i></th>";
                $data_html .= "<th>Nota Sesiune Reexaminari<hr/><i>Semnatura si Data</i></th>";
                $data_html .='</tr>';
                $data_html .='</thead>';
                $data_html .='<tbody>';
                 
                
                 $i  = 1;
                 foreach($student_grades as $g)
                 {
                    $data_html .='<tr>';
                    $data_html .="<td style='width: 50px;'>$i</td>";
                    foreach($g as $key=>$val)
                    {
                        if($key != "student_id")
                        {
                            $data_html .="<td>$val</td>";
                        }
                        
                    }
                    $data_html .= "<td><hr/></td>";
                    $data_html .= "<td><hr/></td>";
                    $data_html .= "<td><hr/></td>";
                    $data_html .= "</tr>";
                    $i++;
                 }
                 
                $data_html .=' </tbody>';
                $data_html .='</table>';
               
           }
           else
           {
                $data_html .= "Nu sunt studenti in baza de date!";
           }


            $mpdf = new \Mpdf\Mpdf(['tempDir' => __dir__ . '/tmp', 'orientation' => 'P',
                'format' => 'A5-P']);
               
            
        
        
            $mpdf->WriteHTML($data_html);
            $mpdf->Output("student_data_$cat_id"."_".time().".pdf","D");
       
        } 
        
        else 
        {
            echo "Nu s-a specificat nici un ID de materie!!";
        }
?>