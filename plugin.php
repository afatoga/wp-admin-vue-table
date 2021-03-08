<?php

/**
 * Plugin Name: aa_usertable
 * Description: WC plugin for user table
 * Author: Afatoga
 * Version: 1.0
 * Author URI: https://leoweb.cz
 */



/**
 * Register a custom page
 */
function af_registerPluginAdminPage()
{
   $usertable_page = add_menu_page(
      __('Žádosti o registraci', 'aa'), //titulek
      __('Žádosti o registraci', 'aa'), //titulek v menu
      'edit_users', // capabilities
      'aa_usertable', //wp admin page slug
      'aa_usertable_frontpage', //callback function
      '',
      99
   );

   add_action( 'load-' . $usertable_page, 'aa_loadAdmin' );
}

add_action('admin_menu', 'af_registerPluginAdminPage');

/**
 * admin scripts
 */

 // This function is only called when our plugin's page loads!
 function aa_loadAdmin(){
   add_action( 'admin_enqueue_scripts', 'aa_enqueueAdmin' );
}

function aa_enqueueAdmin(){
   wp_register_script( 'vue2','https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js' );
   wp_enqueue_script( 'vue2' );
   //wp_enqueue_script( 'unfetch', 'https://cdn.jsdelivr.net/npm/unfetch@4.2.0/dist/unfetch.js' );
   wp_enqueue_script( 'vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.4.5/dist/vuetify.js' );

   wp_localize_script( 'vue2', 'wpRestApi', [
         'root'  => esc_url_raw( rest_url().'aa_restserver/v1' ), // /wp-json/aa_restserver/v1
         'nonce' => wp_create_nonce( 'wp_rest' ),
   ] );

   wp_enqueue_style( 'vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.4.5/dist/vuetify.min.css', false );
   wp_enqueue_style( 'material-design-icons', 'https://cdn.jsdelivr.net/npm/@mdi/font@4.9.95/css/materialdesignicons.min.css', false );
}

/**
 * frontend template
 */

function aa_usertable_frontpage()
{   

   if (isset($_POST["submit"])) {
      //action
   }

   load_template(dirname(__FILE__) . '/admin-frontpage.php');
}
