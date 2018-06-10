<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class SwitchLocaleEventListener.
 */
class SwitchLocaleEventListener
{
    private $request;
    private $session;
    private $listLocaleByHost = array();
    private $locale;


    /**
     * SwitchLocaleEventListener constructor.
     *
     * @param RequestStack     $requestStack
     * @param SessionInterface $session
     * @param string           $locale
     */
    public function __construct(RequestStack $requestStack, SessionInterface $session, string $locale)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelRequest()
    {
        $requestLocale = $this->request->get('_locale');
        if (!empty($requestLocale)) {
            $this->session->set('_locale', $requestLocale);
            $this->request->setLocale($requestLocale);
        } else {
            if ($this->session->has('_locale')) {
                $currentLocale = $this->session->get('_locale');
                $this->request->setLocale($currentLocale);
            }
        }
    }
}
