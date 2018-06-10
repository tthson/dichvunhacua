<?php
namespace AppBundle\Entity\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\DateDiff;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package AppBundle\Entity\Repository
 */
class UserRepository extends EntityRepository
{

    /**
     * @param int    $groupId
     * @return \Pagerfanta\Pagerfanta
     */
    public function getUserByGroupId(int $groupId)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()->from('AppBundle:User', 'u')
            ->leftJoin('u.groups', 'groups');
        $queryBuilder->where('groups.id = :groupId');
        $queryBuilder->setParameter('groupId', $groupId, Type::INTEGER);
        $queryBuilder->select("u");

        return $this->getPaginator($queryBuilder);
    }

    /**
     * @param int    $groupId
     * @return array
     */
    public function getListUsersByGroupId(int $groupId)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()->from('AppBundle:User', 'u')
            ->leftJoin('u.groups', 'groups');

        $queryBuilder->where('groups.id = :groupId');
        $queryBuilder->setParameter('groupId', $groupId, Type::INTEGER);
        $queryBuilder->select("u");

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserById(int $userId)
    {
        $result = $this->getEntityManager()
            ->createQueryBuilder()->from('AppBundle:User', 'u')
            ->where("u.id=:userId")
            ->setParameter('userId', $userId, Type::INTEGER)
            ->select('u')
            ->getQuery()
            ->getArrayResult();

        return $result[0] ?? [];
    }

    /**
     * @param array $userIds
     * @param bool  $isHrAdmin
     * @return array
     */
    public function getListUserByIds(array $userIds, $isHrAdmin = false): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $userIdsStr = empty($userIds) ? 0 : implode(',', $userIds);
        $queryBuilder->from('AppBundle:User', 'user')->select('user')
            ->where("user.id IN (".$userIdsStr.")");

        $userIds = $queryBuilder->getQuery()->getResult();

        return $userIds;
    }

    /**
     * @return array
     */
    public function getInactiveUsersIn2Weeks():array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $inactiveUserList = $queryBuilder->from('AppBundle:User', 'u')->select('u')
            ->where('u.enabled = 0')
            ->andWhere('DATEDIFF(CURRENT_TIMESTAMP(), u.createdAt) = 14')
            ->getQuery()
            ->getArrayResult();

        return $inactiveUserList;
    }

    /**
     * @param string $roleName
     * @return array
     */
    public function getUserByRole(string $roleName): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $users = $queryBuilder->from('AppBundle:User', 'user')->select('user')
            ->where(" user.roles LIKE :roleName")
            ->setParameter('roleName', "%{$roleName}%", Type::STRING)
            ->getQuery()
            ->getArrayResult();

        return $users;
    }

    /**
     * @param string $email
     * @return array
     */
    public function getUserByEmail(string $email)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $users = $queryBuilder->from('AppBundle:User', 'user')->select('user')
            ->where("user.email = :email")
            ->setParameter('email', $email, Type::STRING)
            ->getQuery()
            ->getResult();

        return count($users) ? $users[0] : null;
    }

    /**
     * @param array $userIds
     */
    public function inactiveUsers(array $userIds)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $userIdsStr = empty($userIds) ? 0 : implode(',', $userIds);

        $queryBuilder->update('ICareUserBundle:User', 'user')
                     ->set('user.enabled', 0)
                     ->where("user.id IN (".$userIdsStr.")")
                     ->getQuery()
                     ->execute();

        return;
    }

    /**
     * @param string $nsCustomerId
     * @param int    $groupId
     * @return array
     */
    public function getListUsersByGroupIdHydrateMode(string $nsCustomerId, int $groupId)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()->from('AppBundle:User', 'u')
            ->leftJoin('u.groups', 'groups');

        $queryBuilder->where('groups.id = :groupId');
        $queryBuilder->setParameter('groupId', $groupId, Type::INTEGER);
        $queryBuilder->select("u");

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param string $keyword
     * @return array
     */
    public function getUserIdsByKeyword($keyword)
    {
        $ids = [];

        $qb = $this->createQueryBuilder('u')
            ->select('u.id')
            ->where('u.username = :username')
            ->setParameter('username', $keyword, Type::STRING)
            ->orWhere('u.firstName LIKE :keyword')
            ->orWhere('u.lastName LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%', Type::STRING);
        $rows = $qb->getQuery()
            ->getArrayResult();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $ids[] = $row['id'];
            }
        }

        return $ids;
    }
}
