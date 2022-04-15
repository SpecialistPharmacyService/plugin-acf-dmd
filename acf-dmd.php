<?php

/*
Plugin Name: Advanced Custom Fields: dm+d
Plugin URI: https://bitbucket.org/makeandship/plugin-acf-dmd
Description: Select a Medicine from Dictionary of Medicines and Devices
Version: 1.2.12
Author: Make and Ship Limited
Author URI: http://www.makeandship.com
 */

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// check if class already exists
if (!class_exists('acf_plugin_dmd')):

    require_once __DIR__ . '/vendor/autoload.php';

    class acf_plugin_dmd
{

        /*
         *  __construct
         *
         *  This function will setup the class functionality
         *
         *  @type    function
         *  @date    17/02/2016
         *  @since   1.0.0
         *
         *  @param    n/a
         *  @return    n/a
         */

        public function __construct()
    {
            // vars
            $this->settings = array(
                'version' => '1.2.7',
                'url'     => plugin_dir_url(__FILE__),
                'path'    => plugin_dir_path(__FILE__),
            );

            // set text domain
            // https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
            load_plugin_textdomain('acf-dmd', false, plugin_basename(dirname(__FILE__)) . '/lang');

            // include all files
            add_action('acf/include_field_types', array($this, 'include_dependencies')); // v5
            add_action('acf/register_fields', array($this, 'include_dependencies')); // v4

        }

        /*
         *  include_dependencies
         *
         *  This function will include the field type class
         *
         *  @type    function
         *  @date    17/02/2016
         *  @since    1.0.0
         *
         *  @param    $version (int) major ACF version. Defaults to 5
         *  @return    n/a
         */

        public function include_dependencies($version = 4)
    {

            include_once 'fields/acf-vtm-v' . $version . '.php';
            include_once 'fields/acf-vtm-vmp-v' . $version . '.php';
            include_once 'fields/acf-vmp-amp-vmpp-ampp-v' . $version . '.php';
            include_once 'fields/acf-amp-vmpp-ampp-v' . $version . '.php';
            include_once 'fields/acf-vmp-v' . $version . '.php';
            include_once 'fields/acf-vmpp-v' . $version . '.php';
            include_once 'fields/acf-amp-v' . $version . '.php';
            include_once 'fields/acf-ampp-v' . $version . '.php';
        }

    }

// initialize
    new acf_plugin_dmd();

// class_exists check
endif;
