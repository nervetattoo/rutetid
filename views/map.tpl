{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}
    <form method="get">
        <select name="stop">
        {foreach from=$stops item=s key=k}
            <option value="{$k}">{$s.name}</option>
        {/foreach}
        </select>
        <input type="submit" value="-&gt;" />
    </form>
    <div id="map_canvas" style="width:100%; height:512px"></div>
    <script type="text/javascript" src="/js/map.js"></script>
    <script type="text/javascript">
        var loc = {
            lat : {$lat},
            lon : {$long},
            zoom: 14
        }
        {if $stop}
        initMap(loc, $("#map_canvas")[0]);
        {/if}
    </script>
{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
