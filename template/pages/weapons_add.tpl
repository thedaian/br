<form action="{URL}weapons/added" method="POST">
<table border="0">
<tr><td valign="top">
<tr><td class="text">Weapon Name</td><td><input type="text" name="name"/></td></tr>
<tr><td class="text">Description</td><td><textarea name="desc" rows="6" cols="30"></textarea></td></tr>
<tr><td class="text">Ammo</td><td><input type="text" name="ammo" value="0" size="10"/></td></tr>
<tr><td class="text">Min Damage</td><td><input type="text" name="minDmg" value="0" size="10"/></td></tr>
<tr><td class="text">Max Damage</td><td><input type="text" name="maxDmg" value="0" size="10"/></td></tr>
<tr><td class="text">Additional Notes</td><td><textarea name="notes" rows="2" cols="25"></textarea></td></tr>

<tr><td colspan="2" class="center"><input class="button" type="submit" value="Add weapon"/></td></tr>
</table>