<?php
namespace Category\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A category
 *
 * @ORM\Entity
 * @ORM\Table(name="category")
 * @property int    $cat_id
 * @property string $cat_name
 * @property string $description
 * @property int    $state
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $cat_id;
       
    /**
     * @ORM\Column(type="string")
     */
    protected $cat_name;
    
	/**
	 * @ORM\Column(type="text")
	 */
	protected $description;
     
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
    	$this->cat_id = $data['cat_id'];
        $this->cat_name = $data['cat_name'];
        $this->description = $data['description'];
        $this->state = $data['state'];
    }
}