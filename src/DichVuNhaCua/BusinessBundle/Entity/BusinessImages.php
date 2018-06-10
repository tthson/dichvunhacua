<?php
/**
 * Created by PhpStorm.
 * User: tranthaison
 * Date: 11/21/17
 * Time: 11:03 PM
 */

namespace DichVuNhaCua\BusinessBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Business
 *
 * @ORM\Table(name="business_images")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class BusinessImages
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    private $temp;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     *
     */
    protected $fileName;

    /**
     * @Vich\UploadableField(mapping="business_images", fileNameProperty="fileName")
     * @var File
     */
    private $image;

    /**
     * Many Images have One Business.
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\BusinessBundle\Entity\Business", inversedBy="images")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    protected $business;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="created_at",type="datetime", nullable=true)
     * @var \DateTime
     */
    private $createdAt;


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
     * Set title
     *
     * @param string $title
     * @return BusinessImages
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return BusinessImages
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        if ($fileName) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set business
     *
     * @param \DichVuNhaCua\BusinessBundle\Entity\Business $business
     * @return BusinessImages
     */
    public function setBusiness(Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \DichVuNhaCua\BusinessBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    public function setImage(File $image = null)
    {
        $this->image = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getAbsolutePath()
    {
        return null === $this->fileName
            ? null
            : $this->getUploadRootDir().'/uploads/business_images/'.$this->fileName;
    }

    public function getWebPath()
    {
        return null === $this->fileName
            ? null
            : $this->fileName;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web';
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/business_images';
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BusinessImages
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
     * @return BusinessImages
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
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }
}