<?php
namespace User\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;

class SignedInUserNavigationFactory extends AbstractNavigationFactory
{
    protected function getName()
    {
        return 'signedin_user';
    }
}