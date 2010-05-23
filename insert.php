<?php

require_once("Config.php");
require_once("View.php");

$db = Config::getDb();
$view = new View;

$buses = $db->buses->find();
$busList = array();
while ($bus = $buses->getNext())
{
    $busId = $bus['id'];
    if (isset($_GET['route']))
        $routeId = $_GET['route'];
    else
        $routeId = false;
    foreach ($bus['routes'] as $i => $route) {
        $busList[] = array(
            'id'=>$busId, 
            'name'=>$route['name'], 
            'i'=>$i,
            'selected' => ($routeId && $routeId == $busId."_".$i)
        );
    }
}
$view->assign('routes', $busList);

if (isset($_GET['route']))
{
    list($busId, $routeKey) = explode('_', $_GET['route']);
    $view->assign('stops', $route['stops']);

    $route = null;
    $bus = null;
    foreach ($buses as $bus)
        if ($bus['id'] == $busId) 
            $route = $bus['routes'][$routeKey];
    if ($route && $bus) {
        $stops = $route['stops'];
        $view->assign('stops', $route['stops']);
    }
}

?>

<form action="" method="post">
    <br /><label><input type="text" name="route" value="5_0" /> route</label>
    <br /><label><input type="text" name="stopIndex" value="2" /> stopIndex</label>
    <br /><label><input type="text" name="stopName" value="Mulen" /> stopName</label>
    <br /><label><input type="text" name="stopTime" value="5" /> stopTime</label>
    <br /><input type="submit" value="send" />
</form>

<?php

if (isset($_POST['route']) && isset($_POST['stopIndex']) && isset($_POST['stopName']))
{
    $routeInfo   = explode('_', $_POST['route']);
    $routeNumber = $routeInfo[0];
    $routeName   = $routeInfo[1];
    $stopIndex   = $_POST['stopIndex'];
    $stopName    = $_POST['stopName'];
    $stopTime    = $_POST['stopTime'];
    
    $stop = $db->stops->findOne(array('name' => $stopName));
    if (!isset($stop['_id']))
    {
        exit('OBS! Fant ikke stoppet');
    }
    $stopId = $stop['_id'];

    $route = null;
    $bus = $db->buses->findOne(array('id' => $routeNumber));
    foreach ($bus['routes'] as $route)
    {
        if ($routeName == $route['name'])
        {
            $route = $route;
            break;
        }
    }
    if ($route)
    {
        echo '<pre>';
        print_r($route);
        echo '</pre>';

        if (isset($route['stops'][$stopIndex]))
        {
            $stopsBefore = array_slice($route['stops'], 0, $stopIndex + 1);
            $stopsAfter  = array_slice($route['stops'], $stopIndex + 1);

            $newStop = array(array(
                'name'   => $stopName,
                'time'   => $stopTime,
                'stopId' => $stopId
            ));

            $route['stops'] = array_merge($stopsBefore, $newStop, $stopsAfter);

            echo '<pre>';
            print_r($route);
            echo '</pre>';

            //$route->save();
            exit('saved');
        }
    }
    else
    {
        exit('OBS! Fant ikke ruten');
    }
}

//$view->display('insert.tpl');
