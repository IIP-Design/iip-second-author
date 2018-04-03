<?php
/**
 * Plugin Name: Double Author Plugin for IIP Properties
 * Description: Adds extra metabox to admin panel which can be used to add a second author to a post byline
 * Version: 1.0.0
 * Author: Marek Rewers
 * Text Domain: iip-double-author
 * License: GPLv2 or laters
 */

// Adds a second author metabox to the post editing screen
function double_author_custom_meta() {
  add_meta_box( 'double_author_meta', __( 'Second Author', 'iip-double-author' ), 'double_author_meta_callback', 'post' );
}

add_action( 'add_meta_boxes', 'double_author_custom_meta' );


// Callback for the content of the metabox
function double_author_meta_callback( $post ) {
 	wp_nonce_field( basename( __FILE__ ), 'double_author_nonce' );
 	$double_author_value = get_post_meta( $post->ID, "_iip_add_second_author", true );
 	?>

	<div class="double-author-row-content">
    <span class="double-author-row-title"><?php _e( 'Add second author?', 'iip-double-author' )?></span><br/>

		<div class="double-author-options" style="margin: 10px 0 15px 5px;">
      <input
        type="radio"
        id="double-author-yes"
        name="_iip_add_second_author"
        value="yes"
        style="margin-top:-1px; vertical-align:middle;"
        <?php checked( $double_author_value, 'yes' ); ?>
      />
      <label for="double-author-radio-yes"><?php _e( 'Yes', 'iip-double-author' )?></label>

			<input
        type="radio"
        id="double-author-no"
        name="_iip_add_second_author"
        value="no"
        style="margin-top:-1px; margin-left: 10px; vertical-align:middle;"
        <?php checked( $double_author_value, '' ); ?>
        <?php checked( $double_author_value, 'no' ); ?>
      />
			<label for="double-author-radio-no"><?php _e( 'No', 'iip-double-author' )?></label>

    </div> <!-- End double-author-options -->
  </div> <!-- End double-author-row-content-->

  <?php
  $double_author_id = get_post_meta( $post->ID, "_iip_post_second_author", true );
	wp_dropdown_users( array(
		'name' => '_iip_post_second_author',
		'selected' => $double_author_id,
		'include_selected' => true,
		'show' => 'display_name',
	) );
  }

// Saves the custom meta input
function double_author_meta_save( $post_id ) {

 	// Checks save status
 	$is_autosave = wp_is_post_autosave( $post_id );
 	$is_revision = wp_is_post_revision( $post_id );
 	$is_valid_nonce = ( isset( $_POST[ 'double_author_nonce' ] ) && wp_verify_nonce( $_POST[ 'double_author_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

 	// Exits script depending on save status
 	if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
 		return;
 	}

 	// Checks for input and saves if needed
 	if( isset( $_POST[ '_iip_add_second_author' ] ) ) {
 		update_post_meta( $post_id, '_iip_add_second_author', $_POST[ '_iip_add_second_author' ] );
 	}

  if( isset( $_POST[ '_iip_post_second_author' ] ) ) {
 		update_post_meta( $post_id, '_iip_post_second_author', $_POST[ '_iip_post_second_author' ] );
 	}
 }

add_action( 'save_post', 'double_author_meta_save' );
