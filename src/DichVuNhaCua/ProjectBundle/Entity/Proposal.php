<?php

namespace DichVuNhaCua\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proposal
 *
 * @ORM\Table(name="proposal")
 * @ORM\Entity(repositoryClass="DichVuNhaCua\ProjectBundle\Entity\Repository\ProposalRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Proposal
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
     * @ORM\Column(name="project_id", type="integer", nullable=true)
     */
    private $projectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="business_id", type="integer", nullable=true)
     */
    private $businessId;

    /**
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\ProjectBundle\Entity\Project", inversedBy="proposals")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="DichVuNhaCua\BusinessBundle\Entity\Business", inversedBy="proposals")
     * @ORM\JoinColumn(name="business_id", referencedColumnName="id")
     */
    private $business;

    /**
     * @var integer
     *
     * @ORM\Column(name="estimated_time", type="integer", nullable=true)
     */
    private $estimatedTime;

    /**
     * @var float
     *
     * @ORM\Column(name="estimated_cost", type="float", precision=10, scale=0, nullable=true)
     */
    private $estimatedCost;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set estimatedTime
     *
     * @param integer $estimatedTime
     *
     * @return Proposal
     */
    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = $estimatedTime;

        return $this;
    }

    /**
     * Get estimatedTime
     *
     * @return integer
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    /**
     * Set estimatedCost
     *
     * @param float $estimatedCost
     *
     * @return Proposal
     */
    public function setEstimatedCost($estimatedCost)
    {
        $this->estimatedCost = $estimatedCost;

        return $this;
    }

    /**
     * Get estimatedCost
     *
     * @return float
     */
    public function getEstimatedCost()
    {
        return $this->estimatedCost;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Proposal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Proposal
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
     * @return Proposal
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
     * Set project
     *
     * @param \DichVuNhaCua\ProjectBundle\Entity\Project $project
     *
     * @return Proposal
     */
    public function setProject(\DichVuNhaCua\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \DichVuNhaCua\ProjectBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set business
     *
     * @param \DichVuNhaCua\BusinessBundle\Entity\Business $business
     *
     * @return Proposal
     */
    public function setBusiness(\DichVuNhaCua\BusinessBundle\Entity\Business $business = null)
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
