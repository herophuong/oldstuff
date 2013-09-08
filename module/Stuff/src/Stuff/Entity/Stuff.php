<?php
namespace Stuff\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A user
 *
 * @ORM\Entity
 * @ORM\Table(name="stuff")
 * @property int    $stuff_id
 * @property int	$user_id
 * @property int	$cat_id
 * @property float	$price
 * @property string $description
 * @property int    $state
 */
class Stuff
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $stuff_id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $cat_id;
    
    /**
     * @ORM\Column(type="float")
     */
    protected $price;
    
	/**
	 * @ORM\Column(type="string")
	 */
	protected $description;
	
    /**
     * @ORM\Column(type="integer")
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
    	$this->stuff_id = $data['stuff_id'];
        $this->user_id = $data['user_id'];
		$this->cat_id = $data['cat_id'];
        $this->price = $data['price'];
        $this->description = $data['description'];
        $this->state = $data['state'];
    }
}