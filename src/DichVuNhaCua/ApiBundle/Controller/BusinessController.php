<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class BusinessController extends FOSRestController
{
    /**
     * @Route("own-business", name="api_get_own_business")
     * @Rest\View
     */
    public function getBusinessesAction(Request $request)
    {
        //$userManager = $this->container->get('fos_user.user_manager');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_BU_OWNER') === FALSE) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $pageSize = 10;

        $condition = array(
            "userId" => $user->getId(),
        );
        $orders = ['sortBy' => '', 'sortOrder' => ''];

        $queryBusinesses = $this->get('dich_vu_nha_cua_business.handler')->search(
            $condition,
            $orders,
            $page,
            $pageSize,
            false
        );
        $total = $queryBusinesses->select('count(b.id)')->getQuery()->getSingleScalarResult();
        $businesses = $queryBusinesses->select('b')->getQuery()->getResult();

        return array('total'=>$total,'business' => $businesses,'page_size'=>$pageSize,'current_page'=>$page);
    }
}