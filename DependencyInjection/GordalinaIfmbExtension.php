<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class GordalinaIfmbExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['anti_phishing_key'])) {
            $container->setParameter('gordalina_ifmb.config.anti_phishing_key', $config['anti_phishing_key']);
        }

        if (isset($config['backoffice_key'])) {
            $container->setParameter('gordalina_ifmb.config.backoffice_key', $config['backoffice_key']);
        }

        if (isset($config['entity'])) {
            $container->setParameter('gordalina_ifmb.config.entity', $config['entity']);
        }

        if (isset($config['sub_entity'])) {
            $container->setParameter('gordalina_ifmb.config.sub_entity', $config['sub_entity']);
        }

        $container->setParameter('gordalina_ifmb.config.api_endpoint', $config['api_endpoint']);
        $container->setParameter('gordalina_ifmb.config.sandbox', $config['sandbox']);
    }
}
