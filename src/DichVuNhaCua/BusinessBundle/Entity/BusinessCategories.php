<?php

namespace DichVuNhaCua\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessCategories
 *
 * @ORM\Table(name="business_categories")
 * @ORM\Entity
 */
class BusinessCategories
{
    /**
     * @var integer
     *
     * @ORM\Column(name="business_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $businessId;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $categoryId;



    /**
     * Set businessId
     *
     * @param integer $businessId
     *
     * @return BusinessCategories
     */
    public function setBusinessId($businessId)
    {
        $this->businessId = $businessId;

        return $this;
    }

    /**
     * Get businessId
     *
     * @return integer
     */
    public function getBusinessId()
    {
        return $this->businessId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return BusinessCategories
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
}
