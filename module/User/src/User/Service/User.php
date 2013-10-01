<?php
namespace User\Service;

// Service Manager
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

// Doctrine
use Doctrine\DBAL\DBALException;

// Encryption
use Zend\Crypt\Password\Bcrypt;

class User implements ServiceManagerAwareInterface
{
    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var User\Form\UserForm
     */
    protected $form;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $authService;
    
    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
    
    /**
     * Get service manager instance
     *
     * @return ServiceManager 
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
    
    /**
     * Get current form
     *
     * @return UserForm
     */
    public function getForm()
    {
        return $this->form;
    }
    
    /**
     * Get the entity manager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->serviceManager->get('doctrine.entitymanager.orm_default');
        }
        
        return $this->em;
    }
    
    /**
     * Get authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if ($this->authService === null) {
            $this->authService = $this->serviceManager->get('Zend\Authentication\AuthenticationService');
        }
        
        return $this->authService;
    }
    
    /**
     * Register service
     *
     * @param array Array of email, password and passwordconfirmation
     *
     * @return null|\User\Entity\User null on failure, upon created entity on success
     * @throw DBALException
     */
    public function register($data)
    {
        // Create registration form instance
        $form = $this->serviceManager->get('UserForm');
        $form->setInputFilter($this->serviceManager->get('User\Filter\RegisterFilter')->getInputFilter());
        
        // Populate data
        $form->setData($data);
        
        // Validate data
        $result = $form->isValid();
        
        // Store current form
        $this->form = $form;
        
        if ($result) {
            $formData = $form->getData();
            $user     = new \User\Entity\User();
            $userData     = $user->getArrayCopy();
            $bcrypt   = new Bcrypt();
            $userData['email']      = $formData['email'];
            $userData['password']   = $bcrypt->create($formData['password']);
            $userData['state']      = 1;
            
            // Set up empty contact for this new user
            $contact = new \User\Entity\Contact();
            $contact->populate(array(
                'address' => '',
                'city' => '',
                'state' => '',
                'zipcode' => '',
                'country' => '',
                'phone' => '',
            ));
            $userData['contact'] = $contact;
            
            $user->populate($userData);
            
            try {
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
            } catch (DBALException $e) {
                throw $e; // Rethrow this
            }
            return $user;
        }
        
        return null;
    }
    
    /**
     * Authenticate service
     *
     * @param array Array of email, password
     *
     * @return null|\Zend\Authentication\Result the authentication result
     */
    public function authenticate($data)
    {
        // Create login form instance
        $form = $this->serviceManager->get('UserForm');
        $form->setInputFilter($this->serviceManager->get('User\Filter\LoginFilter')->getInputFilter());
        
        // Populate data
        $form->setData($data);
        
        // Validate data
        $result = $form->isValid();
        
        // Store current form
        $this->form = $form;
        
        if ($result) {
            
            // Get back the validated data
            $data = $form->getData();
            
            // Get the authentication service created by doctrine
            $authService = $this->getAuthenticationService();
            
            // Get the doctrine adapter
            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($data['email']);
            $adapter->setCredentialValue($data['password']);
            $authResult = $authService->authenticate();
            
            return $authResult;
        }
        
        return null;
    }
    
    /**
     * Convienient proxy to clearIdentity() method of authentication service
     *
     * @return void
     */
    public function clearIdentity()
    {
        return $this->getAuthenticationService()->clearIdentity();
    }
}