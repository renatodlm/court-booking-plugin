<?php

/**
 * Plugin Name: Court Booking Plugin
 * Description: Plugin para agendamento de quadras.
 * Version: 1.1
 * Author: Gustavo Santos e Renato Marques
 **/

include(plugin_dir_path(__FILE__) . '/includes/CourtLogger.php');
include(plugin_dir_path(__FILE__) . '/includes/CourtManager.php');
include(plugin_dir_path(__FILE__) . '/includes/CourtRegister.php');
include(plugin_dir_path(__FILE__) . '/includes/CourtRegisterAjax.php');
include(plugin_dir_path(__FILE__) . '/includes/panel.php');

add_shortcode('court_booking_form', 'register_court_shortcode');

function register_court_shortcode()
{
   ob_start();
   include(plugin_dir_path(__FILE__) . '/templates/shortcode.php');
   return ob_get_clean();
}

add_action('wp_enqueue_scripts', 'court_enqueue_scripts');
function court_enqueue_scripts()
{
   wp_enqueue_style('court-style', plugin_dir_url(__FILE__) . 'css/style.css', [], '1.2.0');
   wp_enqueue_script('court-script', plugin_dir_url(__FILE__) . 'js/script.js', [], '1.2.0', true);

   $options      = get_option('court_booking_settings', '[]');
   $redirect_url = isset($options['redirect_url']) ? $options['redirect_url'] : '';

   wp_localize_script('court-script', 'courtAjax', [
      'url'         => admin_url('admin-ajax.php'),
      'redirectUrl' => $redirect_url,
   ]);
}

add_action('admin_enqueue_scripts', 'court_admin_enqueue_scripts');
function court_admin_enqueue_scripts()
{
   wp_enqueue_style('court-dataTables', plugin_dir_url(__FILE__) . 'css/dataTables.min.css', [], '2.0.2');
   wp_enqueue_script('court-dataTables', plugin_dir_url(__FILE__) . 'js/dataTables.min.js', [], '2.0.2', true);

   wp_enqueue_style('court-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css', [], '1.2.0');
   wp_enqueue_script('court-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', ['jquery'], '1.2.0', true);

   $options       = get_option('court_booking_settings', '[]');
   $datetimesJson = isset($options['datetimes_json']) ? $options['datetimes_json'] : '[]';

   wp_localize_script('court-admin-script', 'courtAjax', [
      'url'       => admin_url('admin-ajax.php'),
      'datetimes' => $datetimesJson
   ]);
}
