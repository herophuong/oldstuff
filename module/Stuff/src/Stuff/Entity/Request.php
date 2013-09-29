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
 * @property string $address
 * @property string $phone
 * @property string $payment_method
 * @property string $description
 * @property int    $state
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="requesting_id", referencedColumnName="user_id")
     */
    protected $requesting;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Stuff\Entity\Stuff")
     * @ORM\JoinColumn(name="stuff_id", referencedColumnName="stuff_id")
     */
    protected $stuff;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $address;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    
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
        $this->requesting = $data['requesting'];
        $this->stuff = $data['stuff'];
        $this->address = $data['address'];
        $this->phone = $data['phone'];
        $this->description = $data['description'];
        $this->payment_method = $data['payment_method'];
        $this->state = $data['state'];
    }
}