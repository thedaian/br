<h3>Run Game</h3>
From here, you can control the action of the game itself, from moving on to the next round to viewing important character information.<br/><br/>
<table class="full">
<tr><td class="half">
Name: {NAME}<br/><br/>
Description<br/>{description}
<br/><br/>
<a href="{URL}run/list/{WHICH}" class="small">List Characters</a>
</td>
<td class="half">
Current Round: {CURRENT_ROUND}<br/><br/>
<form action="{URL}run/nextround/" method="post">
<input type="hidden" name="which" value="{WHICH}"/>
<input type="submit" value="{NEXT_ROUND_BUTTON}"/>
</form>
</tr></table>