<?php

/**
 * To create new API calls, you have to instanciate the API controller and start adding endpoints
*/
$api = new \WPAS\Controller\WPASAPIController([ 
    'version' => '1', 
    'application_name' => 'sample_api', 
    'namespace' => 'Rigo\\Controller\\',
    'allow-origin' => '*',
    'allow-methods' => 'GET,POST,PUT'
]);


/**
 * Then you can start adding each endpoint one by one
 * Url to check: <domain_name.com>/wp-json/sample_api/v1/<route>
 * For JWT validation add: 'capability' => 'read' (or other capability)
*/
$api->get([ 
    'path' => '/courses', 
    'controller' => 'SampleController:getDraftCourses'
    ]); 


$api->get(['path' => '/test', 'controller' => 'TestController:getTests']);

$api->get([ 'path' => '/product', 'controller' => 'ProductController:getAllProducts']);






$api->get([ 'path' => '/meetups', 'controller' => 'MeetupController:getMeetups' ]); 
$api->get([ 'path' => '/events', 'controller' => 'EventController:getEvents'/*, 'capability' => 'delete_others_posts'*/]);
/*$api->post([ 
    'path' => '/events', 
    'controller' => 'EventController:saveEvent'/*, 
    'capability' => 'activate_plugins', 
    'config' => array(
        'id' => array('required' => true), 
        'name' => 'string' //=> array('validate_callback' => function($param, $request, $key) {return is_numeric( $param );}),
        )
    ]);
*/
$api->put(
    [ 
        'path' => '/events/rsvp/(?P<id>[\d]+)', 
        'controller' => 'EventController:registerRSVP', 
        'capability' => 'activate_plugins',
        'args' => array(
            'answer' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key){
                    return $param === 'yes' || $param === 'no';
                }
            ),
            'username' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key){
                    return is_string($param);
                }
            )
            
        )
    ]
); 
