{if $status == 1}

<!--
<h2>Siste uke</h2>
<table name="week">
    <thead>
    <tr>
        <th>Dag</th>
        <th>Søk</th>
    </tr>
    </thead>
    {foreach from=$searches key=i item=s}
    <tr>
        <td>{$days.$i}</td>
        <td>{$s|@count}</td>
    </tr>
    {/foreach}
</table>
-->
<div id="chartdiv" style="height:200px;width:700px;"></div>
<div id="ratio-stat" style="height:200px;width:700px;"></div>
<script type="text/javascript">
    var qs = {
        service : 'searches',
        module : 'Stats',
    };
    $.getJSON("/admin", qs, function(data) {
        var statHits = [];
        var statNoHits = [];
        var statDiff = [];
        var xTicks = [];
        var i = 0;
        $.each(data, function(item) {
            statHits.push([i, this.hits]);
            statNoHits.push([i, this.noHits]);
            statDiff.push([i, (this.noHits / (this.hits + this.noHits))]);
            xTicks.push([i, item]);
            i++;
        });
        $.jqplot('chartdiv', [statHits, statNoHits], {
            title: "Søk",
            stackSeries: true,
            legend: {
                show: true, 
                'location': 'nw'
            },
            series: [
                { label: 'Med treff', fill: true }, 
                { label: 'Uten treff', fill: true }
            ],
            axes: {
                xaxis: {
                    ticks : xTicks
                },
                yaxis: {
                    ticks : [0,50,100,150,200,250,300,350]
                }
            }
        });
        $.jqplot('ratio-stat', [statDiff], {
            title: "Treffratio",
            axes: {
                xaxis: {
                    ticks : xTicks
                },
                yaxis: {
                    min : 0,
                    max : 1,
                    numberTicks : 9
                }
            }
        });
    });

</script>


{elseif $status == -1}
    {include file="login.tpl"}
{/if}
