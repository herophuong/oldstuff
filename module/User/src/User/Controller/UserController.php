<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Filter\RegisterFilter;
use User\Filter\ProfileFilter;
use User\Form\UserForm;
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
    
    /**
     * Set the entity manager
     *
     * @param EntityManager $em
     * @return void
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
 
    /**
     * Get the entity manager
     *
     * @return EntityManager
     */
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
        $form = new UserForm();
        $filter = new RegisterFilter();
        $form->setInputFilter($filter->getInputFilter());
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $formdata = $form->getData();
                $user     = new User();
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

    public function profileAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (!$id) {
            $this->getResponse()->setStatusCode(404);
        } else {
            $user = $this->getEntityManager()->getRepository('User\Entity\User')->find(array('user_id' => $id));
            
            if (!$user)
                $this->getResponse()->setStatusCode(404);
            else 
                return array('user' => $user);
        }
    }
    
    public function editAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        if (!$id) {
            $this->getResponse()->setStatusCode(404);
        } else {
            // Get the request
            $request = $this->getRequest();
            
            // We only accept POST data
            if ($request->isPost()) {
                // Get the POST data
                $data = $request->getPost();
                
                // Create a new user form and populate data into it
                $form = new UserForm();
                $filter = new ProfileFilter();
                $form->setInputFilter($filter->getInputFilter());
                $form->setData($data);

                // Validate data
                if ($form->isValid()) {
                    // Get filtered and validated data
                    $validData = $form->getData();
                    
                    // Find the user whose data need to be modified
                    $user = $this->getEntityManager()->find('User\Entity\User', $id);
                    
                    // Change user display name
                    $user->display_name = $validData['display_name'];
                    
                    // Change user password
                    if (isset($validData['password']) && !empty($validData['password'])) {
                        $bcrypt = new BCrypt();
                        $user->password = $bcrypt->create($validData['password']);
                    }
                    
                    // Now store user data
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                }
            }
        }
    }
    
    public function loginAction()
    {
    }

    public function deleteAction()
    {
    }
}
