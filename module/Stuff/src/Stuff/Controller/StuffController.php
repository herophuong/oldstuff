<?php
namespace Stuff\Controller;

// MVC
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

// Entities
use Category\Entity\Category;
use Stuff\Entity\Stuff;

// Form, filters
use Stuff\Form\StuffForm;
use Stuff\Filter\AddStuffFilter;
use Zend\Filter\File\Rename;

// Doctrine
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

// Paginator
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

// Container
use Zend\Session\Container;

/**
 * 
 */
class StuffController extends AbstractActionController {
	 /**
     * @var Doctrine\ORM\EntityManager
     */
	protected $em;    
	
	public function getEntityManager(){
		if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
		return $this->em;
	}
	
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	public function indexAction(){
        
        $container = new Container('user');      
              
        $user_id_param = (int) $this->params()->fromroute('user_id',0);        
		$tab_param = $this->getRequest()->getQuery()->tab;        
        if ($tab_param)
            $container->offsetSet('tab',$tab_param);
                
//         if (!($user = $this->identity()) || ($user->__get('user_id') != $user_id_param) || (!$user_id_param))
//             return $this->redirect()->toRoute('user',array('action' => 'login'));
        $user = $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('user_id' => $user_id_param));       
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff');
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        
        if ($container->tab == "inventory" || $container->tab == "")
        {
            $queryBuilder->select('s')
                         ->from('Stuff\Entity\Stuff', 's')
                         ->where('s.user = '.$user_id_param.' and s.state != -1')
                         ->orderBy('s.stuff_id', 'DESC');
           
        }
        else if ($container->tab == "done")
        {
            $queryBuilder->select('s')
                         ->from('Stuff\Entity\Stuff', 's')
                         ->where('s.user = '.$user_id_param.' and s.state = 2')
                         ->orderBy('s.stuff_id', 'DESC'); 
        }      
        else if ($container->tab == "request")
        {            
            
        }
        
