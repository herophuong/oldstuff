<?php
namespace User\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;

class AnonymousUserNavigationFactory extends AbstractNavigationFactory
{
    protected function getName()
    {
        return 'anonymous_user';
    }
}