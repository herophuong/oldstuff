<?php
namespace User\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class AnonymousUserNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'anonymous_user';
    }
}