<?php
error_reporting(E_ALL & ~E_NOTICE);

require "query_generator.php";
require "config.php";
require "array_debug.php";
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$con) {
    die("The connection parameters are wrong,") . mysqli_error($con);
}


//set standard mysql encoding to UTF8 for romanian chars
mysqli_set_charset( $con, 'utf8'); 
mysqli_query($con,"SET NAMES 'utf8'");
mb_internal_encoding('UTF-8');

//shortens a title or string
function short_title($string)
{
    if (strlen($string) >= 20) {
    return substr($string, 0, 20). " ... " . substr($string, -5);
}
else {
   return $string;
}
}

//gets subcategories
function get_children($parent_id)
{
    global $con;
            $get_sub = mysqli_query($con, "SELECT * from categories WHERE cat_parent = '$parent_id'");
            if(mysqli_num_rows($get_sub) > 0)
            {
                while($b = mysqli_fetch_assoc($get_sub))
                {
                    $sub_id = $b["cat_id"];
                    $sub_name = $b["cat_name"];
                    $sub_slug = $b["slug"];
                    $sub_parent = $b["cat_parent"];
                    
                    $child = get_children($sub_id);
                    if(empty($child))
                    {
                        $child = "0";
                    }
                    else
                    {
                        $child = $child;
                    }
                    
                    $subs[] = array
                    (
                        "cat_id" => $sub_id,
                        "cat_name" => $sub_name,
                        "cat_slug" => $sub_slug,
                        "children" => $child,
                    );
                }
                return $subs;
            }
}
//gets main categories
function get_parents()
{
    global $con;
    $get_parents = mysqli_query($con,"SELECT * from categories WHERE cat_parent = '0'");


if(mysqli_num_rows($get_parents) > 0)
{
    while($a = mysqli_fetch_assoc($get_parents))
    {
        $parent_id = $a["cat_id"];
        $parent_name  = $a["cat_name"];
        
        
        $children = get_children($parent_id);
        
        if(empty($children))
        {
            $children = "0";
            
        }
        else
        {
            $children = $children;
        }
                
        $cats[] = array
        (
            "cat_id" => $parent_id,
            "cat_name" => $parent_name,
            "children" => $children,
        );

    }
    return $cats;
}
}

//pagination script
function get_page()
{
    global $con;

    if (!isset($_GET["p"]) && empty($_GET["p"])) {
   
        include "inc/pages/default.php";

    } else {
         $page = $_GET["p"];
        if (file_exists("inc/pages/" . $page . ".php")) {
            include "inc/pages/" . $page . ".php";
        } else {
            echo "<div style='min-height: 300px;height: 600px;'><div class='card'><div class='card-body'><center><h3>404: Negasit in sistem!</h3></center></div></div></div>";
        }
    }
}

//cleans up strings
function clean($string)
{
    $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-\|]/', '', $string); // Removes special chars.
}

//reverses date time
function reverse_date($input)
{

    $date = date_create_from_format('Y-m-d', $input);
    return date_format($date, 'd m Y');
}





//useful redirect script
function redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
        exit;
    }
}

// Snippet from PHP Share: http://www.phpshare.org
//formats bytes into whatever
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}


function group_by($key, $array) {
    $result = array();

    foreach($array as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }

    return $result;
}

