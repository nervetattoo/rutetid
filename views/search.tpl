{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}
                <form id="route-search" class="rel hid clear" action="search.php" method="get">
                    <fieldset>
                        <input type="hidden" name="time" value="06:00" />
                        <ul>
                            <li>
                                <label for="from">Fra</label>
                                <input type="text" name="from" id="from" />
                                {*<select name="from" id="from">
                                    Populate with JS, or PHP
                                </select>*}
                            </li>
                            <li>
                                <label for="to">Til</label>
                                <input type="text" name="to" id="to" />
                            </li>
                        </ul>
                        <button type="submit" class="submit rel hid">Vis ruter</button>
                    </fieldset>
                </form>
            {if $routes}
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
                        {foreach from=$routes item=route name=routes}
                            <tr class="{cycle values='odd,even'}{if $smarty.foreach.routes.first} first{elseif $smarty.foreach.routes.last} last{/if}">
                                <td class="no">Rutebil <strong>{$route.id}</strong></td>
                                <td class="here">{$route.wait} minutter ({$route.startTime})</td>{*$route.name*}
                                <td class="there">{$route.runningTime + $route.wait} minutter ({$route.arrivalTime})</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <a id="show-more-routes" class="abs hid clear" href="#">Vis flere ruter</a>
                </div>
            {/if}
{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
