<?php

namespace DichVuNhaCua\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectBusiness
 *
 * @ORM\Table(name="project_business")
 * @ORM\Entity
 */
class ProjectBusiness
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
     * @ORM\OneToOne(targetEntity="DichVuNhaCua\ProjectBundle\Entity\Proposal")
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id")
     */
    private $proposal;

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
     * Set project
     *
     * @param \DichVuNhaCua\ProjectBundle\Entity\Project $project
     *
     * @return ProjectBusiness
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
     * @return ProjectBusiness
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
     * Set proposal
     *
     * @param \DichVuNhaCua\ProjectBundle\Entity\Proposal $proposal
     *
     * @return ProjectBusiness
     */
    public function setProposal(\DichVuNhaCua\ProjectBundle\Entity\Proposal $proposal = null)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \DichVuNhaCua\ProjectBundle\Entity\Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }
}
