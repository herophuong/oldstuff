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
        // Get this user
        $user = $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('user_id' => $user_id));
        if (!$user) {
            $this->redirect()->toRoute('home');
        }
        
        // Get filter criteria from request or session
        $filter_category = $this->_getStateFromPostRequest('filter.category', 'filter_category', 0, 'stuff\user\\'.$user_id);
        $filter_purpose = $this->_getStateFromPostRequest('filter.purpose', 'filter_purpose', '', 'stuff\user\\'.$user_id);
        $filter_search = $this->_getStateFromPostRequest('filter.search', 'filter_search', '', 'stuff\user\\'.$user_id);
        
        /*--- Filter by user tabs ---*/
        if ($user == $this->identity()) {
            $filter_tab = $this->_getStateFromPostRequest('filter.tab', 'filter_tab', 'inventory', 'stuff\user\\'.$user_id);
            switch ($filter_tab) {
                case 'inventory':
                    $filter_states = array(0, 1);
                    break;
                case 'done':                
                    $filter_states = array(2, 3);
                    break;
                case 'request':
                    // Get stuffs having pending requests
                    $filter_requests = 0;
                    $filter_states = 1;
                    break;
                default:
                    break;
            }
        } else {
            $filter_states = 1;
        }
        /*--- End filter ---*/
        
        // Get the query
        $queryBuilder = $this->_buildQuery(array(
            'category' => $filter_category,
            'purpose' => $filter_purpose,
            'keyword' => $filter_search,
            'states' => isset($filter_states) ? $filter_states : null,
            'requests' => isset($filter_requests) ? $filter_requests : null,
            'user' => $user_id,
        ));

        // Get the paginator
        $paginator = $this->_buildPaginator($queryBuilder);
        
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
            if(strpos($temp['purpose'], 'sell') !== false) {
                $filter->getInputFilter()->get('price')->setRequired(true)->setErrorMessage("To sell, you need to enter a price");
            }
            if(strpos($temp['purpose'], 'trade') !== false) {
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
        
        // Request repository
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Request');
        $requests = $repository->findBy(array('requested_stuff' => $stuff));
        
        // Get accepted user for sold/traded item
        if ($stuff->state > 2) {
            $acceptedRequest = $repository->findOneBy(array('state' => 1, 'requested_stuff' => $stuff_id));
        }
        return array(
            'stuff' => $stuff,       
            'requests' => $requests,
            'acceptedRequest' => isset($acceptedRequest) ? $acceptedRequest : null,
        );           
    }
    
    public function buyAction(){
        //Check if user is logged in
        if(!($user = $this->identity())){
            $this->flashMessenger()->addInfoMessage('You need to login first to buy this stuff!');
            return $this->redirect()->toRoute('login', array(), array('query' => array('redirect' => $this->getRequest()->getUriString())));
        }
        //Check that stuff doesn't belong to current user
        $stuff_id = $this->params()->fromRoute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user == $user){
            $this->flashMessenger()->addInfoMessage('You can not buy your own item!');
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        //Check that stuff is available to buy
        if(strpos($stuff->purpose, 'sell') === false || $stuff->state != 1){
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
                // Create a new request
                $request = new Request();
                $request->payment_method    = $formdata['payment_method'];
                $request->requestor         = $user;
                $request->type              = 'sell';
                $request->requested_stuff   = $stuff;
                $request->state             = 1;
                $request->created_time      = new \DateTime("now");
                
                // Change the state of stuff to "sold"
                $stuff->state = 2;
                
                try{
                    $this->getEntityManager()->persist($request);
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Buy completed");
                    
                    return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
                } catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
            }
        }
        return array('form' => $form, 'stuff' => $stuff);        
    }
    
    public function tradeAction(){
       //Check if user is logged in
        if(!($user = $this->identity())){
            $this->flashMessenger()->addInfoMessage("You need to log in first to trade with this item!");
            return $this->redirect()->toRoute('login', array(), array('query' => array('redirect' => $this->getRequest()->getUriString())));
        }
        //Check that stuff doesn't belong to current user
        $stuff_id = $this->params()->fromRoute('id',0);
        $stuff = $this->getEntityManager()->find('Stuff\Entity\Stuff',$stuff_id);
        if($stuff->user == $user){
            $this->flashMessenger()->addInfoMessage("You can not trade with your own item!");
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        //Check that stuff is available to trade
        if(strpos($stuff->purpose, 'trade') === false || $stuff->state != 1){
            $this->flashMessenger()->addInfoMessage("Stuff is not available to trade!");
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        
        $filter = new TradeFilter();
        $form = new TradeForm();
        $form->setInputFilter($filter->getInputFilter());
        
        // Inject current user's stuff into stuff list select
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff');
        $Ddlstuffs = $repository->findBy(array('user' => $this->identity(), 'state' => 1));
        $Ddlstuff_list = array();
        foreach ($Ddlstuffs as $Ddlstuff) {            
            $Ddlstuff_list[$Ddlstuff->stuff_id] = $Ddlstuff->stuff_name;            
        }
        $option = $form->get('proposed_stuff')->setValueOptions($Ddlstuff_list);
        
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setData($request->getPost());
            if($form->isValid()){
                $validData = $form->getData();
                //Copy form to entity
                $request = new Request();
                $request->payment_method    = 'exchange';
                $request->requestor         = $user;
                $request->requested_stuff   = $stuff;
                $request->proposed_stuff    = $repository->find($validData['proposed_stuff']);
                $request->type              = 'trade';
                $request->state             = 0; // Pending state
                $request->created_time      = new \DateTime("now");
                try{
                    $this->getEntityManager()->persist($request);
                    $this->getEntityManager()->persist($stuff);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage("Request for trade sent.");
                    return $this->redirect()->toRoute('stuff',array('action' => 'user', 'id' => $user->user_id));
                }                            
                catch(DBALException $e){
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }                
            }
        }
        return array('form' => $form, 'stuff' => $stuff);     
    }
    
    public function requestAction(){       
        if(!($user = $this->identity())){
            return $this->redirect()->toRoute('login');
        }    
        $request_id = $this->params()->fromRoute('id', 0);
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Request');
        $request = $repository->find($request_id);
        if (!$request) {
            return $this->redirect()->toRoute('stuff', array('action' => 'user', 'id' => $user->user_id));
        }
        return array('request' => $request);
    }
    
    public function rejectAction()
    {
        // TODO Rewrite this code
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
        $request_id = $this->params()->fromRoute('id', 0);
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Request');
        $request = $repository->find($request_id);
        if (!$request) {
            return $this->redirect()->toRoute('home');
        }
        
        // Prevent anonymous or other user from accessing this
        if (!($user = $this->identity()) || $user != $request->requested_stuff->user) {
            return $this->redirect()->toRoute('home');
        }
        
        // Accept the request
        $request->state = 1;
        
        // Change the state for the requested stuff and proposed stuff to "traded"
        $request->requested_stuff->state = 3;
        $request->proposed_stuff->state = 3;
            
        // TODO REJECT ALL OTHER REQUESTS TO THE REQUESTED STUFF HERE
        
        // TODO REJECT ALL OTHER REQUESTS TO THE PROPOSED STUFF HERE
        
        // Persist the changes
        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->persist($request->requested_stuff);
        $this->getEntityManager()->persist($request->proposed_stuff);
        $this->getEntityManager()->flush();
          
        $this->flashMessenger()->addSuccessMessage("You've accepted an offer on item '". $request->requested_stuff->stuff_name. "' from '" . $request->requestor->display_name . "'.");
        return $this->redirect()->toRoute('stuff', array('action' => 'item', 'id' => $request->requested_stuff->stuff_id));
    }
    
    public function homeAction()
    {
        // Get filter request
        $filter_category    = $this->_getStateFromPostRequest('filter.category', 'filter_category');
        $filter_purpose     = $this->_getStateFromPostRequest('filter.purpose', 'filter_purpose');
        $filter_search      = $this->_getStateFromPostRequest('filter.search', 'filter_search');

        // Get stuffs
        $queryBuilder = $this->_buildQuery(array(
            'category' => $filter_category,
            'purpose' => $filter_purpose,
            'keyword' => $filter_search,
            'states' => 1,
        ));
        
        // Create a paginator
        $paginator = $this->_buildPaginator($queryBuilder, 11);
        
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
    
    /**
     * Helper function to build query for listing stuffs
     *
     * @param array $filters Array of filters
     *
     * @return QueryBuilder
     */
    private function _buildQuery(array $filters = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s')
                     ->from('Stuff\Entity\Stuff', 's')
                     ->orderBy('s.stuff_id', 'DESC');
        
        // Call helper methods to add filter to this query builder
        if (is_array($filters)) {
            foreach ($filters as $criterion => $value) {
                if (!empty($value))
                    call_user_func_array(array($this, '_filterStuff' . $criterion), array(&$queryBuilder, $value));
            }
        }
        
        return $queryBuilder;
    }
    
    private function _filterStuffCategory(&$queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.category = :category')
                     ->setParameter('category', $value, 'integer');
    }
    
    private function _filterStuffPurpose(&$queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.purpose LIKE :purpose')
                     ->setParameter('purpose', '%'.$value.'%', 'string');
    }
    
    private function _filterStuffKeyword(&$queryBuilder, $value)
    {
        $or = $queryBuilder->expr()->orX();
        $or->add($queryBuilder->expr()->like('s.stuff_name', ':search'));
        $or->add($queryBuilder->expr()->like('s.description', ':search'));
        $queryBuilder->andWhere($or);
        $queryBuilder->setParameter('search', '%'.$value.'%', 'string');
    }
    
    private function _filterStuffUser(&$queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.user = :user_id');
        $queryBuilder->setParameter('user_id', $value, 'integer');
    }
    
    private function _filterStuffStates(&$queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.state IN (:state)');
        $queryBuilder->setParameter('state', $value);        
    }
    
    private function _filterStuffRequests(&$queryBuilder, $value)
    {
        $queryBuilder->innerJoin('s.requests', 'r', 'WITH', 'r.state IN :req_state');
        $queryBuilder->setParameter('req_state', $value);
    }
    
    /**
     * Helper to build paginator
     *
     * @param QueryBuilder $queryBuilder
     * @param integer $count Item count per page
     * 
     * @return Paginator
     */
    private function _buildPaginator($queryBuilder, $count = 10)
    {
        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($queryBuilder)));
        $paginator->setItemCountPerPage($count);
        $page = (int) $this->params()->fromQuery('page');
        if ($page) 
            $paginator->setCurrentPageNumber($page);
            
        return $paginator;
    }
}