//generate SEO SLUG
function slug($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

//showcase the user rank
function user_type_rank()
{
    global $con;
    $session = return_session();
    $user_rank = $session["user_rank"];
    $rank_title = "";
    if($user_rank == "admin")
    {
        $rank_title .= "Administrator";
    }
    elseif($user_rank == "teacher")
    {
        $rank_title .= "Profesor"; 
    }
    else
    {
        $rank_title .= "Student";
    }
    
    return $rank_title;
}
function build_tree($cat_id)
{
    global $con;
    
    $get_sub = mysqli_query($con, "SELECT * from materii WHERE cat_id= '$cat_id' LIMIT 1");
    if(mysqli_num_rows($get_sub) == 1)
    {
        $a = mysqli_fetch_assoc($get_sub);
        
            $sub_id = $a["cat_id"];
            $sub_name = $a["cat_name"];
            $sub_parent = $a["cat_parent"];
            
            
           
            if($sub_parent == "0")
            {
                return $sub_name;
            }
            else
            {
                $tree = "$sub_name";
                $get_parents = mysqli_query($con, "SELECT * FROM materii WHERE cat_id='$sub_parent'");
                if(mysqli_num_rows($get_parents) > 0)
                {
                    
                    $b = mysqli_fetch_assoc($get_parents);
                    
                        $parent_id = $b["cat_id"];
                        $parent_name = $b["cat_name"];
                        $parent_parent = $b["cat_parent"];
                        
                        
                        $tree .= "/$parent_name";
                        if($parent_parent != "0")
                        {
                            $get_main = mysqli_query($con, "SELECT * FROM product_cats WHERE cat_id='$parent_parent'");
                            if(mysqli_num_rows($get_main) > 0)
                            {
                                $c = mysqli_fetch_assoc($get_main);
                                
                                    $main_id = $c["cat_id"];
                                    $main_name = $c["cat_name"];
                                    
                                    $tree .= "/$main_name";
                                
                            }
                        }
                        
                    
                   
                
            }
            $trees = explode("/",$tree);
            krsort($trees,"2");
            
            return implode("/",$trees);
            
        }
        
    }
    
    
}




function return_title()
{
    if(isset($_GET["p"]) && !empty($_GET["p"]))
  {
     
     if($_GET["p"] == "default")
     {
        $page_title = "Acasa";
     }
     elseif($_GET["p"] == "cats")
     {
       $page_title =  "Materii";
     }
     elseif($_GET["p"] == "users")
     {
       $page_title =  "Useri";
     }
      elseif($_GET["p"] == "series")
     {
       $page_title =  "Administrare Serii";
     }
      elseif($_GET["p"] == "groups")
     {
       $page_title =  "Administrare Grupe";
     }
      elseif($_GET["p"] == "add_series")
     {
       $page_title =  "Adauga serie";
     }
      elseif($_GET["p"] == "add_group")
     {
       $page_title =  "Adauga grup";
     }
     elseif($_GET["p"] == "grade_components")
     {
       $page_title =  "Formula de calcul";
     }
     elseif($_GET["p"] == "edit_user")
     {
        $page_title = "Editare user";
     }
     elseif($_GET["p"] == "new_user")
     {
       $page_title =  "Adauga user";
     }
     elseif($_GET["p"] == "settings")
     {
       $page_title =  "Setari cont";
     }
     
     elseif($_GET["p"] == "students")
     {
       $page_title =  "Studenti";
     }
     elseif($_GET["p"] == "new_student")
     {
       $page_title =  "Adauga student";
     }
     elseif($_GET["p"] == "admin_catalog")
     {
       $page_title =  "Catalog Profesor";
     }
     elseif($_GET["p"] == "import_csv")
     {
       $page_title = "Import CSV";
     }
     elseif($_GET["p"] == "user_catalog")
     {
        $page_title = "Catalog Student";
     }
     else
     {
        $page_title =  "Negasit";
     }
  }
  else
  {
    $page_title =  "Acasa";
  }
  
  return $page_title;
}


//login functionality
function login($username, $password)
{
    global $con;
    $get_data = mysqli_query($con,
        "SELECT id,user_rank,status FROM users WHERE username = '" . addslashes($username) .
        "' AND password='" . md5($password) . "' LIMIT 1");

    if (mysqli_num_rows($get_data) == 1) {
        $a = mysqli_fetch_assoc($get_data);
        $user_id = $a["id"];
        $user_rank = $a["user_rank"];
          
        $status = $a["status"];
         
          if($status == "1")
          {
             register_session($user_id, $user_rank);
             return array("status" => "true");
          }  
          else
          {
            return array("status" => "false","error" => "Contul este inactiv sau banat. Contacteaza adminul!");
          }
    } else {
        return array("status" => "false", "error" =>
                "Credentiale de logare incorecte! Reincercati!");
    }
}

//sesuion_Register functionality
function register_session($user_id, $user_rank)
{
    if (!session_id()) {
        session_start();
    }

    $_SESSION["user_id"] = $user_id;
    $_SESSION["user_rank"] = $user_rank;

    return true;
}

//return session functionality
function return_session()
{

    if (!session_id()) {
        session_start();
    }


    if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]) && isset($_SESSION["user_rank"]) &&
        !empty($_SESSION["user_rank"])) {
        return array(
            "status" => "true",
            "user_id" => $_SESSION["user_id"],
            "user_rank" => $_SESSION["user_rank"]);
    } else {
        return array("status" => "false");
    }
}

