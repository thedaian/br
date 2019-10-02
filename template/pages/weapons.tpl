<h4>Weapon List</h4>
{MSG}
<a href="{URL}weapons/add" class="small">Add weapon</a><br/><br/>
<form action="{URL}weapons/" method="GET">
<select name="sort">
<option value="id" {id}>Id</option>
<option value="name" {name}>Name</option>
<option value="ammo" {ammo}>Ammo</option>
<option value="min_dmg" {min_dmg}>Min Damage</option>
<option value="max_dmg" {max_dmg}>Max Damage</option>
<option value="creator_ID" {creator_ID}>Creator ID</option>
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
<th class="padded">Ammo</th>
<th class="padded">Min Damage</th>
<th class="padded">Max Damage</th>
<th class="padded">Creator</th>
</tr>
{TABLE}
</table>