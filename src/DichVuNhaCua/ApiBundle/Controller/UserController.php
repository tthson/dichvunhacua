<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;

class UserController extends FOSRestController
{
    /**
     * @Route("register", name="api_registration")
     */
    public function register(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array('csrf_protection' => false));
        $form->setData($user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        //$form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                $response = new Response($this->serialize('User created.'), Response::HTTP_CREATED);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                $view = $this->view($user, Response::HTTP_CREATED);

                return $this->handleView($view);
                //return $this->setBaseHeaders($response);
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
        }
        $view = $this->view($form, Response::HTTP_BAD_REQUEST);

        return $this->handleView($view);
    }

    /**
     * Data serializing via JMS serializer.
     *
     * @param mixed $data
     *
     * @return string JSON string
     */
    private function serialize($data)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->get('jms_serializer')
            ->serialize($data, 'json', $context);
    }

    /**
     * Set base HTTP headers.
     *
     * @param Response $response
     *
     * @return Response
     */
    private function setBaseHeaders(Response $response)
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("find-user-by-username/{username}", name="api_find_user_by_username")
     * @Rest\View
     */
    public function getUserAction($username)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        return array('user' => $user);
    }
}