<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RequestRejectLink extends AbstractHelper
{
    public function __invoke($request_id)
    {
        return $this->getView()->url('stuff', array('action' => 'reject', 'id' => (integer) $request_id));
    }
}