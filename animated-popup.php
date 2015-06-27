<?php
/**
* Plugin Name: Animated Popup
* Plugin URI: https://www.aklosismedia.com/quotes/dev/downloads/animated-popup
* Description: An animated or static popup that can be activated several ways. Install with a shortcode or widget. Pro version includes implementation with MailChimp, AWeber, Mailgun and Mad Mimi.
* Version: 1.1.2
* Author: Marty Boggs
* Author URI: https://www.aklosismedia.com/quotes/dev
* Text Domain: Optional. Plugin's text domain for localization. Example: mytextdomain
* Domain Path: Optional. Plugin's relative directory path to .mo files. Example: /locale/
* License: GPLv2
*/


// Front-end scripts
function popup_script () {
    $apData = get_site_option('animated_popup_data');
    if (!$apData) {
        require_once('animated-popup-data-init.php');
        update_site_option('animated_popup_data', $apData);
    }
    $enabled = array();
    if ($apData['wpmail_enabled']) { $enabled[] = 'wpmail'; }
    if ($apData['mailchimp_enabled']) { $enabled[] = 'mailchimp'; }
    if ($apData['aweber_enabled']) { $enabled[] = 'aweber'; }
    if ($apData['mailgun_enabled']) { $enabled[] = 'mailgun'; }
    if ($apData['madmimi_enabled']) { $enabled[] = 'madmimi'; }
    if ($apData['popup_or_embed'] === 'popup') {
        wp_enqueue_style('ap-popup', plugin_dir_url( __FILE__ ) . 'css/animated-popup-popup.css');
    } else {
        wp_enqueue_style('ap-embed', plugin_dir_url( __FILE__ ) . 'css/animated-popup-embed.css');
    }
    wp_enqueue_script( 'timelinemax',   plugin_dir_url( __FILE__ ) . 'js/TimelineMax.min.js', array() );
    wp_enqueue_script( 'tweenlite',     plugin_dir_url( __FILE__ ) . 'js/TweenLite.min.js', array() );
    wp_enqueue_script( 'easepack',      plugin_dir_url( __FILE__ ) . 'js/EasePack.min.js', array() );
    wp_enqueue_script( 'cssplugin',     plugin_dir_url( __FILE__ ) . 'js/CSSPlugin.min.js', array() );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery.cookie.js', array('jquery') );
    wp_enqueue_script( 'ap-elements',   plugin_dir_url( __FILE__ ) . 'js/elements.js', array( 'jquery' ) );
    wp_enqueue_script( 'ap-subscribe',  plugin_dir_url( __FILE__ ) . 'js/subscribe.js', array( 'jquery', 'ap-elements' ) );
    wp_enqueue_script( 'ap-animation',  plugin_dir_url( __FILE__ ) . 'js/animation.js', array( 'jquery', 'ap-subscribe', 'ap-elements' ) );
    wp_localize_script( 'ap-subscribe', 'apLocalize', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'subscribeNonce' => wp_create_nonce('subscribe-nonce'),
        'enabled' => $enabled,
        'animation' => $apData['animation_type'] ? $apData['animation_type'] : 'tornado',
        'popup_or_embed' => $apData['popup_or_embed'],
        'widget_id' => $apData['widget_id']
    ) );
}
add_action('wp_enqueue_scripts', 'popup_script', 12); // priority 12

function admin_scripts() {
    wp_enqueue_style( 'ap-embed',      plugin_dir_url( __FILE__ ) . 'css/animated-popup-embed.css');
    wp_enqueue_style( 'ap-admin',      plugin_dir_url( __FILE__ ) . 'css/animated-popup-admin.css');
    wp_enqueue_script( 'timelinemax',  plugin_dir_url( __FILE__ ) . 'js/TimelineMax.min.js', array() );
    wp_enqueue_script( 'tweenlite',    plugin_dir_url( __FILE__ ) . 'js/TweenLite.min.js', array() );
    wp_enqueue_script( 'easepack',     plugin_dir_url( __FILE__ ) . 'js/EasePack.min.js', array() );
    wp_enqueue_script( 'cssplugin',    plugin_dir_url( __FILE__ ) . 'js/CSSPlugin.min.js', array() );
    wp_enqueue_script( 'ap-elements',  plugin_dir_url( __FILE__ ) . 'js/elements.js', array( 'jquery' ) );
    wp_enqueue_script( 'ap-admin',     plugin_dir_url( __FILE__ ) . 'js/ap-admin.js', array('jquery') );
    wp_enqueue_script( 'ap-animation', plugin_dir_url( __FILE__ ) . 'js/animation.js', array( 'jquery', 'ap-elements' ) );
}
add_action('admin_enqueue_scripts', 'admin_scripts');



