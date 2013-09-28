<?php
namespace Vote\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Doctrine\ORM\EntityManager;

use Vote\Entity\Vote;
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
        if($user->user_id != $user_id){
            return $this->redirect()->toRoute('user',array('action' => 'login'));
        }
        
        $voted_user_id = (int) $this->params()->fromroute('voted_user_id',0);
		$con=mysqli_connect("localhost","root","mysql","oldstuff");

        // Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $result = mysqli_query($con, "SELECT * FROM vote where voted_user_id=$voted_user_id");
        while($row = mysqli_fetch_array($result))
        {
            $sumRate += $row['ratescore'];
            $numOfVote++;
        }
		$avgRate = (float) $sumRate / $numOfVote;

        //mysqli_query($con, "SELECT * FROM vote where voted_user_id=$voted_user_id");

        mysqli_close($con);
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
        $vote = $this->getEntityManager()->find('Vote\Entity\Vote', $voted_user_id);
        if ($vote == null)
        {
            echo "Khong co user can vote, tao moi";
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
                        return $this->redirect()->toRoute('home',array('user_id' => $user_id,
                                                                  'action' => 'home',
                        ));
                    }
                    catch(DBALException $e){
                                
                    }
                }
            }
        }
        else
        {
            if ($vote->user_id == $user_id)
            {
                echo "Co user can vote, user vote da tung vote, chi thay doi ratescore";
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
                            return $this->redirect()->toRoute('home',array('user_id' => $user_id,
                                                                  'action' => 'home',
                            ));
                        }
                        catch(DBALException $e){
                                    
                        }
                    }
                }
            }
            else 
            {
                echo "Co user can vote, user vote chua tung vote, tao moi";
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
                            return $this->redirect()->toRoute('home',array('user_id' => $user_id,
                                                                  'action' => 'home',
                            ));
                        }
                        catch(DBALException $e){
                                    
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