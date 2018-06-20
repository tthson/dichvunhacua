<?php

namespace DichVuNhaCua\ProjectBundle\Controller;

use AppBundle\Entity\User;
use DichVuNhaCua\ProjectBundle\Entity\Project;
use DichVuNhaCua\ProjectBundle\Entity\ProjectBusiness;
use DichVuNhaCua\ProjectBundle\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProposalController extends Controller
{
    /**
     * @param Request $request
     * @Route("proposal", name="home_service_proposal_index")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $dql   = "SELECT p FROM DichVuNhaCuaProjectBundle:Proposal p ";
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $dql .= "WHERE p.project.userId= {$user->getId()}";
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
     * Creates a new proposal entity.
     *
     * @Route("proposal/new", name="admin_create_new_proposal")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /*if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }*/
        $proposal = new Proposal();
        $form = $this->createForm('DichVuNhaCua\ProjectBundle\Form\ProposalType', $proposal);
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            //var_dump($data);exit;
            $dataToSubmit = array(
                "projectId" => $data['proposal-project-id'],
                "businessId" => $data['proposal-business-id'],
                "estimatedTime" => $data['proposal-estimated-time'],
                "estimatedCost" => $data['proposal-estimated-cost'],
                "description" => $data['proposal-description'],
            );
            //dump($dataToSubmit);exit;
            $form->submit($dataToSubmit);
            if ($form->isSubmitted()) {
                $project = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Project")->find($dataToSubmit['projectId']);
                $business = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Business")->find($dataToSubmit['businessId']);
                $proposal->setProject($project);
                $proposal->setBusiness($business);
                $proposal->setEstimatedTime($dataToSubmit['estimatedTime']);
                $proposal->setEstimatedCost($dataToSubmit['estimatedCost']);
                $proposal->setDescription($dataToSubmit['description']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($proposal);
                $projectBusiness = $em->getRepository("DichVuNhaCuaProjectBundle:ProjectBusiness")->findOneBy(array("project"=>$dataToSubmit['projectId'],"business"=>$dataToSubmit['businessId']));
                if (empty($projectBusiness)) {
                    $projectBusiness = new ProjectBusiness();
                    $projectBusiness->setProject($project);
                    $projectBusiness->setBusiness($business);
                }
                $projectBusiness->setProposal($proposal);
                $em->persist($projectBusiness);
                $em->flush();
            }
            $dataToSubmit['id'] = $proposal->getId();
            $jsonContent = json_encode($dataToSubmit);
            //$encoders = array(new XmlEncoder(), new JsonEncoder());
            //$normalizers = array(new ObjectNormalizer());

            //$serializer = new Serializer($normalizers, $encoders);
            //$jsonContent = $serializer->serialize($proposal, 'json');
            $response = new Response($jsonContent);

            return $response;
        }
    }

    /**
     * Displays a form to edit an existing project entity.
     *
     * @Route("proposal/{id}/edit", name="admin_proposal_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Proposal $proposal)
    {
        $user = $this->getUser();
        /*if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER') && $user->getId() != $project->getUserId()) {
            throw new AccessDeniedException('Access denied');
        }*/
        $form = $this->createForm('DichVuNhaCua\ProjectBundle\Form\ProposalType', $proposal);
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            //dump($data);exit;
            $dataToSubmit = array(
                "projectId" => $data['proposal-project-id'],
                "businessId" => $data['proposal-business-id'],
                "estimatedTime" => $data['proposal-estimated-time'],
                "estimatedCost" => $data['proposal-estimated-cost'],
                "description" => $data['proposal-description'],
            );
            $form->submit($dataToSubmit);
            if ($form->isSubmitted()) {
                $proposal->setProject($this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Project")->find($dataToSubmit['projectId']));
                $proposal->setBusiness($this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Business")->find($dataToSubmit['businessId']));
                $proposal->setEstimatedTime($dataToSubmit['estimatedTime']);
                $proposal->setEstimatedCost($dataToSubmit['estimatedCost']);
                $proposal->setDescription($dataToSubmit['description']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($proposal);
                $em->flush();
            }
        }
        $dataToSubmit = $dataToSubmit + array(
            "id" => $proposal->getId(),
        );
        $jsonContent = json_encode($dataToSubmit);
        $response = new Response($jsonContent);

        return $response;
    }

}
