<?php
/**
 * Created by PhpStorm.
 * User: khoa
 * Date: 8/9/16
 * Time: 10:39 AM
 */

namespace AppBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_group")
 */
class Group extends BaseGroup
{
    const GROUP_ADMIN = 1;
    const GROUP_USER = 2;
    const GROUP_SUPER_ADMIN = 4;
    const GROUP_BU_OWNER = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