function return_user($user_id)
{
    global $con;

    $get_user = mysqli_query($con, "
    SELECT 
    *
    FROM 
    users 
    WHERE id = '$user_id'
    
    ");

    if (mysqli_num_rows($get_user) == 1) {
        $a = mysqli_fetch_assoc($get_user);

        return $a;
    } else {
        return false;
    }
}

function rank($string)
{
    if ($string == "admin") {
        return "Admin";
    } elseif ($string == "teacher") {
        return "Profesor";
    } elseif ($string == "student") {
        return "Student";
    } else {
        return null;
    }
}

function return_cat_name($cat_id)
{
    global $con;
    
    $cat = build_tree($cat_id);
    
    if($cat != false)
    {
        return $cat;
    }
    else
    {
        return "Indisponibil";
    }
}

function SW_ImplodeCSV(array $rows, $headerrow=true, $mode='EXCEL', $fmt='2D_FIELDNAME_ARRAY')
    // SW_ImplodeCSV - returns 2D array as string of csv(MS Excel .CSV supported)
    // AUTHOR: tgearin2@gmail.com
    // RELEASED: 9/21/13 BETA
      { $r=1; $row=array(); $fields=array(); $csv="";
        $escapes=array('\r', '\n', '\t', '\\', '\"');  //two byte escape codes
        $escapes2=array("\r", "\n", "\t", "\\", "\""); //actual code

        if($mode=='EXCEL')// escape code = ""
         { $delim=','; $enclos='"'; $rowbr="\r\n"; }
        else //mode=STANDARD all fields enclosed
           { $delim=','; $enclos='"'; $rowbr="\r\n"; }

          $csv=""; $i=-1; $i2=0; $imax=count($rows);

          while( $i < $imax )
          {
            // get field names
            if($i == -1)
             { $row=$rows[0];
               if($fmt=='2D_FIELDNAME_ARRAY')
                { $i2=0; $i2max=count($row);
                  while( list($k, $v) = each($row) )
                   { $fields[$i2]=$k;
                     $i2++;
                   }
                }
               else //if($fmt='2D_NUMBERED_ARRAY')
                { $i2=0; $i2max=(count($rows[0]));
                  while($i2<$i2max)
                   { $fields[$i2]=$i2;
                     $i2++;
                   }
                }

               if($headerrow==true) { $row=$fields; }
               else                 { $i=0; $row=$rows[0];}
             }
            else
             { $row=$rows[$i];
             }

            $i2=0;  $i2max=count($row); 
            while($i2 < $i2max)// numeric loop (order really matters here)
            //while( list($k, $v) = each($row) )
             { if($i2 != 0) $csv=$csv.$delim;

               $v=$row[$fields[$i2]];

               if($mode=='EXCEL') //EXCEL 2quote escapes
                    { $newv = '"'.(str_replace('"', '""', $v)).'"'; }
               else  //STANDARD
                    { $newv = '"'.(str_replace($escapes2, $escapes, $v)).'"'; }
               $csv=$csv.$newv;
               $i2++;
             }

            $csv=$csv."\r\n";

            $i++;
          }

         return $csv;
       }

    function SW_ExplodeCSV($csv, $headerrow=true, $mode='EXCEL', $fmt='2D_FIELDNAME_ARRAY')
     { // SW_ExplodeCSV - parses CSV into 2D array(MS Excel .CSV supported)
       // AUTHOR: tgearin2@gmail.com
       // RELEASED: 9/21/13 BETA
       //SWMessage("SW_ExplodeCSV() - CALLED HERE -");
       $rows=array(); $row=array(); $fields=array();// rows = array of arrays

       //escape code = '\'
       $escapes=array('\r', '\n', '\t', '\\', '\"');  //two byte escape codes
       $escapes2=array("\r", "\n", "\t", "\\", "\""); //actual code

       if($mode=='EXCEL')
        {// escape code = ""
          $delim=','; $enclos='"'; $esc_enclos='""'; $rowbr="\r\n";
        }
       else //mode=STANDARD 
        {// all fields enclosed
          $delim=','; $enclos='"'; $rowbr="\r\n";
        }

       $indxf=0; $indxl=0; $encindxf=0; $encindxl=0; $enc=0; $enc1=0; $enc2=0; $brk1=0; $rowindxf=0; $rowindxl=0; $encflg=0;
       $rowcnt=0; $colcnt=0; $rowflg=0; $colflg=0; $cell="";
       $headerflg=0; $quotedflg=0;
       $i=0; $i2=0; $imax=strlen($csv);   

       while($indxf < $imax)
         {
           //find first *possible* cell delimiters
           $indxl=strpos($csv, $delim, $indxf);  if($indxl===false) { $indxl=$imax; }
           $encindxf=strpos($csv, $enclos, $indxf); if($encindxf===false) { $encindxf=$imax; }//first open quote
           $rowindxl=strpos($csv, $rowbr, $indxf); if($rowindxl===false) { $rowindxl=$imax; }

           if(($encindxf>$indxl)||($encindxf>$rowindxl))
            { $quoteflg=0; $encindxf=$imax; $encindxl=$imax;
              if($rowindxl<$indxl) { $indxl=$rowindxl; $rowflg=1; }
            }
           else 
            { //find cell enclosure area (and real cell delimiter)
              $quoteflg=1;
              $enc=$encindxf; 
              while($enc<$indxl) //$enc = next open quote
               {// loop till unquoted delim. is found
                 $enc=strpos($csv, $enclos, $enc+1); if($enc===false) { $enc=$imax; }//close quote
                 $encindxl=$enc; //last close quote
                 $indxl=strpos($csv, $delim, $enc+1); if($indxl===false)  { $indxl=$imax; }//last delim.
                 $enc=strpos($csv, $enclos, $enc+1); if($enc===false) { $enc=$imax; }//open quote
                 if(($indxl==$imax)||($enc==$imax)) break;
               }
              $rowindxl=strpos($csv, $rowbr, $enc+1); if($rowindxl===false) { $rowindxl=$imax; }
              if($rowindxl<$indxl) { $indxl=$rowindxl; $rowflg=1; }
            }

           if($quoteflg==0)
            { //no enclosured content - take as is
              $colflg=1;
              //get cell 
             // $cell=substr($csv, $indxf, ($indxl-$indxf)-1);
              $cell=substr($csv, $indxf, ($indxl-$indxf));
            }
           else// if($rowindxl > $encindxf)
            { // cell enclosed
              $colflg=1;

             //get cell - decode cell content
              $cell=substr($csv, $encindxf+1, ($encindxl-$encindxf)-1);

              if($mode=='EXCEL') //remove EXCEL 2quote escapes
                { $cell=str_replace($esc_enclos, $enclos, $cell);
                }
              else //remove STANDARD esc. sceme
                { $cell=str_replace($escapes, $escapes2, $cell);
                }
            }

           if($colflg)
            {// read cell into array
              if( ($fmt=='2D_FIELDNAME_ARRAY') && ($headerflg==1) )
               { $row[$fields[$colcnt]]=$cell; }
              else if(($fmt=='2D_NUMBERED_ARRAY')||($headerflg==0))
               { $row[$colcnt]=$cell; } //$rows[$rowcnt][$colcnt] = $cell;

              $colcnt++; $colflg=0; $cell="";
              $indxf=$indxl+1;//strlen($delim);
            }
           if($rowflg)
            {// read row into big array
              if(($headerrow) && ($headerflg==0))
                {  $fields=$row;
                   $row=array();
                   $headerflg=1;
                }
              else
                { $rows[$rowcnt]=$row;
                  $row=array();
                  $rowcnt++; 
                }
               $colcnt=0; $rowflg=0; $cell="";
               $rowindxf=$rowindxl+2;//strlen($rowbr);
               $indxf=$rowindxf;
            }

           $i++;
           //SWMessage("SW_ExplodeCSV() - colcnt = ".$colcnt."   rowcnt = ".$rowcnt."   indxf = ".$indxf."   indxl = ".$indxl."   rowindxf = ".$rowindxf);
           //if($i>20) break;
         }

       return $rows;
     }
     
function insert_csv_record($nr_mat,$nume,$prenume,$email,$group_id,$series_id,$cat_id,$login_id)
{
    global $con;
    
    if(mysqli_num_rows(mysqli_query($con, "SELECT id FROM users WHERE username = '$nr_mat' LIMIT 1")) < 1)
    {
        $cols = array("username","password","email","first_name","last_name","user_rank","group_id","series_id","status","created_at");
        $vals = array($nr_mat,md5("parola_$nr_mat"),$email,$prenume,$nume,"student",$group_id,$series_id,"1",date("Y-m-d H:i:s"));
        
        $insert_user = PushData("users",$cols,$vals);
        
        if($insert_user["result"] == "true")
        {
            $user_id = $insert_user["query_id"];
            
            if(mysqli_num_rows(mysqli_query($con, "SELECT user_id FROM import_data WHERE user_id ='$user_id'")) < 1)
            {
                $insert_cat = mysqli_query($con,"INSERT into user_meta VALUES (DEFAULT,'$user_id','$cat_id','student','0')");
                if(!$insert_cat)
                {
                    return mysqli_error($con);
                }
                $insert_import = mysqli_query($con, "INSERT into import_data VALUES (DEFAULT,'$user_id','$login_id','".date("Y-m-d H:i:s")."')");
                
                if($insert_import)
                {
                    return " - importat";
                }
                else
                {
                    return mysqli_error($con);
                }
            }
            
        }
        else
        {
            return $insert_user["error"];
        }
    }
    else
    {
        return "exista";
    }
}

function fetch_user_cat($user_id)
{
    global $con;
    
    $get = mysqli_query($con, "SELECT cat_ids FROM users WHERE id='$user_id' LIMIT 1");
    
    if(mysqli_num_rows($get) == 1)
    {
        $a = mysqli_fetch_assoc($get);
        
        $cat_id = $a["cat_ids"];
        
        return $cat_id;
    }
    else
    
    {
        return "0";
    }
    
}




function outputCsv($fileName, $assocDataArray)
{
    ob_clean();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $fileName);    
    if(isset($assocDataArray['0'])){
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($assocDataArray['0']));
        foreach($assocDataArray AS $values){
            fputcsv($fp, $values);
        }
        fclose($fp);
    }
    ob_flush();
}


