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
                                    om du mener det er noe feil i systemet.</p>
                                    <p>Denne versjonen av søket støtter
                                    ikke overganger mellom ruter så du må søke stopp på samme rute.
                                    </p>
                                </td>
                            </tr>
                    {elseif $easteregg == "samestop"}
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
                                    <p>Rute.bz hjelper deg å finne din buss i Bergen.</p>
                                    <p>Akkurat nå støtter ikke søket overganger så du må søke på direktelinjer. Er du i tvil om hva stoppet ditt heter?
                                    Når du skriver inn Til-feltet vil systemet foreslå holdeplasser som har avganger.</p>
                                    <p>Vi gjør små forbedringer på systemet kontinuerlig, har du tilbakemeldinger så kan du <a href="mailto:raymond.julin@gmail.com">ta kontakt</a></p>
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
