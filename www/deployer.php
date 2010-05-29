<?php
/* vim:set tabstop=4: vim:set shiftwidth=4: vim:set smarttab: vim:set expandtab: */
require('/home/raymond/sites/aether/Aether.php');

function routeAutoLoad($name) {
    $path = split("/",pathinfo(__FILE__, PATHINFO_DIRNAME));
    $path = join("/", array_slice($path, 0, -1)) . "/";
    $file = $path . "libs/" . $name . ".php";
    if (file_exists($file))
        require_once($file);
    else
        return false;
}
spl_autoload_register("routeAutoLoad");

/**
 * 
 * A default deployer for web
 * 
 * Created: 2010-05-29
 * @author Raymond Julin
 * @package
 */

try {
    $aether = new Aether();
    $aether->render();
} 
catch (Exception $e) {
    trigger_error("Uncaught error: " . $e);
}
?>
