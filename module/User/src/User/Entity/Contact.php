<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A contact
 *
 * @ORM\Entity
 * @ORM\Table(name="contact")
 * @property int    $contact_id
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property string $country
 * @property string $phone
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $contact_id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $city;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $state;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $zipcode;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $country;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $phone;
    
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
        
    }
}