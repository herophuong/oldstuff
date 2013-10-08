<?php
namespace Vote\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Entity
 * @ORM\Table(name="vote")
 * @property int    $user_id
 * @property int    $voted_user_id
 * @property int	$ratescore
 */
class Vote
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     */
    protected $voted_user_id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $ratescore;
     
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $user_id;
    
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
        $this->voted_user_id = $data['voted_user_id'];
        $this->ratescore = $data['ratescore'];
    }
}