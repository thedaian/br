<?php
if(isset($_REQUEST['style'])) {
	$style=$_REQUEST['style']; //registar globals is OFF, gets style
} else { $style=""; }
if(isset($_REQUEST['sort'])) {
	$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
} else { $sort=""; }
if(isset($_REQUEST['start'])) {
  $start=$_REQUEST['start']; //registar globals is OFF, gets start
} else { $start=0; }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }
?>
<center><form action="admin.php?mode=view" method="GET">
<select name="sort">
<option value="user_id" <?php if($sort=="user_id") { echo 'selected'; }?>>Id</option>
<option value="username" <?php if($sort=="username") { echo 'selected'; }?>>Name</option>
<option value="last_login" <?php if($sort=="last_login") { echo 'selected'; }?>>Login</option>
<option value="created" <?php if($sort=="created") { echo 'selected'; }?>>Creation Date</option>
<option value="last_IP" <?php if($sort=="last_IP") { echo 'selected'; }?>>IP Address</option>
<option value="gamesIN" <?php if($sort=="gamesIN") { echo 'selected'; }?>>Games In</option>
<option value="gamesRUN" <?php if($sort=="gamesRUN") { echo 'selected'; }?>>Games Run</option>
<option value="gamesTOTAL" <?php if($sort=="gamesTOTAL") { echo 'selected'; }?>>Games Total</option>
<option value="gamesSURVIVED" <?php if($sort=="gamesSURVIVED") { echo 'selected'; }?>>Games Survived</option>
<option value="gamesDIED" <?php if($sort=="gamesDIED") { echo 'selected'; }?>>Games Died</option>
</select>
<select name="style">
<option value="ASC">Lowest First</option>
<option value="DESC" <?php if($style=="DESC") { echo 'selected'; }?>>Highest First</option>
</select>
<input type="hidden" name="mode" value="view">
<input type="hidden" name="start" value=<?php echo $start; ?>>
<input type="submit" class="button" value="Sort List"><br>
<?php
if(empty($sort)) { $sort="user_id"; $style="ASC"; $start=0; }

if($action!="multis") {
  $sql="SELECT SQL_CALC_FOUND_ROWS * FROM users ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", 100";
} else {
  $which=$_GET['which'];
  $sql="SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE last_IP='$which' ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", 100";
}
$query=query($db, $sql);
$total=get_row($db,"SELECT FOUND_ROWS()",1);
$num=how_many($query);
$result=next_row($query);

$pages=($total[0]-1)/100;
$currentpage=($start/100);
echo '<br>Page ';
$pagestart=0;

for($page=0;$page<=$pages;$page++) {
 if($page!=0) { echo ', '; }
 if($currentpage!=$page) { printf('<a href="admin.php?mode=view&sort=%s&style=%s&start=%s">%s</a>',$sort,$style,$pagestart+$page*100,$page+1);
 } else { echo $page+1; }
}

?>
</center>
<table border="1" width="70%" class="margin">
<tr><th>ID</th>
<th>Name</th>
<th>Last Login</th>
<th>Last IP</th>
<th>Multis</th>
<th>Privilages</th>
<th>Games In</th>
<th>Characters</th></tr>

<?php
for($i=0;$i<$num;$i++) {
	print('<tr><td>'.$result['user_id'].'</td>');
	print('<td><a href="admin.php?mode=edit&which='.$result['user_id'].'">'.$result['username'].'</a></td>');
	//if($result['DoNotDelete']) { $dont=" X"; } else { $dont=""; }
	print('<td>'.date("F j, G:i:s",$result['last_login']).'</td>');
	print('<td>'.long2ip($result['last_IP']).'</td>');

	$sql="SELECT SQL_CALC_FOUND_ROWS user_id FROM users WHERE (last_IP=" . $result['last_IP'] . " AND user_id<>" . $result['user_id'].')';
	query($db,$sql);
	$multistotal=get_row($db,"SELECT FOUND_ROWS()",1);

	if($multistotal[0]!=0) {
		print('<td><a href="admin.php?mode=view&action=multis&which='.$result['last_IP'].'">'.$multistotal[0].' other users found</a></td>');
	} else { echo '<td>No other users found</td>'; }

	echo '<td>';
	switch($result['user_level']) {
		case 0:
			echo 'Not Active';
			break;
		case 1:
			echo 'Normal User';
			break;
		case 2:
			echo 'Power User';
			break;
		case 3:
			echo 'Game Moderater';
			break;
		case 4:
			echo 'Administrator';
			break;
	}
	echo '</td><td class="center">';
	echo $result['gamesIN'];
	echo '</td><td>';

	$sql="SELECT SQL_CALC_FOUND_ROWS id FROM characters WHERE user_id=" . $result['user_id'];
	query($db,$sql);
	$chartotal=get_row($db,"SELECT FOUND_ROWS()",1);

	if($chartotal[0]!=0) {
		print('<a href="admin.php?mode=chars&which='.$result['user_id'].'">'.$chartotal[0].' characters found</a>');
	} else { echo 'No characters found'; }

	echo '</td></tr>';
	$result= next_row($query);
}
?>