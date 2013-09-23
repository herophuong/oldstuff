<?php
namespace Stuff\Controller;
use Category\Entity\Category;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Stuff\Form\StuffForm;
use Stuff\Entity\Stuff;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Stuff\Filter\AddStuffFilter;
use Zend\Filter\File\Rename;
// Paginator
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

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
        // Get stuffs
        $repository = $this->getEntityManager()->getRepository('Stuff\Entity\Stuff');
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('s')
                     ->from('Stuff\Entity\Stuff', 's')
                     ->orderBy('s.stuff_id', 'DESC');
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
}
