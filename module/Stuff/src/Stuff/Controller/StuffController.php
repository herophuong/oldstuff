<?php
namespace Stuff\Controller;

// MVC
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

// Entities
use Category\Entity\Category;
use Stuff\Entity\Stuff;
use Stuff\Entity\Request;

// Form, filters
use Stuff\Form\StuffForm;
use Stuff\Form\BuyForm;
use Stuff\Form\TradeForm;
use Stuff\Filter\AddStuffFilter;
use Stuff\Filter\EditStuffFilter;
use Stuff\Filter\BuyFilter;
use Stuff\Filter\TradeFilter;
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
	
	public function userAction()
    {                 
        $user_id = (int) $this->params()->fromroute('id', 0);
        if (!$user_id) {
            // Redirect on invalid request
            $this->redirect()->toRoute('home');
        }
        
        // Init query builder
        $user = $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('user_id' => $user_id));       
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff');
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s');
        $queryBuilder->from('Stuff\Entity\Stuff', 's');
        
        // Get filter criteria from request or session
        $filter_category = $this->_getStateFromPostRequest('filter.category', 'filter_category', 0, 'stuff\user\\'.$user_id);
        $filter_purpose = $this->_getStateFromPostRequest('filter.purpose', 'filter_purpose', '', 'stuff\user\\'.$user_id);
        $filter_search = $this->_getStateFromPostRequest('filter.search', 'filter_search', '', 'stuff\user\\'.$user_id);
        
        // Build the filter expression
        $and = $queryBuilder->expr()->andX();
        $parameters = new ArrayCollection();
        
        // Only show stuffs from this user
        $and->add($queryBuilder->expr()->eq('s.user', ':user_id'));
        $parameters->add(new Parameter('user_id', $user_id, 'integer'));
        
        /*--- Filter by searching ---*/
        if ($filter_category) {
            $and->add($queryBuilder->expr()->eq('s.category', ':cat_id'));
            $parameters->add(new Parameter('cat_id', $filter_category, 'integer'));
        }
        if ($filter_purpose) {
            $and->add($queryBuilder->expr()->eq('s.purpose', ':purpose'));
            $parameters->add(new Parameter('purpose', $filter_purpose, 'string'));
        }
        if ($filter_search) {
            $or = $queryBuilder->expr()->orX();
            $or->add($queryBuilder->expr()->like('s.stuff_name', ':search'));
            $or->add($queryBuilder->expr()->like('s.description', ':search'));
            $and->add($or);
            $parameters->add(new Parameter('search', '%'.$filter_search.'%', 'string'));
        }
        
        /*--- Filter by user tabs ---*/
        if ($user == $this->identity()) {
            $filter_tab = $this->_getStateFromPostRequest('filter.tab', 'filter_tab', 'inventory', 'stuff\user\\'.$user_id);
            switch ($filter_tab) {
                case 'inventory':
                    $and->add($queryBuilder->expr()->in('s.state', array(0, 1)));
                    break;
                case 'done':                
                    $and->add($queryBuilder->expr()->eq('s.state', 2));
                    break;
                case 'request':
                    // TODO Implement what stuffs from this user are requested
                    $queryBuilder->innerJoin('s.requests', 'r', 'WITH', 'r.state = 1');
//                    $and->add($queryBuilder->expr()->eq('r.state', 1));
                    break;
                default:
                    // Prevent error by select nothing in query builder
                    $and->add($queryBuilder->expr()->eq('s.stuff_id', 0));
                    break;
            }
        } else {
            $and->add($queryBuilder->expr()->eq('s.state', 1));
        }
        /*--- End filter ---*/
        
        $queryBuilder->where($and)->setParameters($parameters)->orderBy('s.stuff_id', 'DESC');
        /*--- End filter ---*/
