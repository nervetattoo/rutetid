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
                                <input type="text" name="time" id="time" value="{$time}" maxlength="5" tabindex="-1" />
                                <span class="help">Valgfritt</span>
                            </li>
                        </ul>
                        <button type="submit" class="submit rel hid">Vis ruter</button>
                    </fieldset>
                </form>
                <div id="routes" class="{if $routes}has-routes {/if}rel hid clear">
                    <table>
                        <thead>
                            <tr>
                                <th class="no" scope="col">Rutebil nr.</th>
                                <th class="here" scope="col">Er ved deg om</th>
                                <th class="there" scope="col">Er framme om</th>
                            </tr>
                            <tr class="shadow">
                            {if $routes}
                                <td class="no"></td>
                                <td class="here"></td>
                                <td class="there"></td>
                            {else}
                                <td class="no-borders" colspan="3"></td>
                            {/if}
                            </tr>
                        </thead>
                        <tbody>
                    {if $routes}
                        {foreach from=$routes item=route name=routes}
                            <tr class="{cycle values='odd,even'}{if $smarty.foreach.routes.first} first{elseif $smarty.foreach.routes.last} last{/if}">
                                <td class="no">Rutebil <strong>{$route.id}</strong></td>
                                <td class="here">{$route.wait} minutter <span class="dim">({$route.startTime})</span></td>
                                <td class="there">{$route.arrivalSpan} minutter <span class="dim">({$route.arrivalTime})</span></td>
                            </tr>
                        {/foreach}
                    {else}
                            <tr class="info odd">
                                <td colspan="3" class="no-borders">
                                    <h2>Hallao!</h2>
                                    <p>Vi driver stadig og legger inn bussruter. Tygger de med litt salt og pepper. Akkurat nå har vi disse bussene i systemet vårt: {foreach from=$activeRoutes item=route name=activeRoutes}{if $smarty.foreach.activeRoutes.first}<strong>{$route}</strong>{elseif $smarty.foreach.activeRoutes.last} og <strong>{$route}</strong>.{else}, <strong>{$route}</strong>{/if}{/foreach}</p>
                                    <p>Akkurat nå er det forresten <strong>{$departures}</strong> avganger i systemet.</p>
                                    <p>Vil du hjelpe til, få dine faste ruter her? <a href="mailto:raymond.julin@gmail.com">Ta kontakt</a></p>
                                </td>
                            </tr>
                    {/if}
                        </tbody>
                </table>
                {if $routes}
                    <a id="show-more-routes" class="abs hid clear" href="#">Vis flere ruter</a>
                {else}
                    <div class="bottom abs hid clear"></div>
                {/if}
                <a id="about" class="abs hid clear" href="/om.php" rel="popover">Les om denne tjenesten</a>
                </div>
{include file='contentbottom.tpl'}
                <div class="clear"></div>
                {include file='aboutpopover.tpl'}
            </div>
        </div>
{include file='footer.tpl'}
