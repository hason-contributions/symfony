<?php

$container->loadFromExtension('framework', array(
    'secret' => 's3cr3t',
    'router' => array(
        'resource' => array(
            '_import' => array(
                'resource' => '%kernel.root_dir%/config/routing.xml',
            ),
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
        ),
    ),
));
