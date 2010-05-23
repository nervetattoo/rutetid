{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}

<h3>{$route.id} til {$route.name}</h3>
<ul>
{foreach from=$route.stops item=stop}
    <li>{$stop.name} ({$stop.times|@count} avganger)</li>
{/foreach}
</ul>
<img src="{$mapUrl}" />

{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
