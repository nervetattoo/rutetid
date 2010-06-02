<div id="user-list">
<h2>Brukere</h2>
<table class="ui-widget ui-widget-content">
    <thead>
        <tr class="ui-widget-header">
            <th class="username" scope="col">Brukernavn</th>
            <th class="email" scope="col">E-post</th>
            <th class="isAdmin" scope="col">Admin?</th>
            <th class="actions" scope="col">Lagre/Slett</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$users item=u name=users}
        <tr id="{$u._id}" class="{cycle values='odd,even'}{if $smarty.foreach.users.first} first{/if}">
            <td class="username"><strong>{$u.username}</strong></td>
            <td class="email">{$u.email}</td>
            <td class="isAdmin">
                <input type="checkbox" name="is-admin[{$u._id}]"{if $u.isAdmin} checked="checked"{/if} />
            </td>
            <td class="actions">
                <a href="#del_{$u._id}" class="ui-icon ui-icon-trash">Slett</a>
            </td>
        </tr>
        {/foreach}
        <tr id="new-user" class="{cycle values='odd,even'} last">
            <td class="username">
                <input type="text" name="username" class="text ui-widget-content ui-corner-all" />
            </td>
            <td class="email">
                <input type="text" name="email" class="text ui-widget-content ui-corner-all" />
            </td>
            <td class="isAdmin">
                <input type="checkbox" name="isAdmin" class="text ui-widget-content ui-corner-all" />
            </td>
            <td class="actions">
                <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
                    <span class="ui-button-text">Lag bruker</span>
                </button>
            </td>
        </tr>
    </tbody>
</table>
<div id="new-user-info">
</div>
</div>
