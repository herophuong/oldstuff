<?php
namespace Vote\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

use Doctrine\ORM\EntityManager;

use Vote\Entity\Vote;
use Vote\Entity\Userrate;
use Vote\Form\VoteForm;


class VoteController extends AbstractActionController
{
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
    
    public function indexAction()
    {
    }

	public function avgvoteAction()
	{
        $avgRate = 0;
        $numOfVote = 0;
		$sumRate = 0;
        //Authenticate user
        $user_id = (int) $this->params()->fromroute('user_id',0);
        $user = $this->identity();
        if($user->user_id != $user_id)
        {
            return $this->redirect()->toRoute('user',array('action' => 'login'));
        }
        
        $voted_user_id = (int) $this->params()->fromroute('voted_user_id',0);
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select("avg(v.ratescore) as average_rate, count(v.user_id) as rate_count")
                     ->from("Vote\Entity\Vote", 'v')
                     ->where("v.voted_user_id = :id")
                     ->groupBy("v.voted_user_id")
                     ->setParameter('id', $voted_user_id);
        $query = $queryBuilder->getQuery();
        $result = $query->getSingleResult();
        
        $userrate = $this->getEntityManager()->find('Vote\Entity\Userrate', $voted_user_id);
        if (!$userrate) {
            $userrate = new Userrate();
            $userrate->user_id = $voted_user_id;
        }
        $userrate->avgrate = $result['average_rate'];
        $userrate->numofvote = $result['rate_count'];
        
        try
        {
            $this->getEntityManager()->persist($userrate);
            $this->getEntityManager()->flush();
            $this->flashMessenger()->addSuccessMessage("Thank you for your vote");
            return $this->redirect()->toRoute('stuff',array('id' => $voted_user_id,
                                                            'action' => 'user',
            ));
        }
        catch (DBALException $e)
        {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }
	}

    public function voteAction()
    {
        //Authenticate user
		$user_id = (int) $this->params()->fromroute('user_id',0);
        $user = $this->identity();
		if($user->user_id != $user_id) {
			return $this->redirect()->toRoute('user',array('action' => 'login'));
		}
        
        $voted_user_id = (int) $this->params()->fromroute('voted_user_id',0);
        if ($user_id == $voted_user_id)
        {
            $this->flashMessenger()->addErrorMessage("You can not rate yourself!");
            return $this->redirect()->toRoute('stuff',array('id' => $user_id,
                                                            'action' => 'user',
            ));
        }
        else
        {
            $vote = $this->getEntityManager()->getRepository('Vote\Entity\Vote')->findOneBy(array('voted_user_id' => $voted_user_id, 'user_id' => $user_id));
            if ($vote == null)
            {
                $form = new VoteForm();

                $request = $this->getRequest();
                if ($request->isPost())
                {
                    $form->setData($request->getPost());
                    if ($form->isValid())
                    {
                        $formdata = $form->getData();
                        $vote = new Vote();
                        $data = $vote->getArrayCopy();
                        $data['user_id'] = $user_id;
                        $data['voted_user_id'] = $voted_user_id;
                        $data['ratescore'] = $formdata['rate_box'];

                        $vote->populate($data);
                        try
                        {
                            $this->getEntityManager()->persist($vote);
                            $this->getEntityManager()->flush();
                            return $this->redirect()->toRoute('vote',array('user_id' => $user_id,
                                                                        'voted_user_id' => $voted_user_id,
                                                                      'action' => 'avgvote',
                            ));
                        }
                        catch(DBALException $e){
                            $this->flashMessenger()->addErrorMessage($e->getMessage());          
                        }
                    }
                }
            }
            else
            {
                if ($vote->user_id == $user_id)
                {
                    $form = new VoteForm();
                    $form->setData(array('rate_box' => $vote->ratescore));
                    $request = $this->getRequest();
                    if ($request->isPost())
                    {
                        $form->setData($request->getPost());
                        if ($form->isValid())
                        {
                            $formdata = $form->getData();
                            $data = $vote->getArrayCopy();
                            $data['user_id'] = $vote->user_id;
                            $data['voted_user_id'] = $vote->voted_user_id;
                            $data['ratescore'] = $formdata['rate_box'];

                            $vote->populate($data);
                            try
                            {
                                $this->getEntityManager()->persist($vote);
                                $this->getEntityManager()->flush();
                                $this->flashMessenger()->addSuccessMessage("Thank you for your vote");
                                return $this->redirect()->toRoute('vote',array('user_id' => $user_id,
                                                                        'voted_user_id' => $voted_user_id,
                                                                      'action' => 'avgvote',
                            ));
                            }
                            catch(DBALException $e){
                                $this->flashMessenger()->addErrorMessage($e->getMessage());        
                            }
                        }
                    }
                }
                elseif ($vote->user_id != $user_id)
                {
                    $form = new VoteForm();

                    $request = $this->getRequest();
                    if ($request->isPost())
                    {
                        $form->setData($request->getPost());
                        if ($form->isValid())
                        {
                            $formdata = $form->getData();
                            $vote = new Vote();
                            $data = $vote->getArrayCopy();
                            $data['user_id'] = $user_id;
                            $data['voted_user_id'] = $voted_user_id;
                            $data['ratescore'] = $formdata['rate_box'];

                            $vote->populate($data);
                            try
                            {
                                $this->getEntityManager()->persist($vote);
                                $this->getEntityManager()->flush();
                                $this->flashMessenger()->addSuccessMessage("Thank you for your vote");
                                return $this->redirect()->toRoute('vote',array('user_id' => $user_id,
                                                                        'voted_user_id' => $voted_user_id,
                                                                      'action' => 'avgvote',
                            ));
                            }
                            catch(DBALException $e){
                                $this->flashMessenger()->addErrorMessage($e->getMessage());        
                            }
                        }
                    }
                }
            }
        }
        return array(
            'form' => $form,
        );
    }
}