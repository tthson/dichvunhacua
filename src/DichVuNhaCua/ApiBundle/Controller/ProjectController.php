<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use DichVuNhaCua\ProjectBundle\Entity\Proposal;
use DichVuNhaCua\ProjectBundle\Form\ProposalType;
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
     * @Route("create-proposal", name="api_create_proposal")
     */
    public function createProposalAction(Request $request)
    {
        //$userManager = $this->container->get('fos_user.user_manager');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_BU_OWNER') === FALSE) {
            throw new AccessDeniedException();
        }
        $data = json_decode($request->getContent(), true);
        $proposal = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:Proposal")->findOneBy(array("business"=>$data['business'],"project"=>$data['project']));
        if (empty($proposal)) {
            $proposal = new Proposal();
        }
        $form = $this->createForm(ProposalType::class, $proposal, array('csrf_protection' => false));
        $form->submit($data);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $proposal = $form->getData();
                $em->persist($proposal);
                $em->flush();
                $projectBusiness = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectBusiness")->findOneBy(array("project"=>$proposal->getProject(),"business"=>$proposal->getBusiness()));
                $projectBusiness->setProposal($proposal);
                $em->persist($projectBusiness);
                $em->flush();
                $view = $this->view($proposal, Response::HTTP_CREATED);

                return $this->handleView($view);
            }
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
}