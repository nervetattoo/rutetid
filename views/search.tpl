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
                    {elseif $error == "noHits"}
                            <tr class="info odd">
                                <td colspan="3" class="no-borders">
                                    <h2>Du må gå!</h2>
                                    <p>Neida, det kan hende det var vår feil?
                                    I så fall <a href="mailto:raymond.julin@gmail.com">ta kontakt</a>
                                    om du mener det er noe feil i systemet. Denne versjonen av søket støtter
                                    ikke overganger mellom ruter så du må søke stopp på samme rute.
                                    </p>
                                </td>
                            </tr>
                    {elseif $easteregg}
                            <tr class="odd first">
                                <td class="no">Til <strong>fots</strong></td>
                                <td class="here">Du står på de!</td>
                                <td class="there">Pust ut, du er fremme.</td>
                            </tr>
                            <tr class="even">
                                <td class="no">På <strong>hender</strong></td>
                                <td class="here">De henger på deg!</td>
                                <td class="there">Kan du det?</td>
                            </tr>
                    {else}
                            <tr class="info odd">
                                <td colspan="3" class="no-borders">
                                    <h2>Hallao!</h2>
                                    <p>Vi driver stadig og legger inn bussruter. Tygger de med litt salt og pepper. I dette øyeblikk er det <strong>{$activeRoutes|@count}</strong> rutebiler i systemet vårt.</p>
                                    <p>Akkurat nå er det forresten <strong>{$departures}</strong> avganger å søke blant.</p>
                                    <p>Vil du hjelpe til, send feilmeldinger og tips da. <a href="mailto:raymond.julin@gmail.com">Ta kontakt</a></p>
                                    {if $import}
                                    <h2>Tygging pågår</h2>
                                    <p>Akkurat nå tygges det bussruter så bitsene og bytsene flyr.
                                    Hele <strong>{$import.pct|truncate:4:""}%</strong> er unnagjort så bare hold tøylene litt til om du ikke får treff akkurat nå.</p>
                                    {/if}
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
                <!-- {$timeUsed}
                -->
            </div>
        </div>
{include file='footer.tpl'}
