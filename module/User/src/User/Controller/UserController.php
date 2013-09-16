<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// Form and filters
use User\Form\UserForm;
use User\Filter\RegisterFilter;
use User\Filter\ProfileFilter;
use User\Filter\LoginFilter;

// Doctrin and entity
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use User\Entity\User;

// Encryption
use Zend\Crypt\Password\Bcrypt;

// Messenger plugin for passing messages between pages
use Zend\Mvc\Controller\Plugin\FlashMessenger;

// Authentication Result class
use Zend\Authentication\Result;

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
        // Auto redirect logged in user to his profile page
        if ($user = $this->identity()) {
            $this->redirect()->toRoute('user', array('action' => 'profile', 'id' => $user->user_id));
        }
        
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
            }
        }
        
        return array(
            'form' => $form,
        );
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
            // Find the user whose data need to be modified
            $user = $this->getEntityManager()->find('User\Entity\User', $id);
            
            // Preven anonymous or other users from accessing this page
            if ($this->identity() != $user) {
                $this->redirect()->toRoute('home');
            }
            
            // Create a new user form
            $form = new UserForm();
            
            // Put some data into our current form
            $form->setData(array('display_name' => $user->display_name));
            
            // Get the request
            $request = $this->getRequest();
            
            // We only accept POST data
            if ($request->isPost()) {
                // Get the POST data
                $data = $request->getPost();
                
                // Get input filter 
                $filter = new ProfileFilter();
                
                // Password confirmation filter should be required when password exists
                if (isset($data['password']) && !empty($data['password'])) {
                    $filter->getInputFilter()->get('passwordconfirmation')->setRequired(true)->setErrorMessage('Your password fields is not matched!');
                }
                
                // Set the filter into form
                $form->setInputFilter($filter->getInputFilter());
                
                // Populate data into the form 
                $form->setData($data);
                
                // Validate data
                if ($form->isValid()) {
                    // Get filtered and validated data
                    $validData = $form->getData();
                    
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
                    
                    // Add success message
                    $this->flashMessenger()->addSuccessMessage('Your profile is updated successfully!');
                    $this->redirect()->toRoute('user', array('action' => 'profile', 'id' => $user->user_id));
                }
            }
            
            return array(
                'form' => $form,
                'user_id' => $id,
            );
        }
    }
    
    public function loginAction()
    {
        // Prevent login page from accessing by logged in user
        if ($this->identity()) {
            $user = $this->identity();
            $this->redirect()->toRoute('user', array('action' => 'profile', 'id' => $user->user_id));
        }
        
        $form = new UserForm();
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost();
            
            // Filter the login form
            $filter = new LoginFilter();
            $form->setInputFilter($filter->getInputFilter());
            
            // Populate form data
            $form->setData($data);
            
            if ($form->isValid()) {
                // Get back the validated data
                $data = $form->getData();
                
                // Get the authentication service created by doctrine
                $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                
                // Get the doctrine adapter
                $adapter = $authService->getAdapter();
                $adapter->setIdentityValue($data['email']);
                $adapter->setCredentialValue($data['password']);
                $authResult = $authService->authenticate();
                
                if ($authResult->isValid()) {
                    $this->flashMessenger()->addSuccessMessage('You have successfully logged in!');
                    $this->redirect()->toRoute('user', array('action' => 'profile', 'id' => $authService->getIdentity()->user_id));
                } else {
                    switch($authResult->getCode()) {
                        case Result::FAILURE_IDENTITY_NOT_FOUND:
                            $this->flashMessenger()->addErrorMessage('This email isn\'t signed up yet!');
                            break;
                        case Result::FAILURE_CREDENTIAL_INVALID:
                            $this->flashMessenger()->addErrorMessage('The password is not valid. Please try again!');
                            break;
                        default:
                            $this->flashMessenger()->addErrorMessage('Your email and/or password is not valid!');
                            break;
                    }
                }
            }
        }
        
        return array(
            'form' => $form,
        );
    }

    public function logoutAction()
    {
        if ($this->identity()) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $authService->clearIdentity();
            $this->flashMessenger()->addSuccessMessage('You have been successfully logged out!');
        }
        
        $this->redirect()->toRoute('login');
        
        $response = $this->getResponse();
        $response->setStatusCode(303);
        return $response;
    }
    
    public function deleteAction()
    {
    }
}
