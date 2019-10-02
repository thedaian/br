<h4>Character List</h4>
<span class="small"><a href="{URL}character/new">Create New Character</a><br/>
<a href="{URL}game/find">Go to the games list</a></span><br/><br/>

<form action="{URL}character/list" method="GET">
<select name="sort">
<option value="id" {id}>Id</option>
<option value="name" {name}>Name</option>
<option value="game_id" {game_id}>Game ID</option>
<option value="gender" {gender}>Gender</option>
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
<th>Edit</th>
<th>Game Name</th>
<th>Applied To</th>
<th>Gender</th>
<th>Health</th>
</tr>
{TABLE}
</table>