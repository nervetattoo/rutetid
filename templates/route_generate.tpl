{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}

<form action="route_generate.php" method="get">
    Nummer: <input type="text" name="num"><br>
    Stop: <textarea name="stops" cols="20" rows="20"></textarea><br>
    <input type="submit" />
</form>

xml: <textarea name="xml" cols="20" rows="20">{$xml}</textarea><br>

{include file='contentbottom.tpl'}
                <div class="clear"></div>
            </div>
        </div>
{include file='footer.tpl'}
