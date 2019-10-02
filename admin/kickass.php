<?php
if(($user!=1)&&($user!=3)) { echo 'Fuck yeah!'; exit(); }

if(isset($_REQUEST['action'])) {
  $action=$_REQUEST['action']; //registar globals is OFF, gets action
} else { $action=""; }

switch($action) {
 case "query":
  $somesql=$_POST['somesql'];
  echo stripslashes($somesql);
  echo '<br>';
  query($db, stripslashes($somesql));
  echo 'Query probably worked.  Hopefully it didn\'t fuck things up.';
  break;
 default: ?>
 <table width=100%>
 <td width=50%>
  <form action="admin.php?mode=kickass&action=query" method="POST">
  <textarea name="somesql" rows=6 cols=60></textarea><br>
  <input type="submit" class="button" value="Perform SQL query"></form>
 </td><td valign=top>
</td><tr>
<td colspan=2 class="center">
  <a href="admin.php?mode=news&action=view">Edit News</a><hr>
  <a href="maintence.php">Maintence</a><hr>
  <a href="backup.php">Backup Database</a><br>(Not 100% working)
</td></table>
<?php
}
?>