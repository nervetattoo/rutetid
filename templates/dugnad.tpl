{$aether.providers.header}
<div id="rutetid">
    <div id="wrap" class="wide rel hid clear">
        <div id="section-content" class="rel hid clear">
            <div id="route-insert">
                <div id="edit-route" class="rel hid clear">
                    <form id="choose-stop" action="" method="get" class="rel hid clear">
                        <fieldset>
                            <label for="route">Velg holdeplass</label>
                            {$aether.providers.stops}
                            <button type="submit">›</button>
                        </fieldset>
                    </form>
                </div>
                <div id="stop-map" class="rel hid clear">
                    <img src="{$mapUrl}" />
                </div>
            </div>
{include file='contentbottom.tpl'}
    </div>
</div>
{$aether.providers.stop}
{$aether.providers.footer}
