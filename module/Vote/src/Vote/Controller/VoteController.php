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
        
        $con=mysqli_connect("localhost","root","mysql","oldstuff");

        // Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $result = mysqli_query($con, "SELECT * FROM vote WHERE voted_user_id=$voted_user_id");
        while($row = mysqli_fetch_array($result))
        {
            $sumRate += $row['ratescore'];
            $numOfVote++;
        }
        if ($numOfVote != 0) 
        {
            $avgRate = (float) $sumRate / $numOfVote;
        }
        else
        {
            $avgRate = 0;
        }
        echo "avgRate = " . $avgRate;
        $userrate = $this->getEntityManager()->find('Vote\Entity\Userrate', $voted_user_id);
        if (!$userrate)
        {
            $userrate = new Userrate();
            $data = $userrate->getArrayCopy();
            $data['user_id'] = $voted_user_id;
            $data['avgrate'] = $avgRate;
            $data['numofvote'] = $numOfVote;
        }
        else
        {
            $data = $userrate->getArrayCopy();
            $data['user_id'] = $voted_user_id;
            $data['avgrate'] = $avgRate;
            $data['numofvote'] = $numOfVote;
        }
        

        $userrate->populate($data);
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
		if($user->user_id != $user_id){
			return $this->redirect()->toRoute('user',array('action' => 'login'));
		}
        
        $voted_user_id = (int) $this->params()->fromroute('voted_user_id',0);
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
                echo "<div style='color: #4FDBBB; font-size:22px; text-align:center'>". "You rated this user before" ."</div>";
                $form = new VoteForm();

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
        return array(
            'form' => $form,
        );
    }
}