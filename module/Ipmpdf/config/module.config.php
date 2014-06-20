<?php
return array(
    'router' => array(
        'routes' => array(
            'ipmpdf' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ipmpdf[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Ipmpdf\Controller\IndexController',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    
    'controllers' => array(
        'factories' => array(
            'Ipmpdf\Controller\IndexController' => 'Ipmpdf\Controller\IndexControllerFactory'
        ),
    ),
    
    'service_manager' => array(
        'factories'  => array(
            'Ipmpdf\Service\Ipmpdf'   => 'Ipmpdf\Service\IpmpdfServiceFactory',
        ),
    ),
    
    'view_manager' => array(
        'doctype' => 'HTML5',
        'template_map' => array(
            'invoice' => __DIR__ . '/../view/ipmpdf/ipmpdf.phtml',
            'domain-invoice' => __DIR__ . '/../view/ipmpdf/pdf/DomainInvoice.phtml',
            'ext-domain-invoice' => __DIR__ . '/../view/ipmpdf/pdf/ExtDomainInvoice.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);