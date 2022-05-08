<?php
/**
 * Primary controller for Recipepocket
 *
 * @package Recipepocket
 * @author  Steven Wett <stevenwett@gmail.com>
 * @version 0.0.1
 */

/**
 * The primary class for this site.
 */
class Site extends Timber\Site {
	/**
	 * User authentication status
	 *
	 * @var bool $is_authorized Is signed in.
	 */
	public $is_authorized = false;

	/**
	 * Firebase unique ID for the current user
	 * Created by Firebase
	 *
	 * @var string $firebase_uid Firebase ID for the user.
	 */
	public $firebase_uid = '';

	/**
	 * The current user record.
	 *
	 * @var mixed|object $user Current User Record.
	 */
	public $user = array();

	/** Add timber support. */
	public function __construct() {
		// Firebase auth controller.
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		// add_action( 'init', array( $this, 'register_post_types' ) );
		// add_action( 'init', array( $this, 'register_taxonomies' ) );

		require_once ROOTPATH . '/vendor/stevenwett/wp-firebase-auth/src/class-auth.php';
		$auth_controller = new \Stevenwett\WPFirebaseAuth\Auth( true, true );

		// $auth_controller->authenticate_user('stevenwett@gmail.com', '123456');
		$this->is_authorized = $auth_controller->is_authorized;
		$this->firebase_uid  = $auth_controller->firebase_uid;

		require_once get_template_directory() . '/includes/class-user-controller.php';
		$user_controller = new \Recipepocket\User_Controller( $this->is_authorized, $this->firebase_uid, true );

		$this->user = $user_controller->current_user();

		require_once get_template_directory() . '/includes/class-recipe-controller.php';
		$recipe_controller = new \Recipepocket\Recipe_Controller( true );

		// var_dump( $this->user );
		// var_dump( $this->is_authorized );

		// $auth_controller->remove_user_authentication();
		// $auth_controller->reset_password('n4K8bvHP8leV6KJybyl6R4DsDqA3', '123456');
		// $auth_controller->enable_user('n4K8bvHP8leV6KJybyl6R4DsDqA3');
		// $new_user = $auth_controller->create_firebase_user( 'stevenwett+1@gmail.com' );
		// var_dump( $new_user );

		// Removing comments.
		add_action( 'admin_init', array( $this, 'disable_comments_post_types_support' ) );
		add_filter( 'comments_open', array( $this, 'disable_comments_status' ), 20, 2 );
		add_filter( 'pings_open', array( $this, 'disable_comments_status' ), 20, 2 );
		add_filter( 'comments_array', array( $this, 'disable_comments_hide_existing_comments', 10, 2 ) );
		add_action( 'admin_menu', array( $this, 'disable_comments_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'disable_comments_admin_menu_redirect' ) );
		add_action( 'admin_init', array( $this, 'disable_comments_dashboard' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_comments_admin_bar' ) );
		add_action( 'init', array( $this, 'disable_comments_admin_bar' ) );

		// Remove head information.
		add_action( 'init', array( $this, 'remove_head_info' ) );

		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu']  = new \Timber\Menu();
		$context['site']  = $this;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}
