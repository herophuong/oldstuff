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
 * @property int $exchange_id
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $exchange_id;
    
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
        $this->requesting = $data['requesting'];
        $this->stuff = $data['stuff'];
        $this->type = $data['type'];
        $this->exchange_id = $data['exchange_id'];
        $this->payment_method = $data['payment_method'];
        $this->state = $data['state'];
    }
}