<?php


class class_dashboard {

	/**
	 * Register Post Type: Dashboard Infos.
	 */
	function register_dashpage() {

		$labels = [
			"name" => __( "Dashboard Infos", "custom-post-type-ui" ),
			"singular_name" => __( "Dashboard Info", "custom-post-type-ui" ),
		];

		$args = [
			"label" => __( "Dashboard Infos", "custom-post-type-ui" ),
			"labels" => $labels,
			"description" => "Jede veröffentlichte Seite wird in einem eigenen Dashboard Widget dargestellt.",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => true,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => true,
			"capability_type" => [ "dashpage", "dashpages" ],
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => [ "slug" => "dashpage", "with_front" => true ],
			"query_var" => true,
			"menu_position" => 80,
			"menu_icon" => "dashicons-editor-table",
			"supports" => [ "title", "editor" ],
			"show_in_graphql" => false,
		];

		register_post_type( "dashpage", $args );
	}

	/**
	 * Add css and js for dispalying gutenberg blocks in admin
	 */
	function add_styles_and_scripts(){

		if (is_admin()) {
			$screen = get_current_screen();

			if ($screen -> id == "dashboard") {


				wp_enqueue_style( 'blocksy-dynamic-global-css', 'https://my.relilab.org/wp-content/uploads/sites/3/blocksy/css/global.css' );

				//kadence
				wp_enqueue_style( 'kadence-blocks-infobox-css', 'https://my.relilab.org/wp-content/plugins/kadence-blocks/dist/blocks/infobox.style.build.css?ver=2.1.2' );
				wp_enqueue_style( 'kadence-blocks-tabs-css', 'https://my.relilab.org/wp-content/plugins/kadence-blocks/dist/blocks/tabs.style.build.css?ver=2.1.2' );

				//getwid
				wp_enqueue_style( 'fontawesome-free-css', 'https://my.relilab.org/wp-content/plugins/getwid/vendors/fontawesome-free/css/all.min.css?ver=5.5.0' );
				wp_enqueue_style( 'magnific-popup-css', 'https://my.relilab.org/wp-content/plugins/getwid/vendors/magnific-popup/magnific-popup.min.css?ver=1.1.0');
				wp_enqueue_style( 'getwid-blocks-css', 'https://my.relilab.org/wp-content/plugins/getwid/assets/css/blocks.style.css?ver=1.7.4' );
				wp_enqueue_style( 'ct-getwid-styles-css', 'https://my.relilab.org/wp-content/themes/blocksy/static/bundle/getwid.min.css?ver=1.8.4.4' );

				//editors kid
				wp_enqueue_style( 'editorskit-frontend-css', 'https://my.relilab.org/wp-content/plugins/block-options/build/style.build.css?ver=1.31.5' );

				//getwid scripts
				wp_enqueue_script( 'getwid-blocks-frontend-js-js', 'https://my.relilab.org/wp-content/plugins/getwid/assets/js/frontend.blocks.js?ver=1.7.4' ,null,null,true );
				wp_enqueue_script( 'kadence-blocks-tabs-js-js', 'https://my.relilab.org/wp-content/plugins/kadence-blocks/dist/kt-tabs-min.js?ver=2.1.2' ,null,null,true );
				//wp_enqueue_script( 'main-js', 'https://my.relilab.org/wp-content/themes/blocksy/static/bundle/main.min.js' ,null,null,true );


			}
			wp_enqueue_style( 'chld_thm_admin_css', trailingslashit( get_stylesheet_directory_uri() ) . 'admin.css');
		}


	}
	/**
	 *	Adds hidden content to admin_footer, then shows with jQuery, and inserts after welcome panel
	 *
	 *	@author Ren Ventura <EngageWP.com>
	 *	@see http://www.engagewp.com/how-to-create-full-width-dashboard-widget-wordpress
	 */
	function welcome() {

		// Bail if not viewing the main dashboard page
		if ( get_current_screen()->base !== 'dashboard' ) {
			return;
		}

		?>
        	<div class="welcome-panel-content">
                <h2>Willkommen in <strong><em>my relilab</em></strong>, deiner religionspädagogischen OER-Werkstatt!</h2>
				<p class="about-description">Wir haben einige Links zusammengestellt, um Dir den Einstieg zu erleichtern:</p>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h3>Los geht's</h3>
						<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo admin_url(); ?>/profile.php">Anpassen Deines Profils</a>
						<a class="button button-primary button-hero hide-if-customize" href="<?php echo admin_url(); ?>/profile.php">Anpassen Deines Profils</a>
						<p class="hide-if-no-customize">Ändere dein Autoren Profil Anzeigenamen</p>
					</div>
					<div class="welcome-panel-column">
						<h3>Nächste Schritte</h3>
						<ul>
							<li><a href="<?php echo admin_url(); ?>/edit.php?post_type=material&author=<?php echo get_current_user_id();?>" class="welcome-icon welcome-edit-page">Deine Materialien auflisten</a></li>
                            <li><a href="<?php echo home_url(); ?>/" class="welcome-icon welcome-add-page">OER-Impuls auswählen und Material erstellen</a></li>
                            <li><a href="<?php echo admin_url(); ?>/post-new.php?post_type=material" class="welcome-icon welcome-add-page">Ohne Vorlage beginnen</a></li>

						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h3>Weitere Aktionen</h3>
						<ul>
                            <li><a href="https://gutenberg-fibel.de/" class="welcome-icon welcome-learn-more">Arbeiten mit dem Wordpress Blockeditor</a></li>
                            <li><a href="<?php echo admin_url(); ?>/edit-comments.php" class="welcome-icon welcome-comments">Kommentare ansehen und verwalten</a></li>
                            <li><a href="https://matrix.rpi-virtuell.de/#/room/#RelilabOER:rpi-virtuell.de" class="welcome-icon dashicons-format-status">Im OER-Maker Channel nach Unterstützung fragen</a></li>

                        </ul>
					</div>
				</div>
			</div>


	<?php
	}
	function remove_dashboard_widgets() {
		global $wp_meta_boxes;

		//var_dump('<pre>',$wp_meta_boxes); die();

		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['admin_dashboard_last_edits_register']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		//unset($wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard']);

	}
	function add_dashboard_pages_widgets() {

	    $args =array(
	       'post_type' => 'dashpage',
            'post_status' => 'publish'
        );

	    $posts = get_posts($args);

		//global $widget;


	    foreach ($posts as $widget){
		    $content = apply_filters( 'the_content',$widget->post_content);
		    wp_add_dashboard_widget(
			    'dashboard_widget_post_' . $widget->ID,
			    $widget->post_title,
			    array ('class_dashboard','display_widget_content'),
                array ('class_dashboard','edit_widget_content'),
               $content,'normal','core'
		    );

	    }
	}
	static function edit_widget_content($id, $args){

	    $id = intval(str_replace('dashboard_widget_post_','', $args['id']));
	    $url = home_url().'/wp-admin/post.php?action=edit&post=';


		echo 'Inhalt des Artikel <a href="'.$url.$id.'">bearbeiten</a>';

		echo '<script>setTimeout(function(){location.href="'.$url.$id.'";},550);</script>';
		die();

	}
	static function display_widget_content($id = null,$content){
			?>
            <div class="entry-content" style="max-width:100%; overflow: hidden;">
				<?php echo $content['args'];  ?>
            </div>
			<?php

	}

