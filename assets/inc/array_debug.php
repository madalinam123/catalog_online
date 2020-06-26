<?php
//generate html table from array
function html_table($data)
{
	
    $rows = array();
    foreach ($data as $key=>$val) {
		
        $cells = array();
		if(is_array($val))
		{
			
			$val = html_table($val);
		}
		else
			
			{
				$val = $val;
			}
        $cells[] = "<td style='border: 1px solid black;'><b>".ucfirst($key)."</b></td><td style='border: 1px solid black;'>$val</td>";
        $rows[] = "<tr >" . implode('', $cells) . "</tr>";
	
  }
    return "<br><table style='background-color: #f9f9f9;font-size: 14px; width: 50%; border-collapse:collapse; margin: auto;'>" . implode('', $rows) . "</table>
	<hr>
	";
}
//check if the array contains more arrays
function look_for_array(array $test_var) {
  foreach ($test_var as $key => $el) {
    if (is_array($el)) {
      return $key;
    }
  }
  return null;
}

//ouput the arrays
function dump_array($data)
{
	if(is_null(look_for_array($data)))
	{
		echo "<center><h4>Dataset Dump</h4></center>";
		echo html_table($data);
		
	}
	else
	{
		$i = 1;
		foreach ($data as $res)
		{
			echo "<center><h4>Dataset Dump $i</h4></center>";
			echo html_table($res);
			$i++;
		}
	}
		
}
?>