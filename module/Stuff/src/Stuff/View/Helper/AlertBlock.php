<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class AlertBlock extends AbstractHelper
{
    /**
     * Helper for display an alert block
     * 
     * @param string $message The message
     * @param string $class Additional class for the block
     *
     * @return $string The html for the field
     */
    public function __invoke($message, $class = null)
    {
        $html = "<div class='alert fade in" . (!empty($class) ? ' '.$class : '') . "'><button type='button' class='close' data-dismiss='alert'>&times;</button>$message</div>";
        
        return $html;
    }
}