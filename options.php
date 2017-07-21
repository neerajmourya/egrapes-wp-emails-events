<?php
/**
 * Define class EGrapesWPEmailsEventsOptions if not exists
 */
if (!class_exists('EGrapesWPEmailsEventsOptions')) {

    /**
     * EGrapesWPEmailsEventsOptions class
     * Implements the options user interface inside settings
     * to customise messages corresponding to the email events.
     * 
     * @since 1.0.0
     */
    class EGrapesWPEmailsEventsOptions {

        /**
         * @var EGrapesWPEmailsEventsOptions 
         */
        private static $eGrapesWPEmailEventsOptions;
        
        /**
         * Construts the EGrapesWPEmailsEventsOptions instance
         */
        private function __construct() {
            add_action('admin_menu', array($this, 'admin_menu'));
        }
        
        /**
         * Returns EGrapesWPEmailsEventsOptions object if exists
         * else instantiate a new object and return.
         * 
         * @return EGrapesWPEmailsEventsOptions
         * 
         * | Returns EGrapesWPEmailsEventsOptions object if exists
         * else instantiate a new object and return.
         * 
         * @since 1.0.0
         */
        public static function get_instance(){
            if(isset(self::$eGrapesWPEmailEventsOptions) && is_object(self::$eGrapesWPEmailEventsOptions)){
                
            }else{
                self::$eGrapesWPEmailEventsOptions = new EGrapesWPEmailsEventsOptions();
            }
            return self::$eGrapesWPEmailEventsOptions;
        }

        /**
         * Adds the options page and menu to the admin menu
         * 
         * @since 1.0.0
         */
        public function admin_menu() {
            if (is_admin()) {
                $page_title = 'eGrapes Emails';
                $menu_title = 'eGrapes Emails';
                $capability = 'edit_themes';
                $menu_slug = 'egeb-options-menu';
                $function = array($this, 'page');
                $icon_uri = plugin_dir_url('') . '/egrapes-license-manager/images/admin-icon.png';
                add_options_page($page_title, $menu_title, $capability, $menu_slug, $function);
            }
        }

        /**
         * Controls and decides whether to process the form
         * or display(render) the page.
         * 
         * @since 1.0.0
         */
        public function page() {
            $errors = array();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = $this->validate();
                if (isset($errors) && is_array($errors) && empty($errors)) {
                    $this->submit();
                }
            }
            $this->render($errors);
        }

        /**
         * Display or render the page
         * @param array $errors Errors list
         * 
         * @since 1.0.0
         */
        public function render($errors = array()) {
            $messages = get_option('ewee_messages');
            $eGrapesWPEmailsEvents = EGrapesWPEmailsEvents::get_instance();
            $eGrapesWPEmailsEvents->enqueue_admin_scripts();
            ?>
            <div class="egeb-admin-container wrap">

                <form name="license_manager_options" class="egeb-form" method="POST">
                    <?php wp_nonce_field('ewee_options_form', 'ewee_options_form_nonce'); ?>            

                    <div style="text-align:center;">
                        <p>
                            <a href="http://www.egrapes.in" title="Profession website design, animation, software development"><img src="<?php echo plugin_dir_url(__FILE__); ?>/images/egrapes-logo-1000x300.png" width="500"></a>                
                        </p>
                        <p>
                            <a href="http://www.egrapes.in" title="Profession website design, animation, software development" style="display: inline-block; padding:5px 20px; border-right: 1px solid #ccc; text-decoration:none;"><img src="<?php echo plugin_dir_url(__FILE__); ?>/images/globe.png" style="display: inline-block; vertical-align: middle;"> <span style="display: inline-block; vertical-align: middle;"> &nbsp; Website : http://www.egrapes.in</span></a>
                            <a href="mailto:info@egrapes.in" title="Profession website design, animation, software development" style="display: inline-block; padding:5px 20px; border-right: 1px solid #ccc; text-decoration:none;"><img src="<?php echo plugin_dir_url(__FILE__); ?>/images/email.png" style="display: inline-block; vertical-align: middle;"> <span style="display: inline-block; vertical-align: middle;"> &nbsp; Email : info@egrapes.in</span></a>
                            <a href="mailto:support@egrapes.in" title="Profession website design, animation, software development" style="display: inline-block; padding:5px 20px; text-decoration:none;"><img src="<?php echo plugin_dir_url(__FILE__); ?>/images/speech-bubble.png" style="display: inline-block; vertical-align: middle;"> <span style="display: inline-block; vertical-align: middle;"> &nbsp; Email : support@egrapes.in</span></a>
                        </p>

                    </div>

                    <h1 style="text-align: center;">eGrapes EMails</h1>
                    <p class="description" style="text-align: center;">Configure Email Messages to send on different events.</p>
                    <?php
                    $email_events_groups = apply_filters('ewee_events_groups', array());
                    $email_events = apply_filters('ewee_events', array());
                    ?>
                    <div class="egeb-form-accordion">
                        <?php foreach ($email_events_groups as $group_key => $group_value) : ?>
                            <h3><?php echo $group_value; ?></h3>
                            <div>
                                <table class="form-table">
                                    <?php foreach ($email_events as $key => $value): ?>
                                        <?php if ($value['group'] == $group_key): ?>
                                            <tr>
                                                <th colspan="2">
                                                    <label for="<?php echo $key; ?>"><?php echo $value['name']; ?></label>
                                                    <?php if (isset($value['description'])): ?>
                                                        <?php
                                                        $description = $value['description'];
                                                        if (isset($value['tokens'])) {
                                                            $description .= '<br>Available Tokens:<br>';
                                                            foreach ($value['tokens'] as $token) {
                                                                $description .= '%' . $token . '% , ';
                                                            }
                                                        }
                                                        ?>                        
                                                        <p class="description"><?php echo $description; ?></p>
                                                    <?php endif; ?>                        
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <label for="<?php echo $key; ?>_send">Send</label>
                                                </th>
                                                <td>
                                                    <?php
                                                    $send = $value['send'];
                                                    if (isset($messages) && $messages != false && isset($messages[$key]['send'])) {
                                                        $send = $messages[$key]['send'];
                                                    }
                                                    ?>
                                                    <input type="checkbox" name="<?php echo $key; ?>_send" <?php echo isset($send) && !empty($send) && ($send == 'true' || $send == true) ? 'checked' : ''; ?> value="true" class="widefat" id="<?php echo $key; ?>_send" placeholder="SEND">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <label for="<?php echo $key; ?>_subject">Subject</label>
                                                </th>
                                                <td>
                                                    <?php
                                                    $subject = $value['subject'];
                                                    if (isset($messages) && $messages != false && isset($messages[$key]['subject'])) {
                                                        $subject = $messages[$key]['subject'];
                                                    }
                                                    ?>
                                                    <input type="text" name="<?php echo $key; ?>_subject" value="<?php echo $subject; ?>" class="widefat" id="<?php echo $key; ?>_subject" placeholder="subject">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <label for="<?php echo $key; ?>_message">Message</label>
                                                </th>
                                                <td>
                                                    <?php
                                                    $message = $value['message'];
                                                    if (isset($messages) && $messages != false && isset($messages[$key]['message'])) {
                                                        $message = $messages[$key]['message'];
                                                    }
                                                    wp_editor($message, $key . '_message', array(
                                                        'textarea_name' => $key . '_message',
                                                        'textarea_rows' => 7,
                                                    ));
                                                    ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>


                    <p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit"></p>
                </form>
            </div>
            <?php
        }

        /**
         * Validates the submitted form.
         * 
         * @return array
         * | Returns the errors if found else empty array
         * 
         * @since 1.0.0
         */
        function validate() {
            $errors = array();

            return $errors;
        }

        /**
         * Handles the form submit
         * Stores the values if form validated.
         * @return void
         * @since 1.0.0
         */
        function submit() {
            // Check if our nonce is set.    
            if (!isset($_POST['ewee_options_form_nonce'])) {
                return;
            }

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['ewee_options_form_nonce'], 'ewee_options_form')) {
                return;
            }

            $email_events = apply_filters('ewee_events', array());
            $messages = array();
            foreach ($email_events as $key => $value) {
                $post_key = trim($key) . '_subject';
                if (isset($_POST[$post_key]) && !empty($_POST[$post_key])) {
                    $messages[$key]['subject'] = $_POST[$post_key];
                }

                $post_key = trim($key) . '_message';
                if (isset($_POST[$post_key]) && !empty($_POST[$post_key])) {
                    $messages[$key]['message'] = $_POST[$post_key];
                }
            }
            update_option('ewee_messages', $messages);
        }
    }
    
    //Instantiates the EGrapesWPEmailsEventsOptions object
    $eGrapesWPEmailsEventsOptions = EGrapesWPEmailsEventsOptions::get_instance();
}