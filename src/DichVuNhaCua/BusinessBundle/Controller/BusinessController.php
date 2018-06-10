<?php

namespace DichVuNhaCua\BusinessBundle\Controller;

use AppBundle\Entity\User;
use DichVuNhaCua\BusinessBundle\Entity\Business;
use DichVuNhaCua\BusinessBundle\Entity\BusinessImages;
use DichVuNhaCua\ProjectBundle\Entity\Project;
use DichVuNhaCua\ProjectBundle\Entity\ProjectBusiness;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Business controller.
 */
class BusinessController extends Controller
{
    /**
     * @param Request $request
     * @Route("home-service-provider", name="home-service-provider_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $pageSize = 10;

        $dql   = "SELECT b FROM DichVuNhaCuaBusinessBundle:Business b ";
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $dql .= "WHERE b.usersId= {$user->getId()}";
        }
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $businesses = $paginator->paginate(
            $query,
            $page,
            $pageSize
        );
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();

        return $this->render('@DichVuNhaCuaBusiness/Business/index.html.twig', array(
            'businesses' => $businesses,
            'industries' => $industries,
        ));
    }

    /**
     * @param Request $request
     * @param int     $projectId
     * @Route("home-service-provider/match-business-by-project/{projectId}", name="home-service-provider_matching_by_project")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function matchingByProjectAction(Request $request, $projectId)
    {
        /* @var User $user*/
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $pageSize = 10;
        $orders = ['sortBy' => '', 'sortOrder' => ''];
        $condition = array(
            "projectId" =>$projectId,
        );
        $queryBusinesses = $this->get('dich_vu_nha_cua_lead.handler')->search(
            $condition,
            $orders
        );
        $businessPaginate  = $this->get('knp_paginator');
        $pagination = $businessPaginate->paginate(
            $queryBusinesses,
            $page,
            $pageSize
        );
        $project = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Project")->find($projectId);
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();

