<?php

/*
  Plugin Name: eGrapes WP EMails Events
  Plugin URI:  http://www.egrapes.in/projects/egrapes-wp-emails-events
  Description: A Plugin or framework for developers to add events to send customised email messages. This also adds a user interface in wordpress's settings/options menu in wp admin to customise each event's email message. Subject and message text can be edited from user interface. Events can be added by filter ewee_events.
  Author: Neeraj Mourya
  Author URI: http://neerajmourya.tumblr.com
  Version: 1.0.0
  License: GPLv3
 */

if (!class_exists('EGrapesWPEmailsEvents')) {
    /**
     * EGrapesWPEmailsEvents class
     * Defines the functionality to create custom email events
     * and send mails on specific event.
     * 
     * @since 1.0.0
     */
    class EGrapesWPEmailsEvents {
        /**
         *
         * @var EGrapesWPEmailsEvents
         * @since 1.0.0
         */
        private static $eGrapesWPEmailsEvents;

        /**
         * Construct the EGrapesWPEmailsEvents object.
         */
        private function __construct() {
            add_filter('ewee_events', array($this, 'emptyArray'), 5);
            add_filter('ewee_events_groups', array($this, 'emptyArray'), 5);
            require_once dirname(__FILE__) . '/options.php';
        }
        
        /**
         * Get the active instance of EGrapesWPEmailsEvents
         * 
         * @return EGrapesWPEmailsEvents
         * @since 1.0.0
         */
        static function get_instance(){
            if(isset(self::$eGrapesWPEmailsEvents) && is_object(self::$eGrapesWPEmailsEvents)){
                
            }  else {
                self::$eGrapesWPEmailsEvents = new EGrapesWPEmailsEvents();
            }
            return self::$eGrapesWPEmailsEvents;
        }

        /**
         * Enqueues the admin scripts
         * @since 1.0.0
         */
        public function enqueue_admin_scripts() {
            $jquery_ui = array(
                "jquery",
                "jquery-ui-core", //UI Core - do not remove this one
                "jquery-ui-widget",
                "jquery-ui-mouse",
                "jquery-ui-accordion",
                "jquery-ui-autocomplete",
                "jquery-ui-slider",
                "jquery-ui-tabs",
                "jquery-ui-sortable",
                "jquery-ui-draggable",
                "jquery-ui-droppable",
                "jquery-ui-selectable",
                "jquery-ui-position",
                "jquery-ui-datepicker",
                "jquery-ui-resizable",
                "jquery-ui-dialog",
                "jquery-ui-button",
                "jquery-ui-selectmenu",
                "jquery-ui-spinner"
            );
            foreach ($jquery_ui as $script) {
                wp_enqueue_script($script);
            }

            wp_enqueue_style('jquery-ui-main', plugins_url('css/base/jquery-ui.css', __FILE__));
            wp_enqueue_style('jquery-ui-theme', plugins_url('css/base/theme.css', __FILE__), array('jquery-ui-main'));
            wp_enqueue_style('egeb-styles', plugins_url('css/style.css', __FILE__), array('jquery-ui-main', 'jquery-ui-theme'));

            wp_enqueue_script('egeb-scripts', plugins_url('js/scripts.js', __FILE__), $jquery_ui);
        }

        /**
         * Returns empty array
         * @param array $values
         * @return array always return empty array
         * @since 1.0.0
         */
        public function emptyArray(){
            return array();
        }
        

        /**
         * Get the specific message corresponding to event_key,
         * replaces all the tokens to values,
         * returns the final message
         * 
         * @param string $event_key Event Key
         * @param array $tokens Contains the tokens values
         * @return array|boolean
         * | Returns an array containing message on success 
         * else returns false on fail
         * @since 1.0.0
         */
        public function get_message($event_key, $tokens) {
            $messages = get_option('ewee_messages');
            $email_events = apply_filters('ewee_events', array());
            $send = '';
            $subject = '';
            $message = '';

            if (isset($email_events[$event_key]['send']) && !empty($email_events[$event_key]['send'])) {
                $send = $email_events[$event_key]['send'];
            }
            if (isset($email_events[$event_key]['subject']) && !empty($email_events[$event_key]['subject'])) {
                $subject = $email_events[$event_key]['subject'];
            }
            if (isset($email_events[$event_key]['message']) && !empty($email_events[$event_key]['message'])) {
                $message = $email_events[$event_key]['message'];
            }

            if (isset($messages) && $messages != false) {
                if (isset($messages[$event_key]) && !empty($messages[$event_key])) {
                    if (isset($messages[$event_key]['send'])) {
                        $send = $messages[$event_key]['send'];
                    }
                    if (isset($messages[$event_key]['subject'])) {
                        $subject = $messages[$event_key]['subject'];
                    }
                    if (isset($messages[$event_key]['message'])) {
                        $message = $messages[$event_key]['message'];
                    }
                }
            }

            if ($send == 'true' || $send == true) {
                foreach ($tokens as $token => $value) {
                    $token_key = '%' . $token . '%';
                    $subject = str_replace($token_key, $value, $subject);
                    $message = str_replace($token_key, $value, $message);
                }

                $message = array(
                    'subject' => $subject,
                    'message' => $message,
                );

                return $message;
            } else {
                return false;
            }
        }

        /**
         * Sends mail on specific event occurance.
         * 
         * @param string $event_key Event key
         * @param array $tokens contains tokens values
         * @param string $to Reciever email address
         * @param string $headers Email headers
         * @param array $attachments Attachments
         * 
         * @since 1.0.0
         */
        public function send_mail($event_key, $tokens = array(), $to, $headers = '', $attachments = array()) {
            $message = $this->get_message($event_key, $tokens);
            if (isset($message) && !empty($message) && $message != false) {
            wp_mail($to, $message['subject'], $message['message'], $headers, $attachments);
            }
        }
    }
    //Instatiating the plugin
    $eGrapesWPEmailsEvents = EGrapesWPEmailsEvents::get_instance();
}