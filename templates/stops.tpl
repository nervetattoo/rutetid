<select name="stopId" id="stopId">
    <option value="0" disabled="disabled">Velg stopp</option>
    {foreach from=$stops item=s name=stops}
        <option value="{$s.id}"{if $s.selected} selected="selected"{/if}>
        {$s.name}
        </option>
    {/foreach}
</select>
