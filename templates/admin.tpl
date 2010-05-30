{if $status == 1}
<h2>Siste uke</h2>
<table name="week">
    <thead>
    <tr>
        <th>Dag</th>
        <th>Søk</th>
    </tr>
    </thead>
{foreach from=$searches key=i item=s}
    <tr>
        <td>{$days.$i}</td>
        <td>{$s|@count}</td>
    </tr>
{/foreach}
</table>
<table name="distribution">
    <thead>
    <tr>
        <th>Fra</th>
        <th>Til</th>
        <th>Treff</th>
        <th>Antall</th>
    </tr>
    </thead>
{foreach from=$distribution item=d}
    <tr>
        <td>{$d.from}</td>
        <td>{$d.to}</td>
        <td>{$d.totalHits / $d.searches}</td>
        <td>{$d.searches}</td>
    </tr>
{/foreach}
</table>
{elseif $status == 0}
<p>Du er ikke admin</p>
{elseif $status == -1}
<form method="post">
    <fieldset>
        <ul>
            <li>
                <label for="username">Brukernavn</label>
                <input type="text" id="username" name="username" >
            </li>
            <li>
                <label for="password">Passord</label>
                <input type="password" id="password" name="password">
            </li>
        </ul>
        <button class="submit rel hid" type="submit">Logg inn</button>
    </fieldset>
</form>
{/if}
