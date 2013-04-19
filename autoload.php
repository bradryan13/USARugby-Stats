<?php
 
set_include_path(realpath('lib/Source/ResqueJobs') . PATH_SEPARATOR . get_include_path());
 
function autoload($className)
{
        require($className . '.php');
}
 
spl_autoload_register('autoload');
 
?>
