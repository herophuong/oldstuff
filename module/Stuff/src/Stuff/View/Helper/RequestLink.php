<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RequestLink extends AbstractHelper
{
    public function __invoke($request_id)
    {
        return $this->getView()->url('stuff', array('action' => 'request', 'id' => (integer) $request_id));
    }
}