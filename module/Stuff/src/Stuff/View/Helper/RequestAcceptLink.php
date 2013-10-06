<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RequestAcceptLink extends AbstractHelper
{
    public function __invoke($request_id)
    {
        return $this->getView()->url('stuff', array('action' => 'accept', 'id' => (integer) $request_id));
    }
}