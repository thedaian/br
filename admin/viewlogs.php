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
<option value="id" <?php if($sort=="id") { echo "selected"; }?>>Id</option>
<option value="char_name" <?php if($sort=="char_name") { echo "selected"; }?>>Name</option>
<option value="last_login" <?php if($sort=="last_login") { echo "selected"; }?>>Login</option>
<option value="lastIP" <?php if($sort=="lastIP") { echo "selected"; }?>>IP Address</option>
<option value="char_money" <?php if($sort=="char_money") { echo "selected"; }?>>Money</option>
<option value="TurnsLeft" <?php if($sort=="TurnsLeft") { echo "selected"; }?>>Turns Left</option>
<option value="char_level" <?php if($sort=="char_level") { echo "selected"; }?>>Level</option>
<option value="char_exp" <?php if($sort=="char_exp") { echo "selected"; }?>>Experience Points</option>
<option value="which_group" <?php if($sort=="which_group") { echo "selected"; }?>>Guild</option>
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
  $sql="SELECT SQL_CALC_FOUND_ROWS * FROM characters ORDER BY " . $sort . $style . " LIMIT " . $start . ", 100";
} else {
  $which=$_GET['which'];
  $sql="SELECT SQL_CALC_FOUND_ROWS * FROM characters WHERE lastIP='$which' ORDER BY " . $sort . $style . " LIMIT " . $start . ", 100";
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
<table border=1 width=70% class="margin">
<tr><th>ID</th>
<th>Name</th>
<th>Last Login</th>
<th>Last IP</th>
<th>Multis</th>
<th>Privilages</th>
<th>Money</th>
<th>Bank</th>
<th>Guild</th></tr>

<?php
$i=0;
while($i<$num) {
print('<tr><td>'.$result['id'].'</td>');
print('<td><a href="adminpanel.php?mode=edit&which='.$result['id'].'">'.$result['char_name'].'</a></td>');
//if($result['DoNotDelete']) { $dont=" X"; } else { $dont=""; }
print('<td>'.date("F j, G:i:s",$result['last_login']).'</td>');
print('<td>'.$result['lastIP'].'</td>');

$sql="SELECT id, char_name FROM characters WHERE (`lastIP`='" . $result['lastIP'] . "' AND `id`<>" . $result['id'].")";
$ip_query=query($db,$sql);
$total=how_many($ip_query);

if($total!=0) {
  print('<td><a href="adminpanel.php?mode=view&action=multis&which='.$result['lastIP'].'">'.$total.' other users found</a></td>');
} else { echo '<td>No other users found</td>'; }

echo '<td>';
switch($result['game_rank']) {
  case 0:
	  echo 'Not Active';
		break;
  case 1:
    echo 'Normal User';
    break;
  case 2:
    echo 'Game Moderater';
    break;
  case 3:
    echo 'Administrator';
    break;
}
echo '</td><td>';
echo $result['char_money'];
echo '</td><td>';
echo $result['money_bank'];
if($result['which_group']==0) {
  echo '</td><td>No Guild';
} else {
  echo '</td><td>';
	echo $result['which_group'];
}
echo '</td></tr>';
$result= next_row($query);
$i++;
}
?>