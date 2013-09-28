<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffEditLink extends AbstractHelper
{
    public function __invoke($stuff_id)
    {
        return $this->getView()->url('stuff', array('action' => 'edit', 'id' => (integer) $stuff_id));
    }
}