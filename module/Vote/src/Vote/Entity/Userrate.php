<?php
namespace Vote\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userrate
 *
 * @ORM\Entity
 * @ORM\Table(name="userrate")
 * @property int    $user_id
 * @property float	$avgrate
 * @property int    $numofvote
 */
class Userrate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     */
    protected $user_id;
    
    /**
     * @ORM\Column(type="float")
     */
    protected $avgrate;

    /**
     * @ORM\Column(type="integer")
     */
    protected $numofvote;

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
    	$this->user_id = $data['user_id'];
        $this->avgrate = $data['avgrate'];
        $this->numofvote = $data['numofvote'];
    }
}