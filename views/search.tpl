{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}
                <form id="route-search" class="rel hid clear" method="get">
                    <fieldset>
                        <ul>
                            <li>
                                <label for="from">Fra</label>
                                <input type="text" name="from" id="from" value="{$from}" />
                            </li>
                            <li>
                                <label for="to">Til</label>
                                <input type="text" name="to" id="to" value="{$to}" />
                            </li>
                            <li class="dim small">
                                <label for="time">Tid</label>
                                <input type="text" name="time" id="time" value="" maxlength="5" />
                                <span class="help">Valgfritt</span>
                            </li>
                        </ul>
                        <button type="submit" class="submit rel hid">Vis ruter</button>
                    </fieldset>
                </form>
                <div id="routes" class="rel hid clear">
                    <table>
                        <thead>
                            <tr>
                                <th class="no" scope="col">Rutebil nr.</th>
                                <th class="here" scope="col">Er ved deg om</th>
                                <th class="there" scope="col">Er framme om</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="shadow">
                                <td class="no"></td>
                                <td class="here"></td>
                                <td class="there"></td>
                            </tr>
                    {if $routes}
                        {foreach from=$routes item=route name=routes}
                            <tr class="{cycle values='odd,even'}{if $smarty.foreach.routes.first} first{elseif $smarty.foreach.routes.last} last{/if}">
                                <td class="no">Rutebil <strong>{$route.id}</strong></td>
                                <td class="here">{$route.wait} minutter <span class="dim">({$route.startTime})</span></td>
                                <td class="there">{$route.arrivalSpan} minutter <span class="dim">({$route.arrivalTime})</span></td>
                            </tr>
                        {/foreach}
                    {else}
                            <tr class="empty">
                                <td colspan="3">Foreta et søk, da vel.</td>
                            </tr>
                    {/if}
                        </tbody>
                </table>
                {if $routes}
                    <a id="show-more-routes" class="abs hid clear" href="#">Vis flere ruter</a>
                {/if}
                </div>
{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
