<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * ArrayLoader loads routes from array.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class ArrayLoader extends Loader
{
    /**
     * Loads a configuration and creates a route and adds it to the RouteCollection.
     *
     * @param array       $array An array of routes configuration
     * @param string|null $type  The resource type
     *
     * @throws \InvalidArgumentException When route can't be parsed
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function load($routes, $type = null)
    {
        $routes = static::processConfiguration(static::getConfigurationBuilder(), $routes);
        $collection = new RouteCollection();
        foreach ($routes as $name => $config) {
            if (isset($config['resource'])) {
                $collection->addCollection($this->import($config['resource'], $config['type']), $config['prefix'], $config['defaults'], $config['requirements'], $config['options'], $config['hostname_pattern']);
            } elseif (!isset($config['pattern'])) {
                throw new \InvalidArgumentException(sprintf('You must define a "pattern" for the "%s" route.', $name));
            } else {
                $route = new Route($config['pattern'], $config['defaults'], $config['requirements'], $config['options'], $config['hostname_pattern']);
                $collection->add($name, $route);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_array($resource) && (!$type || 'array' === $type);
    }

    /**
     * Processes an array of routes configuration.
     *
     * @param TreeBuilder $treeBuilder Configuration builder
     * @param array       $config      An array of routes configuration
     *
     * @return array An array of normalized routes configuration
     */
    public static function processConfiguration(TreeBuilder $treeBuilder, array $config)
    {
        $processor = new Processor();
        $routes = $processor->process($treeBuilder->buildTree(), array(array('routes' => $config)));

        return $routes['routes'];
    }

    /**
     * Returns configuration builder.
     *
     * @return TreeBuilder
     */
    public static function getConfigurationBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('routing')
            ->fixXmlConfig('route')
            ->children()
                ->arrayNode('routes')
                ->useAttributeAsKey('id')
                ->prototype('array')
                    ->fixXmlConfig('default')
                    ->fixXmlConfig('requirement')
                    ->fixXmlConfig('option')
                    ->children()
                        ->scalarNode('pattern')->end()
                        ->variableNode('resource')->end()
                        ->scalarNode('type')->defaultNull()->end()
                        ->scalarNode('prefix')->defaultNull()->end()
                        ->scalarNode('class')->defaultNull()->end()
                        ->scalarNode('hostname_pattern')->defaultValue('')->end()
                        ->arrayNode('defaults')
                            ->useAttributeAsKey('key')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('requirements')
                            ->useAttributeAsKey('key')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('key')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $builder;
    }
}
