<?php
namespace Rigo\Types;
    
use WPAS\Types\BasePostType;

class Meetup extends BasePostType{
    
    function initialize(){
        
        add_action('acf/init', array($this, 'add_local_fields'));
    }

    function add_local_fields() {
	
    	acf_add_local_field_group(array(
    		'key' => 'test_group',
    		'title' => 'Test Group',
    		'fields' => array (
    			array (
    				'key' => 'day',
    				'label' => 'Day',
    				'name' => 'day',
    				'type' => 'date_picker',
    			),
    			array (
    				'key' => 'time',
    				'label' => 'Time',
    				'name' => 'time',
    				'type' => 'time_picker',
    			),
    			array(
	                'key' => 'meetup',
	                'label' => 'Meetup',
	                'name' => 'meetup_id',
	                'type' => 'relationship',
                	'post_type' => array('meetup'),
                	'filters' => array('post_type'),
                	'max' => 1
                )
    		),
    		'location' => array (
    			array (
    				array (
    					'param' => 'post_type',
    					'operator' => '==',
    					'value' => 'test',
    				),
    			),
    		),
    	));
    	
    }
}

?>