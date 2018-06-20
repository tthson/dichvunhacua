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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Types\Type;
use DoctrineExtensions\Query\Mysql\Date;

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
            ->setName('dichvunhacua:send-proposal-to-consumer:send')
            ->setDescription('DichVuNhaCua send all proposal to consumer')
            ->setDefinition(
                array(
                    new InputOption(
                        '--project_id',
                        '-p',
                        InputOption::VALUE_OPTIONAL,
                        'project id'
                    ),
                    new InputOption(
                        '--proposal_id',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'proposal id'
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
        /* @var $doctrine EntityManagerInterface */
        $doctrine = $this->getContainer()->get('doctrine')->getManager();
        $qb = $doctrine->createQueryBuilder();
        $today = (new \DateTime())->format("Y-m-d");
        $qb->select('p')->from('DichVuNhaCuaProjectBundle:Project', 'p');
        $qb->join("DichVuNhaCuaProjectBundle:Proposal", 'pro', 'WITH',"p.id = pro.projectId");
        $projects = $qb->where('DATE(pro.latestSentAt) < DATE(:today)')
            ->setParameter('today', $today,Type::STRING)
            ->orWhere($qb->expr()->isNull('pro.latestSentAt'))
            ->getQuery()->getResult();
        if (!empty($projects)) {
            /**
             * @var $project Project
             */
            foreach ($projects as $project) {
                $proposals = $doctrine->getRepository("DichVuNhaCuaProjectBundle:Proposal")->getProposalByProjectId($project->getId());
                $message = (new \Swift_Message("Chúng tôi vừa tìm thấy ".count($proposals)." đề nghị mới, phù hợp với yêu cầu {$project->getCategory()->getName()} #{$project->getId()} của bạn"))
                    ->setFrom(array("{$this->getContainer()->getParameter('mailer_user')}" => 'Dich Vu Nha Cua Support'))
                    ->setTo($project->getCreatedBy()->getUsername())
                    //->setTo("ttson284@gmail.com")
                    ->setBody(
                        $this->getContainer()->get('templating')->render(
                            '@App/emails/send_proposal.html.twig',
                            array(
                                'project' => $project,
                                'proposals' => $proposals,
                                'user' => $project->getCreatedBy(),
                            )
                        ),
                        'text/html'
                    );
                $this->getContainer()->get('mailer')->send($message);
                /**
                 * @var $proposal Proposal
                 */
                foreach ($proposals as $proposal) {
                    $proposal->setLatestSentAt(new \DateTime());
                    $doctrine->persist($proposal);
                }
            }
        }

        return false;
    }
}
