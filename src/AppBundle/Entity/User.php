<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="`user`")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="The first name is too short.",
     *     maxMessage="The first name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */

    protected $firstName;


    /**
     * @ORM\Column(name="last_name",type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="The last name is too short.",
     *     maxMessage="The last name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $lastName;

    /**
     * @ORM\Column(name="phone",type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="activated_at", type="datetime", nullable=true)
     */
    protected $activatedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        if (!empty($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        }

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        if ($this->createdAt) {
            return $this->createdAt->format('Y-m-d');
        }

        return $this->createdAt;
    }

    /**
     * Set activatedAt
     *
     * @param \DateTime $activatedAt
     *
     * @return User
     */
    public function setActivatedAt($activatedAt)
    {
        if (!empty($activatedAt)) {
            $this->activatedAt = new \DateTime($activatedAt);
        }

        return $this;
    }

    /**
     * Get activatedAt
     *
     * @return \DateTime
     */
    public function getActivatedAt()
    {
        if ($this->activatedAt) {
            return $this->activatedAt->format('Y-m-d');
        }

        return $this->activatedAt;
    }

    public function __toString()
    {
        return (string)$this->getUsername();
    }
}