        return $this->render('@DichVuNhaCuaBusiness/Business/match_by_project.html.twig', array(
            'project' => $project,
            'leads' => $pagination,
            'industries' => $industries,
        ));
        /* @var Business $business*/
        /*foreach ($pagination as $businessData) {
            $business = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Business")->find($businessData['id']);
            $message = (new \Swift_Message("Your business is matched with ".$project->getName()." request from ".$project->getFirstName()))
                ->setFrom(array("{$this->getParameter('mailer_user')}" => 'Dich Vu Nha Cua Support'))
                //->setTo($business->getEmail())
                    ->setTo('ttson@yopmail.com')
                ->setBody(
                    $this->renderView(
                        '@DichVuNhaCuaBusiness/emails/matched_business.html.twig',
                        array(
                            'city' => $city,
                            'project' => $project,
                            'business' => $business,
                        )
                    ),
                    'text/html'
                )
            ;

            $this->get('mailer')->send($message);
        }*/
    }

    /**
     * @param Request $request
     * @Route("home-service-provider/create", name="home-service-provider_create")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        if ($user->hasRole('ROLE_BU_OWNER')) {
            throw new AccessDeniedException('Access denied');
        }
        $business = new Business();
        $form = $this->createForm('DichVuNhaCua\BusinessBundle\Form\BusinessType', $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($business);
            $em->flush();

            return $this->redirectToRoute('home-service-provider_edit', array('id' => $business->getId()));
        }

        return $this->render('@DichVuNhaCuaBusiness/Business/create.html.twig', array(
            'business' => $business,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a business entity.
     *
     * @Route("home-service-provider/{id}", name="home-service-provider_show")
     * @Method("GET")
     */
    public function showAction(Business $business)
    {
        $deleteForm = $this->createDeleteForm($business);

        return $this->render('@DichVuNhaCuaBusiness/Business/show.html.twig', array(
            'business' => $business,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing business entity.
     *
     * @Route("home-service-provider/{id}/edit", name="home-service-provider_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Business $business)
    {
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $user->getId() != $business->getUsersId()) {
            throw new AccessDeniedException('Access denied');
        }
        $originalImages = new ArrayCollection();
        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($business->getImages() as $image) {
            $originalImages->add($image);
        }
        $originalImageFiles = array();
        foreach ($business->getImages() as $image) {
            $originalImageFiles[$image->getId()] = $image->getFileName();
        }
        $originalLogo = $business->getLogo();
        $deleteForm = $this->createDeleteForm($business);
        $approveForm = $this->createApproveForm($business);
        $editForm = $this->createForm('DichVuNhaCua\BusinessBundle\Form\BusinessType', $business);
        $editForm->handleRequest($request);
        $requestParams = $request->query->all();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (!empty($requestParams['business-images'])) {
                $images = $requestParams['business-images'];
                foreach ($images as $image) {
                    $imageData = explode("#_#", $image);
                    $image = $imageData[0];
                    $imageId = isset($imageData[1])?$imageData[1]:0;
                    $businessId = isset($imageData[2])?$imageData[2]:0;
                    if (!empty($imageId)) {
                        $businessImages = $this->getDoctrine()
                            ->getRepository("DichVuNhaCuaBusinessBundle:BusinessImages")
                            ->findOneBy(array("id"=>$imageId, "business"=>$businessId));
                    } else {
                        $businessImages = new BusinessImages();
                        $businessImages->setBusiness($business);
                    }
                    $businessImages->setFileName($image);
                    $this->getDoctrine()->getManager()->persist($businessImages);
                }
                $this->getDoctrine()->getManager()->flush();
            } else {
                foreach ($originalImages as $image) {
                    $image->setBusiness(null);
                    $image->setFileName($originalImageFiles[$image->getId()]);
                    $this->getDoctrine()->getManager()->remove($image);
                }
            }
            if (!empty($originalImages)) {
                foreach ($originalImages as $image) {
                    $businessImages = $this->getDoctrine()
                        ->getRepository("DichVuNhaCuaBusinessBundle:BusinessImages")
                        ->findOneBy(array("id"=>$image->getId(), "business"=>$image->getBusiness(), "fileName"=> null));
                    if (!empty($businessImages)) {
                        $image->setBusiness(null);
                        $image->setFileName($originalImageFiles[$image->getId()]);
                        $this->getDoctrine()->getManager()->remove($image);
                    }
                }
            }
            if (!empty($originalLogo) &&  $originalLogo != $business->getLogo()) {

            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home-service-provider_edit', array('id' => $business->getId()));
        }

        return $this->render('@DichVuNhaCuaBusiness/Business/edit.html.twig', array(
            'business' => $business,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'approve_form' => $approveForm->createView(),
        ));
    }

    public function removeFile($file)
    {
        $file_path = $this->getUploadRootDir().'/'.$file;
        if(file_exists($file_path)) unlink($file_path);
    }

    /**
     * Deletes a business entity.
     *
     * @Route("home-service-provider/{id}", name="home-service-provider_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Business $business)
    {
        $form = $this->createDeleteForm($business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($business);
            $em->flush();
        }

        return $this->redirectToRoute('home-service-provider_index');
    }

    /**
     * Approve a business entity.
     *
     * @Route("home-service-provider/{id}/approve", name="home-service-provider_approve")
     * @Method("POST")
     */
    public function approveAction(Request $request, Business $business)
    {
        $form = $this->createApproveForm($business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $business->setStatus(51);
            $em->persist($business);
            $em->flush();
        }

        return $this->redirectToRoute('home-service-provider_edit', array('id' => $business->getId()));
    }

    /**
     * @param Request $request
     * @param string  $categorySlug
     * @param int     $categoryParentId
     * @param int     $categoryId
     * @Route("{categorySlug}/b-{categoryParentId}-{categoryId}", name="home-service-provider_matching_by_category")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getBusinessByCategoryAction(Request $request, $categorySlug, $categoryParentId, $categoryId)
    {
        /* @var User $user*/
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $pageSize = 10;
        $condition = array(
            "industryId" =>$categoryId,
        );
        $orders = ['sortBy' => '', 'sortOrder' => ''];

        $businessesQuery = $this->get('dich_vu_nha_cua_business.handler')->search(
            $condition,
            $orders,
            $page,
            $pageSize
        );
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();
        $businessPaginate  = $this->get('knp_paginator');
        $pagination = $businessPaginate->paginate(
            $businessesQuery,
            $page,
            $pageSize
        );

        return $this->render('@DichVuNhaCuaBusiness/Business/list.html.twig', array(
            'businesses' => $pagination,
            'industries' => $industries,
        ));
    }

    /**
     * @param Request $request
     * @param string  $locationSlug
     * @param int     $locationId
     * @Route("{$locationSlug}/l-{$locationId}", name="home-service-provider_matching_by_location")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getBusinessByLocationAction(Request $request, $locationSlug, $locationId)
    {
        /* @var User $user*/
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->getInt('page', 1);
        $pageSize = 1;
        $condition = array(
            "cityId" =>$locationId,
        );
        $orders = ['sortBy' => '', 'sortOrder' => ''];

        $businessesQuery = $this->get('dich_vu_nha_cua_business.handler')->search(
            $condition,
            $orders,
            $page,
            $pageSize
        );
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();
        $businessPaginate  = $this->get('knp_paginator');
        $pagination = $businessPaginate->paginate(
            $businessesQuery,
            $page,
            $pageSize
        );

        return $this->render('@DichVuNhaCuaBusiness/Business/list.html.twig', array(
            'businesses' => $pagination,
            'industries' => $industries,
        ));
    }

    /**
     * @param Request $request
     * @Route(path = "/admin/business/matching", name = "admin_business_matching")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function matchingBusinessAction(Request $request)
    {
        /* @var User $user*/
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $projectId = $request->query->get('id');
        $page = $request->query->getInt('page', 1);
        $pageSize = 7;
        $project = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Project")->find($projectId);
        $condition = array(
            'projectId' => $projectId,
            "keyword" => $project->getName(),
            "industryId" =>$project->getCategoryId(),
            "cityId" => $project->getCityId()
        );
        $orders = ['sortBy' => '', 'sortOrder' => ''];

        $queryBusinesses = $this->get('dich_vu_nha_cua_business.handler')->match(
            $condition,
            $orders,
            $page,
            $pageSize
        );
        $businessPaginate  = $this->get('knp_paginator');
        $pagination = $businessPaginate->paginate(
            $queryBusinesses,
            $page,
            $pageSize
        );
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();
        $project = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Project")->find($projectId);
        return $this->render('@DichVuNhaCuaBusiness/Business/admin_matching_list.html.twig', array(
            'entity' => 'Business',
            'fields' => [
                'id',
                'name',
                'address',
                'phone',
                'email',
                'address',
            ],
            'project' => $project,
            'businesses' => $pagination,
            'industries' => $industries,
        ));
    }

    /**
     * @param Business $business
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Business $business)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('home-service-provider_delete', array('id' => $business->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Business $business
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createApproveForm(Business $business)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('home-service-provider_approve', array('id' => $business->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }
}
