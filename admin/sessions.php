<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

if($action=="remove") {
 $which=$_GET['which'];
 $sql="DELETE FROM sessions WHERE user_ID='$which'";
 query($db,$sql);
 echo 'Sessions removed. Player ID=';
 echo $which;
 echo '<br/>';
}

$sql="SELECT * FROM sessions";
$query=query($db, $sql);
$num=how_many($query);
$result=next_row($query);
?>
<table border="1" rules="bottom" cellpadding="2" class="margin">
<tr><td width=20></td>
<th>Player Name</th>
<th>Edit</th>
<th>Session Began</th>
<th>Session Expires</th>
<th>Current Action</th>
<th>Current IP</th>
<th>Remove</th></tr>

<?php
$i=0;
while($i<$num) {
	print('<tr><td>'.($i+1).'</td>');
	if($result['user_id']>0) {
		$username=get_row($db,"SELECT username FROM users WHERE ".$result['user_id']."=user_id",1);
	} else { $username[0]="Guest"; }
	print('<td><a href="view.php?which='.$result['user_id'].'" target="_blank">'.$username[0].'</a></td>');
	print('<td><a href="admin.php?mode=edit&which='.$result['user_id'].'">Edit</a></td><td>');
	echo date("M d H:i:s",$result["session_begun"]);
	echo '</td><td>';
	echo date("M d H:i:s",$result["end"]);
	echo '</td><td>';
	echo $result['action'];
	echo '</td><td>';
	echo long2ip($result['user_IP']);
	print('</td><td><a href="admin.php?mode=sessions&action=remove&which='.$result['user_id'].'">Remove</a></td></tr>');

	$result=next_row($query);
	$i++;
}
?>
</table>