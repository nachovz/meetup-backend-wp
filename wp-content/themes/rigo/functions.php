<?php

/**
* Autoload for PHP Composer and definition of the ABSPATH
*/

//defining the absolute path for the wordpress instalation.
if ( !defined('ABSPATH') ) define('ABSPATH', dirname(__FILE__) . '/');

//including composer autoload
require ABSPATH."vendor/autoload.php";

//including the custom post types
require('setup_types.php');

//including the api endpoints
require('setup_api.php');

//including any monolitic tempaltes
require('setup_templates.php');

add_theme_support( 'post-thumbnails' );



function add_meta_boxes() {
    //Event - Meetup Relationship - WRITE
    
                    //ID            //LABEL                 //CALLBACK      //SCREEN
    add_meta_box( 'some_metabox', 'meetups Relationship', 'meetup_field', 'event' );
    
    //Meetup - Event Relationship - READ
    add_meta_box( 'list_events', 'List of Events', 'events_list', 'meetup' );
    
    //RSVP - List of RSVP users
    add_meta_box( 'rsvp_metabox', 'RSVP', 'rsvp_field', 'event' );
}
add_action( 'admin_init', 'add_meta_boxes' );

function meetup_field() {
    global $post;//Current EVENT
    $selected_meetups = get_post_meta( $post->ID, '_meetup', true );
    //var_dump($selected_meetups);
    $all_meetups = get_posts( array(
        'post_type' => 'meetup',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'ASC'
    ) );
    ?>
    <input type="hidden" name="meetups_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <table class="form-table">
    <tr valign="top"><th scope="row">
    <label for="meetup">Meetups</label></th>
    <td><select name="meetup">
        <option value="Select One">Select a Meetup</option>
    <?php foreach ( $all_meetups as $meetup ) : ?>
        <option value="<?php echo $meetup->ID; ?>"<?php echo $meetup->ID === intval($selected_meetups) ? ' selected="selected"' : ''; ?> ><?php echo $meetup->post_title; ?></option>
    <?php endforeach; ?>
    </select></td></tr>
    </table>
<?php
}

function save_meetup_field( $post_id ) {
    
    
    // only run this for series
    if ( 'event' != get_post_type( $post_id ) )
        return $post_id;        

    // verify nonce
    if ( empty( $_POST['meetups_nonce'] ) || !wp_verify_nonce( $_POST['meetups_nonce'], basename( __FILE__ ) ) )
        return $post_id;


    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // check permissions
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

    
    // save
    update_post_meta( $post_id, '_meetup', $_POST['meetup'] );

}
add_action( 'save_post', 'save_meetup_field' );


//EXTRA
function events_list(){
    global $post;
    
    $args = array(
           'post_type' => 'event',
           'meta_key' => '_meetup',
           'meta_value' => $post->ID,
           'compare' => '='
    );
    $the_query = new WP_Query($args);
    //var_dump($query);
    if ( $the_query->have_posts() ) {
    	echo '<ul>';
    	while ( $the_query->have_posts() ) {
    		$the_query->the_post();
    		echo '<li>' . get_the_title() . '</li>';
    	}
    	echo '</ul>';
    	/* Restore original Post Data */
    	wp_reset_postdata();
    } else {
    	// no posts found
    	echo 'No Events';
    }
}

function rsvp_field() {
    global $post;//Current EVENT
    $rsvp_list = get_post_meta( $post->ID, '_rsvp' );
    //var_dump($selected_meetups);
    /*$all_meetups = get_posts( array(
        'post_type' => 'meetup',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'ASC'
    ) );*/
    //var_dump($rsvp_list);
    ?>
    <input type="hidden" name="rsvp_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <table class="form-table">
        <tr valign="top">
            <td>
                <ul>
                
                <?php foreach ( $rsvp_list as $rsvp_user ) : ?>
                    <li> <a href="<?php echo $rsvp_user; ?>" ><?php echo unserialize($rsvp_user); ?></a></li>
                <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>
<?php
}

function my_manage_event_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'duration' column. */
		case 'duration' :

			/* Get the post meta. */
			$duration = get_post_meta( $post_id, 'duration', true );

			/* If no duration is found, output a default message. */
			if ( empty( $duration ) )
				echo __( 'Unknown' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf( __( '%s minutes' ), $duration );

			break;

		/* If displaying the 'genre' column. */
		case 'genre' :

			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'genre' );

			/* If terms were found. */
			if ( !empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'genre' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'genre', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'No Genres' );
			}

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
add_action( 'manage_event_posts_custom_column', 'my_manage_event_columns', 10, 2 );

/*add_action('jwt_auth_expire', 'new_expire');
function new_expire(){
    return time() + 10;
}*/