//        echo $queryBuilder; die;
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($queryBuilder)));
        $paginator->setItemCountPerPage(10);
        $page = (int) $this->params()->fromQuery('page');
        if ($page) 
            $paginator->setCurrentPageNumber($page);
        $categories = $this->getEntityManager()->getRepository('Category\Entity\Category')->findBy(array(), array('cat_name' => 'ASC'));
        return array(
            'user' => $user,       
            'paginator' => $paginator,
            'categories' => $categories,
        );
	}
	
	public function addAction(){
	    //Check if user is logged in
	    if(!($user = $this->identity())){
	        return $this->redirect()->toRoute('login');
	    }
		$form = new StuffForm();
		$filter = new AddStuffFilter();
		
		$request = $this->getRequest();
		
		if($request->isPost()){
		    $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $temp = $request->getPost();
            if($temp['purpose']== 'sell'){
                $filter->getInputFilter()->get('price')->setRequired(true)->setErrorMessage("To sell, you need to enter a price");
            }
            else if($temp['purpose'] == 'trade'){
                $filter->getInputFilter()->get('desiredstuff')->setRequired(true)->setErrorMessage("To trade, you need to enter desired stuff");
            }
		    $form->setInputFilter($filter->getInputFilter());
		    
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
                $data['state']         = $formdata['state'];
                
                $stuff->populate($data);
				try{
					$this->getEntityManager()->persist($stuff);
					$this->getEntityManager()->flush();
					$this->flashMessenger()->addSuccessMessage("Add new stuff successfully");
					return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
				}
				catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
				}
			}	
		}
        else {
            $form->setInputFilter($filter->getInputFilter());
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
	    //Check if user is logged in
	    if(!($user = $this->identity())){
	        return $this->redirect()->toRoute('login');
	    }
        //Check if stuff_id is valid and stuff belongs to right user
        $stuff_id = (int) $this->params()->fromroute('id',0);
        if(!$stuff_id){
            return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
        }
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user != $user){
            $this->flashMessenger()->addErrorMessage("Delete error.");
            return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
        }
        $request = $this->getRequest();        
        $data = $stuff->getArrayCopy();
        $data['state'] = -1;
        $stuff->populate($data);
        $this->getEntityManager()->flush();
        $this->flashMessenger()->addSuccessMessage("Delete stuff successfully.");                    

                
        return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
	}
	
	public function editAction(){
        //Check if stuff_id is valid and stuff belongs to right users
        $user = $this->identity();
        if(!$user){
            return $this->redirect()->toRoute('login');   
        }
        $stuff_id = (int) $this->params()->fromroute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);        
        if(!$stuff_id){
            return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
        }
        if(!$stuff || $stuff->user != $user){
            return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
        }
        
        $form = new StuffForm();
        $filter = new EditStuffFilter();
        $request = $this->getRequest();
        
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $temp = $request->getPost();
            if($temp['purpose']== 'sell'){
                $filter->getInputFilter()->get('price')->setRequired(true)->setErrorMessage("To sell, you need to enter a price");
            }
            else if($temp['purpose'] == 'trade'){
                $filter->getInputFilter()->get('desiredstuff')->setRequired(true)->setErrorMessage("To trade, you need to enter desired stuff");
            }
            $form->setInputFilter($filter->getInputFilter());
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
                $data['state']         = $formdata['state'];
                $stuff->populate($data);
                try{
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Edit stuff successfully");
                    return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
                }
                catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
            }  
        }
        else{
            //Load stuff data
            $form->setInputFilter($filter->getInputFilter());
            $formdata['stuffname']   = $stuff->stuff_name;
            $formdata['description'] = $stuff->description;
            $formdata['price']       = $stuff->price;
            $formdata['category']    = $stuff->category->cat_name;
            $formdata['purpose']     = $stuff->purpose;
            $formdata['desiredstuff']= $stuff->desired_stuff;
            $formdata['state']       = $stuff->state;
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
    
    public function itemAction(){
        //Get stuff from db and check if it is valid
        $stuff_id = $this->params()->fromRoute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if(!$stuff){
            return $this->redirect()->toRoute('home');
        }
        $request = $this->getEntityManager()->getRepository('Stuff\Entity\Request');
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select('r')
                ->from('Stuff\Entity\Request', 'r')
                ->where('r.stuff = '.$stuff_id);
        $results = $queryBuilder->getQuery()->execute();        
        
        $accepted = 0;
        foreach ($results as $result) $accepted = $result->requesting;       
       
        $usercontact = $this->getEntityManager()->getRepository('User\Entity\User')->find($accepted); 
        return array(
            'stuff' => $stuff,       
            'results' => $results,
            'contact' => $usercontact,
        );           
    }
    
    public function buyAction(){
        //Check if user is logged in
        if(!($user = $this->identity())){
            $this->flashMessenger()->addInfoMessage('You need to login first to buy this stuff!');
            return $this->redirect()->toRoute('login');
        }
        //Check that stuff doesn't belong to current user
        $stuff_id = $this->params()->fromRoute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user == $user){
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        //Check that stuff is available to buy
        if($stuff->purpose != 'sell' || $stuff->state != 1){
            $this->flashMessenger()->addErrorMessage("Stuff is not available to buy");
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        
        $filter = new BuyFilter();
        $form = new BuyForm();
        $form->setInputFilter($filter->getInputFilter());
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                //Copy form to entity
                $buyrequest = new Request();
                $data = $buyrequest->getArrayCopy();
                $data['payment_method']= $formdata['paymentmethod'];
                $data['requesting']    = $user;
                $data['exchange_id']   = null;
                $data['type']          = $stuff->purpose;
                $data['stuff']         = $stuff;
                $data['state']         = 1;
                $stuff->state = 2;
                $buyrequest->populate($data);
                try{
                    $this->getEntityManager()->persist($buyrequest);
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Buy completed");
                    return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
                }
                catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
            }
        }
        return array('form' => $form, 'stuff' => $stuff);        
    }
    
    public function tradeAction(){
       //Check if user is logged in
        if(!($user = $this->identity())){
            return $this->redirect()->toRoute('login');
        }
        //Check that stuff doesn't belong to current user
        $stuff_id = $this->params()->fromRoute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user == $user){
            $this->flashMessenger()->addErrorMessage("This is your own item.");
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        //Check that stuff is available to trade
        if($stuff->purpose != 'trade' || $stuff->state != 1){
            $this->flashMessenger()->addErrorMessage("Stuff is not available to trade");
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        
        $filter = new TradeFilter();
        $form = new TradeForm();
        $form->setInputFilter($filter->getInputFilter());
        
        $Ddlstuffs = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findBy(array('user' => $this->identity(), 'state' => 1));
       
        $Ddlstuff_list = array();
        foreach ($Ddlstuffs as $Ddlstuff)
        {            
            $Ddlstuff_list[$Ddlstuff->stuff_id] = $Ddlstuff->stuff_name;            
        }
        $option = $form->get('exchangeStuff')->setValueOptions($Ddlstuff_list);             
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost());
            if($form->isValid()){
                $formdata = $form->getData();
                //Copy form to entity
                $traderequest = new Request();
                $data = $traderequest->getArrayCopy();
                $data['payment_method']= 'exchange';
                $data['requesting']    = $user;
                $data['exchange_id']   = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $formdata['exchangeStuff']));
                $data['type']          = $stuff->purpose;
                $data['stuff']         = $stuff;
                $data['state']         = 1;               
                $traderequest->populate($data);
                $duplicateTest = $this->getEntityManager()->getRepository('Stuff\Entity\Request')->findOneBy(array('stuff' => $stuff,'requesting'=>$user));   
                if (is_null($duplicateTest))                
                try{
                    $this->getEntityManager()->persist($traderequest);
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Request for trade sent.");
                    return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
                }                            
                catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
                else $this->flashMessenger()->addErrorMessage("You've already sent request for this item.");
                
            }
        }
        return array('form' => $form, 'stuff' => $stuff);     
    }
    
    public function viewrequestAction(){       
        if(!($user = $this->identity())){
            return $this->redirect()->toRoute('login');
        }    
        $stuff_id = $_GET['stuff'];  $exchange_id = $_GET['exchange'];
        
        if (($stuff_id == "")||($exchange_id == "")) {
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        } 
        $stuff = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $stuff_id));       
        $exchange = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $exchange_id));       
        return array('stuff' => $stuff, 'exchange' => $exchange);     
    }
    
    public function rejectAction()
    {
        $stuff_id = $_GET['stuff'];  $requesting_id = $_GET['requester'];
        $request = $this->getEntityManager()->getRepository('Stuff\Entity\Request')->findOneBy(array('stuff' => $stuff_id, 'requesting' => $requesting_id));
        $data = $request->getArrayCopy();
        $data['state'] = 2;
        $request->populate($data);
        $this->getEntityManager()->flush();
        $stuff = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $stuff_id));  
        $user = $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('user_id' => $requesting_id));  
        $this->flashMessenger()->addSuccessMessage("You've rejected an offer on item '". $stuff->stuff_name. "' from '" . $user->email. "'.");
        return $this->redirect()->toRoute('stuff', array('action' => 'item', 'id' => $stuff_id));
    }
    
     public function acceptAction()
    {
         // TODO : receiving contact from requestor
        $stuff_id = $_GET['stuff'];  $requesting_id = $_GET['requester'];
        $request = $this->getEntityManager()->getRepository('Stuff\Entity\Request')->findOneBy(array('stuff' => $stuff_id, 'requesting' => $requesting_id));
        $exchange_id = $request->exchange_id;
        $data = $request->getArrayCopy();
        $data['state'] = 3;
        $request->populate($data);
        $this->getEntityManager()->flush();
        
        $stuff = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $stuff_id));      
        $data = $stuff->getArrayCopy();
        $data['state'] = 2;
        $stuff->populate($data);
        $this->getEntityManager()->flush();
        
        $stuff = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff')->findOneBy(array('stuff_id' => $exchange_id));      
        $data = $stuff->getArrayCopy();
        $data['state'] = 2;
        $stuff->populate($data);
        $this->getEntityManager()->flush();
        
        $user = $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('user_id' => $requesting_id));  
        $this->flashMessenger()->addSuccessMessage("You've accepted an offer on item '". $stuff->stuff_name. "' from '" . $user->email. "'.");
        return $this->redirect()->toRoute('stuff', array('action' => 'item', 'id' => $stuff_id));
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
            $or = $queryBuilder->expr()->orX();
            $or->add($queryBuilder->expr()->like('s.stuff_name', ':search'));
            $or->add($queryBuilder->expr()->like('s.description', ':search'));
            $and->add($or);
            $parameters->add(new Parameter('search', '%'.$filter_search.'%', 'string'));
        }
        
        // Only show published stuffs
        $and->add($queryBuilder->expr()->like('s.state', ':state'));
        $parameters->add(new Parameter('state', 1, 'integer'));
        
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
     * @param string $namespace The namespace to initialize the session container
     *
     * @return mixed|null
     */
    private function _getStateFromPostRequest($key, $parameter, $default = null, $namespace = 'stuff')
    {
        $request = $this->getRequest()->getPost();
        
        $value = $request->get($parameter, null);
        // Exchange request value with session value
        $session = new Container($namespace);
        if ($value !== null) {
            $session->offsetSet($key, $value);
        } else if ($session->offsetGet($key) === null) {
            $session->offsetSet($key, $default);
            $value = $default;
        } else {
            $value = $session->offsetGet($key);
        }
        
        return $value;
    }
}
