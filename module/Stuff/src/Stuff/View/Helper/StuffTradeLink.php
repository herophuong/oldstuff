<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffTradeLink extends AbstractHelper
{
    public function __invoke($stuff_id)
    {
        return $this->getView()->url('stuff', array('action' => 'trade', 'id' => (integer) $stuff_id));
    }
}