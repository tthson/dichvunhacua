<?php

namespace DichVuNhaCua\ProjectBundle\Controller;

use AppBundle\Entity\User;
use DichVuNhaCua\ProjectBundle\Entity\Project;
use DichVuNhaCua\ProjectBundle\Entity\ProjectBusiness;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProjectController extends Controller
{
    /**
     * @param Request $request
     * @Route("project", name="home_service_project_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $dql   = "SELECT p FROM DichVuNhaCuaProjectBundle:Project p ";
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $dql .= "WHERE p.userId= {$user->getId()}";
        }
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();

        return $this->render('@DichVuNhaCuaProject/Project/index.html.twig', array(
            'projects' => $projects,
            "industries" => $industries,
        ));
    }

    /**
     * Creates a new project entity.
     *
     * @Route("project/new", name="create_new_project")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $project = new Project();
        $form = $this->createForm('DichVuNhaCua\ProjectBundle\Form\ProjectType', $project);
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $dataToSubmit = array(
                "name" => isset($data['projectName'])?$data['projectName']:'',
                "categoryId" => $data['projectIndustry'],
                "status" => $data['projectStatus'],
                "timeToBeCompleted" => $data['projectCompleteTime'],
                "locationType" => $data['projectLocationKind'],
                "detail" => $data['projectDetail'],
                "address" => $data['projectAddressStreet'],
                //"cityId" => ($data['projectAddressCity'])?$data['projectAddressCity']:305,
                "firstName" => $data['projectContactPersonFirstName'],
                "lastName" => $data['projectContactPersonLastName'],
                "email" => $data['projectContactPersonEmail'],
                "phone" => $data['projectContactPersonPhone'],
                "isFreeProjectCost" => $data['isFreeProjectCost'],
                'projectPeriod' => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->find($data['projectCompleteTime']),
                'projectLocationType' => $this->getDoctrine()->getRepository("AppBundle:LocationType")->find($data['projectLocationKind']),
                "projectStatus" => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->find($data['projectStatus']),
                'city' => $this->getDoctrine()->getRepository("AppBundle:Location")->getLocationByName($data['administrativeAreaLevel1']),
                'category' => $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->find($data['projectIndustry']),
                'createdBy' => $user,
                "userId" => $user->getId(),
            );
            //dump($dataToSubmit);exit;
            $form->submit($dataToSubmit);
            if ($form->isSubmitted()) {
                $project->setCreatedBy($user);
                $project->setCategory($dataToSubmit['category']);
                $project->setProjectStatus($dataToSubmit['projectStatus']);
                $project->setProjectPeriod($dataToSubmit['projectPeriod']);
                $project->setProjectLocationType($dataToSubmit['projectLocationType']);
                $project->setCity($dataToSubmit['city']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $user->setPhone($data['projectContactPersonPhone']);
                $em->persist($user);
                $em->flush();
            }
        }
        if (!empty($project->getId())) {
            $condition = array(
                "keyword" => $project->getName(),
                "industryId" =>$project->getCategoryId(),
                "cityId" => $project->getCityId()
            );
            $orders = ['sortBy' => '', 'sortOrder' => ''];

            $queryBusinesses = $this->get('dich_vu_nha_cua_business.handler')->search($condition, $orders,0,0);
            $matchedBusiness = $queryBusinesses->setMaxResults(10)->getResult();
            if (!empty($matchedBusiness)) {
                foreach ($matchedBusiness as $business) {
                    $newProjectBusiness = new ProjectBusiness();
                    $newProjectBusiness->setBusiness($business);
                    $newProjectBusiness->setProject($project);
                    $em->persist($newProjectBusiness);
                }
                $em->flush();
            }
            $message = (new \Swift_Message($project->getName()." request is created successful"))
                ->setFrom(array("{$this->getParameter('mailer_user')}" => 'Dich Vu Nha Cua Support'))
                ->setTo($user->getUsername())
                ->setBody(
                    $this->renderView(
                        '@DichVuNhaCuaBusiness/emails/created_project.html.twig',
                        array(
                            'project' => $project,
                            'businesses' => $matchedBusiness,
                            'user' => $user,
                        )
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
            return $this->redirectToRoute('home-service-provider_matching_by_project', array('projectId' => $project->getId()));
        } else {
            return $this->redirectToRoute('homepage');
        }

    }

    /**
     * Creates a new lead entity by business.
     * @param Request $request
     * @param int     $businessId
     *
     * @Route("project/create-lead-by-business-{businessId}", name="create_lead_by_business")
     * @Method({"GET", "POST"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createLeadByBusinessAction(Request $request, $businessId)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $business = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Business")->find($businessId);
        $project = new Project();
        $form = $this->createForm('DichVuNhaCua\ProjectBundle\Form\ProjectType', $project);
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            //var_dump($data);exit;
            $dataToSubmit = array(
                "name" => isset($data['projectName'])?$data['projectName']:'',
                "categoryId" => $data['projectIndustry'],
                "status" => $data['projectStatus'],
                "timeToBeCompleted" => $data['projectCompleteTime'],
                "locationType" => $data['projectLocationKind'],
                "detail" => $data['projectDetail'],
                "address" => $data['projectAddressStreet'],
                //"cityId" => ($data['projectAddressCity'])?$data['projectAddressCity']:305,
                "firstName" => $data['projectContactPersonFirstName'],
                "lastName" => $data['projectContactPersonLastName'],
                "email" => $data['projectContactPersonEmail'],
                "phone" => $data['projectContactPersonPhone'],
                "isFreeProjectCost" => $data['isFreeProjectCost'],
                'projectPeriod' => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->find($data['projectCompleteTime']),
                'projectLocationType' => $this->getDoctrine()->getRepository("AppBundle:LocationType")->find($data['projectLocationKind']),
                "projectStatus" => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->find($data['projectStatus']),
                'city' => $this->getDoctrine()->getRepository("AppBundle:Location")->getLocationByName($data['administrativeAreaLevel1']),
                'category' => $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->find($data['projectIndustry']),
                'createdBy' => $user,
                "userId" => $user->getId(),
            );
            //dump($dataToSubmit);exit;
            $form->submit($dataToSubmit);
            if ($form->isSubmitted()) {
                $project->setUserId($user->getId());
                $project->setCreatedBy($user);
                $project->setCategory($dataToSubmit['category']);
                $project->setProjectStatus($dataToSubmit['projectStatus']);
                $project->setProjectPeriod($dataToSubmit['projectPeriod']);
                $project->setProjectLocationType($dataToSubmit['projectLocationType']);
                $project->setCity($dataToSubmit['city']);
                $em->persist($project);
                $user->setPhone($data['projectContactPersonPhone']);
                $em->persist($user);
                $em->flush();
            }
        }
        if (!empty($project->getId())) {
            $condition = array(
                "keyword" => $project->getName(),
                "industryId" =>$project->getCategoryId(),
                "cityId" => $project->getCityId()
            );
            $orders = ['sortBy' => '', 'sortOrder' => ''];

            $queryBusinesses = $this->get('dich_vu_nha_cua_business.handler')->search($condition, $orders,0,0);
            $matchedBusiness = $queryBusinesses->setMaxResults(10)->getResult();
            if (!empty($matchedBusiness)) {
                foreach ($matchedBusiness as $business) {
                    $newProjectBusiness = new ProjectBusiness();
                    $newProjectBusiness->setBusiness($business);
                    $newProjectBusiness->setProject($project);
                    $em->persist($newProjectBusiness);
                }
                $em->flush();
            }
            $message = (new \Swift_Message($project->getName()." request is created successful"))
                ->setFrom(array("{$this->getParameter('mailer_user')}" => 'Dich Vu Nha Cua Support'))
                ->setTo($user->getUsername())
                ->setBody(
                    $this->renderView(
                        '@DichVuNhaCuaBusiness/emails/created_project.html.twig',
                        array(
                            'project' => $project,
                            'businesses' => $matchedBusiness,
                            'user' => $user,
                        )
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
            return $this->redirectToRoute('home-service-provider_matching_by_project', array('projectId' => $project->getId()));
        } else {
            return $this->redirectToRoute('homepage');
        }

    }

    /**
     * Displays a form to edit an existing project entity.
     *
     * @Route("project/{id}/edit", name="project_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Project $project)
    {
        $user = $this->getUser();

        return $this->redirectToRoute('home-service-provider_matching_by_project', array('projectId' => $project->getId()));
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER') && $user->getId() != $project->getUserId()) {
            throw new AccessDeniedException('Access denied');
        }
        $form = $this->createForm('DichVuNhaCua\ProjectBundle\Form\ProjectType', $project);
        //$form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            //dump($data);exit;
            $dataToSubmit = array(
                "name" => $data['name'],
                "categoryId" => $data['categoryId'],
                'projectPeriod' => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->find($data['timeToBeCompleted']),
                'projectLocationType' => $this->getDoctrine()->getRepository("AppBundle:LocationType")->find($data['locationType']),
                "projectStatus" => $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->find($data['status']),
                'city' => $this->getDoctrine()->getRepository("AppBundle:Location")->find($data['cityId']),
                "detail" => $data['detail'],
                "address" => $data['address'],
                "firstName" => $data['firstName'],
                "lastName" => $data['lastName'],
                "email" => $data['email'],
                "phone" => $data['phone'],
                "_token" => $data['dichvunhacua_projectbundle_project']['_token'],
            );
            $form->submit($dataToSubmit);
            if ($form->isSubmitted()) {
                if (empty($project->getCreatedBy())) {
                    $project->setCreatedBy($user);
                }
                if (empty($project->getUserId())) {
                    $project->setUserId($user->getId());
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
            }
        }
        $locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findAll();
        $locationTypes = $this->getDoctrine()->getRepository("AppBundle:LocationType")->findAll();
        $projectStatuses = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->findAll();
        $projectPeriods = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->findAll();
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();

        return $this->render('@DichVuNhaCuaProject/Project/detail.html.twig', array(
            'project' => $project,
            'form' => $form->createView(),
            'locations' => $locations,
            'locationTypes' => $locationTypes,
            'projectStatuses' => $projectStatuses,
            'projectPeriods' => $projectPeriods,
            'industries' => $industries,
        ));
    }

}
