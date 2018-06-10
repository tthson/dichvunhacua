<?php

namespace DichVuNhaCua\BusinessBundle\Entity;

use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Business
 *
 * @ORM\Table(name="business")
 * @ORM\Entity(repositoryClass="DichVuNhaCua\BusinessBundle\Entity\Repository\BusinessRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Business
{
    /**
     * Business constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="DichVuNhaCua\BusinessBundle\Entity\BusinessImages", mappedBy="business", cascade={"all"}, orphanRemoval=true)
     */
    protected $images;

    /**
     * @var integer
     *
     * @ORM\Column(name="users_id", type="integer", nullable=false)
     */
    private $usersId = '0';

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="businesses")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer", length=255, nullable=true)
     */
    private $cityId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location", inversedBy="business")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="landmark", type="string", length=255, nullable=true)
     */
    private $landmark;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=25, nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=50, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=25, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="toll_free", type="string", length=15, nullable=true)
     */
    private $tollFree;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text", length=65535, nullable=false)
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="working_hours", type="text", length=65535, nullable=true)
     */
    private $workingHours;

    /**
     * @var integer
     *
     * @ORM\Column(name="employees", type="integer", nullable=true)
     */
    private $employees;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity", type="integer", nullable=true)
     */
    private $entity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="turn_over", type="string", length=150, nullable=true)
     */
    private $turnOver;

    /**
     * @var string
     *
     * @ORM\Column(name="certification", type="string", length=100, nullable=true)
     */
    private $certification;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_person", type="string", length=150, nullable=true)
     */
    private $contactPerson;

    /**
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     * @var string
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="business_logo", fileNameProperty="logo")
     * @var File
     */
    private $imageFile;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratting", type="integer", nullable=true)
     */
    private $ratting = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="agree", type="integer", nullable=true)
     */
    private $agree = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="long", type="string", length=50, nullable=true)
     */
    private $long;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=50, nullable=true)
     */
    private $lat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="featured", type="boolean", nullable=true)
     */
    private $featured = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="verified", type="boolean", nullable=true)
     */
    private $verified = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="viewed", type="datetime", nullable=true)
     */
    private $viewed;

    /**
     * Many Business have Many Categories.
     * @ORM\ManyToMany(targetEntity="DichVuNhaCua\BusinessBundle\Entity\Categories")
     * @ORM\JoinTable(name="business_categories",
     *      joinColumns={@ORM\JoinColumn(name="business_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     */
    private $categories;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Business
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usersId
     *
     * @param integer $usersId
     *
     * @return Business
     */
    public function setUsersId($usersId)
    {
        $this->usersId = $usersId;

        return $this;
    }

    /**
     * Get usersId
     *
     * @return integer
     */
    public function getUsersId()
    {
        return $this->usersId;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $user
     *
     * @return Business
     */

    public function setCreatedBy(User $user)
    {
        $this->createdBy = $user;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Business
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return Business
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set City
     *
     * @param Location $location
     *
     * @return Business
     */
    public function setCity($location)
    {
        $this->city = $location;

        return $this;
    }

    /**
     * Get city
     *
     * @return Location
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Business
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set landmark
     *
     * @param string $landmark
     *
     * @return Business
     */
    public function setLandmark($landmark)
    {
        $this->landmark = $landmark;

        return $this;
    }

    /**
     * Get landmark
     *
     * @return string
     */
    public function getLandmark()
    {
        return $this->landmark;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Business
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Business
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Business
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return Business
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Business
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set tollFree
     *
     * @param string $tollFree
     *
     * @return Business
     */
    public function setTollFree($tollFree)
    {
        $this->tollFree = $tollFree;

        return $this;
    }

    /**
     * Get tollFree
     *
     * @return string
     */
    public function getTollFree()
    {
        return $this->tollFree;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return Business
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set workingHours
     *
     * @param string $workingHours
     *
     * @return Business
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return string
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set employees
     *
     * @param integer $employees
     *
     * @return Business
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;

        return $this;
    }

    /**
     * Get employees
     *
     * @return integer
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * Set entity
     *
     * @param integer $entity
     *
     * @return Business
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return integer
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Business
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set turnOver
     *
     * @param string $turnOver
     *
     * @return Business
     */
    public function setTurnOver($turnOver)
    {
        $this->turnOver = $turnOver;

        return $this;
    }

    /**
     * Get turnOver
     *
     * @return string
     */
    public function getTurnOver()
    {
        return $this->turnOver;
    }

    /**
     * Set certification
     *
     * @param string $certification
     *
     * @return Business
     */
    public function setCertification($certification)
    {
        $this->certification = $certification;

        return $this;
    }

    /**
     * Get certification
     *
     * @return string
     */
    public function getCertification()
    {
        return $this->certification;
    }

    /**
     * Set contactPerson
     *
     * @param string $contactPerson
     *
     * @return Business
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * Get contactPerson
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Set ratting
     *
     * @param integer $ratting
     *
     * @return Business
     */
    public function setRatting($ratting)
    {
        $this->ratting = $ratting;

        return $this;
    }

    /**
     * Get ratting
     *
     * @return integer
     */
    public function getRatting()
    {
        return $this->ratting;
    }

    /**
     * Set agree
     *
     * @param integer $agree
     *
     * @return Business
     */
    public function setAgree($agree)
    {
        $this->agree = $agree;

        return $this;
    }

    /**
     * Get agree
     *
     * @return integer
     */
    public function getAgree()
    {
        return $this->agree;
    }

    /**
     * Set long
     *
     * @param string $long
     *
     * @return Business
     */
    public function setLong($long)
    {
        $this->long = $long;

        return $this;
    }

    /**
     * Get long
     *
     * @return string
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * Set lat
     *
     * @param string $lat
     *
     * @return Business
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set featured
     *
     * @param integer $featured
     *
     * @return Business
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return integer
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set verified
     *
     * @param integer $verified
     *
     * @return Business
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return integer
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Business
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Business
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Business
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set viewed
     *
     * @param \DateTime $viewed
     *
     * @return Business
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get viewed
     *
     * @return \DateTime
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param Categories $category
     *
     * @return $this
     */
    public function removeCategory(Categories $category)
    {
        if ($category != null) {
            $this->categories->remove($category->getId());
        }

        return $this;
    }

    /**
     * @param Categories $category
     *
     * @return $this
     */
    public function addCategory(Categories $category)
    {
        if (is_null($category->getId())) {
            $this->categories->add($category);
        } else {
            $this->categories->set($category->getId(), $category);
        }

        return $this;
    }

    /**
     * @param BusinessImages $image
     */
    public function addImage(BusinessImages $image)
    {
        if ($this->images->contains($image)) {
            return;
        }

        $this->images[] = $image;
        $image->setBusiness($this);
    }

    /**
     * @param BusinessImages $image
     */
    public function removeImage(BusinessImages $image)
    {
        $this->images->removeElement($image);
        $image->setBusiness(null);
    }
    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function __toString()
    {
        return (string)$this->getName();
    }
}
