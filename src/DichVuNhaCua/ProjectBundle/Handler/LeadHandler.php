<?php

namespace DichVuNhaCua\ProjectBundle\Handler;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Class BusinessHandler
 *
 * @package DichVuNhaCua\ProjectBundle\Handler
 */
class LeadHandler
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
     * @param bool  $allowPagging
     *
     * @return \Doctrine\ORM\Query|QueryBuilder
     */
    public function search(array $condition, $orders, $allowPagging=true)
    {
        $projectId = (isset($condition['projectId']) && !empty($condition['projectId']))? $condition['projectId'] : "";
        $businessId = (isset($condition['businessId']) && !empty($condition['businessId']))? $condition['businessId'] : "";
        $proposalId = (isset($condition['proposalId']) && !empty($condition['proposalId']))? $condition['proposalId'] : "";
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('l')
            ->from('DichVuNhaCuaProjectBundle:ProjectBusiness', 'l');
        if (!empty($businessId)) {
            $queryBuilder->andWhere('l.business = :businessId')
                ->setParameter('businessId', $businessId, Type::INTEGER);
        }
        if (!empty($projectId)) {
            $queryBuilder->andWhere('l.project = :projectId')
                ->setParameter('projectId', $projectId, Type::INTEGER);
        }
        if (!empty($proposalId)) {
            $queryBuilder->andWhere('l.proposal = :proposalId')
                ->setParameter('proposalId', $proposalId, Type::INTEGER);
        }

        $queryBuilder = $this->buildOrderQuery($queryBuilder, $orders);

        if ($allowPagging) {
            return $queryBuilder->getQuery();
        } else {
            return $queryBuilder;
        }
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
