<?php 
/*
 Plugin Name: VW Event Planner Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Event Planner Pro Theme.
 Author: VW Themes
 Version: 1.0
 Author URI: https://www.vwthemes.com/
*/

define( 'vw_event_planner_pro_POSTTYPE_VERSION', '1.0' );

add_action( 'init', 'vw_event_planner_pro_posttype_create_post_type' );

function vw_event_planner_pro_posttype_create_post_type() {
  register_post_type( 'services',
    array(
        'labels' => array(
            'name' => __( 'Services','vw-event-planner-pro-posttype' ),
            'singular_name' => __( 'Services','vw-event-planner-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );
  register_post_type( 'testimonials',
    array(
  		'labels' => array(
  			'name' => __( 'Testimonials','vw-event-planner-pro-posttype' ),
  			'singular_name' => __( 'Testimonials','vw-event-planner-pro-posttype' )
  		),
  		'capability_type' => 'post',
  		'menu_icon'  => 'dashicons-businessman',
  		'public' => true,
  		'supports' => array(
  			'title',
  			'editor',
  			'thumbnail'
  		)
		)
	);
  register_post_type( 'team',
    array(
      'labels' => array(
        'name' => __( 'Our Team','vw-event-planner-pro-posttype' ),
        'singular_name' => __( 'Our Team','vw-event-planner-pro-posttype' )
      ),
        'capability_type' => 'post',
        'menu_icon'  => 'dashicons-businessman',
        'public' => true,
        'supports' => array( 
          'title',
          'editor',
          'thumbnail'
      )
    )
  );
  register_post_type( 'faq',
    array(
      'labels' => array(
        'name' => __( 'Faq','vw-event-planner-pro-posttype' ),
        'singular_name' => __( 'Faq','vw-event-planner-pro-posttype' )
        ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-media-spreadsheet',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
        )
      )
  );
}


// --------------- Services ------------------
// Serives section
function vw_event_planner_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('vw-event-planner-pro-posttype-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'vw_event_planner_pro_posttype_images_metabox_enqueue');

function vw_event_planner_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    
}


function vw_event_planner_pro_posttype_bn_meta_save_services( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
 
  
}
add_action( 'save_post', 'vw_event_planner_pro_posttype_bn_meta_save_services' );

/* Services shortcode */
function vw_event_planner_pro_posttype_services_func( $atts ) {
  $services = '';
  $services = '<div id="services">
              <div class="row">';
  $query = new WP_Query( array( 'post_type' => 'services') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=services');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $services_image= get_post_meta(get_the_ID(), 'meta-image', true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        if(get_post_meta($post_id,'meta-services-url',true !='')){$custom_url =get_post_meta($post_id,'meta-services-url',true); } else{ $custom_url = get_permalink(); }
        $services .= '<div class="col-lg-4 col-md-4 col-sm-6 services-content-div services-content-shrtcode">
                        <div class="services-content">
                            <div class="services-img">
                             <img src="'.esc_url($thumb_url).'" />
                          </div>
                          <div class="row services-data">
                            <div class="col-lg-9 col-md-9 col-9">
                              <a href="'.esc_url($custom_url).'"><h4 class="services-title">'.esc_html(get_the_title()) .'</h4></a>
                            </div>
                            <div class="col-lg-3 col-md-3 col-3">
                              <a href="'.esc_url($custom_url).'">
                                <span class="services-icon"><i class="fa fa-angle-right"></i></span>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>';
    if($k%2 == 0){
      $services.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $services = '<h2 class="center">'.esc_html__('Post Not Found','vw-event-planner-pro-posttype').'</h2>';
  endif;
  $services .= '</div></div>';
  return $services;
}

add_shortcode( 'list-services', 'vw_event_planner_pro_posttype_services_func' );


/*------------------ Testimonial section -------------------*/

/* Adds a meta box to the Testimonial editing screen */
function vw_event_planner_pro_posttype_bn_testimonial_meta_box() {
	add_meta_box( 'vw-event-planner-pro-posttype-testimonial-meta', __( 'Enter Details', 'vw-event-planner-pro-posttype' ), 'vw_event_planner_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_event_planner_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function vw_event_planner_pro_posttype_bn_testimonial_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'vw_event_planner_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
	$desigstory = get_post_meta( $post->ID, 'vw_event_planner_pro_posttype_testimonial_desigstory', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
				<tr id="meta-1">
					<td class="left">
						<?php _e( 'Designation', 'vw-event-planner-pro-posttype' )?>
					</td>
					<td class="left" >
						<input type="text" name="vw_event_planner_pro_posttype_testimonial_desigstory" id="vw_event_planner_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function vw_event_planner_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['vw_event_planner_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['vw_event_planner_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Save desig.
	if( isset( $_POST[ 'vw_event_planner_pro_posttype_testimonial_desigstory' ] ) ) {
		update_post_meta( $post_id, 'vw_event_planner_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'vw_event_planner_pro_posttype_testimonial_desigstory']) );
	}

}

add_action( 'save_post', 'vw_event_planner_pro_posttype_bn_metadesig_save' );

/*---------------Testimonials shortcode -------------------*/
function vw_event_planner_pro_posttype_testimonial_func( $atts ) {

    $testimonial = ''; 
    $testimonial = '<div id="testimonials">
                    <div class="row">';
    $custom_url = '';
      $new = new WP_Query( array( 'post_type' => 'testimonials' ) );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();

          $post_id = get_the_ID();
          $excerpt = wp_trim_words(get_the_excerpt(),25);
          if(has_post_thumbnail()) {
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
            $thumb_url = $thumb['0'];
          }
          $desigstory= get_post_meta($post_id,'vw_event_planner_pro_posttype_testimonial_desigstory',true);
            $testimonial .= '<div class="col-md-4">
                        <div class="testi-data"> 
                          <div class="testimonial_box w-100 mb-3 box-testi" >
                            <div class="testimonials-icon"><i class="fa fa-quote-right"></i></div>
                            <div class="content_box w-100">
                              <div class="short_text pb-3">
                                <p>'.$excerpt.'</p>
                              </div>
                            </div>
                            <div class="textimonial-img">
                              <img src="'.$thumb_url.'" alt=""/>
                            </div>
                            <div class="testimonial-box">
                              <h4 class="testimonial_name">
                                <a href="'.get_permalink().'">'.get_the_title().'</a>
                              </h4>
                            </div>
                          </div>
                        </div>';  
            $testimonial .= '</div>';

            if($k%3 == 0){
                $testimonial.= '<div class="clearfix"></div>'; 
            } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
      else :
        $project = '<h2 class="center">'.__('Not Found','vw-event-planner-pro-posttype').'</h2>';
      endif;
    $testimonial.= '</div></div>';
  return $testimonial;
  //
}
add_shortcode( 'list-testimonials', 'vw_event_planner_pro_posttype_testimonial_func' );

/*--------------Team -----------------*/
/* Adds a meta box for Designation */
function vw_event_planner_pro_posttype_bn_team_meta() {
    add_meta_box( 'vw_event_planner_pro_posttype_bn_meta', __( 'Enter Details','vw-event-planner-pro-posttype' ), 'vw_event_planner_pro_posttype_ex_bn_meta_callback', 'team', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_event_planner_pro_posttype_bn_team_meta');
}
/* Adds a meta box for custom post */
function vw_event_planner_pro_posttype_ex_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'vw_event_planner_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );

    //Email details
    if(!empty($bn_stored_meta['meta-desig'][0]))
      $bn_meta_desig = $bn_stored_meta['meta-desig'][0];
    else
      $bn_meta_desig = '';

    //Phone details
    if(!empty($bn_stored_meta['meta-call'][0]))
      $bn_meta_call = $bn_stored_meta['meta-call'][0];
    else
      $bn_meta_call = '';


    //facebook details
    if(!empty($bn_stored_meta['meta-facebookurl'][0]))
      $bn_meta_facebookurl = $bn_stored_meta['meta-facebookurl'][0];
    else
      $bn_meta_facebookurl = '';


    //linkdenurl details
    if(!empty($bn_stored_meta['meta-linkdenurl'][0]))
      $bn_meta_linkdenurl = $bn_stored_meta['meta-linkdenurl'][0];
    else
      $bn_meta_linkdenurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-twitterurl'][0]))
      $bn_meta_twitterurl = $bn_stored_meta['meta-twitterurl'][0];
    else
      $bn_meta_twitterurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-googleplusurl'][0]))
      $bn_meta_googleplusurl = $bn_stored_meta['meta-googleplusurl'][0];
    else
      $bn_meta_googleplusurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-designation'][0]))
      $bn_meta_designation = $bn_stored_meta['meta-designation'][0];
    else
      $bn_meta_designation = '';

    ?>
    <div id="agent_custom_stuff">
        <table id="list-table">         
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-1">
                    <td class="left">
                        <?php _e( 'Email', 'vw-event-planner-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-desig" id="meta-desig" value="<?php echo esc_attr($bn_meta_desig); ?>" />
                    </td>
                </tr>
                <tr id="meta-2">
                    <td class="left">
                        <?php _e( 'Phone Number', 'vw-event-planner-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-call" id="meta-call" value="<?php echo esc_attr($bn_meta_call); ?>" />
                    </td>
                </tr>
                <tr id="meta-3">
                  <td class="left">
                    <?php _e( 'Facebook Url', 'vw-event-planner-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_url($bn_meta_facebookurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-4">
                  <td class="left">
                    <?php _e( 'Linkedin URL', 'vw-event-planner-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-linkdenurl" id="meta-linkdenurl" value="<?php echo esc_url($bn_meta_linkdenurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-5">
                  <td class="left">
                    <?php _e( 'Twitter Url', 'vw-event-planner-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_url( $bn_meta_twitterurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-6">
                  <td class="left">
                    <?php _e( 'GooglePlus URL', 'vw-event-planner-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_url($bn_meta_googleplusurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-7">
                  <td class="left">
                    <?php _e( 'Designation', 'vw-event-planner-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($bn_meta_designation); ?>" />
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom Designation meta input */
function vw_event_planner_pro_posttype_ex_bn_metadesig_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', esc_html($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', esc_html($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url($_POST[ 'meta-googleplusurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', esc_html($_POST[ 'meta-designation' ]) );
    }
}
add_action( 'save_post', 'vw_event_planner_pro_posttype_ex_bn_metadesig_save' );

add_action( 'save_post', 'bn_meta_save' );
/* Saves the custom meta input */
function bn_meta_save( $post_id ) {
  if( isset( $_POST[ 'vw_event_planner_pro_posttype_team_featured' ] )) {
      update_post_meta( $post_id, 'vw_event_planner_pro_posttype_team_featured', esc_attr(1));
  }else{
    update_post_meta( $post_id, 'vw_event_planner_pro_posttype_team_featured', esc_attr(0));
  }
}
/*------------ SHORTCODES ----------------*/

/*------------- Team Shorthcode -------------*/
function vw_event_planner_pro_posttype_team_func( $atts ) {
    $team = ''; 
    $team = '<div id="team">
              <div class="row">';
      $new = new WP_Query( array( 'post_type' => 'team') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_event_planner_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'meta-designation',true);
          $call = get_post_meta($post_id,'meta-call',true);
          $facebookurl = get_post_meta($post_id,'meta-facebookurl',true);
          $linkedin = get_post_meta($post_id,'meta-linkdenurl',true);
          $twitter = get_post_meta($post_id,'meta-twitterurl',true);
          $googleplus = get_post_meta($post_id,'meta-googleplusurl',true);

          $team .= '<div class="team_outer col-lg-3 col-sm-6 mb-4">
            <div class="team_wrap">';
              if (has_post_thumbnail()){
                $team .= '<div class="team-image">
                 <img src="'.esc_url($url).'">
                  <div class="team-socialbox">';
                   $team .= '<div class="inner_socio">';                           
                      if($facebookurl != '' || $linkedin != '' || $twitter != '' || $googleplus != ''){?>
                          <?php if($facebookurl != ''){
                            $team .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                           } if($twitter != ''){
                            $team .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';                          
                           } if($linkedin != ''){
                           $team .= ' <a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                          }if($googleplus != ''){
                            $team .= '<a class="" href="'.esc_url($googleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                          }
                        }
                    $team .= '</div>
                  </div>
                </div>
                <div class="team-box">
                  <h4 class="team_name"><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4>';
                  if($designation != ''){
                  $team .= '<p>'.esc_html($designation).'</p>';
                  }
                $team .='</div>';
              }                    
            $team .='</div></div>';
          if($k%4 == 0){
              $team.= '<div class="clearfix"></div>'; 
          } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $team.= '</div></div>';
      else :
        $team = '<div id="team" class="team_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-event-planner-pro-posttype').'</h2></div>';
      endif;
    return $team;
}
add_shortcode( 'list-team', 'vw_event_planner_pro_posttype_team_func' );

/*------------Testimonial Shorthcode -----------*/
function vw_event_planner_pro_posttype_testimonials_func( $atts ) {
    $testimonial = ''; 
    $testimonial = '<div id="testimonials" class="test_shortcode_bg"><div class="inner-test-bg">';
      $new = new WP_Query( array( 'post_type' => 'testimonials') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
          $url = $thumb['0'];
          $excerpt = vw_event_planner_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'vw_event_planner_pro_posttype_testimonial_desigstory',true);

          $testimonial .= '<div class="col-md-12 w-100 float-left mb-4"> 
                <div class="testimonial_box w-100 mb-3">
                  
                  <div class="content_box w-100">
                    <div class="short_text pb-3">'.$excerpt.'</div>
                  </div>
                  <div class="testimonial-box">
                    <h4 class="testimonial_name"><a href="'.get_the_permalink().'">'.get_the_title().'</a> <cite>'.esc_html($designation).'</cite></h4>
                  </div>
                </div>
                <div class="textimonial-img">';
                  if (has_post_thumbnail()){
                    $testimonial.= '<img src="'.esc_url($url).'">';
                  }
                $testimonial.= '</div>
              </div><div class="clearfix"></div>';
          
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $testimonial.= '</div>';
      else :
        $testimonial = '<div id="testimonial" class="testimonial_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','vw-event-planner-pro-posttype').'</h2></div></div></div>';
      endif;
    return $testimonial;
}
add_shortcode( 'vw-event-planner-pro-testimonials', 'vw_event_planner_pro_posttype_testimonials_func' );

/* Faq shortcode */
function vw_event_planner_pro_posttype_faq_func( $atts ) {
  $faq = '';
  $faq = '<div id="accordion" class="row">';
  $query = new WP_Query( array( 'post_type' => 'faq') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=faq');
  while ($new->have_posts()) : $new->the_post();
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $faq .= '
        <div class="faq col-md-6 w-100 mb-3">
          <div class="card">
            <div class="card-header card-header-'.esc_attr($k).'" id="heading'.esc_attr($k).'">
              <a href="#panelBody'.esc_attr($k).'" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion">
                <div class="row">
                  <div class="col-lg-10 col-md-10">
                    <b class="panel-title">'.get_the_title().'</b>
                  </div>
                  <div class="col-lg-2 col-md-2 faq-i">
                    <i class="fas fa-plus"></i>
                  </div>
                </div> 
              </a>
            </div>
            <div id="panelBody'.esc_attr($k).'" class="panel-collapse collapse in">
            <div class="panel-body">
                <p>'.get_the_content().'</p>
              </div>
            </div>
          </div>
          </div>';
    if($k%2 == 0){
      $faq.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $faq = '<h2 class="center">'.esc_html__('Post Not Found','vw-lawyer-pro-posttype-pro').'</h2>';
  endif;
  $faq .= '</div>';
  return $faq;
}
add_shortcode( 'list-faq', 'vw_event_planner_pro_posttype_faq_func' );