	/********* remove istems from the admin bar *******************/
	function remove_toolbar_items() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_node('wp-logo');

		//var_dump($wp_admin_bar);	die();
		if (!is_super_admin()){
			//$wp_admin_bar->remove_node('site-name');
			$wp_admin_bar->remove_node('my-sites');
			$wp_admin_bar->remove_node('new-blockmeister_pattern');
			$wp_admin_bar->remove_node('vaa');
			$wp_admin_bar->remove_node('customize');

			$wp_admin_bar->remove_node('blocksy_preview_hooks');
		}


	}
	function add_toolbar_relilab($admin_bar){
		if (is_admin()) {
			$admin_bar->add_menu( array(
				'id'    => 'relilab-item',
				'title' => '<img src="' . home_url() . '/wp-content/uploads/sites/3/2021/07/myrelilab-2048x6831-1-300x100.png" style="height:32px; vertical-align: top;">',
				'href'  => home_url(),
                'parent' => 'top-secondary',
				'meta'  => array(
					'title' => 'Startseite',
				),
			) );
		}
	}
	function add_toolbar_items($admin_bar){
		$admin_bar->add_menu( array(
			'id'    => 'rpi-item',
			'title' => '<img src="' . home_url() . '/wp-content/uploads/sites/3/2021/07/rpi-virtuell-Logo-2021-offiziell-nur-Schrift-weiss-e1627078308905.png" style="height:32px;vertical-align: top; ">',
			'href'  => '#',
			'meta'  => array(
				'title' => __('Homepage rpi-virtuell'),
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'my-sub-item',
			'parent' => 'rpi-item',
			'title' => 'rpi Homepage',
			'href'  => 'https://rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'materialpool',
			'parent' => 'rpi-item',
			'title' => 'rpi  Materialpool',
			'href'  => 'https://material.rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'runet',
			'parent' => 'rpi-item',
			'title' => 'religionsunterricht.net',
			'href'  => 'https://religionsunterricht.net',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),

		));
		$admin_bar->add_menu( array(
			'id'    => 'news',
			'parent' => 'rpi-item',
			'title' => 'rpi News',
			'href'  => 'https://news.rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),

		));
		$admin_bar->add_menu( array(
			'id'    => 'matrix',
			'parent' => 'rpi-item',
			'title' => 'Element (Materix)',
			'href'  => 'https://matrix.rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'cloud',
			'parent' => 'rpi-item',
			'title' => 'rpi Cloud',
			'href'  => 'https://cloud.rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'cloud',
			'parent' => 'rpi-item',
			'title' => 'rpi Etherpad',
			'href'  => 'https://pad.rpi-virtuell.de',
			'meta'  => array(
				'title' => __('My Second Sub Menu Item'),
				'target' => '_blank',
				'class' => 'my_menu_item_class'
			),
		));
	}


}

//Dashpoard Widgets
add_action( 'init', array('class_dashboard','register_dashpage') );
remove_action( 'welcome_panel', 'wp_welcome_panel' );
add_action( 'welcome_panel', array('class_dashboard','welcome' ));
add_action( 'wp_dashboard_setup',  array('class_dashboard','add_dashboard_pages_widgets') );
add_filter('user_has_cap',function ($capabilities){
	global $pagenow;
	if ($pagenow == 'index.php'){
		$capabilities['edit_theme_options'] = true;
	}
	return $capabilities;
});

//Styling
add_action( 'admin_enqueue_scripts', array('class_dashboard', 'add_styles_and_scripts'));

//manipulita Admin menu
add_action('wp_before_admin_bar_render',  array('class_dashboard','remove_toolbar_items'), 999);
add_action('admin_bar_menu',  array('class_dashboard','add_toolbar_items'), 0);
add_action('admin_bar_menu',  array('class_dashboard','add_toolbar_relilab'), 20);


add_action('wp_dashboard_setup', array('class_dashboard','remove_dashboard_widgets'),9999 );
