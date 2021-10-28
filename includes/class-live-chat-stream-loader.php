<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://pbdigital.com.au/
 * @since      1.0.0
 *
 * @package    Live_Chat_Stream
 * @subpackage Live_Chat_Stream/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Live_Chat_Stream
 * @subpackage Live_Chat_Stream/includes
 * @author     Paul Bright <paul@pbdigital.com.au>
 */
class Live_Chat_Stream_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {


		$this->add_action( 'wp_ajax_pbd_event_stream', $this, "pbd_get_live_event_stream_ajax");
		$this->add_action( 'wp_ajax_nopriv_pbd_event_stream', $this, "pbd_get_live_event_stream_ajax");
		$this->add_action( 'wp_ajax_pbd_event_stream_add_comment', $this, "pbd_add_live_event_stream_comment_ajax");
		$this->add_action( 'wp_ajax_nopriv_pbd_event_stream_add_comment', $this, "pbd_add_live_event_stream_comment_ajax");


		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		add_shortcode('pbd_event_stream', [$this,'pbd_live_stream_shortcode']);

	}

	static function pbd_live_stream_shortcode($atts){
		$atts = shortcode_atts(
			array(
				'heading' => 'Live Chat',
				'welcome' => 'Welcome to the Live Stream',
				'color' => buddyboss_theme_get_option( 'accent_color' ),
				'text_color' => buddyboss_theme_get_option( 'body_text_color' ),
				'date_color' => '#939597',
				'refresh' => 10,
				'post_id' => $post->ID //Default to current post ID shortcode is added to
			), $atts
		);
		global $post;
		$post_id = $post->ID; 
		ob_start();
		?>
		<style>
			.stream_comment span {
				color: <?php echo $atts['color'];?>;
				
			}
		</style>
		<div class="live_event_stream" style="background-color:<?php echo $atts['color'];?>; border:6px solid <?php echo $atts['color'];?>">
			<input type="hidden" id="live_event_comment_last_id" value="0">
			<div class="heading">
				<?php echo $atts['heading'];?>
			</div>
			<div class="live-comments">
				<div class="welcome" style="color:<?php echo $atts['color'];?>">
					<?php echo $atts['welcome'];?>
				</div>
				<div class="live_event_comments" >
					<div class="loading">Loading Comments</div>
				</div>
			</div>
			<div class="submit_section" >
				<textarea id="live_event_comments_message"></textarea>
				<!-- <input id="live_event_comments_btn" type="submit" value="submit"> -->
				<button type="submit" id="live_event_comments_btn" disabled="disabled">
					<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.71 1.29a1 1 0 0 0-1.05-.23l-22 8a1 1 0 0 0 0 1.87l8.59 3.43L15.59 8 17 9.41l-6.37 6.37 3.44 8.59A1 1 0 0 0 15 25a1 1 0 0 0 .92-.66l8-22a1 1 0 0 0-.21-1.05Z" fill="#F46852"/></svg>
					<svg class="spinner" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 29.333A2.667 2.667 0 1 0 16 24a2.667 2.667 0 0 0 0 5.333Z" fill="#F46852"/><path d="M12 3a9 9 0 0 1 9 9h-2a7 7 0 0 0-7-7V3Z" fill="#fff"/><path d="M16 8a2.667 2.667 0 1 0 0-5.333A2.667 2.667 0 0 0 16 8ZM8.457 26.21a2.667 2.667 0 1 0 0-5.334 2.667 2.667 0 0 0 0 5.333ZM23.543 11.124a2.667 2.667 0 1 0 0-5.333 2.667 2.667 0 0 0 0 5.333ZM5.333 18.668a2.668 2.668 0 1 0 0-5.336 2.668 2.668 0 0 0 0 5.336ZM26.667 18.667a2.667 2.667 0 1 0 0-5.334 2.667 2.667 0 0 0 0 5.334ZM8.457 11.125a2.667 2.667 0 1 0 0-5.333 2.667 2.667 0 0 0 0 5.333ZM23.543 26.21a2.667 2.667 0 1 0 0-5.333 2.667 2.667 0 0 0 0 5.334Z" fill="#F46852"/></svg>
				</button>
			</div>
		</div>
		
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				liveStreamPageId = <?=$post_id?>;
				
				setTimeout( () => {
					liveStreamGetComments();
				},200);
			});
		</script>
		
		<?php
		return ob_get_clean();
	}	

	static function pbd_get_live_event_stream_ajax(){
		$post_id = $_GET['post_id'];
		$last_id = $_GET['last_id'];
		echo json_encode(pbd_get_live_stream_comments($post_id, $last_id ));
		wp_die();
	}

	static function pbd_add_live_event_stream_comment_ajax(){
		$post_id = $_POST['post_id'];
		$message = $_POST['message'];
		wp_insert_comment( array(
			'comment_post_ID' => $post_id,
			'comment_content' => esc_attr($message),
			'user_id' => get_current_user_id()	
		));
		echo json_encode(array('success'=>true));
		wp_die();
	}



}
