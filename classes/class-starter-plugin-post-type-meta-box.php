<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Starter Plugin Post Type Meta Box Class (Legacy)
 *
 * Provides the classic-editor meta box fallback for post types that are not
 * using the block editor.  When Gutenberg is active this class is not
 * instantiated.
 *
 * @package WordPress
 * @subpackage Starter_Plugin
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Starter_Plugin_Post_Type_Meta_Box {

	/**
	 * The post type this instance manages a meta box for.
	 * @access protected
	 * @since  1.0.0
	 * @var    string
	 */
	protected $post_type;

	/**
	 * Callable that returns the field definitions array.
	 * @access protected
	 * @since  1.0.0
	 * @var    callable
	 */
	protected $fields_callback;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param string   $post_type       The post type slug.
	 * @param callable $fields_callback Returns the field definitions.
	 */
	public function __construct( $post_type, callable $fields_callback ) {
		$this->post_type       = $post_type;
		$this->fields_callback = $fields_callback;

		add_action( 'admin_menu', array( $this, 'setup' ), 20 );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Register the meta box.
	 *
	 * Only runs when the classic editor is active for this post type. This check
	 * is intentionally deferred to the admin_menu hook so that init has already
	 * fired and the post type object exists when use_block_editor_for_post_type()
	 * is called.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function setup() {
		if ( function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( $this->post_type ) ) {
			return;
		}

		add_meta_box(
			$this->post_type . '-data',
			__( 'Thing Details', 'starter-plugin' ),
			array( $this, 'render' ),
			$this->post_type,
			'side',
			'high'
		);
	}

	/**
	 * Render the meta box contents.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function render() {
		global $post_id;
		$fields     = get_post_custom( $post_id );
		$field_data = call_user_func( $this->fields_callback );

		$html = '';

		$html .= '<input type="hidden" name="starter_plugin_' . $this->post_type . '_noonce" id="starter-plugin_' . $this->post_type . '_noonce" value="' . wp_create_nonce( plugin_basename( dirname( Starter_Plugin()->plugin_path ) ) ) . '" />';

		if ( 0 < count( $field_data ) ) :
			foreach ( $field_data as $k => $v ) :
				$data = $v['default'];
				if ( isset( $fields[ '_' . $k ] ) && isset( $fields[ '_' . $k ][0] ) ) {
					$data = $fields[ '_' . $k ][0];
				}
				?>
<p><label for="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v['name'] ); ?></label></p>
	<p><input name="<?php echo esc_attr( $k ); ?>" type="text" id="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $data ); ?>" /></p>
<p class="description"><?php echo esc_html( $v['description'] ); ?></p>
				<?php
			endforeach;
		endif;
	}

	/**
	 * Save meta box data.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  int $post_id
	 * @return int|void
	 */
	public function save( $post_id ) {
		global $post, $messages;

		if ( get_post_type() !== $this->post_type ) {
			return $post_id;
		}

		if ( ! isset( $_POST[ 'starter_plugin_' . $this->post_type . '_noonce' ] ) || ! wp_verify_nonce( $_POST[ 'starter_plugin_' . $this->post_type . '_noonce' ], plugin_basename( dirname( Starter_Plugin()->plugin_path ) ) ) ) { // phpcs:ignore
			return $post_id;
		}

		if ( isset( $_POST['post_type'] ) && 'page' === esc_attr( $_POST['post_type'] ) ) { // phpcs:ignore
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$field_data = call_user_func( $this->fields_callback );
		$fields     = array_keys( $field_data );

		foreach ( $fields as $f ) {
			${$f} = wp_strip_all_tags( trim( $_POST[ $f ] ) ); // phpcs:ignore

			if ( 'url' === $field_data[ $f ]['type'] ) {
				${$f} = esc_url( ${$f} );
			}

			if ( '' === get_post_meta( $post_id, '_' . $f ) ) {
				add_post_meta( $post_id, '_' . $f, ${$f}, true );
			} elseif ( get_post_meta( $post_id, '_' . $f, true ) !== ${$f} ) {
				update_post_meta( $post_id, '_' . $f, ${$f} );
			} elseif ( '' === ${$f} ) {
				delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
			}
		}
	}
}
