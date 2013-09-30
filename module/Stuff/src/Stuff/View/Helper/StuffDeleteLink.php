<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffDeleteLink extends AbstractHelper
{
    public function __invoke($stuff_id)
    {
        return $this->getView()->url('stuff', array('action' => 'delete', 'id' => (integer) $stuff_id));
    }
}