<?php
/**
 * This file is part of the ICare B2B package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command\Cron;

use DichVuNhaCua\ProjectBundle\Entity\Project;
use DichVuNhaCua\ProjectBundle\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ApplyMaternityLeaveCommand
 *
 * @package ICare\CoreBundle\Command
 */
class SendProposalsToConsumerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('i-care:employee-maternity-leave:apply')
            ->setDescription('ICare set in-active for employee')
            ->setDefinition(
                array(
                    new InputOption(
                        '--before_day_number',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'number of days before the maternity-leave starting'
                    ),
                )
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine')->getManager();
        $qb = $doctrine->createQueryBuilder();
        $today = (new \DateTime())->format("Y-m-d");
        $getMaternityLeaveQuery = $qb->select('ic')
            ->from('ICareEmployeeBundle:ICareMember', 'ic')
            ->where(':today BETWEEN DATE(ic.maternityLeaveStart) AND DATE(ic.maternityLeaveEnd)')
            ->setParameter('today', $today)
            ->andWhere("ic.inactiveReasons NOT LIKE '%maternity%'")
            ->getQuery();
        $iCareMembers = $getMaternityLeaveQuery->getResult();
        if (!empty($iCareMembers)) {
            /**
             * @var $iCareMember ICareMember
             */
            $coreICareMemberManager = $this->getContainer()->get('i_care_core.icare_member_manager');
            foreach ($iCareMembers as $iCareMember) {
                $billingClient = $this->getContainer()->get('i_care_b2b_em_billing.api_client');
                $billingClient->rescheduleLoan(
                    $iCareMember->getId(),
                    $iCareMember->getFullName(),
                    $iCareMember->getOrganizationId(),
                    $iCareMember->getCustomerId(),
                    $iCareMember->getBusinessUnitId(),
                    $iCareMember->getMaternityLeaveStart()->format("Y-m-d"),
                    $iCareMember->getMaternityLeaveEnd()->format("Y-m-d")
                );
                $iCareMember = $coreICareMemberManager->getICareMember(
                    $iCareMember->getId()
                );
                $isChange = false;
                if ($iCareMember->getIsActive() == 1) {
                    $iCareMember->setIsActive(0);
                    $isChange = true;
                }
                $inactiveReasons = $iCareMember->getInactiveReasons();
                if (!in_array("maternity", $inactiveReasons)) {
                    $inactiveReasons[] = "maternity";
                    $iCareMember->setInactiveReasons($inactiveReasons);
                    $isChange = true;
                }
                if ($isChange) {
                    $coreICareMemberManager->updateICareMember($iCareMember);
                }
            }
        }

        $qb = $doctrine->createQueryBuilder();
        $today = (new \DateTime())->format("Y-m-d");
        $getMaternityLeaveQuery = $qb->select('ic')
            ->from('ICareEmployeeBundle:ICareMember', 'ic')
            ->where('DATE(ic.maternityLeaveEnd) < :today')
            ->andWhere("ic.inactiveReasons LIKE '%maternity%'")
            ->setParameter('today', $today)
            ->getQuery();
        $iCareMembers = $getMaternityLeaveQuery->getResult();
        if (!empty($iCareMembers)) {
            /**
             * @var $iCareMember ICareMember
             */
            $coreICareMemberManager = $this->getContainer()->get('i_care_core.icare_member_manager');
            foreach ($iCareMembers as $iCareMember) {
                $iCareMember = $coreICareMemberManager->getICareMember(
                    $iCareMember->getId()
                );
                $inactiveReasons = $iCareMember->getInactiveReasons();
                $newInactiveReasons = [];
                if (!empty($inactiveReasons)) {
                    for ($i = 0; $i < count($inactiveReasons); $i++) {
                        if ('maternity' == $inactiveReasons[$i]) {
                            continue;
                        }
                        $newInactiveReasons[] = $inactiveReasons[$i];
                    }

                    if (count($newInactiveReasons) < count($inactiveReasons)) {
                        $iCareMember->setInactiveReasons($newInactiveReasons);
                        if (empty($newInactiveReasons)) {
                            $iCareMember->setIsActive(1);
                        }
                        $coreICareMemberManager->updateICareMember($iCareMember);
                    }
                }
            }
        }

        return false;
    }
}
