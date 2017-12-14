<?php

/**
 * Class VTI_Pgrid_Helper_Utils
 */
class VTI_Pgrid_Helper_Utils extends Mage_Core_Helper_Abstract
{
    /**
     * @param int $code
     */
    public function _exit($code = 0)
    {
        $exit = create_function('$a', 'exit($a);');
        $exit($code);
    }

    /**
     * @param $a
     */
    public function _echo($a)
    {
        $echo = create_function('$a', 'echo $a;');
        $echo($a);
    }
}
