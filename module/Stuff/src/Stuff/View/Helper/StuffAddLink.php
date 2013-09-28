<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffAddLink extends AbstractHelper
{
    public function __invoke()
    {
        return $this->getView()->url('stuff', array('action' => 'add'));
    }
}