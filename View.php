<?php
require_once('libs/smarty/libs/Smarty.class.php');

/**
 * Facade over the main Smarty class so to
 * set some common variables and setup data
 *
 * @author Raymond Julin (raymond.julin@keyteq.no)
 */

class View extends Smarty
{
    /**
     * Constructor
     *
     * @return KQSmarty
     */
    public function __construct()
    {
        parent::__construct();
        $this->template_dir =  Config::getPath() . 'views/';
        $this->compile_dir =  Config::getPath() . 'views_c/';
    }
}
