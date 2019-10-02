<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }
switch($action) {
 case "add":
  ?>
  <form action="admin.php?mode=weapons&action=added" name="addItem" method="POST">
  <center>
	<table border="0">
	<tr><td valign="top">
  <tr><td class="text">Name</td><td><input type="text" name="name"></td></tr>
  <tr><td class="text">Description</td><td><textarea name="desc" rows="6" cols="30"></textarea></td></tr>
  <tr><td class="text">Ammo</td><td><input type="text" name="ammo" value="0"></td></tr>
  <tr><td class="text">Min Damage</td><td><input type="text" name="minDmg" value="0"></td></tr>
  <tr><td class="text">Max Damage</td><td><input type="text" name="maxDmg" value="0"></td></tr>
	<tr><td class="text">Additional Notes</td><td><textarea name="notes" rows="2" cols="25"></textarea></td></tr>

  <tr><td colspan="2"><center><input class="button" type="submit" value="Add weapon"></center></td></tr>
  </table></center>
  <?php
  break;
 case "edited":
	$which=$_GET['which'];
 case "added":
	$name=$_POST['name'];
	$desc=$_POST['desc'];
	$ammo=$_POST['ammo'];
	$minDmg=$_POST['minDmg'];
	$maxDmg=$_POST['maxDmg'];
	$notes=$_POST['notes'];

	if($action=="added") {
    $sql="INSERT INTO weapons (name, description, ammo, min_dmg, max_dmg, creator_ID, notes)
						VALUES(?,?,?,?,?,'$user',?)";
  }
  if($action=="edited") {
		$sql="UPDATE weapons SET name=?, description=?, ammo=?, min_dmg=?, max_dmg=?, notes=?
          WHERE id='$which'";
  }
	query($db, $sql, $name, $desc, $ammo, $minDmg, $maxDmg, $notes);
	printf("%s successfully %s<br>",$name,$action);

 case "view":
  if(isset($_REQUEST['style'])) {
		$style=$_REQUEST['style']; //registar globals is OFF, gets style
	} else { $style=""; }
  if(isset($_REQUEST['sort'])) {
		$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
	} else { $sort=""; }
?>
<center><form action="" method="GET">
<input type="hidden" name="mode" value="itemlist">
<input type="hidden" name="action" value="view">
<input type="hidden" name="start" value="0">
<?php if(isset($_GET['type'])) {	echo '<input type="hidden" name="type" value="'.$_GET['type'].'">'; }  ?>
<select name="sort">
<option value="id" <?php if($sort=="id") { echo "selected"; }?>>Id</option>
<option value="name" <?php if($sort=="name") { echo "selected"; }?>>Name</option>
<option value="ammo" <?php if($sort=="ammo") { echo "selected"; }?>>Ammo</option>
<option value="min_dmg" <?php if($sort=="min_dmg") { echo "selected"; }?>>Min Damage</option>
<option value="max_dmg" <?php if($sort=="max_dmg") { echo "selected"; }?>>Max Damage</option>
<option value="creator_ID" <?php if($sort=="creator_ID") { echo "selected"; }?>>Creator ID</option>
</select>
<select name="style">
<option value="ASC">Lowest First</option>
<option value="DESC" <?php if($style=="DESC") { echo "selected"; }?>>Highest First</option>
</select>
<input type="submit" class="button" value="Sort List"></center>
<?php
  if(empty($sort)) { $sort="id"; $style="ASC"; $start=0; }

  $sql="SELECT SQL_CALC_FOUND_ROWS weapons.*, users.username FROM weapons JOIN users ON weapons.creator_ID=users.user_id";
	if(isset($_GET['type'])) { $sql.=" WHERE Actions=". $_GET['type']; }
	$sql.=" ORDER BY " . $sort .' '. $style . " LIMIT " . $start . ", 100";
  $query=query($db, $sql);
	$total=get_row($db,"SELECT FOUND_ROWS()",1);
  $num=how_many($query);
  $result=next_row($query);
	
  $pages=($total[0]-1)/100;
  $currentpage=($start/100);

  echo "<center>Page ";
  $pagestart=0;
  $page=0;
  while($page<=$pages) {
   if($page!=0) { echo ', '; }
   if($currentpage!=$page) {
		print('<a href="admin.php?mode=itemlist&action=view&sort='.$sort.'&style='.$style.'&start='.($pagestart+$page*100).'">'.($page+1).'</a>');
   } else { echo $page+1; }
   $page++;
  }
  ?>
  <br></center>
	<div class="margin">
  <a href="admin.php?mode=weapons&action=add">Add item</a>
  <table border=1 cellpadding=1>
  <th>ID</th>
  <th>Name</th>
	<th class="padded">Info</th>
  <th class="padded">Ammo</th>
  <th class="padded">Min Damage</th>
  <th class="padded">Max Damage</th>
  <th class="padded">Creator</th>


  <?php
  for($i=0;$i<$num;$i++) { 
		print('<tr><td>'.$result['id']);
    print('</td><td><a href="admin.php?mode=weapons&action=edit&which='.$result['id'].'">'.$result['name'].'</a></td>');
		print('<td class="center"><a href="admin.php?mode=weapons&action=info&which='.$result['id'].'">Info</a></td>');

    printf('<td>%s</td>',$result['ammo']);
    printf('<td>%s</td>',$result['min_dmg']);
    printf('<td>%s</td>',$result['max_dmg']);
    print('</td><td><a href="admin.php?mode=edit&which='.$result['creator_ID'].'">'.$result['username'].'</a></td>');

    $result=next_row($query);
  }
  echo '</table><a href="admin.php?mode=weapons&action=add">Add item</a></div>';
  break;
  case "edit":
	$which=$_GET['which'];
  $sql="SELECT * FROM weapons WHERE id=$which";
  $result=get_row($db, $sql);
  ?>
  <center>
	<table border=0>
	<td colspan=2 class="center"><b>Item ID:</b> <?php echo $result['id']; ?></td><tr>
	<td valign=top>
	<form action="admin.php?mode=weapons&action=edited&which=<?php echo $which; ?>" method="POST">
	<table border=0>
  <td class="text">Item Name</td><td><input type="text" name="name" value="<?php echo $result['name']; ?>"></td><tr>
  <td class="text">Description</td>
	<td><textarea name="desc" rows="6" cols="30"><?php echo stripslashes($result['description']); ?></textarea></td></tr>
	<td class="text">Ammo</td><td><input type="text" name="ammo" value="<?php echo $result['ammo']; ?>"></td></tr>
  <tr><td class="text">Min Damage</td><td><input type="text" name="minDmg" value="<?php echo $result['min_dmg']; ?>"></td></tr>
  <tr><td class="text">Max Damage</td><td><input type="text" name="maxDmg" value="<?php echo $result['max_dmg']; ?>"></td></tr>
	<tr><td class="text">Additional Notes</td>
	<td><textarea name="notes" rows="2" cols="25"><?php echo stripslashes($result['description']); ?></textarea></td></tr>
	
	<tr><td colspan="2"><center><input class="button" type="submit" value="Edit <?php echo $result['name']; ?>"></center></td></tr>
  </table></center>
  <?php
  break;
	
	case "info":
	$which=$_GET['which'];
	$sql="SELECT id, Name, Actions, Type FROM itemlist WHERE id=$which";
  $result=get_row($db, $sql);
	
	$sql="SELECT itemAmount, playerID FROM inventory WHERE itemID='$which' AND itemAmount>0 ORDER BY itemAmount ASC";
	$query=query($db,$sql);
	$hasItem=how_many($query);
	if($hasItem>0) {
	  $highest=next_row($query,1);
		$lowest=get_row($db,"SELECT itemAmount, playerID FROM inventory WHERE itemID='$which' AND itemAmount>0 ORDER BY itemAmount DESC LIMIT 1",1);
	} else { $highest[0]=0; $lowest[0]=0; }
  ?>
  <table border=0 width=100%>
	<td colspan=2><h3>
	Information on <a href="adminpanel.php?mode=itemlist&action=edit&which=<?php echo $result['id']; ?>"><?php echo $result['Name']; ?>
	</a></h3></td><tr>
	<td width=50% valign=top><center>
	<table border=0>
	<td class="text">Amount of users with <?php echo $result['Name']; ?>:</td><td class="padded"><?php echo $hasItem; ?></td><tr>
	<td class="text">Lowest amount:</td><td class="padded"><?php echo $highest[0]; ?></td><tr>
	<td class="text">Highest amount:</td><td class="padded"><?php echo $lowest[0]; ?></td>
	<?php
	/*if($result['Actions']==1) {
	  $sql="SELECT playerID FROM inventory WHERE itemID='$which' AND equipped>0";
	  $query=query($db,$sql);
	  $hasEquipped=how_many($query);
	?>
	<tr>
	<td class="text">Amount of users with <?php echo $result['Name']; ?> equipped:</td><td class="padded"><?php echo $hasEquipped; ?></td>
	<?php
	}*/
	?>
	</table>
	</center>
	</td><td valign=top><center>
	<table border=0>
	<td class="text">Stores selling <?php echo $result['Name']; ?>:</td><td class="padded">0</td><tr>
	<td class="text">Lowest Price in a store:</td><td class="padded">0</td><tr>
	<td class="text">Highest Price in a store:</td><td class="padded">0</td><tr>
	<?php
  $sql="SELECT * FROM market WHERE itemID='$which' AND amount>0 ORDER BY price DESC";
  $query=query($db,$sql);
	$total=how_many($query);
	
	$grandtotal=0;
	$highPrice=0;
	$lowPrice=0;
	
	if($total>0) {
		$market=next_row($query);
		$highPrice=$market['price'];
		for($i=0;$i<$total;$i++) {
			$grandtotal+=$market['amount'];
			$lowPrice=$market['price'];
			$market=next_row($query);
		}
	}
	?>
	<td class="text">Amount of <?php echo $result['Name']; ?>s on the market:</td><td class="padded"><?php echo $grandtotal; ?></td><tr>
	<td class="text">Lowest Price on the market:</td><td class="padded"><?php echo $lowPrice; ?></td><tr>
	<td class="text">Highest Price on the market:</td><td class="padded"><?php echo $highPrice; ?></td>
	</table>
	</center>
	</td>
	<?php
	  $sql="SELECT id FROM recipes WHERE first_item='".$result['id']."' OR second_item='".$result['id']."' OR result_item='".$result['id']."'";
		$comboQuery=query($db, $sql);
		$comboAmount=how_many($comboQuery);
		
    if($comboAmount!=0) {
		  echo "<tr><td colspan=2 class=\"center\">";
   		printf("<a href=\"adminpanel.php?mode=recipes&action=view&item=%s\">%s Recipes Found</a></td>",$result['id'],$comboAmount);
		}
  ?>
	</table>
<?php
	break;
  default:
    echo "WTF, Mates?";
    break;
}
?>