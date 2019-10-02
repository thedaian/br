<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

if(!isset($_GET['which'])) {
	echo 'This shouldn\'t happen.';
	exit();
}
$which=$_GET['which'];
if(($which==1)&&($user!=1)) {
	echo 'I R TEH GOD, and cannot be edited.';
	exit();
}
if($action=="change") {
	$permissions=$_POST['permissions'];
	$sql="UPDATE users SET user_level='$permissions' WHERE user_id='$which'";
	query($db, $sql);

	echo "User is now a";
	switch($permissions) {
	case 1:
		echo " normal user.";
		break;
	case 2:
		echo " power user.";
		break;
	case 3:
		echo " moderater.";
		break;
	case 4:
		echo "n administrator.";
		break;
	}
} elseif($action=="update") {
 $changes=$_POST['confirm'];
 if($changes=="Yes") {
	$email=$_POST['email'];
	$name=$_POST['name'];
	$race=$_POST['race'];
	$gender=$_POST['gender'];
	
	$body=$_POST['body'];
	$body_base=$_POST['body_base'];
	$mind=$_POST['mind'];
	$mind_base=$_POST['mind_base'];
	$looks=$_POST['looks'];
	$looks_base=$_POST['looks_base'];
	
	$hp=$_POST['hp'];
	$hp_max=$_POST['hp_max'];
	$base_hp=$_POST['base_hp'];
	
	$sp=$_POST['sp'];
	$sp_max=$_POST['sp_max'];
	$base_sp=$_POST['base_sp'];
	
	$attack=$_POST['attack'];
	$attack_base=$_POST['attack_base'];
	$defense=$_POST['defense'];
	$defense_base=$_POST['defense_base'];
	
	$money=$_POST['money'];
	$money_bank=$_POST['bank_money'];
	$exp=$_POST['exp'];
	
	$sql="UPDATE users SET char_name='$name', email='$email', char_race='$race', char_gender='$gender',
  	body='$body', body_base='$body_base', mind='$mind', mind_base='$mind_base', looks='$looks', looks_base='$looks_base',
		char_hp='$hp', hp_max='$hp_max', hp_baseMax='$base_hp', char_sp='$sp', sp_max='$sp_max', sp_baseMax='$base_sp',
		char_attack='$attack', attack_base='$attack_base', char_defense='$defense', defense_base='$defense_base',
		char_money='$money', money_bank='$money_bank',
		char_exp='$exp'
		WHERE id='$which'";
	query($db,$sql);

	echo "<h3>Character Changed.</h3>";
 } else {
  echo "<h3>Changes not made, check the box</h3>";
 }
}
$sql="SELECT * FROM users WHERE user_id='$which'";
$user=get_row($db, $sql);
?>
<table border=0 width=100%>
<td colspan=3><h3>Edit User</h3></td><tr>
<td width=50></td><td width=50%>
<table border=0>
<form action="admin.php?mode=edit&which=<?php echo $which; ?>" method=POST>
<input type="hidden" name="action" value="update">
<td width=150 class="text">Name:</td><td><input type="text" name="name" value="<?php echo $user['username']; ?>"></td>
<tr>
<td class="text">Email:</td><td><input type="text" name="email" value="<?php echo $user['email']; ?>"></td>
<tr>
<td colspan=2 class="text"><br>
<input type="checkbox" name="confirm" value="Yes"> Confirm Changes<br>
<input type="submit" value="Edit User" class="button">
</form>
</td>
</table>
</td><td valign=top>
<table border=0>
<form action="admin.php?mode=edit&which=<?php echo $which; ?>" method="POST">
<input type="hidden" name="action" value="change">
<td colspan=2 class="text"><hr><center>Change Permissions</td><tr>
<td class="text">Normal User</td>
<td><input type="radio" name="permissions" value="1" <?php if( $user['user_level'] == 1 ) { echo 'checked'; } ?></td><tr>
<td class="text">Power User: Creates games</td>
<td><input type="radio" name="permissions" value="2" <?php if( $user['user_level'] == 2 ) { echo 'checked'; } ?></td><tr>
<td class="text">Moderator</td>
<td><input type="radio" name="permissions" value="3" <?php if( $user['user_level'] == 3 ) { echo 'checked'; } ?></td><tr>
<td class="text">Administrator</td>
<td><input type="radio" name="permissions" value="4" <?php if( $user['user_level'] == 4 ) { echo 'checked'; } ?></td><tr>
<td colspan=2><input class="button" type="submit" value="Change Permissions"></td><tr>
</form>
<td height=20 colspan=2 class="text"><hr><center>Delete User</td><tr>
<form action="adminpanel.php?mode=delete&which=<?php echo $which; ?>" method="POST">
<td colspan=2 class="text">
<input type="checkbox" name="confirm" value="Yes"> Confirm Deletion (Cannot be undone)</td><tr>
<td colspan=2 class="center"><input class="button" type="submit" value="Delete User"></td><tr>
</form>
</table>
<br><b>
<?php
  $sql="SELECT user_id, username FROM users WHERE last_IP='" . $user['last_IP'] . "' AND user_id<>" . $which;
  $query=query($db,$sql);
  $total=how_many($query);
  if($total!=0) {
    $multis=next_row($query,1);
    $names="<a href=\"admin.php?mode=edit&which=" . $multis[0] . "\">" . $multis[1] . "</a>";
    $i=1;
    while($i<$total) {
      $multis=next_row($query,1);
      $names.=", <a href=\"admin.php?mode=edit&which=" . $multis[0] . "\">" . $multis[1] . "</a>";
      $i++;
    }
    echo 'Users with the same IP address:</b></br>';
    echo $names;
  } else { echo 'No other users with the same IP found.</b>'; }
?>
</td>
</table>