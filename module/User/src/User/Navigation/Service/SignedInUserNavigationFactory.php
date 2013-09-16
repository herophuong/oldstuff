<?php
namespace User\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class SignedInUserNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'signedin_user';
    }
}