function csv_to_array($csv, $delimiter = ';', $header_line = true)
{
    // CSV from external sources may have Unix or DOS line endings. str_getcsv()
    // requires that the "delimiter" be one character only, so we don't want
    // to pass the DOS line ending \r\n to that function. So first we ensure
    // that we have Unix line endings only.
    $csv = str_replace("\r\n", "\n", $csv);

    // Read the CSV lines into a numerically indexed array. Use str_getcsv(),
    // rather than splitting on all linebreaks, as fields may themselves contain
    // linebreaks.
    $all_lines = str_getcsv($csv, "\n");
    if (!$all_lines) {
        return false;
    }

    $csv = array_map(function (&$line) use ($delimiter){
        return str_getcsv($line, $delimiter);
    }, $all_lines);

    if ($header_line) {
        // Use the first row's values as keys for all other rows.
        array_walk($csv, function (&$a) use ($csv){
            $a = array_combine($csv[0], $a);
        });
        // Remove column header row.
        array_shift($csv);
    }

    return $csv;
}


function generate_cat_names($cat_ids)
{
    if($cat_ids != "0")
    {
        $cats = explode(",",$cat_ids);
        
        foreach($cats as $cat_id)
        {
            $names[] = build_tree($cat_id);
        }
        
        $cat_names  = implode("<br/>",$names);
    }
    else
    {
        $cat_names = "N/A";
    }
    
    return $cat_names;
}

