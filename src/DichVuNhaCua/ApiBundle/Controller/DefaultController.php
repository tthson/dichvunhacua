<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends FOSRestController
{
    /**
     * @Route("configuration", name="api_get_configuration")
     * @Rest\View
     */
    public function getConfigurationAction()
    {
        $categories = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();
        $locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findAll();
        $projectPeriod = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->findAll();
        $projectStatus = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->findAll();
        $locationTypes = $this->getDoctrine()->getRepository("AppBundle:LocationType")->findAll();
        $data = array(
            "categories" => $categories,
            "cities" => $locations,
            "period" => $projectPeriod,
            "status" => $projectStatus,
            "location_types" => $locationTypes,
        );

        $view = $this->view($data, Response::HTTP_OK);

        return $this->handleView($view);
    }
}
