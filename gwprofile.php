<?php
/*
 Plugin Name: GW BP Profile Page
 Plugin URI: 
 Description: Custom Profile page for BP
 Author: GippslandWeb
 Version: 1.6.6
 Author URI: https://gippslandweb.com.au
 GitHub Plugin URI: Gippsland-Web/gw-bp-profile
 */

class GW_BP_Profile {
    public function __construct() {
        add_filter('bp_get_displayed_user_nav_xprofile',array($this,'gw_filterprofile'));
        add_action( 'bp_setup_nav', array($this,'gw_bp_profile_new_nav_item'), 1000 );
        add_action( 'bp_register_member_types', array($this,'bbg_register_member_types') );
        
        add_action("ihc_new_subscription_action",array($this,"gw_update_level"),10,2);
        add_action("ihc_action_after_subscription_activated",array($this,"gw_update_level"),10,2);
        add_action("ihc_action_after_subscription_delete",array($this,"gw_remove_level"),10,2);
        add_action("ihc_action_level_has_expired",array($this,"gw_remove_level"),10,2);
        add_filter('login_redirect',array($this,'gw_login_redirect'),10,3);

         add_action('bp_member_header_actions',array($this,'display_member_id') );
     }

function display_member_id() {
    echo('<div class="member-id">Member Number: ');
    if(bp_get_member_type(bp_displayed_user_id()) == "wwoofer") {
        echo('W'.bp_displayed_user_id() + 10000);
    }
    else if(bp_get_member_type(bp_displayed_user_id()) == "host") {
        echo('H'.bp_displayed_user_id() + 10000);
    }
    echo('</div>');
}
     //Hack to remove default profile view, while letting edit function still work
    function gw_filterprofile($content) {
        $content = array();
        return "";
    }

    function gw_bp_profile_new_nav_item() {
        global $bp;
        bp_core_remove_nav_item("reviews");
        bp_core_new_nav_item(
            array(
                'name'                => 'Profile',
                'slug'                => 'profile',
                'screen_function'     => array($this,'view_manage_tab_gw_profile'),
                'default_subnav_slug' => 'gw-profile',
                'position' => 1,
                'item_css_id' =>'profile'
                )
            );
    }
    
    function view_manage_tab_gw_profile() {
        add_action( 'bp_template_title', array($this,'gw_bp_profile_main_title') );
        add_action( 'bp_template_content', array($this,'gw_bp_profile_main_content') );
        bp_core_load_template( 'members/single/plugins' );
    }

    function gw_bp_profile_main_title() {
        echo(ucfirst(bp_get_member_type(bp_displayed_user_id())).' Profile');
    }

    function gw_bp_get_galleries() {
        $galleries = array();
        $args = array('type' => 'photo', 'user_id' => bp_displayed_user_id());
        $the_media_query = new MPP_Media_Query( $args ); 
        // The Loop
        if ( $the_media_query->have_media() ) 
        {
            echo '<ul>';
            while ( $the_media_query->have_media() ) 
            {
                $the_media_query->the_media();
                $img = new \stdClass();
                $img->title = mpp_get_media_title();
                $img->thumb = mpp_get_media_src('thumbnail');
                $img->src = mpp_get_media_permalink();
                $img->id = mpp_get_media_id();
                array_push($galleries,$img);
            }
            echo '</ul>';
        } 
        else {
            // no posts found
        }
        /* Restore original media data */
        mpp_reset_media_data();
        return $galleries;
    }

    function gw_get_icon_for_field($name) {
        return '';  
    }

    function GetXProfileFields() {       
        //$items = array();
        $data = new \stdClass();
        while ( bp_profile_groups() ) 
        {
            bp_the_profile_group();
            if ( bp_profile_group_has_fields() ) 
            {
                    //do_action( 'bp_before_profile_field_content' );
                    $title = bp_get_the_profile_group_name();
                $title = str_replace(' ', '', $title);
                $data->$title = array();
                    while ( bp_profile_fields() )
                    {
                        $item = new \stdClass();
                        bp_the_profile_field();
                        if ( bp_field_has_data() )
                        {
                                $item->css = bp_get_field_css_class();
                                $item->name = bp_get_the_profile_field_name();
                                $item->val = bp_get_the_profile_field_value();
                        
                            $name = bp_get_the_profile_field_name();
                            $name = str_replace(' ', '', $name);
                            $name = str_replace('/', '', $name);
                        $item->icon = $this->gw_get_icon_for_field($name);
                            //if($item->val != '')
                            $data->$name = $item->val;
                            }
                array_push($data->$title,$item);
                        //array_push($items,$item);
                //do_action( 'bp_profile_field_item' );
                    }
            }
        }
        return $data;
    }

    function gw_bp_profile_main_content() {
        global $bp;
            
        /*if ( ! is_user_logged_in() ) {
            wp_login_form( array( 'echo' => true ) );
            return;
        }*/
        if(!bp_has_profile())
            return;

        $data = $this->GetXProfileFields();
        $reviewsQuery = array(
        'post_type' =>'bp-user-reviews',
        'post_status' =>'publish',
        'posts_per_page' => -1,
        'meta_query' => array(array('key' => 'user_id','value'=> bp_displayed_user_id())));

        $context = Timber::get_context();

        $context['data'] = $data;
        $context['imgs'] = $this->gw_bp_get_galleries();
        $context['id'] = bp_displayed_user_id();//     
        $context['userreview'] = new \BP_Member_Reviews();
        $context['userreview']->bp_screen_scripts();
        $context['reviews'] = get_user_meta(bp_displayed_user_id(),'imported-review',false);

       
        $context['loggedin'] = is_user_logged_in();
        if(is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {
            include('profile-nav-bar.php');    
        }

        if(bp_get_member_type(bp_displayed_user_id()) == "wwoofer") {
            Timber::render('gw-profile-wwoofer.php', $context,false,TimberLoader::CACHE_NONE);
        } else {
                Timber::render('gw-profile-host.php', $context,false,TimberLoader::CACHE_NONE);
        }
    
    }







//Setup our different buddy user levels.

function bbg_register_member_types() {
    bp_register_member_type( 'wwoofer', array(
        'labels' => array(
            'name'          => 'WWOOFERS',
            'singular_name' => 'WWOOFER'
        ),
	'has_directory' => 'wwoofers'
    ));

    bp_register_member_type( 'host', array(
        'labels' => array(
            'name'          => "Host's",
            'singular_name' => 'Host'
        ),
	'has_directory' => 'hosts'
    ));

	    bp_register_member_type( 'expired', array(
        'labels' => array(
            'name'          => "Expired User's",
            'singular_name' => 'Expired User'
        )
    ));

}

/*
UMP Hooks to connect member levels from UMP to BP
*/
function gw_update_level($userid, $levelid) {
    switch($levelid)
    {
        case 6:
        case 4: // wwoofer
        bp_set_member_type($userid, 'wwoofer');

        break;
        
        case 5: //Host
        bp_set_member_type($userid, 'host');
        break;
        
    default:
        bp_set_member_type($userid, 'expired');
    break;
    }
}

function gw_remove_level($userid, $levelid) {
    bp_set_member_type($userid, 'expired');
}

/*Redirect users to profile page on login*/

function gw_login_redirect($redirect_to, $request, $user) {
    //check if user is wwoofer or host or affiliate and send to page as requested
    if(isset($user) && isset($user->id))
        return bp_core_get_user_domain($user->id);
    return $redirect_to;
}




}
$gwprofile = new \GW_BP_Profile();











