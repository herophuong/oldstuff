<?php
namespace Stuff\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stuff
 *
 * @ORM\Entity
 * @ORM\Table(name="stuff")
 * @property int    $stuff_id
 * @property User	$user
 * @property string $stuff_name
 * @property Category $category
 * @property float	$price
 * @property string $image
 * @property string $purpose
 * @property string $desired_stuff
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
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $stuff_name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category\Entity\Category")
     * @ORM\JoinColumn(name="cat_id", referencedColumnName="cat_id")
     */
    protected $category;
    
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $price;
    
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $description;
    
    /**
     * @ORM\Column(type="simple_array")
     */
     protected $image;
    
	/**
     * @ORM\Column(type="string")
     */
    protected $purpose; 
     
    /**
     *@ORM\Column(type="string",nullable=true) 
     */
    protected $desired_stuff;
     
    /**
     * @ORM\Column(type="smallint")
     */
    protected $state;
    
    /**
     * @ORM\OneToMany(targetEntity="Stuff\Entity\Request",mappedBy="requested_stuff")
     */
    protected $requests;
    
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
        $this->user = $data['user'];
		$this->category = $data['category'];
        $this->stuff_name = $data['stuff_name'];
        $this->price = $data['price'];
        $this->image = $data['image'];
        $this->description = $data['description'];
        $this->desired_stuff = $data['desired_stuff'];
        $this->purpose = $data['purpose'];
        $this->state = $data['state'];
    }
}