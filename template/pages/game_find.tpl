<h4>Find a Game</h4>
<form action="{URL}game/find" method="GET">
<select name="sort">
<option value="id" {id}>Id</option>
<option value="name" {name}>Name</option>
<option value="current_males" {current_males}>Males</option>
<option value="current_females" {current_females}>Females</option>
<option value="creation_time" {creation_time}>Creation Time</option>
<option value="last_activity" {last_activity}>Last Activity</option>
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
<th>Owner Name</th>
<th>Males</th>
<th>Females</th>
<th>Total</th>
<th>Remaining</th>
<th>Created</th>
<th>Last Active</th>
</tr>
{TABLE}
</table>