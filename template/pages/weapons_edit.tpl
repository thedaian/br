<form action="{URL}weapons/edited/{WHICH}" method="POST">
<table>
<tr><td colspan="2" class="center"><b>Weapon ID:</b> {ID}</td></tr>
<tr><td class="text">Weapon Name</td><td><input type="text" name="name" value="{NAME}"/></td></tr>
<tr><td class="text">Description</td>
<td><textarea name="desc" rows="6" cols="30">{description}</textarea></td></tr>
<tr><td class="text">Ammo</td><td><input type="text" name="ammo" value="{ammo}" size="10"/></td></tr>
<tr><td class="text">Min Damage</td><td><input type="text" name="minDmg" value="{min_dmg}" size="10"/></td></tr>
<tr><td class="text">Max Damage</td><td><input type="text" name="maxDmg" value="{max_dmg}" size="10"/></td></tr>
<tr><td class="text">Additional Notes</td>
<td><textarea name="notes" rows="2" cols="25">{description}</textarea></td></tr>
<tr><td colspan="2" class="center"><input class="button" type="submit" value="Edit {name}"/></td></tr>
</table>
</form>