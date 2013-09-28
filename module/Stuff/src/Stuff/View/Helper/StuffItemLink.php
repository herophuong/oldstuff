<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffItemLink extends AbstractHelper
{
    public function __invoke($stuff_id)
    {
        return $this->getView()->url('stuff', array('action' => 'item', 'id' => (integer) $stuff_id));
    }
}