function return_years($user_id)
{
    global $con;
    
    $get_mats = mysqli_query($con, "SELECT cat_id FROM user_meta WHERE user_id = '$user_id'");
if(mysqli_num_rows($get_mats) > 0)
{
    while($c = mysqli_fetch_assoc($get_mats))
    {
         $mat_ids[] = $c["cat_id"];
    }
   
    $mat_ids = implode(",",$mat_ids);
    $mat_ids = explode(",",$mat_ids);
    
    foreach($mat_ids as $mat_id)
    {
        $get_parent = mysqli_query($con, "SELECT cat_parent FROM materii WHERE cat_id = '$mat_id' LIMIT 1");
        if(mysqli_num_rows($get_parent) == 1)
        {
            $d = mysqli_fetch_assoc($get_parent);
            $parent_ids[] = $d["cat_parent"]; 
        }
        else
        {
            $parent_ids[] = "";
        }
    }
    
    $parent_ids = implode(",",$parent_ids);
    
    
}
else
{
    $parent_ids = "";
}

return $parent_ids;
}

function is_entitled($user_id)
{
    global $con;
    
    $get_entitled = mysqli_query($con, "SELECT is_entitled FROM users WHERE id='$user_id' LIMIT 1");
                        if(mysqli_num_rows($get_entitled) == 1)
                        {
                            $i = mysqli_fetch_assoc($get_entitled);
                            
                            $is_entitled = $i["is_entitled"];
                            
                        }
                        else
                        {
                            $is_entitled = "0";
                        }
                        
                        return $is_entitled;
}

function return_current_percentage($cat_id)
{
    global $con;
    
    $get_total_percentage = mysqli_query($con, "SELECT COALESCE(sum(type_percentage),0) as total_percentage FROM grade_types WHERE cat_id = '$cat_id'");
            $return = mysqli_fetch_assoc($get_total_percentage);
            $total_percentage = $return["total_percentage"];
    return $total_percentage;
}

function render_formula($cat_id)
{
    global $con;
    
    $get_items= mysqli_query($con, "SELECT type_name, type_percentage FROM grade_types WHERE cat_id = '$cat_id' AND type_percentage > 0");
            if(mysqli_num_rows($get_items) > 0)
            {
                while($p = mysqli_fetch_assoc($get_items))
                {
                     $type_name = $p["type_name"];
                     $percentage  = $p["type_percentage"];
                     
                    
                    
                        $formulas[] = "$percentage% $type_name"; 
                     
                     
                }
                $formula_items = implode("+",$formulas);
                $formula_items = "($formula_items)/100 = Media/Nota Finala";
               
            }
            else
            {
                $formula_items = "";
            }
            
            return $formula_items;
}

