<?php
/*
 Plugin Name: GW BP Profile Page
 Plugin URI: 
 Description: Custom Profile page for BP
 Author: GippslandWeb
 Version: 1.6.1
 Author URI: https://gippslandweb.com.au
 GitHub Plugin URI: Gippsland-Web/gw-bp-profile
 */

class GW_BP_Profile {
    public function __construct() {
        add_filter('bp_get_displayed_user_nav_xprofile',array($this,'gw_filterprofile'));
        add_action( 'bp_setup_nav', array($this,'gw_bp_profile_new_nav_item'), 1000 );

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
        $context['items'] = $items;
        $context['data'] = $data;
        $context['imgs'] = $this->gw_bp_get_galleries();
        $context['id'] = bp_displayed_user_id();//     
        $context['userreview'] = new \BP_Member_Reviews();
        $context['userreview']->bp_screen_scripts();
        $context['reviews'] = get_user_meta(bp_displayed_user_id(),'imported-review',false);

        $context['title'] = $title;
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


}
$gwprofile = new \GW_BP_Profile();











