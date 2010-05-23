{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}

<ul>
{foreach from=$routes item=r}
    <li><a href="routes.php?id={$r.id}">{$r.id} ({$r.name})</a></li>
{/foreach}
</ul>

{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
