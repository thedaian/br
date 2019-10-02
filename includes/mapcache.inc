<?php
function generate_map_cache($which) {
	$sql="SELECT id, pos_x, pos_y, players, options FROM maps WHERE game_id=? ORDER BY pos_y ASC, pos_x ASC";
	$mapQuery=query($db,$sql,$which);
	$total=how_many($mapQuery);
	if($total!=100) {
		error('Error in map table.  Number of map locations do not equal the required amount.  Contact the administrator to fix.');
	}

	$output="<tr><td class=\"normal\"></td>
<td class=\"normal\">0</td><td class=\"normal\">1</td><td class=\"normal\">2</td>
<td class=\"normal\">3</td><td class=\"normal\">4</td><td class=\"normal\">5</td>
<td class=\"normal\">6</td><td class=\"normal\">7</td><td class=\"normal\">8</td><td class=\"normal\">9</td></tr>";

	for($y=0;$y<10;$y++) {
		$output.="<tr><td class=\"normal\">".$y."</td>";
		for($x=0;$x<10;$x++) {
			$map=next_row($mapQuery);
			$output.="<td onClick=\"info(".$map['id'].")\"";
			if($map['options']&DANGER_ZONE) {
				$output.=" class=\"forbid\">";
			} elseif($map['options']&IMPASSIBLE) {
				$output.=" class=\"impass\">";
			} elseif($map['options']&START_LOCATION) {
				$output.=" class=\"start forbid\">";
			} else {	$output.=">"; }
			if($map['players']>0) {
				$output.=$map['players'];
			} 
			$output.="</td>";
		}
		$output.="<td class=\"normal\">".$y."</td></tr>";
	}

	$output.="<tr><td class=\"normal\"></td>
<td class=\"normal\">0</td><td class=\"normal\">1</td><td class=\"normal\">2</td>
<td class=\"normal\">3</td><td class=\"normal\">4</td><td class=\"normal\">5</td>
<td class=\"normal\">6</td><td class=\"normal\">7</td><td class=\"normal\">8</td><td class=\"normal\">9</td></tr>";

	$file=fopen($cache,'w');
	fwrite($file,$output);
	fclose($file);

	return $output;
}
?>