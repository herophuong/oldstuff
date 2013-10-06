<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffBuyLink extends AbstractHelper
{
    public function __invoke($stuff_id)
    {
        return $this->getView()->url('stuff', array('action' => 'buy', 'id' => (integer) $stuff_id));
    }
}