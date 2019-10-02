<div {CLASS}>
<h4>User List</h4>

<form action="{URL}userlist.php" method="GET">
<select name="sort">
<option value="user_id" {user_id}>Id</option>
<option value="username" {username}>Name</option>
<option value="last_login" {last_login}>Login</option>
<option value="created" {created}>Creation Date</option>
<option value="last_IP" {last_IP}>IP Address</option>
<option value="gamesIN" {gamesIN}>Games In</option>
<option value="gamesRUN" {gamesRUN}>Games Run</option>
<option value="gamesTOTAL" {gamesTOTAL}>Games Total</option>
<option value="gamesSURVIVED" {gamesSURVIVED}>Games Survived</option>
<option value="gamesDIED" {gamesDIED}>Games Died</option>
</select>
<select name="style">
<option value="ASC">Lowest First</option>
<option value="DESC" {DESC}>Highest First</option>
</select>
<input type="hidden" name="start" value="{START}">
<input type="submit" class="button" value="Sort List"><br/>

<br/><span class="small">Page {PAGE_NUMBERS}
</span>
<table cellspacing="0" class="list">
<tr>
<th>ID</th>
<th>Name</th>
<th>Rank</th>
<th>Games In</th>
<th>Games Run</th>
<th>Total</th>
<th>Survived</th>
<th>Died</th></tr>
{TABLE}
</table>
</div>