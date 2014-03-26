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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * The kernel.debug value
     *
     * @var boolean
     */
    private $debug;

    /**
     * Constructor.
     *
     * @param boolean $debug The kernel.debug value
     */
    public function __construct($debug)
    {
        $this->debug = (boolean) $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gordalina_ifmb');

        $rootNode
            ->children()
                ->scalarNode('entity')->end()
                ->scalarNode('sub_entity')->end()
                ->scalarNode('backoffice_key')->end()
                ->scalarNode('anti_phishing_key')->end()
                ->scalarNode('api_endpoint')->defaultValue('http://www.ifthensoftware.com/IfmbWS/WsIfmb.asmx')->end()
                ->booleanNode('sandbox')->defaultValue($this->debug)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
