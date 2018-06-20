<?php

namespace DichVuNhaCua\BusinessBundle\Handler;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Class BusinessHandler
 *
 * @package DichVuNhaCua\BusinessBundle\Handler
 */
class BusinessHandler
{
    private $entityManager;
    private $translator;
    private $container;

    /**
     * BankIntegrationHandler constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     * @param ContainerInterface     $container
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->container = $container;
    }

    /**
     * @param array $condition
     * @param       $orders
     * @param       $page
     * @param       $pageSize
     * @param bool  $allowPagging
     *
     * @return \Doctrine\ORM\Query|QueryBuilder
     */
    public function search(array $condition, $orders, $page, $pageSize, $allowPagging=true)
    {
        $offset = $pageSize * ($page - 1);
        $keyword = (isset($condition['keyword']) && !empty($condition['keyword']))? $condition['keyword'] : "";
        $userId = (isset($condition['userId']) && !empty($condition['userId']))? $condition['userId'] : "";
        $industryId = (isset($condition['industryId']) && !empty($condition['industryId']))? $condition['industryId'] : "";
        $cityId = (isset($condition['cityId']) && !empty($condition['cityId']))? $condition['cityId'] : "";
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from('DichVuNhaCuaBusinessBundle:Business', 'b');
        if (!empty($industryId)) {
            $queryBuilder->join('DichVuNhaCuaBusinessBundle:BusinessCategories', 'i', 'WITH', "b.id = i.businessId AND i.categoryId = {$industryId}");
        }
        if (!empty($userId)) {
            $queryBuilder->andWhere('b.usersId = :createdBy')
                ->setParameter('createdBy', $userId, Type::INTEGER);
        }
        if (!empty($cityId)) {
            $queryBuilder->andWhere('b.city = :cityId')
                ->setParameter('cityId', $cityId, Type::INTEGER);
        }
        if (!empty($keyword)) {
            $queryBuilder->andWhere('b.name LIKE :keyword OR b.certification LIKE :keyword OR b.phone LIKE :keyword OR b.email LIKE :keyword OR b.about LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%' , Type::STRING);
        }
        $queryBuilder = $this->buildOrderQuery($queryBuilder, $orders);

        if ($allowPagging) {
            return $queryBuilder->getQuery();
        } else {
            return $queryBuilder->setFirstResult($offset)->setMaxResults($pageSize);
        }
    }

    /**
     * @param $userId
     * @param $orders
     * @param $page
     * @param $pageSize
     *
     * @return array
     */
    public function getBusinessByBusinessProvider($userId, $orders, $page, $pageSize)
    {
        $offset = $pageSize * ($page - 1);
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(b.id) as total')
            ->from('DichVuNhaCuaBusinessBundle:Business', 'b');
        if (!empty($userId)) {
            $queryBuilder->andWhere('b.usersId = :createdBy')
                ->setParameter('createdBy', $userId, Type::INTEGER);
        }
        $queryBuilder = $this->buildOrderQuery($queryBuilder, $orders);
        $result = $queryBuilder->getQuery()->getScalarResult();
        $total = $result ? $result[0]['total'] : 0;
        if ($total > 0) {
            $businessList = $queryBuilder->select('b')
                ->setFirstResult($offset)
                ->setMaxResults($pageSize)
                ->getQuery()
                ->getResult();
            if (empty($businessList)) {
                $businessList = [];
            }
        }

        return array(
            'total'=> $total,
            'businesses'=> $businessList
        );
    }

    /**
     * @param array $condition
     * @param       $orders
     * @param       $page
     * @param       $pageSize
     *
     * @return \Doctrine\ORM\Query
     */
    public function match(array $condition, $orders, $page, $pageSize)
    {
        $offset = $pageSize * ($page - 1);
        $keyword = (isset($condition['keyword']) && !empty($condition['keyword']))? $condition['keyword'] : "";
        $userId = (isset($condition['userId']) && !empty($condition['userId']))? $condition['userId'] : "";
        $industryId = (isset($condition['industryId']) && !empty($condition['industryId']))? $condition['industryId'] : "";
        $cityId = (isset($condition['cityId']) && !empty($condition['cityId']))? $condition['cityId'] : "";
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('b.id, b.logo, b.status, b.name, b.about, b.cityId, b.address, b.phone, p.id as proposalId, p.estimatedTime, p.estimatedCost, p.description')
            ->from('DichVuNhaCuaBusinessBundle:Business', 'b');
        $queryBuilder->leftJoin('DichVuNhaCuaProjectBundle:Proposal', 'p', 'WITH',"b.id = p.businessId AND p.projectId = {$condition['projectId']}");
        $queryBuilder->join('DichVuNhaCuaBusinessBundle:BusinessCategories', 'i', 'WITH', "b.id = i.businessId AND i.categoryId = {$industryId}");

        /*if (!empty($keyword)) {
            $queryBuilder->andWhere('b.name LIKE :keyword OR b.certification LIKE :keyword OR b.phone LIKE :keyword OR b.email LIKE :keyword OR b.about LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%' , Type::STRING);
        }
        if (!empty($userId)) {
            $queryBuilder->andWhere('b.usersId = :createdBy')
                ->setParameter('createdBy', $userId, Type::INTEGER);
        }*/
        if (!empty($cityId)) {
            $queryBuilder->andWhere('b.city = :cityId')
                ->setParameter('cityId', $cityId, Type::INTEGER);
        }
        $queryBuilder = $this->buildOrderQuery($queryBuilder, $orders);

        return $queryBuilder->getQuery();
    }

    /**
     * @param int $projectId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getMatchedBusiness($projectId)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('pb')
            ->from('DichVuNhaCuaProjectBundle:ProjectBusiness', 'pb');
        $queryBuilder->andWhere('pb.project = :projectId')
            ->setParameter('projectId', $projectId, Type::INTEGER);

        $queryBuilder = $this->buildOrderQuery($queryBuilder, array());

        return $queryBuilder->getQuery();
    }


    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $orders
     *
     * @return QueryBuilder
     */
    private function buildOrderQuery(QueryBuilder $queryBuilder, array $orders)
    {
        static $orderMapping = ['ascending' => 'ASC', 'descending' => 'DESC'];
        if (isset($orders['sortBy']) && !empty($orders['sortBy'])) {
            $queryBuilder->orderBy('b.'.$orders['sortBy'], $orderMapping[$orders['sortOrder']]);
        }

        return $queryBuilder;
    }
}
