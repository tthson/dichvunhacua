<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();
        $locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findAll();
        $locationTypes = $this->getDoctrine()->getRepository("AppBundle:LocationType")->findAll();
        $projectStatuses = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectStatus")->findAll();
        $projectPeriods = $this->getDoctrine()->getRepository("DichVuNhaCuaProjectBundle:ProjectPeriod")->findAll();
        return $this->render('@App/default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'industries' => $industries,
            'locations' => $locations,
            'locationTypes' => $locationTypes,
            'projectStatuses' => $projectStatuses,
            'projectPeriods' => $projectPeriods,
            'user' => $user
        ]);
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('@App/default/about_us.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'user' => $user
        ]);
    }

    /**
     * @Route("/services", name="services")
     */
    public function servicesAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('@App/default/services.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'user' => $user
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('@App/default/contact.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'user' => $user
        ]);
    }

    /**
     * @Route("/choose-appropriate", name="choose_appropriate")
     */
    public function chooseAppropriateAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('@App/default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statisticNumberAction(Request $request)
    {
        $businessStatistic = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Business")->getStatistic();
        $statisticData['viet-nam'] = $businessStatistic['total'];
        foreach ($businessStatistic['data'] as $statistic) {
            $statisticData[$statistic['slug']] =  $statistic['totalBusiness'];
        }

        return $this->render(
            '@App/default/popular_locations.html.twig',
            array('statistic'=> $statisticData)
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function servicesFooterAction(Request $request)
    {
        $industries = $this->getDoctrine()->getRepository("DichVuNhaCuaBusinessBundle:Categories")->findAll();

        return $this->render(
            '@App/default/industry_footer.html.twig',
            array('industries' => $industries)
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function switchLanguageAction(Request $request)
    {

        $isShowSwitchLocale = true;
        /*if ($this->getParameter('locale') == 'en') {
            $isShowSwitchLocale = false;
        }*/
        $currentLocale = $request->getLocale();
        $switchLocale = ($currentLocale == $this->getParameter('locale')) ? 'vi' : $this->getParameter('locale');

        return $this->render(
            '@App/default/switch_language.html.twig',
            array('switchLocale' => $switchLocale, 'isShowSwitchLocale' => $isShowSwitchLocale)
        );
    }
}