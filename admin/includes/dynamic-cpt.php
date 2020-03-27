<?php


define( 'DBASE_NAME', plugin_basename( __FILE__ ) );
define( 'DCPT_PATH', plugin_dir_url( __FILE__ ) );

//include_once( dirname( __FILE__ ) . '/cpt-content.php' );


class dynamic_cpt {
	function __construct() {
		// save custom post type
		add_action( 'save_post', array( $this, 'cptSavePostData' ) );
		// init dynamic custom post type
		add_action( 'init', array( $this, 'initCustomPostType' ) );

		//save custom post types
		add_action( 'save_post', array( $this, 'save_cpt' ) );

		// add custom meta boxes for cpt
		add_action( 'add_meta_boxes', array( $this, 'cpt_add_meta_boxes' ) );


	}

	function initCustomPostType() {
		$labels = array(
			'name'               => _x( 'Template', 'post type general name' ),
			'singular_name'      => _x( 'Mass Page Templete', 'post type singular name' ),
			'add_new'            => _x( 'Add New Template', 'mptemplates' ),
			'add_new_item'       => __( 'Add New Template' ),
			'edit_item'          => __( 'Edit Template' ),
			'new_item'           => __( 'New Template' ),
			'all_items'          => __( 'All Templates' ),
			'view_item'          => __( 'View Template' ),
			'search_items'       => __( 'Search Templates' ),
			'not_found'          => __( 'No Templates found' ),
			'not_found_in_trash' => __( 'No Templates found in Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Mass Page Templates' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'page',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
//			'supports'           => array( 'title', 'editor' )
		);

		register_post_type( 'mptemplates', $args );

		$the_query = new WP_Query( array( 'post_type' => array( 'mptemplates' ), 'post_status' => 'publish' ) );
		while ( $the_query->have_posts() ) : $the_query->the_post();
			global $post;
			//*************************get the values
			$cp_public             = true;
			$cp_publicly_queryable = true;
			$cp_show_ui            = true;
			$cp_show_in_menu       = true;
			$cp_query_var          = true;
			$cp_rewrite            = $post->post_name;
			$cp_has_archive        = true;
			$cp_hierarchical       = false;
			$cp_capability_type    = 'page';
			$cp_s[]                = 'title';
			$cp_s[]                = 'editor';
			$cp_s[]                = 'author';
			$cp_s[]                = 'thumbnail';
			$cp_s_excerpt          = array_push( $cp_s, 'excerpt' );
			$cp_s_comments         = array_push( $cp_s, 'comments' );
			$cp_general_name       = strtolower( get_the_title( $post->ID ) );
			$cp_singular_name      =  get_the_title( $post->ID );
			$cp_add_new            = 'Add New ' . get_the_title( $post->ID );
			$cp_add_new_item       = 'Add New ' . get_the_title( $post->ID );
			$cp_edit_item          = 'Edit ' . get_the_title( $post->ID );
			$cp_new_item           = 'New ' . get_the_title( $post->ID );
			$cp_all_items          = 'All ' . get_the_title( $post->ID ) . 's';
			$cp_view_item          = 'View ' . get_the_title( $post->ID );
			$cp_search_items       = 'Search ' . get_the_title( $post->ID ) . 's';
			$cp_not_found          = 'No ' . get_the_title( $post->ID ) . 's found';
			$cp_not_found_in_trash = 'No ' . get_the_title( $post->ID ) . ' in trash';


			$menu_name = wp_strip_all_tags( $post->post_title );
			$post_type = $post->post_name;

			$labels = array(
				'name'               => $cp_general_name,
				'singular_name'      => $cp_singular_name,
				'add_new'            => $cp_add_new,
				'add_new_item'       => $cp_add_new_item,
				'edit_item'          => $cp_edit_item,
				'new_item'           => $cp_new_item,
				'all_items'          => $cp_all_items,
				'view_item'          => $cp_view_item,
				'search_items'       => $cp_search_items,
				'not_found'          => $cp_not_found,
				'not_found_in_trash' => $cp_not_found_in_trash,
				'menu_name'          => $menu_name
			);

			$args = array(
				'labels'             => $labels,
				'public'             => $cp_public,
				'publicly_queryable' => $cp_publicly_queryable,
				'show_ui'            => $cp_show_ui,
				'show_in_menu'       => $cp_show_in_menu,
				'query_var'          => $cp_query_var,
				'rewrite'            => array('slug' => $cp_rewrite),
				'capability_type'    => $cp_capability_type,
				'has_archive'        => $cp_has_archive,
				'hierarchical'       => $cp_hierarchical,
				'supports'           => array(
					'title',
					'editor',
					'excerpt',
					'author',
					'thumbnail',
					'comments',
					'revisions',
					'custom-fields',
				)
			);

			$uuid = get_post_meta( $post->ID, '_uuid', true );

			register_post_type( $uuid ?: $post->post_name, $args );

		endwhile;
	} // end dynamic custom post type init

	function cpt_add_meta_boxes() {

		$this->display_meta_selection();
		$this->display_state_selection();
		$this->display_shortcodes();

	}

	function display_state_selection() {
		$cptContent = new cpt_Content();

		add_meta_box( 'cpt_selection_id', 'Location Settings', array(
			$cptContent,
			'cptInnerCustomBox'
		), 'mptemplates', 'normal' );
	}

	function display_meta_selection() {
		$metaContent = new meta_content();

		add_meta_box( 'cpt_meta_id', 'Job Meta Settings', array(
			$metaContent,
			'metaCustomBox'
		), 'mptemplates', 'side' );

	}

	function display_shortcodes() {
		$shortcode_content = new shortcode_content();
		//$the_query = new WP_Query( array( 'post_type' => array( 'mptemplates' ), 'post_status' => 'publish' ) );
		//$post_types[] = 'mptemplates';
		//while ( $the_query->have_posts() ) : $the_query->the_post();
		//	$post_types[] = $the_query->post_type;
		//endwhile;

		add_meta_box( 'shortcode_display', 'Available Shortcodes', array(
			$shortcode_content,
			'shortcodeCustomBox',
		), 'mptemplates', 'side' );

	}


	function cptSavePostData( $post_id ) {
		global $post;
	}

	public function save_cpt() {
//        pre_print( $_POST );
	}
}

$dynamiccpt = new dynamic_cpt();
