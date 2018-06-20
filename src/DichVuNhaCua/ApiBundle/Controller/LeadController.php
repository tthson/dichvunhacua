<?php

namespace DichVuNhaCua\ApiBundle\Controller;

use AppBundle\Entity\User;
use DichVuNhaCua\ProjectBundle\Entity\Project;
use DichVuNhaCua\ProjectBundle\Entity\ProjectBusiness;
use DichVuNhaCua\ProjectBundle\Entity\Proposal;
use DichVuNhaCua\ProjectBundle\Form\ProjectType;
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

class LeadController extends FOSRestController
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
    }

    /**
     * @Route("create-lead", name="api_create_lead")
     */
    public function createLeadAction(Request $request)
    {
        //$userManager = $this->container->get('fos_user.user_manager');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_BU_OWNER') === FALSE) {
            throw new AccessDeniedException();
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $data = json_decode($request->getContent(), true);
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project, array('csrf_protection' => false));
        $form->submit($data);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $project = $form->getData();
                $project->setCreatedBy($user);
                $em->persist($project);
                $em->flush();
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
                }
                $view = $this->view($project, Response::HTTP_CREATED);

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