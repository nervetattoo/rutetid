{include file='header.tpl'}
        <div id="rutetid">
            <div id="wrap" class="rel hid clear">
{include file='contenttop.tpl'}
                <p>Prøv f.eks fra "Olav Kyrres gate" til "Minde"</p>
                <form action="search.php" method="get">
                    <input type="text" name="time" value="06:00" />
                    <label>
                        Fra:
                        <input type="text" name="from" />
                    </label>
                    <label>
                        Til:
                        <input type="text" name="to" />
                    </label>
                    <input type="submit" value="Søk" />
                </form>
                <div id="routes">
                    <h2>Treff</h2>
                    <ul>
                    {foreach from=$routes item=route}
                        <li>{$route.id} {$route.name} går fra {$from} {$route.time}</li>
                    {/foreach}
                    </ul>
                </div>
{include file='contentbottom.tpl'}
            </div>
        </div>
{include file='footer.tpl'}
