{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="wide rel hid clear">
                <div id="section-content" class="rel hid clear">
                    <div id="route-insert">
                        <div id="edit-route" class="rel hid clear">
                            <form id="choose-route" action="" method="get" class="rel hid clear">
                                <fieldset>
                                    <label for="route">Velg rutebil</label>
                                    <select name="route" id="route">
                                        <option value="0" selected="selected" disabled="disabled">Rutebil</option>
                                        {foreach from=$routes item=route name=routes}
                                            <option value="{$route.id}_{$route.i}"{if $route.selected} selected="selected"{/if}>
                                            {$route.id} til {$route.name}
                                            </option>
                                        {/foreach}
                                    </select>
                                    <button type="submit">›</button>
                                    <p id="departures">Avganger: <span class="departure">08:00</span>, <span class="departure">09:23</span>, <span class="departure">10:33</span>, <span class="departure">11:42</span>, <span class="departure">12:34</span>, <span class="departure">13:45</span>, <span class="departure">15:15</span> <a id="add-new-departure" href="#">Legg til ny</a></p>
                                </fieldset>
                            </form>
                            <ul id="stops" class="rel hid clear">
                            {foreach from=$stops item=stop key=i}
                                <li class="stop">{$i + 1} +{$stop.time} &#150; {$stop.name}</li>
                            {/foreach}
                            </ul>

                        {foreach from=$stops item=stop name=stops}
                            {$stop.name}
                        {/foreach}
                        </div>
                        <div id="route-map" class="rel hid clear">
                            <img src="{$mapUrl}" />
                        </div>
                    </div>
{include file='contentbottom.tpl'}
            </div>
        </div>
{include file='footer.tpl'}
