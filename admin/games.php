<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action'];
} else { $action=""; }

define('ACTIVE_GAME',1);
define('OPTION_PRIVATE',2);
define('OPTION_POWER_USERS_ONLY',4);
define('OPTION_MALES_ONLY',8);
define('OPTION_FEMALES_ONLY',16);

switch($action) {
	case "view":
		if(isset($_REQUEST['style'])) {
			$style=$_REQUEST['style']; //registar globals is OFF, gets style
		} else { $style=""; }
		if(isset($_REQUEST['sort'])) {
			$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
		} else { $sort=""; }
		if(isset($_REQUEST['start'])) {
			$start=$_REQUEST['start']; //registar globals is OFF, gets start
		} else { $start=0; }
		
		define('PER_PAGE',100);
		?>
<center><form action="admin.php?mode=games" method="GET">
<select name="sort">
<option value="id" <?php if($sort=="id") { echo 'selected'; }?>>Id</option>
<option value="name" <?php if($sort=="name") { echo 'selected'; }?>>Name</option>
<option value="current_males" <?php if($sort=="current_males") { echo 'selected'; }?>>Males</option>
<option value="current_females" <?php if($sort=="current_females") { echo 'selected'; }?>>Females</option>
<option value="creation_time" <?php if($sort=="creation_time") { echo 'selected'; }?>>Creation Time</option>
<option value="last_activity" <?php if($sort=="last_activity") { echo 'selected'; }?>>Last Activity</option>
</select>
<select name="style">
<option value="ASC">Lowest First</option>
<option value="DESC" <?php if($style=="DESC") { echo 'selected'; }?>>Highest First</option>
</select>
<input type="hidden" name="mode" value="view">
<input type="hidden" name="start" value="<?php echo $start; ?>">
<input type="submit" class="button" value="Sort List"><br/>
<?php
		if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }

		$sql="SELECT SQL_CALC_FOUND_ROWS games.*, games.current_males+games.current_females AS total, users.user_id, users.username
				FROM games JOIN users ON games.owner_id=users.user_id ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", ".PER_PAGE;
		$query=query($db, $sql);
		$grandtotal=get_row($db,"SELECT FOUND_ROWS()",1);
		$total=how_many($query);
		$result=next_row($query);

		$pages=($grandtotal[0]-1)/PER_PAGE;
		$currentpage=($start/PER_PAGE);
		echo '<br/><span class="small">Page ';
		$pagestart=0;

		for($page=0;$page<=$pages;$page++) {
			if($page!=0) { echo ', '; }
			if($currentpage!=$page) { printf('<a href="userlist.php?sort=%s&style=%s&start=%s">%s</a>',$sort,$style,$pagestart+$page*PER_PAGE,$page+1);
			} else { echo $page+1; }
		}
?>
</center>
<table border="1" width="70%" class="margin">
<tr>
<th>ID</th>
<th>Name</th>
<th>Owner Name</th>
<th>Males</th>
<th>Females</th>
<th>Total</th>
<th>Remaining</th>
<th>Applied</th>
<th>Created</th>
<th>Last Active</th></tr>
<?php
for($i=0;$i<$total;$i++) {
	print('<tr><td class="center">'.$result['id'].'</td>');
	print('<td><a href="admin.php?mode=games&action=edit&which='.$result['id'].'">'.$result['name'].'</a></td>');
	print('<td><a href="admin.php?mode=edit&which='.$result['user_id'].'">'.$result['username'].'</a>');

	print('</td><td class="center">'.$result['current_males']);
	print('</td><td class="center">'.$result['current_females']);
	print('</td><td class="center">'.$result['total']);
	print('</td><td class="center">'.($result['max_players']-$result['total']));
	print('</td><td class="center">'.$result['applied']);
	print('</td><td class="center">'.date("M j, G:i",$result['creation_time']));
	print('</td><td class="center">'.date("M j, G:i",$result['last_activity']).'</td></tr>');
	$result=next_row($query);
}
?>
</table>
<?php
		break;
	default:
		echo 'oh fuck';
}
?>