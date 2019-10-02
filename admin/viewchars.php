<?php
if(isset($_REQUEST['style'])) {
	$style=$_REQUEST['style']; //registar globals is OFF, gets style
} else { $style=""; }
if(isset($_REQUEST['sort'])) {
	$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
} else { $sort=""; }
if(isset($_REQUEST['start'])) {
  $start=$_REQUEST['start']; //registar globals is OFF, gets start
} else { $start=""; }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }
?>
<center><form action="adminpanel.php?mode=view" method="GET">
<select name="sort">
<option value="id" <?php if($sort=="id") { echo 'selected'; }?>>Id</option>
<option value="name" <?php if($sort=="name") { echo 'selected'; }?>>Name</option>
<option value="user_id" <?php if($sort=="user_id") { echo 'selected'; }?>>Owner</option>
<option value="game_id" <?php if($sort=="game_id") { echo 'selected'; }?>>Game ID</option>
<option value="gender" <?php if($sort=="gender") { echo 'selected'; }?>>Gender</option>
</select>
<select name="style">
<option value=" ASC">Lowest First</option>
<option value=" DESC" <?php if($style==" DESC") { echo "selected"; }?>>Highest First</option>
</select>
<input type="hidden" name="mode" value="view">
<input type="hidden" name="start" value=<?php if($start) { echo $start; } else { echo "0"; } ?>>
<input type="submit" class="button" value="Sort List"><br>
<?php
if($sort=="") { $sort="id"; $style=" ASC"; $start=0; }

if($action!="multis") {
  $sql="SELECT SQL_CALC_FOUND_ROWS characters.*, users.username FROM characters JOIN users ON characters.user_id=users.user_id
			ORDER BY " . $sort . $style . " LIMIT " . $start . ", 100";
} else {
  $which=$_GET['which'];
  $sql="SELECT SQL_CALC_FOUND_ROWS characters.*, users.username FROM characters JOIN users ON characters.user_id=users.user_id 
			WHERE user_id='$which' ORDER BY " . $sort . $style . " LIMIT " . $start . ", 100";
}
$query=query($db, $sql);
$total=get_row($db,"SELECT FOUND_ROWS()",1);
$num=how_many($query);
$result=next_row($query);

$pages=($total[0]-1)/100;
$currentpage=($start/100);
echo "<br>Page ";
$pagestart=0;
$page=0;
while($page<=$pages) {
 if($page!=0) { echo ", "; }
 if($currentpage!=$page) { printf("<a href=\"adminpanel.php?mode=view&sort=%s&style=%s&start=%s\">%s</a>",$sort,$style,$pagestart+$page*100,$page+1);
 } else { echo $page+1; }
 $page++;
}

?>
</center>
<table border="1" width="60%" class="margin">
<tr><th>ID</th>
<th>Name</th>
<th>Owner</th>
<th>Game</th>
<th>Gender</th>
<th>Health</th>
<th>Weapon</th>
</tr>

<?php
for($i=0;$i<$num;$i++) {
	print('<tr><td>'.$result['id'].'</td>');
	print('<td><a href="admin.php?mode=chars&action=edit&which='.$result['id'].'">'.$result['name'].'</a></td>');
	print('<td><a href="admin.php?mode=edit&which='.$result['user_id'].'">'.$result['username'].'</a></td>');

	if($result['game_id']>0) {
		$gamename=get_row($db,"SELECT name FROM games WHERE id=".$result['game_id'],1);
		print('<td><a href="games.php?action=view&which='.$result['game_id'].'">'.$gamename[0].'</a></td>');
	} else { echo '<td>Not in a game</td>'; }

	$gender=($result['gender']==1) ? "Female" : "Male";
	print('</td><td class="center">'.$gender);
	print('</td><td class="center">'.$result['health']);
	print('/'.$result['max_health'].'</td>');
			
	if($result['game_id']>0) {
		if($result['weapon_id']>0) {
			$weaponname=get_row($db,"SELECT name FROM weapons WHERE id=".$result['weapon_id'],1);
			print('<td><a href="admin.php?mode=weapon?action=view&which='.$result['weapon_id'].'">'.$weaponname[0].'</a></td>');
		} else {	echo '<td>No weapon assigned</td>'; }
	} else { echo '<td>Not in a game</td>'; }
	echo '</tr>';
	$result= next_row($query);
}
?>