<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Starter Plugin Post Type Class
 *
 * All functionality pertaining to post types in Starter Plugin.
 *
 * @package WordPress
 * @subpackage Starter_Plugin
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Starter_Plugin_Post_Type {
	/**
	 * The post type token.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $post_type;

	/**
	 * The post type singular label.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $singular;

	/**
	 * The post type plural label.
	 * @access public
	 * @since  1.0.0
	 * @var    string
	 */
	public $plural;

	/**
	 * The post type args.
	 * @access public
	 * @since  1.0.0
	 * @var    array
	 */
	public $args;

	/**
	 * Constructor function.
	 * @access public
	 * @since 1.0.0
	 */
	public function __construct( $post_type = 'thing', $singular = '', $plural = '', $args = array() ) {
		$this->post_type  = $post_type;
		$this->singular   = $singular;
		$this->plural     = $plural;
		$this->args       = $args;

		add_action( 'init', array( $this, 'register_post_type' ) );

		// Delegate meta field registration and block editor sidebar to the dedicated class.
		new Starter_Plugin_Post_Type_Meta_Fields(
			$post_type,
			array( $this, 'get_custom_fields_settings' ),
			array( $this, 'get_field_sections' )
		);

		// Always wire up the meta box class — it guards itself inside setup() once init has run.
		if ( is_admin() ) {
			new Starter_Plugin_Post_Type_Meta_Box(
				$post_type,
				array( $this, 'get_custom_fields_settings' )
			);
		}

		if ( is_admin() ) {
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
			add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
		}

		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
		add_action( 'after_theme_setup', array( $this, 'register_image_sizes' ) );
	}

	/**
	 * Register the post type.
	 * @access public
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->singular,
			'add_new'            => _x( 'Add New', 'thing', 'starter-plugin' ), /* translators: add new post */
			'add_new_item'       => sprintf( __( 'Add New %s', 'starter-plugin' ), $this->singular ), /* translators: 'Add new' label for post type entry */
			'edit_item'          => sprintf( __( 'Edit %s', 'starter-plugin' ), $this->singular ), /* translators: 'Edit' label for post type entry */
			'new_item'           => sprintf( __( 'New %s', 'starter-plugin' ), $this->singular ), /* translators: 'New' label for post type entry containing post type singular name */
			'all_items'          => sprintf( __( 'All %s', 'starter-plugin' ), $this->plural ), /* translators: 'All' label for post type entries */
			'view_item'          => sprintf( __( 'View %s', 'starter-plugin' ), $this->singular ), /* translators: 'View' label for post type entry containing singular name */
			'search_items'       => sprintf( __( 'Search %s', 'starter-plugin' ), $this->plural ), /* translators: 'Search' label for post type entry containing plural name */
			'not_found'          => sprintf( __( 'No %s Found', 'starter-plugin' ), $this->plural ), /* translators: 'Not found' label for post type entry containing plural name */
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'starter-plugin' ), $this->plural ), /* translators: 'Not found' label for post type entry containing plural name, looking at trash */
			'parent_item_colon'  => '',
			'menu_name'          => $this->plural,
		);

		$single_slug = sanitize_title_with_dashes( $this->singular );
		$single_slug = apply_filters( 'starter_plugin_single_slug', $single_slug );

		$archive_slug = sanitize_title_with_dashes( $this->plural );
		$archive_slug = apply_filters( 'starter_plugin_archive_slug', $archive_slug );

		$defaults = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $single_slug ),
			'capability_type'    => 'post',
			'has_archive'        => $archive_slug,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-smiley',
		);

		$args = wp_parse_args( $this->args, $defaults );

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Add custom columns for the "manage" screen of this post type.
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $post;

		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		switch ( $column_name ) {
			case 'image':
				$this->get_image( $id, 40, false );
				break;

			default:
				break;
		}
	}

	/**
	 * Add custom column headings for the "manage" screen of this post type.
	 * @access public
	 * @param array $defaults
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {
		$new_columns = array( 'image' => __( 'Image', 'starter-plugin' ) );

		$last_item = array();

		if ( isset( $defaults['date'] ) ) {
			unset( $defaults['date'] ); }

		if ( count( $defaults ) > 2 ) {
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $defaults, $new_columns );

		if ( is_array( $last_item ) && 0 < count( $last_item ) ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[ $k ] = $v;
				break;
			}
		}

		return $defaults;
	}

	/**
	 * Update messages for the post type admin.
	 * @since  1.0.0
	 * @param  array $messages Array of messages for all post types.
	 * @return array           Modified array.
	 */
	public function updated_messages ( $messages ) {
		global $post, $post_ID;

		$messages[ $this->post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			/* translators: 'Updated' notice for post type entry, 1: opening anchor, 2: closing anchor, 3: singular name, 4: lowercase singular name */
			1  => sprintf( __( '%3$s updated. %1$sView %4$s%2$s', 'starter-plugin' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>', $this->singular, strtolower( $this->singular ) ),
			2  => __( 'Custom field updated.', 'starter-plugin' ),
			3  => __( 'Custom field deleted.', 'starter-plugin' ),
			/* translators: %s: date and time of the revision */
			4  => sprintf( __( '%s updated.', 'starter-plugin' ), $this->singular ),
			/* translators: 1: singular name. 2: Revision post title. */
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'starter-plugin' ), $this->singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, /* phpcs:ignore */
			/* translators: 1: singular name. 2: lowercase singular name. 3: opening anchor. 4: closing anchor. */
			6  => sprintf( __( '%1$s published. %3$sView %2$s%4$s', 'starter-plugin' ), $this->singular, strtolower( $this->singular ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
			/* translators: 1: singular name. */
			7  => sprintf( __( '%s saved.', 'starter-plugin' ), $this->singular ),
			/* translators: 1: singular name. 2: lowercase singular name. 3: opening anchor. 4: closing anchor. */
			8  => sprintf( __( '%1$s submitted. %2$sPreview %3$s%4$s', 'starter-plugin' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
			9  => sprintf(
				/* translators: 1: singular name. 2: lowercase singular name. 3: scheduled date wrapped in "strong" tags. 4: opening anchor. 5: closing anchor. */
				__( '%1$s scheduled for: %3$s. %4$sPreview %2$s%5$s', 'starter-plugin' ),
				$this->singular,
				strtolower( $this->singular ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>',
				'<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">',
				'</a>'
			),
			/* translators: 1: singular name. 2: lowercase singular name. 3: opening anchor. 4: closing anchor. */
			10 => sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s', 'starter-plugin' ), $this->singular, strtolower( $this->singular ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
		);

		return $messages;
	}

	/**
	 * Whether the block editor is active for this post type.
	 *
	 * Returns true when Gutenberg (the block editor) handles editing for this
	 * post type, false when the classic editor is in use (e.g. the Classic
	 * Editor plugin is installed and configured to use the old editor).
	 *
	 * @access protected
	 * @since  1.0.0
	 * @return bool
	 */
	protected function is_block_editor_active() {
		if ( function_exists( 'use_block_editor_for_post_type' ) ) {
			return use_block_editor_for_post_type( $this->post_type );
		}

		return false;
	}

	/**
	 * Get the section labels for the custom fields.
	 *
	 * Each key matches the 'section' value used in get_custom_fields_settings().
	 * The value is the human-readable panel title shown in the block editor sidebar.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_field_sections() {
		$sections = array(
			'info' => __( 'Details', 'starter-plugin' ),
		);

		return apply_filters( 'starter_plugin_field_sections', $sections );
	}

	/**
	 * Customise the "Enter title here" text.
	 * @access public
	 * @since  1.0.0
	 * @param string $title
	 * @return void
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() === $this->post_type ) {
			$title = __( 'Enter the thing title here', 'starter-plugin' );
		}
		return $title;
	}

	/**
	 * Get the settings for the custom fields.
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();

		$fields['url'] = array(
			'name'        => __( 'URL', 'starter-plugin' ),
			'description' => __( 'Enter a URL that applies to this thing (for example: http://domain.com/).', 'starter-plugin' ),
			'type'        => 'url',
			'default'     => '',
			'section'     => 'info',
		);

		return apply_filters( 'starter_plugin_custom_fields_settings', $fields );
	}

	/**
	 * Get the image for the given ID.
	 * @param  int 				$id   Post ID.
	 * @param  mixed 			$size Image dimension. (default: "thumbnail")
	 * @param  boolean 			$return Whether to return the result, or to output to the browser. Default: return.
	 * @since  1.0.0
	 * @return string       	<img> tag.
	 */
	protected function get_image ( $id, $size = 'thumbnail', $return = true ) {
		$response = '';

		if ( has_post_thumbnail( $id ) ) {
			// If not a string or an array, and not an integer, default to 150x9999.
			if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 150, 9999 );
			}
			$response = get_the_post_thumbnail( intval( $id ), $size );
		}

		if ( true === $return ) {
			return $response;
		} else {
			echo $response; /* phpcs:ignore */
		}
	}

	/**
	 * Register image sizes.
	 * @access public
	 * @since  1.0.0
	 */
	public function register_image_sizes () {
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( $this->post_type . '-thumbnail', 150, 9999 ); // 150 pixels wide (and unlimited height)
		}
	}

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 */
	public function activation () {
		$this->flush_rewrite_rules();
	}

	/**
	 * Flush the rewrite rules
	 * @access public
	 * @since 1.0.0
	 */
	private function flush_rewrite_rules () {
		$this->register_post_type();
		flush_rewrite_rules();
	}

	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @access public
	 * @since  1.0.0
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' ); }
	}
}
