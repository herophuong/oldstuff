<?php
namespace Stuff\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Request
 *
 * @ORM\Entity
 * @ORM\Table(name="request")
 * @property User    $requesting
 * @property Stuff   $stuff
 * @property string $type
 * @property string $payment_method
 * @property Stuff  $exchange_id
 * @property int    $state
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $request_id;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_time;
    
    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="requestor_id", referencedColumnName="user_id")
     */
    protected $requestor;
    
    /**
     * @ORM\ManyToOne(targetEntity="Stuff\Entity\Stuff",inversedBy="requests")
     * @ORM\JoinColumn(name="requested_id", referencedColumnName="stuff_id")
     */
    protected $requested_stuff;
    
    /**
     * @ORM\ManyToOne(targetEntity="Stuff\Entity\Stuff")
     * @ORM\JoinColumn(name="proposed_id", referencedColumnName="stuff_id", nullable = true)
     */
    protected $proposed_stuff;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $type;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $payment_method;
    
    /**
     * @ORM\Column(type="smallint")
     */
    protected $state;
    
    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) 
    {
        return $this->$property;
    }
 
    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) 
    {
        $this->$property = $value;
    }
    
    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy() 
    {
        return get_object_vars($this);
    }
 
    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array()) 
    {
        if (isset($data['request_id']))
            $this->request_id = $data['request_id'];
        if (isset($data['created_time']))
            $this->created_time = $data['created_time'];
        if (isset($data['requestor']))
            $this->requestor = $data['requestor'];
        if (isset($data['requested_stuff']))
            $this->requested_stuff = $data['requested_stuff'];
        if (isset($data['proposed_stuff']))
            $this->proposed_stuff = $data['proposed_stuff'];
        if (isset($data['type']))
            $this->type = $data['type'];
        if (isset($data['payment_method']))
            $this->payment_method = $data['payment_method'];
        if (isset($data['state']))
            $this->state = $data['state'];
    }
}