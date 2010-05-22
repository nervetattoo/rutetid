{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}
                <form id="route-search" action="search.php" method="get">
                    <fieldset>
                        <input type="hidden" name="time" value="06:00" />
                        <ul>
                            <li>
                                <label for="from">Fra:</label>
                                <input type="text" name="from" id="from" />
                            </li>
                            <li>
                                <label for="to">Til:</label>
                                <input type="text" name="to" id="to" />
                            </li>
                            <li class="controls">
                                <button type="submit" class="rel hid">Vis ruter</button>
                            </li>
                        </ul>
                    </fieldset>
                </form>
                <div id="routes">
                    <h2>Treff</h2>
                    <table>
                        <colgroup>
                            <col class="no" />
                            <col class="here" />
                            <col class="there" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">Buss nr.</th>
                                <th scope="col">er ved deg om...</th>
                                <th scope="col">og er framme om...</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach from=$routes item=route}
                            <tr>
                                <td>Buss <strong>{$route.id}</strong></td>
                                <td>XX minutter ({$route.time})</td>{*$route.name*}
                                <td>XX minutter ({$route.time} + XX minutter)</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}