function grades_percentage_total($cat_id,$student_id)
{
    global $con;
    
    $get_data = mysqli_query($con, "SELECT 
DISTINCT grades.grade_type,
grade_types.type_percentage
FROM 
grades 
LEFT JOIN grade_types on grades.grade_type = grade_types.id
WHERE grades.cat_id = '$cat_id' and grades.student_id = '$student_id'
AND grades.grade_type IN (SELECT id FROM grade_types)

");

if(mysqli_num_rows($get_data)>0)
{
    while($a = mysqli_fetch_assoc($get_data))
    {
        $percent[] = $a["type_percentage"]; 
    }
    
    return array_sum($percent);
}
else
{
    return 0;
}

}

function return_final_grade($cat_id,$student_id)
{
    global $con;
    
    $get_data = mysqli_query($con, "
SELECT 
g.grade_type,
(select count(t.grade_type) from grades as t WHERE t.grade_type = g.grade_type AND t.student_id = g.student_id) as count,
(select SUM(a.grade)/count FROM grades as a WHERE a.grade_type = g.grade_type AND a.student_id = g.student_id) as final,
grade_types.type_percentage
FROM 
grades as g
LEFT JOIN grade_types on g.grade_type = grade_types.id
WHERE g.student_id = '$student_id' AND g.cat_id = '$cat_id' group by grade_type,count

");

if(mysqli_num_rows($get_data) > 0)
{
    while($a = mysqli_fetch_assoc($get_data))
    {
        $percent = $a["type_percentage"];
        $count = $a["count"];
        $final = $a["final"];
        
        $final_average[] = $percent*$final;
        
        
        
    }
    
    return array_sum($final_average)/100;
    

    }
else
{
    return 0;
}

    
}

function return_grades($student_id,$cat_id,$grade_type)
{
    global $con;
    
    $get_grades = mysqli_query($con, "SELECT 
                        grades.*,
                        grade_types.type_name,
                        grade_types.type_percentage
                        FROM 
                        grades 
                        LEFT JOIN grade_types on grades.grade_type = grade_types.id
                        WHERE 
                        grades.student_id = '$student_id' 
                        AND grades.cat_id ='$cat_id'
                        AND grades.grade_type = '$grade_type'
                        LIMIT 1");
                        
    if(mysqli_num_rows($get_grades)  == 1)
    {
        $a = mysqli_fetch_assoc($get_grades);
       
       $array = $a;
    }
    else
    {
       $array = null;
    }
    
    
    return $array;
}

function return_final_average($cats,$student_id)
{
    
    $cats = explode(",",$cats);
    
    foreach($cats as $cat_id)
    {
         $return_total_percentage = return_current_percentage($cat_id);
                                $grade_percentage_total = grades_percentage_total($cat_id,$student_id);
                                
                               
                                
                                if($return_total_percentage == 100)
                                {
                                    if($grade_percentage_total == 100)
                                    {
                                        
                                        $g = return_final_grade($cat_id,$student_id);
                        
                                        $d[] = $g;
                                        
                                        
                                    }
                                    else
                                    {
                                        $d[] = "";
                                    }
                                }
                                else
                                {
                                    $d[] = "";
                                }
    }
    
    
    $d = array_filter($d);
       
       if(count($d) > 0)
       {
            $final = array_sum($d)/count($d);
            $final = number_format($final,2,".",",");
            
            return $final;
       }
       else
       {
        return "Date insuficiente!";
       }                        
                            
}

function return_student_year($student_id)
{
    global $con; 
    
    $get_cat = mysqli_query($con, "SELECT cat_id FROM user_meta WHERE meta_type='student' AND user_id ='$student_id' LIMIT 1");
    if(mysqli_num_rows($get_cat) == 1)
    {
        $a = mysqli_fetch_assoc($get_cat);
    
        $cat = $a["cat_id"];
       
    }
    else
    {
        $cat = "";
    }
    
    return $cat;   
}
function return_cats($type,$user_id)
{
    global $con;
    
    $get_cats = mysqli_query($con, "SELECT * FROM user_meta WHERE user_id = '$user_id' AND meta_type = '$type'");
    if(mysqli_num_rows($get_cats) > 0)
    {
        while($a = mysqli_fetch_assoc($get_cats))
        {
            
            $result[] = array("cat_id" => $a["cat_id"],"cat_name" => build_tree($a["cat_id"]),"is_entitled" => $a["is_entitled"]);
                       
        } 
        return $result;
    }
    else
    {
        return false;
    }
}

function return_teacher_cat($user_id)
{
    global $con;
    
    $get_cat = mysqli_query($con, "SELECT cat_id FROM user_meta WHERE meta_type='teacher' AND user_id ='$user_id'");
    if(mysqli_num_rows($get_cat) > 0)
    {
        while($a = mysqli_fetch_assoc($get_cat))
        {
            $cats[] = "[".build_tree($a["cat_id"])."]";
           
        }
        return implode(",",$cats);    
    }
    else
    {
        return "N/A";
    }
}
function return_user_cat($user_id)
{
    global $con;
    
    $get_cat = mysqli_query($con, "SELECT cat_id FROM user_meta WHERE meta_type='student' AND user_id ='$user_id'");
    if(mysqli_num_rows($get_cat) > 0)
    {
        while($a = mysqli_fetch_assoc($get_cat))
        {
            $cats[] = build_tree($a["cat_id"]);
        }
        
        return implode(",",$cats);
    }
    else
    {
        return "N/A";
    }
}
function return_group_name($group_id)
{
    global $con;
    
$get_group_name = mysqli_query($con,"SELECT * FROM groups WHERE id =  '$group_id'");
if(mysqli_num_rows($get_group_name) > 0)
{
    $a = mysqli_fetch_assoc($get_group_name);
    $group_name = $a["group_name"];
       
}
else
{
    $group_name = "N/A";
}

return $group_name;
}

function return_series_name($series_id)
{
    global $con;
    
            $get_group_name = mysqli_query($con,"SELECT * FROM series WHERE id =  '$series_id'");
if(mysqli_num_rows($get_group_name) > 0)
{
    $a = mysqli_fetch_assoc($get_group_name);
    $group_name = $a["group_name"];
       
}
else
{
    $group_name = "N/A";
}

return $group_name;
}

function return_cat_teachers($cat_id)
{
    global $con;
     $get_teachers = mysqli_query($con, "SELECT user_id,is_entitled FROM user_meta WHERE cat_id = '$cat_id' AND meta_type='teacher'");
                        if(mysqli_num_rows($get_teachers) >  0)
                        {
                            while($a = mysqli_fetch_assoc($get_teachers))
                            {
                                $user = return_user($a["user_id"]);
                                if($a["is_entitled"] == 1)
                                {
                                    $entitled = '<i class="fas fa-star"></i>';
                                }
                                else
                                {
                                    $entitled = "";
                                }
                                $username = $entitled.$user["last_name"]." ".$user["first_name"];
                                $names[] = "[".$username."]";
                                                        
                            }
                            
                            return "Profesori: ".implode(",",$names);
                        }
                        else
                        {
                            return "";
                        }
}

function generate_group($group)
{
    global $con;
    
    $check = mysqli_query($con, "SELECT id FROM groups WHERE group_name = '".addslashes($group)."' LIMIT 1");
    if(mysqli_num_rows($check) == 1)
    {
        //exsits
        $a = mysqli_fetch_assoc($check);
        $group_id = $a["id"];
    }
    else
    {
        $insert_group = mysqli_query($con, "INSERT into groups VALUES (DEFAULT,'".addslashes($group)."','".date("Y-m-d H:i:s")."')");
        if($insert_group)
        {
            $group_id  = mysqli_insert_id($con);
        }
        else
        {
            die(mysqli_error($con));
        }
        
    }
    
    return $group_id;
}

function generate_series($series)
{
    global $con;
    
    $check = mysqli_query($con, "SELECT id FROM series WHERE group_name = '".addslashes($series)."' LIMIT 1");
    if(mysqli_num_rows($check) == 1)
    {
        //exsits
        $a = mysqli_fetch_assoc($check);
        $series_id = $a["id"];
    }
    else
    {
        $insert_series = mysqli_query($con, "INSERT into series VALUES (DEFAULT,'".addslashes($series)."','".date("Y-m-d H:i:s")."')");
        if($insert_series)
        {
            $series_id  = mysqli_insert_id($con);
        }
        else
        {
            die(mysqli_error($con));
        }
        
    }
    
    return $series_id;
}
function get_grade_types($cat_id)
{
    global $con;
    
    $get_types = mysqli_query($con, "SELECT id,type_name,type_percentage FROM grade_types WHERE cat_id = '$cat_id' AND type_percentage != 0");
    if(mysqli_num_rows($get_types) > 0)
    {
        while($a = mysqli_fetch_assoc($get_types))
        {
            $types[] = $a;
        }
        return $types;
    }
    else
    {
        return false;
    }
}
function null_str($str)
{
    if(empty($str))
    {
        return 0;
    }
    elseif($str == "")
    {
        return 0;
    }
    elseif($str == " ")
    {
        return 0;
    }
    elseif(empty($str))
    {
        return 0;
    }
    else
    {
        return $str;
    }
}
function get_grades_by($user_id,$cat_id,$method = "normal")
{
    global $con;
    
    //first get grade types and populate with grades foreach type
    
    $get_types = get_grade_types($cat_id);
    if($get_types != false)
    {
        foreach($get_types  as $t)
        {
            $type_id = $t["id"];
            $type_name = $t["type_name"];
            $type_percentage = $t["type_percentage"];
            
            $get_grade_data = return_grades($user_id,$cat_id,$type_id);
            
            $grades[] = array("type_id" => $type_id,"type_name" => $type_name,"grade" => null_str($get_grade_data["grade"]));
        }
        $new = array_column($grades, 'grade', 'type_name');
        if($method == "normal")
        {
            return $grades;
        }
        else
        {
            return $new;
        }
       
        
      
        return $new;
    }
    else
    {
        return false;
    }
    
}
function array_flatten($array) {
    $return = array();
    foreach ($array as $key => $value) {
        if (is_array($value)){
            $return = array_merge($return, array_flatten($value));
        } else {
            $return[$key] = $value;
        }
    }

    return $return;
}
function return_students($cat_id)
{
    global $con;
    
    //get year based on materie
    
    $get_year = mysqli_query($con, "SELECT cat_parent FROM materii WHERE cat_id='$cat_id' LIMIT 1");
    
    if(mysqli_num_rows($get_year) == 1)
    {
        $a = mysqli_fetch_assoc($get_year);
        
        $year_id = $a["cat_parent"];
        
        $get_students = mysqli_query($con,"SELECT user_id FROM user_meta WHERE cat_id ='$year_id' AND meta_type='student'");
        
        if(mysqli_num_rows($get_students) > 0)
        {
            while($u = mysqli_fetch_assoc($get_students))
            {
                $user_id = $u["user_id"];
                $meta = return_user($user_id);
                $nume = $meta["last_name"]." ".$meta["first_name"];
                $group_id = $meta["group_id"];
                $series_id = $meta["series_id"];
                $series_name = return_series_name($series_id);
                $group_name = return_group_name($group_id);
                $nr_mat = $meta["username"];
                //echo $user_id."<br>";
                $grades = get_grades_by($user_id,$cat_id,"flat");
                $year_name = basename(build_tree($year_id));
                
                $final_grade = array("Nota Finala" => return_final_grade($cat_id,$user_id));
                
                $students_array = array("student_id" => $user_id,"NumePrenume"=>$nume,"Grupa" => $group_name,"Serie" => $series_name,"An" => $year_name);
                $students[] = array_merge($students_array,$grades,$final_grade);
            }
            
            return $students;
        }
        else
        {
            return false;
        }
        
    }
    else
    {
        return false;
    }
    
    
}

function return_finals_students($cat_id)
{
    global $con;
    
    //get year based on materie
    
    $get_year = mysqli_query($con, "SELECT cat_parent FROM materii WHERE cat_id='$cat_id' LIMIT 1");
    
    if(mysqli_num_rows($get_year) == 1)
    {
        $a = mysqli_fetch_assoc($get_year);
        
        $year_id = $a["cat_parent"];
        
        $get_students = mysqli_query($con,"SELECT user_id FROM user_meta WHERE cat_id ='$year_id' AND meta_type='student'");
        
        if(mysqli_num_rows($get_students) > 0)
        {
            while($u = mysqli_fetch_assoc($get_students))
            {
                $user_id = $u["user_id"];
                $meta = return_user($user_id);
                $nume = $meta["last_name"]." ".$meta["first_name"];
                $group_id = $meta["group_id"];
                $series_id = $meta["series_id"];
                $series_name = return_series_name($series_id);
                $group_name = return_group_name($group_id);
                $nr_mat = $meta["username"];
                //echo $user_id."<br>";
                
                $year_name = basename(build_tree($year_id));
                
                $final_grade = array("Nota Finala" => return_final_grade($cat_id,$user_id));
                
                $students_array = array("student_id" => $user_id,"NumePrenume"=>$nume,"Grupa" => $group_name,"Serie" => $series_name,"An" => $year_name);
                $students[] = array_merge($students_array,$final_grade);
            }
            
            return $students;
        }
        else
        {
            return false;
        }
        
    }
    else
    {
        return false;
    }
    
    
}

function return_year($cat_id)
{
    global $con;
    
    $get_year = mysqli_query($con, "SELECT cat_parent FROM materii WHERE cat_id = '$cat_id' LIMIT 1");
    
    if(mysqli_num_rows($get_year) == 1)
    {
        $a = mysqli_fetch_assoc($get_year);
        
        return $a["cat_parent"];
    }
    else
    {
        return "0";
    }
}
function entitled_teacher($cat_id)
{
    global $con;
    $num_cats = mysqli_query($con, "SELECT user_id,is_entitled FROM user_meta WHERE cat_id ='$cat_id' AND is_entitled ='1' LIMIT 1");
        if(mysqli_num_rows($num_cats) == 1)
        {
            $a = mysqli_fetch_assoc($num_cats);
            
          $result = array("user_id" => $a["user_id"],"is_entitled" => $a["is_entitled"]);
        }
        else
        {
           $result = array("user_id" => "0","is_entitled" => "0");
        }
        
        return $result;
}

function group_alert($user_id)
{
    global $con;
    $check = mysqli_query($con,"SELECT group_id FROM users WHERE id = '$user_id' AND user_rank = 'student' LIMIT 1");
    if(mysqli_num_rows($check) == 1)
    {
        $a = mysqli_fetch_assoc($check);
        $group_id = $a["group_id"];
        if($group_id == "0")
        {
            return "alert";
        }
        else
        {
            return "";
        }
    }
    else
    {
        return "";
    }
}

function is_entitled_alert($cat_id)
{
    global $con;
    
    $get_data = mysqli_query($con, "SELECT COUNT(is_entitled) as entitled_teachers FROM user_meta WHERE meta_type='teacher' AND cat_id='$cat_id' AND is_entitled='1'");
    if(mysqli_num_rows($get_data) == 1)
    {
        $a = mysqli_fetch_assoc($get_data);
        
        $entitled_no  = $a["entitled_teachers"];
        
        if($entitled_no < 1)
        {
            return "<span style='color: red'>(!)</span>";
        }
        else
        {
            return "";
        }
    }
    else
    {
        return "<span style='color: red'>(!)</span>";
    }
}
?>