            $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($queryBuilder)));
            $paginator->setItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page) 
                $paginator->setCurrentPageNumber($page);
            return array(
                'user' => $user,       
                'paginator' => $paginator,
                'tab'=> '&'.$container->tab,
            );
	}
	
	public function addAction(){
	    //Authenticate user
		$user_id = (int) $this->params()->fromroute('user_id',0);
        $user = $this->identity();
		if($user->user_id != $user_id){
			return $this->redirect()->toRoute('home',array('action' => 'home'));
		}
        
		$form = new StuffForm();
		$filter = new AddStuffFilter();
		$form->setInputFilter($filter->getInputFilter());
        
		$request = $this->getRequest();
		
		if($request->isPost()){
		    $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
			$form->setData($post);
			if($form->isValid()){
				$formdata = $form->getData();
                //Relocate and rename uploaded image
                $filefilter = new Rename(array("target" => "./public/upload/img.jpg", "randomize" => "true"));
                $image = $filefilter->filter($formdata['image']);
                $stuff = new Stuff();
                $data = $stuff->getArrayCopy();
                $data['stuff_name']    = $formdata['stuffname'];
                $data['purpose']       = $formdata['purpose'];
                $images[0]= substr($image['tmp_name'],8);
                $data['image']         = $images;
                $data['description']   = $formdata['description'];
                $data['price']         = $formdata['price'];
                $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->findOneBy(array('cat_name' => $formdata['category']));
                $data['category']      = $category;
                $data['desired_stuff'] = $formdata['desiredstuff'];
                $data['user']          = $user;
                $data['state']         = 1;
                
                $stuff->populate($data);
				try{
					$this->getEntityManager()->persist($stuff);
					$this->getEntityManager()->flush();
					$this->flashMessenger()->addSuccessMessage("Add new stuff successfully");
					//return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
					//												'action' => 'index',
					//));
					$form = new StuffForm();
				}
				catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
				}
			}	
		}
        //Load categories
        $categories = $this->getEntityManager()->getRepository('Category\Entity\Category')->findAll();
        foreach ($categories as $value) {
            $cat_name = $value->cat_name;
            $valueoptions[$cat_name] = $cat_name;
        }
        $form->get('category')->setValueOptions($valueoptions);
        
		return array(
            'form' => $form,
        );
 	}
	
	public function deleteAction(){
       //Get user_id from URL and check if user is valid
        $user_id = (int) $this->params()->fromroute('user_id',0);
        if($this->identity()->user_id != $user_id){
            return $this->redirect()->toRoute('home',array('action' => 'home'));
        }
        
        //Check if stuff_id is valid and stuff belongs to right user
        $stuff_id = (int) $this->params()->fromroute('stuff_id',0);
        if(!$stuff_id){
            return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                          'action' => 'index',
            ));
        }
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
                            
        $request = $this->getRequest();
		
		if($request->isPost()){
            if($stuff->user->user_id != $user_id){
                $this->flashMessenger()->addErrorMessage("Delete error.");
               return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                              'action' => 'index',
               ));
            }
            $data = $stuff->getArrayCopy();
            $data['state'] = -1;
            $stuff->populate($data);
            $this->getEntityManager()->flush();
            $this->flashMessenger()->addSuccessMessage("Delete stuff successfully.");                    
        }
                
        return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                      'action' => 'index',
        ));
	}
	
	public function editAction(){
	    //Get user_id from URL and check if user is valid
	    $user_id = (int) $this->params()->fromroute('user_id',0);
        if($this->identity()->user_id != $user_id){
            return $this->redirect()->toRoute('home',array('action' => 'home'));
        }
        
        //Check if stuff_id is valid and stuff belongs to right user
        $stuff_id = (int) $this->params()->fromroute('stuff_id',0);
        if(!$stuff_id){
            return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                          'action' => 'index',
            ));
        }
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user->user_id != $user_id){
            return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                          'action' => 'index',
            ));
        }
        
        $form = new StuffForm();
        $filter = new AddStuffFilter();
        $form->setInputFilter($filter->getInputFilter());
        $request = $this->getRequest();
        
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if($form->isValid()){
                $formdata = $form->getData();
                //Overwrite the old image
                if($formdata['image']['name']!=""){
                    $filefilter = new Rename(array("target" => "./public/".$stuff->image[0], "overwrite" => "true"));
                    $filefilter->filter($formdata['image']);
                }
                $data = $stuff->getArrayCopy();
                $data['stuff_name']    = $formdata['stuffname'];
                $data['purpose']       = $formdata['purpose'];
                $data['description']   = $formdata['description'];
                $data['price']         = $formdata['price'];
                $category = $this->getEntityManager()->getRepository('Category\Entity\Category')->findOneBy(array('cat_name' => $formdata['category']));
                $data['category']      = $category;
                $data['desired_stuff'] = $formdata['desiredstuff'];
                $stuff->populate($data);
                try{
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Edit stuff successfully");
                    return $this->redirect()->toRoute('stuff',array('user_id' => $user_id,
                                                                  'action' => 'index',
                    ));
                }
                catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
            }  
        }
        else{
            //Load stuff data
            $formdata['stuffname'] = $stuff->stuff_name;
            $formdata['description'] = $stuff->description;
            $formdata['price'] = $stuff->price;
            $formdata['category'] = $stuff->category->cat_name;
            $formdata['purpose'] = $stuff->purpose;
            $formdata['desiredstuff'] = $stuff->desired_stuff;
            $form->setData($formdata);
        }
        //Load categories to select
        $categories = $this->getEntityManager()->getRepository('Category\Entity\Category')->findAll();
        foreach ($categories as $value) {
            $cat_name = $value->cat_name;
            $valueoptions[$cat_name] = $cat_name;
        }
        $form->get('category')->setValueOptions($valueoptions);
        return array(
            'form' => $form,
        );
	}
    
    public function homeAction()
    {
        // Get filter request
        $filter_category    = $this->_getStateFromPostRequest('filter.category', 'filter_category');
        $filter_purpose     = $this->_getStateFromPostRequest('filter.purpose', 'filter_purpose');
        $filter_search      = $this->_getStateFromPostRequest('filter.search', 'filter_search');

        // Get stuffs
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff');
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s')
                     ->from('Stuff\Entity\Stuff', 's')
                     ->orderBy('s.stuff_id', 'DESC');
        
        // Build the filter expression
        $and = $queryBuilder->expr()->andX();
        $parameters = new ArrayCollection();
        if ($filter_category) {
            $and->add($queryBuilder->expr()->eq('s.category', ':cat_id'));
            $parameters->add(new Parameter('cat_id', $filter_category, 'integer'));
        }
        if ($filter_purpose) {
            $and->add($queryBuilder->expr()->eq('s.purpose', ':purpose'));
            $parameters->add(new Parameter('purpose', $filter_purpose, 'string'));
        }
        if ($filter_search) {
            $and->add($queryBuilder->expr()->like('s.stuff_name', ':search'));
            $parameters->add(new Parameter('search', '%'.$filter_search.'%', 'string'));
        }
        $parts = $and->getParts();
        if (!empty($parts))
            $queryBuilder->where($and)->setParameters($parameters);
        
        // Create a paginator
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($queryBuilder)));
        $paginator->setItemCountPerPage(11);
        $page = (int) $this->params()->fromQuery('page');
        if ($page) 
            $paginator->setCurrentPageNumber($page);
        
        // Get categories
        $categories = $this->getEntityManager()->getRepository('Category\Entity\Category')->findBy(array(), array('cat_name' => 'ASC'));
        return array(
            'categories' => $categories,
            'paginator' => $paginator,
        );
    }
    
    /**
     * Get a value from post request or session if request not available
     * Auto save value into session when the value has been changed
     *
     * @param string $key       Key to get the value from session
     * @param string $parameter The parameter name of the value to get from POST request
     * @param mixed  $default   Default value if value is not available
     *
     * @return mixed|null
     */
    private function _getStateFromPostRequest($key, $parameter, $default = null)
    {
        $request = $this->getRequest()->getPost();
        $value = $request->get($parameter, $default);
        
        // Exchange request value with session value
        $session = new Container('stuff');
        if ($value !== null) {
            $session->offsetSet($key, $value);
        } else {
            $value = $session->offsetGet($key);
        }
        
        return $value;
    }
}
