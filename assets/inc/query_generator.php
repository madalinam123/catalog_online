<?php

function gen_query($params, $table_name)
{

    //defining sort order
    if (isset($params["sort"])) {


        foreach ($params["sort"] as $sort) {
            $get_field = $sort["GET"];
            $sql_field = $sort["sql_field"];
            $sql_order = $sort["order_sql"];

            if (isset($_GET["sort"])) {
                if ($_GET["sort"] == $get_field) {
                    $sql_sort = "ORDER by $sql_field $sql_order";

                }
            } else {
                $sql_sort = "";
            }

        }
        $sql_sort = $sql_sort;
    } else {
        $sql_sort = null;
    }


    //define criteria order as in get criteria from something
    if (isset($params["criteria"]) && !empty($params["criteria"])) {


        foreach ($params["criteria"] as $c) {
            $c_field = $c["GET"];
            $c_sql = $c["sql_field"];

            if (array_key_exists($c_field, $_GET)) {
                $criteria_input = $_GET["$c_field"];
                if ($criteria_input != "") {
                    $criteria[] = "$c_sql = '$criteria_input'";
                }
            }

        }
    } else {
        $criteria[] = null;
    }

    //define regexp criteria
    if (isset($params["regexp"])) {
        foreach ($params["regexp"] as $r) {
            $r_field = $r["GET"];
            $r_sql = $r["sql_field"];

            if (array_key_exists($r_field, $_GET)) {
                $regexp_input = $_GET["$r_field"];

                if ($regexp_input != "") {

                    $regexp_input = explode(" ", $regexp_input);
                    foreach ($regexp_input as $rg) {
                        $exp[] = clean($rg);
                    }

                    $regexp_input = $exp;

                    $regexp_input = array_filter($regexp_input);

                    if (count($regexp_input) > 1) {
                        $regexp_input = implode("|", $regexp_input);
                        //echo $regexp_input;
                        //$regexp_input = clean($regexp_input);
                        $regexp_input = rtrim($regexp_input, "|");

                    } else {
                        $regexp_input = $regexp_input[0];
                    }


                    $regexp[] = "$r_sql REGEXP '$regexp_input'";
                }
            }
        }
    } else {
        $regexp[] = null;
    }

    //define between params
    if (isset($params["between"])) {
        foreach ($params["between"] as $b) {

            $b_from = $b["GET"];
            $b_to = $b["GET2"];
            $b_sql = $b["sql_field"];

            $from = $_GET["$b_from"];
            $to = $_GET["$b_from"];

            if (array_key_exists($b_from, $_GET) && array_key_exists($b_to, $_GET)) {
                $between[] = "$b_sql BETWEEN '$from' AND '$to'";
            }
        }


    } else {
        $between[] = null;
    }
    
    if(isset($params["found_in"]))
    {
    
        foreach($params["found_in"] as $c)
        {
            $b_in_get = $c["GET"];
            $b_in_sql = $c["sql_field"];
            
            $b_in_get_data = $_GET["$b_in_get"];
            if(array_key_exists($b_in_get,$_GET) && !empty($b_in_get_data))
            {
                $found_in[] = "$b_in_sql IN ($b_in_get_data)"; 
            }
        }
    }

    //check for empty criteria array
    if (!is_null($criteria)) {
        $criteria = implode(' AND ', $criteria);
    } else {
        $criteria = null;
    }

    //check for empty regexp array
    if (!is_null($regexp)) {
        $regexp = implode(' OR ', $regexp);
    } else {
        $regexp = null;
    }

    //check for empty between array
    if (!is_null($between)) {
        $between = implode(' AND ', $between);
    } else {
        $between = null;
    }
    
    if(!is_null($found_in))
    {
        $found_in = implode(' OR ',$found_in);
    }
    else
    {
        $found_in = null;
    }
    
    
    // return data
    $data = array(
        $criteria,
        $regexp,
        $between,
        $found_in
        );
    $data = array_filter($data);
    $data = implode(" AND ", $data);

    if (!empty($data)) {
        $sql = "SELECT * FROM $table_name WHERE $data $sql_sort";
    } else {
        $sql = "SELECT * FROM $table_name $sql_sort";
    }


    return $sql;


}

//insert sql data
//make sure you create array of columns
// make sure you create array of values
//both arrays must be the same in size and lenght
function PushData($table, $columns, $values)
{
    //declare con global(conection params))
    global $con;

    $column_count = count($columns);
    $overwriteArr = array_fill(0, $column_count, '?');

    for ($i = 0; $i < sizeof($columns); $i++) {
        $columns[$i] = trim($columns[$i]);
        $columns[$i] = '`' . $columns[$i] . '`';
    }

    $query = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES (" .
        implode(',', $overwriteArr) . ")";

    foreach ($values as $value) {
        $value = trim($value);
        $value = mysqli_real_escape_string($con, $value);
        $value = '"' . $value . '"';
        $query = preg_replace('/\?/', $value, $query, 1);
    }
    $result = mysqli_query($con, $query);

    if ($result == true) {
        return array("result" => "true", "query_id" => mysqli_insert_id($con));
    } else {
        return array(
            "result" => "false",
            "error" => mysqli_error($con),
            "sql" => $query);
    }
}


function check_rows($table, $column, $value)
{
    global $con;

    $num_rows = mysqli_query($con, "SELECT $column FROM $table WHERE $column='" .
        addslashes($value) . "'");
    $num = mysqli_num_rows($num_rows);

    return $num;
}
?>