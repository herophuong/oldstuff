<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class StuffUserLink extends AbstractHelper
{
    public function __invoke($user_id)
    {
        return $this->getView()->url('stuff', array('action' => 'user', 'id' => (integer) $user_id));
    }
}