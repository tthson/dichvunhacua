<?php

namespace DichVuNhaCua\BusinessBundle\Entity\Repository;

use Doctrine\DBAL\Types\Type;

/**
 * BusinessRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessRepository extends \Doctrine\ORM\EntityRepository
{
    public function getStatistic()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.cityId, c.slug, count(b.id) totalBusiness')
            ->join('AppBundle:Location', 'c', 'WITH', 'b.cityId = c.id')
            ->groupBy("b.cityId")
        ;
        $statistic = $qb->getQuery()->getArrayResult();
        $total = $this->createQueryBuilder('b')->select("COALESCE(COUNT(b.id),0)")->getQuery()->getSingleScalarResult();

        return array('data' => $statistic, 'total' => $total);
    }

    /**
     * @param int $categoryId
     *
     * @return \Doctrine\ORM\Query
     */
    public function getBusinessByCategoryId(int $categoryId)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()->from('DichVuNhaCuaBusinessBundle:Business', 'b')
            ->leftJoin('b.categories', 'categories');

        $queryBuilder->where('categories.id = :categoryId');
        $queryBuilder->setParameter('categoryId', $categoryId, Type::INTEGER);
        $queryBuilder->select("COUNT(b.id) as total");

        return $queryBuilder->select('b.id, b.logo, b.status, b.name, b.about, b.cityId')->getQuery();
    }
}
