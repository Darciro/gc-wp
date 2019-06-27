<?php
/**
 * Plugin Name: Code Widget
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Code Widget help you to run <code>Code</code> and simple text in  widget which have different type <code>Short Code</code> <code>PHP Code</code>. Yes, you can also add <code>TEXT</code> and  <code>HTML</code>.
 * Version: 1.0.2
 * Author: Sharaz Shahid
 * Author URI: https://twitter.com/sharazghouri1
 * Text Domain: code-widget
 * Domain Path: /languages/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package code widget
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! defined( 'CODE_WIDGET_PATH' ) ) {
	/**
	 * Absolute path of this plugin
	 *
	 * @since 1.0
	 */

	define( 'CODE_WIDGET_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

define( 'CODE_WIDGET_VERSION', '1.0.2' );
define( 'CODE_WIDGET_TEXT_DOMAIN', 'code-widget' );

if ( ! class_exists( 'Code_Widget' ) ) {

	/**
	 * Adds Code_Widget.
	 */
	class Code_Widget extends WP_Widget {
		/**
		 * Instance of the class
		 *
		 * @var instance
		 */
		public static $instance;
		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			load_plugin_textdomain( CODE_WIDGET_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) );
			parent::__construct(
				'codewidget', // Base ID.
				esc_html__( 'Code Widget', CODE_WIDGET_TEXT_DOMAIN ), // Name.
				array( 'description' => esc_html__( 'Any Text,Short Code,HTML,PHP Code .', CODE_WIDGET_TEXT_DOMAIN ) ) // Args.
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args Widget arguments.
		 *
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			$cw_type    = $instance['cw_type'];
			$cw_filter  = $instance['cw_filter'];
			$cw_content = apply_filters( 'cw_content', $instance['cw_content'], $this );

			if ( 'php_code' == $cw_type ) {
				$cw_final_content = $this->php_exe( $cw_content );
			}

			if ( 'short_code' == $cw_type ) {
				$cw_final_content      = do_shortcode( $cw_content );
			}

			if ( 'html_code' == $cw_type ) {
				$cw_final_content = convert_smilies( balanceTags( $cw_content ) );
			}

			if ( 'text_code' == $cw_type ) {
				$cw_final_content = wptexturize( esc_html( $cw_content ) );
			}

			$cw_final_content = apply_filters( 'cw_final_content', $cw_final_content );

			echo $args['before_widget'] ;

			if ( ! empty( $instance['title'] ) ) {
				echo  $args['before_title']  . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) .  $args['after_title'];
			}
			echo '<div class="code-widget">'.( $cw_filter ? wpautop( $cw_final_content ) : $cw_final_content ) . '</div>';

			echo  $args['after_widget'] ;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$title                  = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', CODE_WIDGET_TEXT_DOMAIN );
			if ( 0 == count( $instance ) ) {
				$instance['cw_type']    = ! empty( $instance['cw_type'] ) ? $instance['cw_type'] : 'short_code';
				$instance['cw_content'] = ! empty( $instance['cw_content'] ) ? $instance['cw_content'] : esc_html__( 'your code ....', CODE_WIDGET_TEXT_DOMAIN );
				$instance['cw_filter']  = ! empty( $instance['cw_filter'] ) ? $instance['cw_filter'] : 0;
			} else {
				$instance['cw_type']    = $instance['cw_type'];
				$instance['cw_content'] = $instance['cw_content'];
				$instance['cw_filter']  = $instance['cw_filter'];
			}
		?>
				<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', CODE_WIDGET_TEXT_DOMAIN ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
				value="<?php echo esc_attr( $title ); ?>">
				</p>
				<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'cw_type' ) ); ?>"><?php esc_attr_e( 'Widget Type:', CODE_WIDGET_TEXT_DOMAIN ); ?></label>
				</p>
				<select  name="<?php echo esc_html( $this->get_field_name( 'cw_type' ) ); ?>" class="widefat" id="<?php esc_html_e( $this->get_field_id( 'cw_type' ) ); ?>">
				<option value="short_code" <?php selected( $instance['cw_type'], 'short_code' ); ?> > Short Code</option>
				<option value="php_code"   <?php selected( $instance['cw_type'], 'php_code' ); ?>> PHP Code</option>
				<option value="html_code"  <?php selected( $instance['cw_type'], 'html_code' ); ?>> HTML</option>
				<option value="text_code"  <?php selected( $instance['cw_type'], 'text_code' ); ?>> Text</option>
				</select>
				<p>
				<textarea class="widefat" rows="12" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'cw_content' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'cw_content' ) ); ?>"><?php echo  $instance['cw_content']  ?></textarea>
				</p
				<p><input id="<?php echo esc_attr( $this->get_field_id( 'cw_filter' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'cw_filter' ) ); ?>"
				type="checkbox" <?php checked( $instance['cw_filter'], 'on' ); ?>/>&nbsp;<label
				for="<?php echo esc_html( $this->get_field_id( 'cw_filter' ) ); ?>"><?php esc_html_e( 'Automatically add paragraphs.', CODE_WIDGET_TEXT_DOMAIN ); ?></label>
				</p>
		<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 *
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance               = array();
			$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['cw_type']    = ( ! empty( $new_instance['cw_type'] ) ) ? strip_tags( $new_instance['cw_type'] ) : '';
			$instance['cw_content'] = ( ! empty( $new_instance['cw_content'] ) ) ? strip_tags( $new_instance['cw_content'] ) : '';
			$instance['cw_filter']  = ( ! empty( $new_instance['cw_filter'] ) ) ? strip_tags( $new_instance['cw_filter'] ) : 0;

			/*
			Unfiltered_html
			Since 2.0
			Allows user to post HTML markup or even JavaScript code in pages, posts, comments and widgets.
			Note: Enabling this   option for untrusted users may result in their posting malicious or poorly formatted code.
			Note: In WordPress Multisite, only Super Admins have the unfiltered_html capability.
			*/
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['cw_content'] = $new_instance['cw_content'];
			} else {
				$instance['cw_content'] = stripslashes( wp_filter_post_kses( $new_instance['cw_content'] ) );
			}
			return $instance;
		}
		/**
		 * Php exe use to excute php code in string
		 *
		 * @param string $content string of content.
		 */
		private function php_exe( $content ) {
			apply_filters( 'before_cw_php_exe', $content );
			ob_start();
			eval( '?>' . $content );
			$text = ob_get_contents();
			ob_end_clean();
			return apply_filters( 'after_cw_php_exe', $text );
		}
		/**
		 * Returns the current instance of the class, in case some other
		 * plugin needs to use its public methods.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 *
		 * @return Code_Widget Returns the current instance of the class
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
					self::$instance = new self();
			}
			return self::$instance;
		}


	} // class Code_Widget

	/**
	 * Simple  code widget register
	 */
	function register_code_widget() {
		register_widget( 'Code_Widget' );
	}

	add_action( 'widgets_init', 'register_code_widget' );

	/** Initialises an object of this class */
	Code_Widget::get_instance();
}
?>
