<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new AppBundle\AppBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            //new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            //new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new DichVuNhaCua\BusinessBundle\DichVuNhaCuaBusinessBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new DichVuNhaCua\ProjectBundle\DichVuNhaCuaProjectBundle(),
            new DichVuNhaCua\ApiBundle\DichVuNhaCuaApiBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