// create settings link on plugins page
add_filter( 'plugin_row_meta', 'ap_row_meta', 10, 2 );
function ap_row_meta( $links, $file ) {
	if ( strpos( $file, 'animated-popup.php' ) !== false ) {
        $settings_page = admin_url() . 'admin.php?page=animated-popup-menu';
		$new_links = array(
			'<a href="' . $settings_page . '" target="_blank">Settings</a>'
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

// Add Shortcode
function popup_shortcode( $atts ) {

    // Attributes
    extract( shortcode_atts(
        array(
        ), $atts )
    );

    $apData = get_site_option('animated_popup_data');
    if (!$apData) {
        require_once('animated-popup-data-init.php');
        update_site_option('animated_popup_data', $apData);
    }

    if (is_front_page() && $apData['popup_or_embed'] === 'popup') {
        require_once 'animated-popup-popup.php';
        return $templateString;
    } else if ($apData['popup_or_embed'] === 'embed') {
        require_once 'animated-popup-embed.php';
        return $templateString;
    }
}
add_shortcode( 'animated_popup', 'popup_shortcode' );


class Popup_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'popup_widget', // Base ID
			__( 'Animated Popup', 'text_domain' ), // Name
			// array( 'description' => __( 'Add an Animated Popup', 'text_domain' ), ) // Args
            array()
		);
	}

	//  * Front-end display of widget.
	public function widget( $args, $instance ) {

        $apData = get_site_option('animated_popup_data');
        if (!$apData) {
            require_once('animated-popup-data-init.php');
        }
        $apData['widget_id'] = $args['widget_id'];
        update_site_option('animated_popup_data', $apData);

		echo $args['before_widget'];

		echo $args['before_title'];
        echo $args['after_title'];

        if (is_front_page() && $apData['popup_or_embed'] === 'popup') {
            require_once 'animated-popup-popup.php';
            echo $templateString;
        } else if ($apData['popup_or_embed'] === 'embed') {
            require_once 'animated-popup-embed.php';
            echo $templateString;
        }

		echo $args['after_widget'];
	}

	// * Back-end widget form.
	public function form( $instance ) {
	}

	//  * Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
        // select menu

		return $instance;
	}

} // class popup_Widget

// Register and load the widget
function popup_load_widget() {
    register_widget( 'Popup_Widget' );
}
add_action( 'widgets_init', 'popup_load_widget' );



/* ADMINISTRATION PAGE */

register_activation_hook(__FILE__, function () {
    // doesn't run for mu-plugins
});

register_deactivation_hook( __FILE__, function () {
    // doesn't run for mu-plugins
    delete_site_option('animated_popup_data');
});

add_action( 'admin_menu', 'animated_popup_register_admin', 5);
add_action( 'network_admin_menu', 'animated_popup_register_network_admin' );

function animated_popup_register_admin() {
    add_menu_page('Animated Popup Options', 'Animated Popup', 'manage_options', 'animated-popup-menu', 'animated_popup_admin', '', '99.23434542');
}
function animated_popup_register_network_admin() {
    add_menu_page('Animated Popup Options', 'Animated Popup', 'delete_users', 'animated-popup-menu', 'animated_popup_admin', '');
}

