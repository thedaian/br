<?php
if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

switch($action) {
 case "add":
  ?>
  <form action="adminpanel.php?mode=news&action=added" method="POST">
  <center><table border=0>
  <td>Date</td><td><?php echo date("F jS, Y",time()); ?></td><tr>

  <td>Update Text</td><td><textarea name="update" rows=5 cols=30></textarea></td><tr>
  <td>Type</td><td>
  <select name="type">
  <option value=1>Major Update</option>
  <option value=2>Minor Update</option>
  <option value=3>Archived</option>
  </td><tr>

  <td colspan=2><center><input class="button" type="submit" value="Add news post"></center></td>
  </table></center>
  <?php
  break;
 case "added":
 case "edited":
 case "archived":
 case "deleted":
  if($action=="edited") {
		$update=$_POST['update'];
		$type=$_POST['type'];
		$which=$_POST['which'];
    $sql="UPDATE news SET newsText=?, type='$type'
          WHERE id='$which'";
  }
	if($action=="added") {
		$update=$_POST['update'];
		$type=$_POST['type'];
		$sql="INSERT INTO `news` (`id`,`time`,`newsText`,`type`) VALUES('','".time()."',?,'$type')";
	}
	if($action!="archived"&&$action!="deleted") {
		query($db,$sql,$update);
	} else {
	  if($action=="archived") {
			$type=$_GET['type']+2;
			$which=$_GET['which'];
			$sql="UPDATE news SET type='$type' WHERE id='$which'";
			query($db, $sql);
		} else {
			$which=$_POST['which'];
			$sql="DELETE FROM news WHERE id='$which'";
			query($db,$sql);
		}
	}
	printf("News successfully %s<br>",$action);
 case "view":
  if(isset($_REQUEST['style'])) {
		$style=$_REQUEST['style']; //registar globals is OFF, gets style
	} else { $style=""; }
  if(isset($_REQUEST['sort'])) {
		$sort=$_REQUEST['sort']; //registar globals is OFF, gets sort
	} else { $sort=""; }
?>
<center><form action="" method="GET">
<input type="hidden" name="mode" value="news">
<input type="hidden" name="action" value="view">
<select name="sort">
<option value="id" <?php if($sort=="id") { echo "selected"; }?>>Id</option>
<option value="Date" <?php if($sort=="Date") { echo "selected"; }?>>Date</option>
<option value="type" <?php if($sort=="type") { echo "selected"; }?>>Type</option>
</select>
<select name="style">
<option value=" ASC">Lowest First</option>
<option value=" DESC" <?php if($style==" DESC") { echo "selected"; }?>>Highest First</option>
</select>
<input type="submit" class="button" value="Sort List">
<br><a href="adminpanel.php?mode=news&action=view">News</a>
</center>
<?php
  if($sort=="") { $sort="id"; $style=" ASC"; }
  $sql="SELECT * FROM news ORDER BY " . $sort . $style;
  $query=query($db, $sql);
  $num=how_many($query);
  $result=next_row($query);
  ?>
  <a href="adminpanel.php?mode=news&action=add">Add News Update</a>
  <table border=1 cellpadding=1>
  <td>ID</td>
  <td>Date</td>
  <td>Text</td>
  <td>Type</td>
  <td>Archive</td><tr>

  <?php
	$i=0;
  while($i<$num) {
  printf("<td>%s</td>",$result['id']);
    printf("<td><a href=\"adminpanel.php?mode=news&action=edit&which=%s\">%s</a></td>",$result['id'],date("F jS, Y",$result['time']));

    printf("<td>%s</td><td>",$result['newsText']);
    if($result['type']==1) { echo "Major Update"; }
    if($result['type']==2) { echo "Minor Update"; }
    if($result['type']==3) { echo "Archived Major"; }
		if($result['type']==4) { echo "Archived Minor"; }
		if($result['type']<3) {
			printf("<td><a href=\"adminpanel.php?mode=news&action=archived&which=%s&type=%s\">Archive</a></td>",$result['id'],$result['type']);
		} else {
		  printf("<td><a href=\"adminpanel.php?mode=news&action=deleted&which=%s\">Delete</a></td>",$result['id']);
		}
    echo "</td><tr>";

    $result=next_row($query);
    $i++;
  }
  echo "</table><a href=\"adminpanel.php?mode=news&action=add\">Add News Update</a>";
  break;
  case "edit":
	$which=$_GET['which'];
  $sql="SELECT * FROM news WHERE id=$which";
  $result=get_row($db, $sql);
  ?>
  <form action="adminpanel.php?mode=news&action=edited&which=<?php echo $which; ?>" method="POST">
  <center><table border=0>
  <td>Date</td><td><?php echo date("F jS, Y",$result['time']); ?></td><tr>

  <td>Update Text</td><td><textarea name="update" rows=5 cols=30><?php echo $result['newsText'];?></textarea></td><tr>
  <td>Type</td><td>
  <select name="type">
  <option value=1 <?php if($result['type']==1) { echo "selected"; }?>>Major Update</option>
  <option value=2 <?php if($result['type']==2) { echo "selected"; }?>>Minor Update</option>
  <option value=3 <?php if($result['type']==3) { echo "selected"; }?>>Archived</option>
  </td><tr>

  <td colspan=2><center><input class="button" type="submit" value="Edit News"></center></td>
  </table></center>
  <?php
  break;
  default:
    echo "WTF, Mates?";
    break;
}
?>