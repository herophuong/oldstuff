<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\RegisterForm;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
    }

    public function registerAction()
    {
        $form = new RegisterForm();
        
        return array(
            'form' => $form,
        );
    }

    public function loginAction()
    {
    }

    public function deleteAction()
    {
    }
}
