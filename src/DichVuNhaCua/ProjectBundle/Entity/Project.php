<?php

namespace DichVuNhaCua\ProjectBundle\Entity;

use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use DichVuNhaCua\BusinessBundle\Entity\Business;
use DichVuNhaCua\BusinessBundle\Entity\Categories;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="DichVuNhaCua\ProjectBundle\Entity\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Project
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId = '0';

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", length=255, nullable=true)
     */
    private $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\BusinessBundle\Entity\Categories", inversedBy="projects")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer", length=255, nullable=true)
     */
    private $cityId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location", inversedBy="projects")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=25, nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_free_project_cost", type="integer", nullable=true)
     */
    private $isFreeProjectCost = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="detail", type="text", length=65535, nullable=true)
     */
    private $detail;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\ProjectBundle\Entity\ProjectStatus", inversedBy="projects")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    private $projectStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="time_to_be_completed", type="integer", nullable=false)
     */
    private $timeToBeCompleted = '1';

    /**
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\ProjectBundle\Entity\ProjectPeriod", inversedBy="projects")
     * @ORM\JoinColumn(name="time_to_be_completed", referencedColumnName="id")
     */
    private $projectPeriod;

    /**
     * @var integer
     *
     * @ORM\Column(name="location_type", type="integer", nullable=false)
     */
    private $locationType = '1';

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LocationType", inversedBy="projects")
     * @ORM\JoinColumn(name="location_type", referencedColumnName="id")
     */
    private $projectLocationType;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;



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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Project
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @return Project
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
     * @return Project
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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return Project
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return Categories
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Categories $category
     *
     * @return Project
     */

    public function setCategory(Categories $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Project
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
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return Project
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
     * @return Project
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
     * Set zip
     *
     * @param string $zip
     *
     * @return Project
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Project
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Project
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Project
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
     * @return Project
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
     * Set isFreeProjectCost
     *
     * @param integer $isFreeProjectCost
     *
     * @return Project
     */
    public function setIsFreeProjectCost($isFreeProjectCost)
    {
        $this->isFreeProjectCost = $isFreeProjectCost;

        return $this;
    }

    /**
     * Get isFreeProjectCost
     *
     * @return integer
     */
    public function getIsFreeProjectCost()
    {
        return $this->isFreeProjectCost;
    }

    /**
     * Set detail
     *
     * @param string $detail
     *
     * @return Project
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Project
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
     * Set project status
     *
     * @param ProjectStatus $projectStatus
     *
     * @return Project
     */
    public function setProjectStatus($projectStatus)
    {
        $this->projectStatus = $projectStatus;

        return $this;
    }

    /**
     * Get status
     *
     * @return ProjectStatus
     */
    public function getProjectStatus()
    {
        return $this->projectStatus;
    }

    /**
     * Set timeToBeCompleted
     *
     * @param integer $timeToBeCompleted
     *
     * @return Project
     */
    public function setTimeToBeCompleted($timeToBeCompleted)
    {
        $this->timeToBeCompleted = $timeToBeCompleted;

        return $this;
    }

    /**
     * Get timeToBeCompleted
     *
     * @return integer
     */
    public function getTimeToBeCompleted()
    {
        return $this->timeToBeCompleted;
    }

    /**
     * @return ProjectPeriod
     */
    public function getProjectPeriod()
    {
        return $this->projectPeriod;
    }

    /**
     * @param ProjectPeriod $projectPeriod
     *
     * @return Project
     */

    public function setProjectPeriod(ProjectPeriod $projectPeriod)
    {
        $this->projectPeriod = $projectPeriod;

        return $this;
    }

    /**
     * Set locationType
     *
     * @param integer $locationType
     *
     * @return Project
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * Get project Location Type
     *
     * @return \AppBundle\Entity\LocationType
     */
    public function getProjectLocationType()
    {
        return $this->projectLocationType;
    }

    /**
     * @param \AppBundle\Entity\LocationType $projectLocationType
     *
     * @return Project
     */

    public function setProjectLocationType(\AppBundle\Entity\LocationType $projectLocationType)
    {
        $this->projectLocationType = $projectLocationType;

        return $this;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Project
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Project
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
     * @return Project
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

}
