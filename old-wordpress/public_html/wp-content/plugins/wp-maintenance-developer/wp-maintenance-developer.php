<?php
/*
 * Plugin Name: WordPress Maintenance Developer
 * Plugin URI: https://github.com/miguelsmuller/wp-maintenance-developer
 * Description: Add a page that prevents your site's content view. Ideal to report a scheduled maintenance or coming soon page.
 * Version: 1.2.1
 * Author: Devim - Agência Web
 * Author URI: http://www.devim.com.br/
 * License: GPLv3 License
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-maintenance-developer
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Maintenance_Developer' ) ) :

/**
 * Wordpress Maintenance Developer Main class
 */
class WP_Maintenance_Developer
{
    /**
     * Instance of this class.
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Array of plugin settings
     *
     * @var array
     */
    protected $maintenance_settings;

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    /**
     * Initialize the plugin public actions.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'load_textdomain' ) );

        $this->do_plugin_settings();

        add_action( 'admin_init', array( &$this, 'admin_init' ));
        add_action( 'admin_menu', array( &$this, 'admin_menu' ));
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
        add_filter( 'plugin_action_links_wp-maintenance-developer/wp-maintenance-developer.php', array( &$this, 'plugin_action_links' ), 10, 5 );

        if ($this->maintenance_settings['status'] === 'TRUE') {
            add_filter( 'login_message', array( &$this, 'login_message' ));
            add_action( 'admin_notices', array( &$this, 'admin_notices' ));
            add_action( 'wp_loaded', array( &$this, 'apply_maintenance_mode' ));
            add_filter( 'wp_title', array( &$this, 'wpTitle' ), 9999, 2 );
        }
    }

    /**
     * Load the plugin text domain for translation.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'wp-maintenance-developer', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Construction of the plugin
     */
    public function do_plugin_settings() {
        if( false == get_option( 'maintenance_settings' )) {
            add_option( 'maintenance_settings' );
            $default = array(
                'status'           => FALSE,
                'description'      => '', // Why the maintenance mode is active
                'time_activated'   => '', // Time that has been activated
                'duration_days'    => '', // Days suspended
                'duration_hours'   => '', // Hours suspended
                'duration_minutes' => '', // Minutos suspended
                'url_allowed'      => '',
                'role_allow_front' => '',
                'role_allow_back'  => ''
            );
            update_option( 'maintenance_settings', $default );
        }

        $this->maintenance_settings = get_option( 'maintenance_settings' );

        if (!isset($this->maintenance_settings['status'])) $this->maintenance_settings['status'] = FALSE;
    }

    /**
     * Cria um formulário pra ser usada pra configuração do tema
     */
    function admin_init(){
        add_settings_section(
            'section_maintenance',
            __('Configure the details of the maintenance mode', 'wp-maintenance-developer'),
            '__return_false',
            'page-wp-maintenance-developer'
        );

        add_settings_field(
            'status',
            __('Enable maintenance mode:', 'wp-maintenance-developer'),
            array( &$this, 'html_input_status' ),
            'page-wp-maintenance-developer',
            'section_maintenance'
        );

        add_settings_field(
            'description',
            __('Motive of maintenance:', 'wp-maintenance-developer'),
            array( &$this, 'html_input_description' ),
            'page-wp-maintenance-developer',
            'section_maintenance'
        );

        add_settings_field(
            'url_allowed',
            __('The following pages have free access:', 'wp-maintenance-developer'),
            array( &$this, 'html_input_url_allowed' ),
            'page-wp-maintenance-developer',
            'section_maintenance'
        );

        add_settings_field(
            'role_allow',
            __('Who can access:', 'wp-maintenance-developer'),
            array( &$this, 'html_input_role_allow' ),
            'page-wp-maintenance-developer',
            'section_maintenance'
        );

        register_setting(
            'page-wp-maintenance-developer',
            'maintenance_settings'
        );
    }

