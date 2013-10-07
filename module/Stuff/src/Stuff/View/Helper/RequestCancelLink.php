<?php
namespace Stuff\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RequestCancelLink extends AbstractHelper
{
    public function __invoke($request_id)
    {
        return $this->getView()->url('stuff', array('action' => 'cancel', 'id' => (integer) $request_id));
    }
}