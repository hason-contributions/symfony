<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Loader;

use Symfony\Component\Routing\Loader\ArrayLoader;
use Symfony\Component\Routing\Route;

class ArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Config\Loader\Loader')) {
            $this->markTestSkipped('The "Config" component is not available');
        }
    }

    /**
     * @covers Symfony\Component\Routing\Loader\ArrayLoader::supports
     */
    public function testSupports()
    {
        $loader = new ArrayLoader();

        $this->assertTrue($loader->supports(array()));
        $this->assertFalse($loader->supports('foo.yml'));
        $this->assertFalse($loader->supports(function() {}));

        $this->assertTrue($loader->supports(array(), 'array'));
        $this->assertFalse($loader->supports(array(), 'foo'));
    }

    public function testLoadWithRoute()
    {
        $loader = new ArrayLoader();
        $routeCollection = $loader->load(array(
            'blog_show' => array(
                'pattern' => '/blog/{slug}',
                'defaults' => array('_controller' => 'MyBundle:Blog:show',),
                'hostname_pattern' => '{locale}.example.com',
                'requirements' => array(
                    '_method' => 'GET',
                    'locale' => '\w+',
                ),
                'options' => array('compiler_class' => 'RouteCompiler',),
            )
        ));
        $routes = $routeCollection->all();

        $this->assertEquals(1, count($routes));
        $this->assertContainsOnly('Symfony\Component\Routing\Route', $routes);
        $route = $routes['blog_show'];
        $this->assertEquals('/blog/{slug}', $route->getPattern());
        $this->assertEquals('MyBundle:Blog:show', $route->getDefault('_controller'));
        $this->assertEquals('GET', $route->getRequirement('_method'));
        $this->assertEquals('\w+', $route->getRequirement('locale'));
        $this->assertEquals('{locale}.example.com', $route->getHostnamePattern());
        $this->assertEquals('RouteCompiler', $route->getOption('compiler_class'));
    }

    public function testLoadWithImport()
    {
        $loader = new ArrayLoader();

        $routeCollection = $loader->load(array(
            'blog_show' => array(
                'resource' => array(
                    'blog_show' => array(
                        'pattern' => '/blog/{slug}',
                        'defaults' => array('_controller' => 'MyBlogBundle:Blog:show',),
                        'options' => array('compiler_class' => 'RouteCompiler',),
                    )
                ),
                'prefix' => '/{foo}',
                'defaults' => array('foo' => '123'),
                'requirements' => array('foo' => '\d+'),
                'options' => array('foo' => 'bar'),
                'hostname_pattern' => '{locale}.example.com',
            )
        ));
        $routes = $routeCollection->all();

        $this->assertEquals(1, count($routes));
        $this->assertContainsOnly('Symfony\Component\Routing\Route', $routes);
        $this->assertEquals('/{foo}/blog/{slug}', $routes['blog_show']->getPattern());
        $this->assertEquals('MyBlogBundle:Blog:show', $routes['blog_show']->getDefault('_controller'));
        $this->assertEquals('123', $routes['blog_show']->getDefault('foo'));
        $this->assertEquals('\d+', $routes['blog_show']->getRequirement('foo'));
        $this->assertEquals('bar', $routes['blog_show']->getOption('foo'));
        $this->assertEquals('{locale}.example.com', $routes['blog_show']->getHostnamePattern());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function testLoadThrowsExceptionInvalidConfiguration()
    {
        $loader = new ArrayLoader();
        $loader->load(array('foo' => 'bar'));
    }
}