    /**
     *
     */
    public function html_input_status(){
        if ($this->maintenance_settings['status'] == TRUE) :
            $return    = $this->calc_time_maintenance();

            $message = sprintf( __("The maintenance mode will end in <strong>%s</strong>", 'wp-maintenance-developer'), $return['return-date'] );

            echo ("<p><span class='description'>$message</span></p><br/>");
        endif;

        $days  = $this->maintenance_settings['status'] == TRUE ? $return['remaining-array']['days'] : '1';
        $hours = $this->maintenance_settings['status'] == TRUE ? $return['remaining-array']['hours'] : '0';
        $mins  = $this->maintenance_settings['status'] == TRUE ? $return['remaining-array']['mins'] : '0';
        ?>

        <input type="hidden" name="maintenance_settings[time_activated]" value="<?php echo current_time('timestamp'); ?>">

        <label>
            <input type="checkbox" id="status" name="maintenance_settings[status]" value="TRUE" <?php checked( 'TRUE', $this->maintenance_settings['status'] ) ?> /> <?php _e('I want to enable', 'wp-maintenance-developer'); ?>
        </label>

        <br/>
        <table>
            <tbody>
                <tr>
                    <td><strong><?php _e('Back in:'); ?></strong></td>
                    <td><input type="text" id="duration_days" name="maintenance_settings[duration_days]" value="<?php echo $days; ?>" size="4" maxlength="5"> <label for="duration_days"><?php _e('Days', 'wp-maintenance-developer'); ?></label></td>
                    <td><input type="text" id="duration_hours" name="maintenance_settings[duration_hours]" value="<?php echo $hours; ?>" size="4" maxlength="5"> <label for="duration_hours"><?php _e('Hours', 'wp-maintenance-developer'); ?></label></td>
                    <td><input type="text" id="duration_minutes" name="maintenance_settings[duration_minutes]" value="<?php echo $mins; ?>" size="4" maxlength="5"> <label for="duration_minutes"><?php _e('Minutes', 'wp-maintenance-developer'); ?></label></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     *
     */
    public function html_input_description(){
        $html = '<textarea id="description" name="maintenance_settings[description]" cols="80" rows="5" class="large-text">'.$this->maintenance_settings['description'].'</textarea>';
        echo $html;
    }

    /**
     *
     */
    public function html_input_url_allowed(){
        $html = '<textarea id="url_allowed" name="maintenance_settings[url_allowed]" cols="80" rows="5" class="large-text">'.$this->maintenance_settings['url_allowed'].'</textarea>';
        $html .= '<br/><span class="description">Digite os caminhos que devem estar acessíveis mesmo em modo de manutenção. Separe os vários caminhos com quebras de linha.<br/>Exemplo: Se você quer liberar acesso á pagina <strong>http://site.com/sobre/</strong>, você deve digitar <strong>/sobre/</strong>.<br/>Dica: Se você quiser liberar acesso a página inicial digite <strong>[HOME]</strong>.</span>';
        echo $html;
    }

    /**
     *
     */
    public function html_input_role_allow(){
        //INPUT FOR ALLOW BACK
        $html = '<label>'. __('Access the administrative panel:', 'wp-maintenance-developer');
        $html .= ' <select id="role_allow_back" name="maintenance_settings[role_allow_back]">
                    <option value="manage_options" ' . selected( $this->maintenance_settings['role_allow_back'], 'manage_options', false) . '>Ninguém</option>
                    <option value="manage_categories" ' . selected( $this->maintenance_settings['role_allow_back'], 'manage_categories', false) . '>Editor</option>
                    <option value="publish_posts" ' . selected( $this->maintenance_settings['role_allow_back'], 'publish_posts', false) . '>Autor</option>
                    <option value="edit_posts" ' . selected( $this->maintenance_settings['role_allow_back'], 'edit_posts', false) . '>Colaborador</option>
                    <option value="read" ' . selected( $this->maintenance_settings['role_allow_back'], 'read', false) . '>Visitante</option>
                </select>';
        $html .= '</label><br />';

        //INPUT FOR ALLOW FRONT
        $html .= '<label>'. __('Access the public site:', 'wp-maintenance-developer');
        $html .= ' <select id="role_allow_front" name="maintenance_settings[role_allow_front]">
                    <option value="manage_options" ' . selected( $this->maintenance_settings['role_allow_front'], 'manage_options', false) . '>Ninguém</option>
                    <option value="manage_categories" ' . selected( $this->maintenance_settings['role_allow_front'], 'manage_categories', false) . '>Editor</option>
                    <option value="publish_posts" ' . selected( $this->maintenance_settings['role_allow_front'], 'publish_posts', false) . '>Autor</option>
                    <option value="edit_posts" ' . selected( $this->maintenance_settings['role_allow_front'], 'edit_posts', false) . '>Colaborador</option>
                    <option value="read" ' . selected( $this->maintenance_settings['role_allow_front'], 'read', false) . '>Visitante</option>
                </select>';
        $html .= '</label><br />';
        echo $html;
    }


    /**
     *
     */
    function admin_menu(){
        add_submenu_page(
            'tools.php',
            __('Maintenance mode', 'wp-maintenance-developer'),
            __('Maintenance mode', 'wp-maintenance-developer'),
            'administrator',
            'page-wp-maintenance-developer',
            array( &$this, 'html_form_settings' )
        );
    }

    /**
     *
     */
    public function html_form_settings(){
    ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('General Settings'); ?></h2>
            <div class="updated">
				<p><?php printf( __( 'Help us keep the %s plugin free making a rate %s on %s. Thank you in advance!', 'wp-maintenance-developer' ), '<strong>' . __( 'WordPress Maintenance Developer', 'wp-maintenance-developer' ) . '</strong>', '<a href="https://wordpress.org/support/view/plugin-reviews/wp-maintenance-developer?rate=5#postform" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/wp-maintenance-developer?rate=5#postform" target="_blank">' . __( 'WordPress.org', 'wp-maintenance-developer' ) . '</a>' ); ?></p>
			</div>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'page-wp-maintenance-developer' );
                do_settings_sections( 'page-wp-maintenance-developer' );
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
	 * Add the settings page.
	*/
	function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'wp-maintenance-developer.php' ) !== false ) {
			$new_links = array(
				'<a href="http://wordpress.org/support/plugin/wp-maintenance-developer/" target="_blank" title="'. __( 'Official Forum', 'wp-maintenance-developer' ) .'">' . __( 'Get Help', 'wp-maintenance-developer' ) . '</a>',
				'<a href="https://github.com/miguelsmuller/wp-maintenance-developer/" target="_blank" title="'. __( 'Official Repository', 'wp-maintenance-developer' ) .'">' . __( 'Get Involved', 'wp-maintenance-developer' ) . '</a>',
				'<a href="https://wordpress.org/support/view/plugin-reviews/wp-maintenance-developer?rate=5#postform" target="_blank" title="'. __( 'Rate WordPress Maintenance Developer', 'wp-maintenance-developer' ) .'">' . __( 'Rate WordPress Maintenance Developer', 'wp-maintenance-developer' ) . '</a>'
			);
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Add the settings page.
	*/
	function plugin_action_links( $actions ) {
		$new_actions = array(
			'<a href="' . admin_url( 'tools.php?page=page-wp-maintenance-developer' ) . '">'. __( 'Settings', 'wp-maintenance-developer' ) .'</a>',
		);
		return array_merge( $new_actions, $actions );
	}

    /**
     *
     */
    public function calc_time_maintenance(){
        // How long will it stay off in seconds
        $time_duration = 0;
        $time_duration += intval($this->maintenance_settings['duration_days']) * 24 * 60;
        $time_duration += intval($this->maintenance_settings['duration_hours']) * 60;
        $time_duration += intval($this->maintenance_settings['duration_minutes']);
        $time_duration = intval($time_duration * 60);

        // Timestamp of time activated, time finished, time current e time remaining
        $time_activated = intval($this->maintenance_settings['time_activated']);
        $time_finished  = intval($time_activated + $time_duration);
        $time_current   = current_time('timestamp');
        $time_remaining = $time_finished - $time_current;

        // Format the date in the format defined by the system
        $return_day  = date_i18n( get_option('date_format'), $time_finished );
        $return_time = date_i18n( get_option('time_format'), $time_finished );
        $return_date = $return_day . ' ' . $return_time;

        $time_calculated = $this->calc_separate_time($time_remaining);

        return array(
            'return-date'       => $return_date,
            'remaining-seconds' => $time_remaining,
            'remaining-array'   => $time_calculated,
        );
    }


    /**
     * Calculates the days, hours and minutes remaining based on the number of seconds
     *
     * @return array Array containing the values of days, hours and minutes remaining
     */
    private function calc_separate_time($seconds){
        $minutes = round(($seconds/(60)), 0);

        $minutes = intval($minutes);
        $vals_arr = array(  'days' => (int) ($minutes / (24*60) ),
                            'hours' => $minutes / 60 % 24,
                            'mins' => $minutes % 60);

        $return_arr = array();

        $is_added = false;

        foreach ($vals_arr as $unit => $amount) {
            $return_arr[$unit] = 0;

            if ( ($amount > 0) || $is_added ) {
                $is_added          = true;
                $return_arr[$unit] = $amount;
            }
        }
        return $return_arr;
    }

    /**
     *
     */
    function apply_maintenance_mode()
    {
        if ( strstr($_SERVER['PHP_SELF'],'wp-login.php')) return;
        if ( strstr($_SERVER['PHP_SELF'], 'wp-admin/admin-ajax.php')) return;
        if ( strstr($_SERVER['PHP_SELF'], 'async-upload.php')) return;
        if ( strstr(htmlspecialchars($_SERVER['REQUEST_URI']), '/plugins/')) return;
        if ( strstr($_SERVER['PHP_SELF'], 'upgrade.php')) return;
        if ( $this->check_url_allowed()) return;

        //Never show maintenance page in wp-admin
        if ( is_admin() || strstr(htmlspecialchars($_SERVER['REQUEST_URI']), '/wp-admin/') ) {
            if ( !is_user_logged_in() ) {
                auth_redirect();
            }
            if ( $this->user_allow('admin') ) {
                return;
            } else {
                $this->display_maintenance_page();
            }
        } else {
            if( $this->user_allow('public') ) {
                return;
            } else {
                $this->display_maintenance_page();
            }
        }
    }


    /**
     *
     */
    function display_maintenance_page()
    {
        $time_maintenance = $this->calc_time_maintenance();
        $time_maintenance = $time_maintenance['remaining-seconds'];

        //Define header as unavailable
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');

        if ( $time_maintenance > 1 ) header('Retry-After: ' . $time_maintenance );

        // Check what used in page will be visitor redirect
        $file503 = get_template_directory() . '/503.php';
        if (file_exists($file503) == FALSE) {
            $file503 = dirname(  __FILE__  ) . '/503-default.php';
        }

        // Show page
        include($file503);

        exit();
    }


    /**
     *
     */
    function check_url_allowed()
    {
        $urlarray = $this->maintenance_settings['url_allowed'];
        $urlarray = preg_replace("/\r|\n/s", ' ', $urlarray); //TRANSFORM BREAK LINES IN SPACE
        $urlarray = explode(' ', $urlarray); //TRANSFORM STRING IN ARRAY
        $oururl = 'http://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']);
        foreach ($urlarray as $expath) {
            if (!empty($expath)) {
                $expath = str_replace(' ', '', $expath);
                if (strpos($oururl, $expath) !== false) return true;
                if ( (strtoupper($expath) == '[HOME]') && ( trailingslashit(get_bloginfo('url')) == trailingslashit($oururl) ) )    return true;
            }
        }
        return false;
    }


    /**
     * Cria um formulário pra ser usada pra configuração do tema
     */
    function user_allow($where)
    {
        if ($where == 'public') {
            $optval = $this->maintenance_settings['role_allow_front'];
        } elseif ($where == 'admin') {
            $optval = $this->maintenance_settings['role_allow_back'];
        } else {
            return false;
        }

        if ( $optval == 'manage_options' && current_user_can('manage_options') ) { return true; }
        elseif ( $optval == 'manage_categories' && current_user_can('manage_categories') ) { return true; }
        elseif ( $optval == 'publish_posts' && current_user_can('publish_posts') ) { return true;   }
        elseif ( $optval == 'edit_posts' && current_user_can('edit_posts') ) { return true; }
        elseif ( $optval == 'read' && current_user_can('read') ) { return true; }
        else { return false; }
    }

    /**
     *
     */
    function login_message( $message ){
        $message = apply_filters( 'smaintenance_loginnotice', __('Currently this site is in MAINTENANCE MODE.', 'wp-maintenance-developer') );

        return '<div id="login_error"><p class="text-center">'. $message .'</p></div>';
    }

    /**
     *
     */
    function admin_notices()
    {
        $edit_url = site_url() . '/wp-admin/admin.php?page=page-wp-maintenance-developer';

        $message1 = __('Currently this site is in MAINTENANCE MODE.', 'wp-maintenance-developer');
        $message2 = sprintf( __('To exit the maintenance mode just change the settings <a href="%s">clicking here</a>.', 'wp-maintenance-developer'), $edit_url);

        $message = apply_filters( 'smaintenance_adminnotice', $message1. ' '. $message2 );

        echo '<div id="message" class="error"><p>'. $message .'</p></div>';
    }

    /**
     *
     */
    function wpTitle()
    {
        return get_bloginfo( 'name' ). ' | Modo Manutenção';
    }

}
add_action( 'plugins_loaded', array( 'WP_Maintenance_Developer', 'get_instance' ), 0 );

endif;