function animated_popup_admin() {

    // one form has all inputs data
    // jquery below controls disabled inputs
    // admin update button submits data via php and redirects to same page with $_POST data

    // front-end html is in template file
    // styles for the html are in a template to load in front and back
    // animation is also in a file to be loaded in front and back
    // jquery/greensock external script (subscribe.js) triggers ajax
    // ajax script to subscribe the person to the list(s)



    $errors = array();

    $apData = get_site_option('animated_popup_data');
    if (!$apData) {
        require_once('animated-popup-data-init.php');
        update_site_option('animated_popup_data', $apData);
    }

    // check if button was pushed
    // for data to persist, it must be added to $apData and saved with updateOption(..., 'advanced_popup_data', ...)
    if (isset($_POST['Submit'])) {
        // clear checkboxes
        $apData['wpmail_enabled'] = false;
        // $apData['mandrill_enabled'] = false;
        $apData['mailchimp_enabled'] = false;
        $apData['mailchimp_double_optin'] = false;
        $apData['mailchimp_welcome'] = false;
        $apData['aweber_enabled'] = false;
        $apData['mailgun_enabled'] = false;
        $apData['madmimi_enabled'] = false;

        // update all options (sanitize first)
        foreach ($_POST as $k => $v) {
            if ($k !== 'Submit' && $k !== '_wpnonce' && $k !== '_wp_http_referer' && trim($v)) {
                $apData[$k] = strip_tags($v);
            }
        }
        update_site_option('animated_popup_data', $apData);
    }


    // ADMIN ERRORS
    $errors['Mailchimp'] = $apData['mailchimp_error'];
    $errors['AWeber'] = $apData['aweber_error'];
    $errors['Mailgun'] = $apData['mailgun_error'];
    $errors['Mad Mimi'] = $apData['madmimi_error'];
    foreach ($errors as $k => $e) {
        if ($e) { ?>
            <div class="error">
                <div class="ap-error-label">
                    <strong><?php echo $k ?> Error:</strong> <?php echo $e ?>
                </div>
                <div class="ap-error-button">
                    <input class="button w3tc" value="gotcha" onclick="jQuery(this).parent().parent().hide()" type="button">
                </div>
            </div>
            <?php
        }
    }
    $apData['mailchimp_error'] = false;
    $apData['aweber_error'] = false;
    $apData['mailgun_error'] = false;
    $apData['madmimi_error'] = false;
    // $apData['success'] = false; // don't delete success option

    function makeSwatch($color, $checked, $name) {
        ?>
        <label>
            <input type="radio" name="<?php echo $name ?>" value="<?php echo $color ?>"
            <?php echo $checked ?> />
            <div class="ap-swatch" style="background: <?php echo $color ?>"></div>
        </label>
        <?php
    }
    ?>

    <div class="wrap">
        <form method="post" name="options" target="_self">
            <h2></h2><!-- title is wonky without this -->

            <h2>Animated Popup Settings</h2>
            <p><em>Use the shortcode or widget to embed your popup somewhere on your site!</em></p>
            <p>Shortcode example: <code>[animated_popup]</code></p>

            <?php wp_nonce_field('update-options'); ?>
            <h3>Box</h3>
            <div class="ap-parent"><div class="ap-label">Header Text:</div>
                <div class="ap-text-input"><input name="header_text" type="text"
                value="<?php echo $apData['header_text'] ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Paragraph Text:</div>
                <div class="ap-text-input">
                    <textarea name="paragraph_text"><?php echo $apData['paragraph_text'] ?></textarea>
                </div>
            </div>
            <div class="ap-parent"><div class="ap-label">Box Color</div>
                <?php
                $colors = ['#1E375D', '#5D1E1E', '#17483A'];
                foreach ($colors as $color) {
                    $checked = $apData['box_color'] === $color ? 'checked' : '';
                    makeSwatch($color, $checked, 'box_color');
                } ?>
                <label>
                    <input type="radio" name="box_color" class="custom-box-color"
                    <?php if ($apData['box_color'] !== '#17483A' &&
                    $apData['box_color'] !== '#5D1E1E' &&
                    $apData['box_color'] !== '#1E375D') { echo 'checked'; } else { echo ''; } ?> />
                </label>
                <input type="text" name="box_color" class="custom-box-color-text" disabled  placeholder="e.g. #1E375D"
                value="<?php echo $apData['box_color'] ?>" />
            </div>

            <div class="ap-parent"><div class="ap-label">Button Color</div>
                <?php
                $colors = ['#4B67B8', '#D21E1E', '#3BB63C'];
                foreach ($colors as $color) {
                    $checked = $apData['button_color'] === $color ? 'checked' : '';
                    makeSwatch($color, $checked, 'button_color');
                } ?>
                <label>
                    <input type="radio" name="button_color" class="custom-button-color"
                    <?php if ($apData['button_color'] !== '#4B67B8' &&
                    $apData['button_color'] !== '#D21E1E' &&
                    $apData['button_color'] !== '#3BB63C') { echo 'checked'; } else { echo ''; } ?> />
                </label>
                <input type="text" name="button_color" class="custom-button-color-text" disabled placeholder="e.g. #4B67B8"
                value="<?php echo $apData['button_color'] ?>" />
            </div>

            <div class="ap-parent"><div class="ap-label">Animation Type</div>

                <select id="animation-select" class="ap-select" name="animation_type">
                    <?php
                    $options = [
                        'tornado', 'gyroscope', 'trampoline',
                        'creeper', 'none',
                        // 'sidewinder', 'twister', 'zigzag', 'flipper', 'bigdipper', 'whirligig', 'spiral',
                    ];
                    foreach ($options as $o) {
                        $s = $apData['animation_type'] === $o ? 'selected' : '';
                        echo '<option value="' . $o . '" ' . $s . '>' . ucfirst($o) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="ap-parent"><div class="ap-label">Window Type</div>
                <select class="ap-select" name="popup_or_embed">
                    <option value="popup" <?php echo $apData['popup_or_embed'] === 'popup' ? 'selected' : ''; ?>>Popup</option>
                    <option value="embed" <?php echo $apData['popup_or_embed'] === 'embed' ? 'selected' : ''; ?>>Embed</option>
                </select>
            </div>

            <?php
            require_once 'animated-popup-embed.php';
            echo $templateString;
            ?>

            <h3>WP Mail</h3>
            <p>Have an email sent to your Admin Email Address whenever someone signs up with Animated Popup!</p>
            <p>For some hosts, Gmail and Yahoo Mail don't work due to strict security requirements. If that's the case with your host, we recommend you use an email address that uses your domain.</p>
            <p>You can always forward those emails over to your Gmail/Yahoo account if you want. For more information, refer to this page <a target="_blank" href="http://codex.wordpress.org/FAQ_Troubleshooting#E-mailed_passwords_are_not_being_received">FAQ Troubleshooting E-mails</a>.</p>

            <div class="ap-parent"><div class="ap-label">Enable WP Mail:</div>
                <div class="ap-text-input"><input name="wpmail_enabled" type="checkbox"
                <?php echo $apData['wpmail_enabled'] === 'on' ? 'checked' : ''; ?> /></div>
            </div>

            <!-- <h3>Mandrill</h3>
            <div class="ap-parent"><div class="ap-label">Enable Mandrill:</div>
                <div class="ap-text-input"><input name="mandrill_enabled" type="checkbox"
                <?php //echo $apData['mandrill_enabled'] === 'on' ? 'checked' : ''; ?> /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mandrill API Key:</div>
                <div class="ap-text-input ap-api-key"><input disabled name="mandrill_api_key" type="text"
                value="<?php //echo $apData['mandrill_api_key']; ?>" /></div>
            </div> -->

            <input type="submit" class="button-primary" name="Submit" value="Update" />
            <br><br>
            <br><br>


            <h2>Premium Features - add people to mailing lists effortlessly!</h2>

            <h3>MailChimp</h3>
            <div class="ap-parent"><div class="ap-label">Enable Mailchimp:</div>
                <div class="ap-text-input"><input name="mailchimp_enabled" type="checkbox" disabled /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mailchimp API Key:</div>
                <div class="ap-text-input ap-api-key"><input disabled name="mailchimp_api_key" type="text"
                value="<?php echo $apData['mailchimp_api_key']; ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mailchimp List ID:</div>
                <div class="ap-text-input"><input disabled name="mailchimp_list_id" type="text"
                value="<?php echo $apData['mailchimp_list_id']; ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Double Opt-In:</div>
                <div class="ap-text-input"><input disabled name="mailchimp_double_optin" type="checkbox"
                <?php echo $apData['mailchimp_double_optin'] === 'on' ? 'checked' : ''; ?> /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Welcome Email:</div>
                <div class="ap-text-input"><input disabled name="mailchimp_welcome" type="checkbox"
                <?php echo $apData['mailchimp_welcome'] === 'on' ? 'checked' : ''; ?> /></div>
            </div>

            <div class="ap-label">
                <h3>AWeber</h3>
            </div>

            <!-- authorize section removed -->

            <div class="ap-parent"><div class="ap-label">Enable Aweber:</div>
                <div class="ap-text-input"><input name="aweber_enabled" type="checkbox" disabled /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your AWeber List Name:</div>
                <div class="ap-text-input"><input disabled name="aweber_list_id" type="text"
                value="<?php echo $apData['aweber_list_id']; ?>" /></div>
            </div>

            <h3>Mailgun</h3>

            <div class="ap-parent"><div class="ap-label">Enable Mailgun:</div>
                <div class="ap-text-input"><input name="mailgun_enabled" type="checkbox" disabled /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mailgun API Key:</div>
                <div class="ap-text-input ap-api-key"><input disabled name="mailgun_api_key" type="text"
                value="<?php echo $apData['mailgun_api_key']; ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mailgun List Address:</div>
                <div class="ap-text-input"><input disabled name="mailgun_list_id" type="text"
                value="<?php echo $apData['mailgun_list_id']; ?>" /></div>
            </div>

            <h3>Mad Mimi</h3>

            <div class="ap-parent"><div class="ap-label">Enable Mad Mimi:</div>
                <div class="ap-text-input"><input name="madmimi_enabled" type="checkbox" disabled />
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mad Mimi Username/Email:</div>
                <div class="ap-text-input"><input disabled name="madmimi_username" type="text"
                value="<?php echo $apData['madmimi_username']; ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mad Mimi API Key:</div>
                <div class="ap-text-input ap-api-key"><input disabled name="madmimi_api_key" type="text"
                value="<?php echo $apData['madmimi_api_key']; ?>" /></div>
            </div>
            <div class="ap-parent"><div class="ap-label">Your Mad Mimi List Name:</div>
                <div class="ap-text-input"><input disabled name="madmimi_list_id" type="text"
                value="<?php echo $apData['madmimi_list_id']; ?>" /></div>
            </div>

            <input type="submit" class="button-primary" name="Submit" value="Update" />

        </form>
    </div>

    <?php //var_dump($apData);var_dump($_POST); ?>

    <?php
}


// ajax function


add_action( 'wp_ajax_nopriv_myajax-submit', 'ap_ajax_submit' );
add_action( 'wp_ajax_myajax-submit', 'ap_ajax_submit' );

function ap_ajax_submit () {

    $nonce = $_POST['subscribeNonce'];
    if ( ! wp_verify_nonce( $nonce, 'subscribe-nonce' ) ) {
        die ( 'not authorized');
    }

    $apData = get_site_option('animated_popup_data');

    $message = '';
    $enabled = array();

    // validate email.. if bad-- exit
    $email = sanitize_email($_POST['email']);
    if (!$email) {
        echo json_encode(array('message' => '', 'enabled' => array(), 'validation' => false));
        exit;
    }

    // WP_MAIL //
    if ($apData['wpmail_enabled'] === 'on') {
        $enabled[] = 'wpmail';

        $eHeaders = array();
        $eHeaders[] = 'From: Animated Popup <' . get_site_option('admin_email') . ">\r\n";
        $eHeaders[] = 'Content-Type: text/html; charset=UTF-8';

        $eSubject = 'Animated Popup - A New Subscriber!';

        $eMessage = '';
        $eMessage .= '
        <div>
            <div style="width: 600px;margin: 0 auto;border: 1px solid lightgray;font-family:Arial,sans-serif">
                <div style="color:white;background:blue;padding: 20px;"><p style="font-size: 22px; margin: 0 10px"><strong>Animated Popup</strong></p></div>

                <div style="background:#f2f2f2;padding: 0 20px 20px 20px;">
                    <div style="background:white;padding: 40px 50px;">
                        <p style="margin: 0; font-size: 16px; line-height: 1.5;">This new guy has signed up to get updates from you:</p>
                        <div style="margin: 40px; text-align: center;"><span style="font-size: 20px; border: 1px solid lightgray; padding: 20px;"><strong>';
                        $eMessage .= $email;
                        $eMessage .= '
                        </strong></span></div>
                        <div style="font-size: 12px; color: #808080; text-align: center;">
                            <p>Wait! Don\'t waste time adding this subscriber to your mailing list manually!</p>
                            <p>Have them added the moment they click the subscribe button!</p>
                            <p>Supported providers: Mailchimp, AWeber, Mailgun, Mad Mimi</p>
                            <p><a href="http://aklosismedia.com/quotes/dev/downloads/animated-popup">Get the Pro version to use this feature now!</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        wp_mail( get_site_option('admin_email'), $eSubject, $eMessage, $eHeaders );
    }


    // MANDRILL //






    update_site_option('animated_popup_data', $apData); // add any errors

    echo json_encode(array('message' => $message, 'enabled' => $enabled, 'validation' => true));
    exit;
}
