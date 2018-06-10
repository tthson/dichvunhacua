<?php
/**
 * Created by PhpStorm.
 * User: khoa
 * Date: 8/9/16
 * Time: 10:39 AM
 */

namespace ICare\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserGroupRepository")
 * @ORM\Table(name="user_user_group")
 */
class UserGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $groupId;

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserGroup
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
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return UserGroup
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
}
