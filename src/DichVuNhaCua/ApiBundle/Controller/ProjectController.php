<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends FOSRestController
{
    /**
     * @Route("leads", name="api_get_matching_leads")
     * @Rest\View
     */
    public function getLeadsAction(Request $request)
    {
        //$userManager = $this->container->get('fos_user.user_manager');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_BU_OWNER') === FALSE) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }
        $businessId = $request->query->getInt('business_id', 0);

        $matchingProject = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectBusiness")->findBy(array('business'=>$businessId));

        $view = $this->view($matchingProject, Response::HTTP_OK);

        return $this->handleView($view);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        dump($matchingProject);exit;

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($matchingProject, 'json');

        return json_decode($jsonContent);
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
}