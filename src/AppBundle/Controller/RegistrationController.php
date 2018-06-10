<?php
namespace AppBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends BaseController
{
    public function __construct()
    {
    }
    /**
     * @Route("/register", name="app_registration_register")
     * @param Request $request
     *
     * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                /*****************************************************
                 * Add new functionality (e.g. log the registration) *
                 *****************************************************/
                $this->container->get('logger')->info(
                    sprintf("New user registration: %s", $user)
                );

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@App/default/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/register/check-email", name="app_registration_check_email")
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkEmailAction(Request $request)
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->get('router')->generate('app_registration_register'));
        }

        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }
        $title = "Dich Vu Nha Cua - Register successfully!";
        return $this->render('@App/default/check_email.html.twig', array(
            'title' => $title,
            'user' => $user,
        ));
    }

    /**
     *
     * @param Request $request
     * @param string  $token
     *
     * Tell the user his account is now confirmed.
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');


        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $title = "CoBa Kitchen - Your account is activated";
            return $this->render('@App/default/confirmed.html.twig', array(
                'title' => $title,
                'user' => $user,
                'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
            ));
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     * @Route("/register/confirmed", name="registration-confirmed")
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $title = "Dich Vu Nha Cua - Your account is activated";
        return $this->render('@App/default/confirmed.html.twig', array(
            'title' => $title,
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
        ));
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->container->get('security.token_storage')->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
}