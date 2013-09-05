<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\RegisterForm;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use User\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

class UserController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
 
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
    
    public function indexAction()
    {
    }

    public function registerAction()
    {
        $form = new RegisterForm();
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $user = new User();
            
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $formdata = $form->getData();
                $data     = $user->getArrayCopy();
                $bcrypt   = new Bcrypt();
                $data['email']      = $formdata['email'];
                $data['password']   = $bcrypt->create($formdata['password']);
                $data['state']      = 1;
                
                $user->populate($data);
                
                try {
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                    
                    $this->flashMessenger()->addSuccessMessage('Register successfully!');
                    
                    $form->setData(array('email' => '', 'password' => '', 'passwordconfirmation' => '')); // clear the form
                } catch (DBALException $e) {
                    switch ($e->getPrevious()->getCode()) {
//                         case 23000: // MySQL Duplicate Key
//                             $this->flashMessenger()->addErrorMessage('This email is already registered!');
//                             break;
                        default:
                            $this->flashMessenger()->addErrorMessage($e->getMessage());
                            break;
                    }
                }
            } else {
                foreach ($form->getMessages() as $message_array) {
                    foreach ($message_array as $message) {
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }
            }
        }
        
        $return = array(
            'form' => $form,
            'success_messages' => $this->flashMessenger()->getCurrentSuccessMessages(),
            'error_messages' => $this->flashMessenger()->getCurrentErrorMessages(),
        );
        
        $this->flashMessenger()->clearCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_SUCCESS);
        $this->flashMessenger()->clearCurrentMessagesFromNamespace(FlashMessenger::NAMESPACE_ERROR);
        
        return $return;
    }

    public function loginAction()
    {
    }

    public function deleteAction()
    {
    }
}
