<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * A user
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @property int    $user_id
 * @property string $email
 * @property string $display_name
 * @property string $password
 * @property int    $state
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_id;
    
    /**
     * @ORM\Column(type="string",unique=true)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $display_name;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $password;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $state;
    
    /**
     * @ORM\OneToOne(targetEntity="User\Entity\Contact",cascade="persist")
     * @ORM\JoinColumn(name="contact_id",referencedColumnName="contact_id")
     */
    protected $contact;
    
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
        if (isset($data['user_id']))
            $this->user_id = $data['user_id'];
        if (isset($data['email']))
            $this->email = $data['email'];
        if (isset($data['display_name']))
            $this->display_name = $data['display_name'];
        if (isset($data['password']))
            $this->password = $data['password'];
        if (isset($data['state']))
            $this->state = $data['state'];
        if (isset($data['contact']))
            $this->contact = $data['contact'];
    }
}