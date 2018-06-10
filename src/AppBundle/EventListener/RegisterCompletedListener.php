<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RegisterCompletedListener
 *
 * @package ICare\UserBundle\EventListener
 */
class RegisterCompletedListener implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $groupId = $event->getRequest()->get('group_id', 1);
        /**
         * @var $user User
         */
        $user = $event->getUser();
        $date = new \DateTime();
        $group = $this->em->getRepository('AppBundle:Group')->find(Group::GROUP_BU_OWNER);
        $user->addGroup($group);
        //var_dump($group);
        //var_dump($user);
        //exit;
        foreach ($group->getRoles() as $role) {
            $user->addRole($role);
        }

        $user->setCreatedAt($date->format('Y-m-d'));

        $this->em->persist($user);
        //$this->em->persist($group);
        $this->em->flush();
    }
}
