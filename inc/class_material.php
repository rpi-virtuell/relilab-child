<?php


class class_material {
	/************ register cpt material *******************/

	static function register_cpt_material() {

		/**
		 * Post Type: Materialien.
		 */

		$labels = [
			"name" => __( "Materialien", "custom-post-type-ui" ),
			"singular_name" => __( "Material", "custom-post-type-ui" ),
			"menu_name" => __( "Meine Materialien", "custom-post-type-ui" ),
			"all_items" => __( "Alle Materialien", "custom-post-type-ui" ),
			"add_new" => __( "Erstellen", "custom-post-type-ui" ),
			"add_new_item" => __( "Neue Material hinzufügen", "custom-post-type-ui" ),
			"edit_item" => __( "Material bearbeiten", "custom-post-type-ui" ),
			"new_item" => __( "Material hinzufügen", "custom-post-type-ui" ),
			"view_item" => __( "Material anzeigen", "custom-post-type-ui" ),
			"view_items" => __( "Materialien anzeigen", "custom-post-type-ui" ),
			"search_items" => __( "Materialien durchsuchen", "custom-post-type-ui" ),
			"not_found" => __( "Keine Materialien gefunden", "custom-post-type-ui" ),
			"not_found_in_trash" => __( "Keine Materialien im Papierkorb", "custom-post-type-ui" ),
			"parent" => __( "Übergeordnetes Material:", "custom-post-type-ui" ),
			"featured_image" => __( "Beitragsbild", "custom-post-type-ui" ),
			"set_featured_image" => __( "Beitragsbild festlegen", "custom-post-type-ui" ),
			"remove_featured_image" => __( "Beitragsbild entfernen", "custom-post-type-ui" ),
			"use_featured_image" => __( "Als Beitragsbild benutzen", "custom-post-type-ui" ),
			"archives" => __( "OER Materialien", "custom-post-type-ui" ),
			"insert_into_item" => __( "In Beitrag einfügen", "custom-post-type-ui" ),
			"uploaded_to_this_item" => __( "Zu diesem Beitrag hochgeladen", "custom-post-type-ui" ),
			"filter_items_list" => __( "Liste der OER-Materialien", "custom-post-type-ui" ),
			"items_list_navigation" => __( "Materialien list navigation", "custom-post-type-ui" ),
			"items_list" => __( "Materialien list", "custom-post-type-ui" ),
			"attributes" => __( "Materialiattribute", "custom-post-type-ui" ),
			"name_admin_bar" => __( "Material", "custom-post-type-ui" ),
			"item_published" => __( "Material veröffentlicht", "custom-post-type-ui" ),
			"item_published_privately" => __( "Material als privat veröffentlicht", "custom-post-type-ui" ),
			"item_reverted_to_draft" => __( "Material in Entwurfstatus zurückgesetzt,", "custom-post-type-ui" ),
			"item_scheduled" => __( "Material auf Termin gesetzt", "custom-post-type-ui" ),
			"item_updated" => __( "Material aktualisiert.", "custom-post-type-ui" ),
			"parent_item_colon" => __( "Übergeordnetes Material:", "custom-post-type-ui" ),
		];

		$args = [
			"label" => __( "Materialien", "custom-post-type-ui" ),
			"labels" => $labels,
			"description" => "",
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
			"exclude_from_search" => false,
			"capability_type" => array("material","materials"),
			"map_meta_cap" => true,
			"hierarchical" => true,
			"rewrite" => [ "slug" => "material", "with_front" => true ],
			"query_var" => true,
			"menu_position" => 3,
			"menu_icon" => "dashicons-media-document",
			"supports" => [ "title", "editor", "thumbnail", "excerpt", "trackbacks", "comments", "revisions", "author" ],
			"taxonomies" => [ "post_tag", "klassenstufe", "themen", "autoren", "lizenz", "editflow" ],
			"show_in_graphql" => false,
		];

		register_post_type( "material", $args );
	}



	static function add_theme_caps() {
		// gets the administrator role
		$admins = get_role( 'administrator' );

		$cts = array('materials');

		foreach ($cts as $plural){

			$admins->add_cap( 'edit_'.$plural );
			$admins->add_cap( 'edit_other_'.$plural );
			$admins->add_cap( 'edit_published_'.$plural );
			$admins->add_cap( 'read_private_'.$plural );
			$admins->add_cap( 'edit_private_'.$plural );
			$admins->add_cap( 'delete_'.$plural );
			$admins->add_cap( 'delete_private_'.$plural );
			$admins->add_cap( 'delete_others_'.$plural );
			$admins->add_cap( 'publish_'.$plural );

		}


	}
	static function on_save($data, $arr, $unfiltered){


		$description = '';

		$blocks = parse_blocks($data['post_content']);

		foreach ($blocks as $block){
			if ($block ["blockName"]=='kadence/tabs'){
				$first_tab_block = $block['innerBlocks'][0]["innerBlocks"][0];
				$description = render_block($first_tab_block);
				break;
			}
		}

		$description = strip_tags($description);


		if($description){
			$data['post_excerpt'] = $description;

		}
		return $data;
	}

	static function after_save(  $post_id,$post,$update )
	{

		$taxonomy = 'autoren';

		//var_dump(get_taxonomies());die(); return;
		if($post->post_type == 'material'){

			$autoren_slugs =array();
			//collect co-authors
			$authors = wp_get_object_terms($post->ID,'author');
			if(is_wp_error($authors)){
				$author  = wp_get_current_user();
				//add current user autoren taxonomie from user display name
				$check = term_exists( $author->user_login, $taxonomy );
				if(!$check){
					$new = wp_insert_term($author->display_name,$taxonomy, array('slug'=>$author->user_login));
				}

				$autoren_slugs[] = $author->user_login;
			}else{
				foreach ($authors as $author){

					preg_match('/\s\d+\s/',$author->description,$match);

					$user_id = intval($match[0]);
					if($user = get_userdata($user_id)){
						$author_name= $user->data->display_name;
						$author_slug= $user->data->user_login;
					}else{
						$author_name = $author->name;
						$author_slug = $author->slug;
					}

					//add current user autoren taxonomie from user display name
					$check = term_exists( $author_slug, $taxonomy );
					if(!$check){
						$new = wp_insert_term($author_name,$taxonomy, array('slug'=>$author_slug));
						if(isset($new['term_id'])){
							$id = $new['term_id'];
						}else{
							continue;
						}
					}else{
						$id = $check['term_id'];
					}
					$t = get_term($id);
					$autoren_slugs[] = $t->slug;
				}
			}




			if(count($autoren_slugs)>0){

				wp_set_post_terms( $post_id, $autoren_slugs, $taxonomy );

				update_post_meta($post_id,'autoren_slugs',$autoren_slugs);

			}


		}

	}
	static function the_content_filter($content){

		global $post;

		if(is_singular('material')){

			$autoren = get_the_term_list($post, 'autoren','',', ');


			$lizenz = get_the_term_list($post, 'lizenz');
			$lizenz =   strip_tags( $lizenz );

			$cc_url = '<a class="cc-license" href="https://creativecommons.org/licenses/%s/4.0" target="_blank">'.$lizenz.'</a>';
			$cc = str_replace('cc ', '',strtolower($lizenz));
			$cc_link = sprintf($cc_url, $cc);

			$search = ['[Lizenz]','[autoren]'];
			$repl = [$cc_link,$autoren];

			return str_replace($search,$repl, $content);

			//return do_blocks(do_shortcode(self::modify_info_tabs()));
		}else{
			return $content;
		}

	}
	/**
	 * Displays a Create OER Buttom at the top of a single material post
	 */
	static function blocksy_single_content_createoer(){
		global $post;
		if(is_singular('material') && !self::is_learnview()){
		    echo '<div class="relilab-buttons">';
			if( self::is_oer_impulse()){
				echo '<a title="OER von diesem Impuls ausgehend erstellen." class="button oercreate" href ="'.home_url().'/oer-creator/?impuls='.$post->ID.'">OER erstellen</a>';
			}else{
				echo '<a title="Dieses Material für SuS anzeigen" class="button learnview" target="_blank" href ="'.get_the_permalink().'/learnview">Zeige Lern-Sicht</a>';
			}
			echo '</div>';
        }

	}
	static function blocksy_single_content_lehrplan(){
		global $post;

		if(self::is_oer_impulse() && !self::is_learnview()){
			$lehrplan = self::relilab_get_lehrplanbezug($post);

			if($lehrplan){
				$html = '<div class="entry-content"><div class="wp-block-relilab-lehrplan"><p><strong>Bildungs-/Lehrplanbezug</strong></p>';
				$html .=  $lehrplan;
				$html .=  '</div></div>';

				echo $html;
			}

		}
	}

	/**
	 * check if loades template is learningview
	 *
	 * @return bool
	 */
	static function is_learnview(){

		global $template;
		$_REQUEST['learnview']=1;


		$bool = false;

		$template_file =   substr( strrchr($template,'/') ,1);

		if($template_file == 'learnview.php'){
			$bool = true;
		}
		return $bool;
	}

	/**
	 * check if material ist oer impuls
	 * @return bool
	 */
	static public function is_oer_impulse(){


		return(
			is_singular('material') && get_post_meta(get_the_ID(), 'oer_impuls',true)
		)? true:false;

	}

	static function copy_lehrplanbezug($post_id, $target_id){
		if( have_rows('lehrplanbezug', $post_id) ):
			while ( have_rows('lehrplanbezug', $post_id) ) : the_row();
		        $row = array();
				$row['country'] = get_sub_field('country');
				$row['kompetenzbereich'] = get_sub_field('kompetenzbereich');
				$row['bildungsplan_url'] = get_sub_field('bildungsplan_url');
				// Do something...add_sub_row($selector, $value, [$post_id]);
				add_row('lehrplanbezug', $row, $target_id);
			endwhile;
		endif;
	}

	static function shortcode_lehrplan(){
	    global $post;

		ob_start();
		if( have_rows('lehrplanbezug') ){
			if($post){
				$post->lehrplanbezug = true;
            }
		    ?>
            <ul>
				<?php while( have_rows('lehrplanbezug') ): the_row(); ?>
                    <li><a href="<?php the_sub_field('bildungsplan_url'); ?>"><?php the_sub_field('kompetenzbereich'); ?></a> (<?php the_sub_field('country'); ?>)</li>
				<?php endwhile; ?>
            </ul>
			<?php
		}
		return ob_get_clean();
	}

	static function shortcode_lehrplan_liste(){
	    $args =  array(
	            'post_status'=>'publish',
                'post_type'=>'material',
                'numberposts' => 1000
        );

		$the_query = new WP_Query($args);

		if($the_query->have_posts()){
			ob_start();

			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$klassen = get_the_term_list(get_the_ID(),'klassenstufe',' für: ',', ');
				$themen = get_the_term_list(get_the_ID(),'themen','<br>zugeordnete Themen: ',', ');
				$tags = get_the_term_list(get_the_ID(),'post_tag','<br>zugeordnete Schlagwörter: ',', ');

				if( have_rows('lehrplanbezug') ){ ?>
                    <ul>
						<?php while( have_rows('lehrplanbezug') ): the_row();

						    $uri = parse_url(get_sub_field('bildungsplan_url'));
						?>
                            <li><strong><em><?php  echo $uri['host'];?> (<?php the_sub_field('country'); ?>)</em></strong>: <br><a href="<?php the_sub_field('bildungsplan_url'); ?>"><?php the_sub_field('kompetenzbereich'); ?></a><br><?php echo $klassen.$themen.$tags;?></li>
						<?php endwhile; ?>
                    </ul>
					<?php
				}
			}
			wp_reset_query();
			return ob_get_clean();
		}else{
			wp_reset_query();
			return '';
		}
	}

	static function relilab_get_lehrplanbezug($impuls){

	    global $post;

		if (!$post || $post->lehrplanbezug === true ) {
		    return;
		}

		$the_query = new WP_Query( array('p'=>$impuls->ID,'post_status'=>'publish','post_type'=>'material', 'numberposts' => 1 ));

		if($the_query->have_posts()){
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				ob_start();
				if( have_rows('lehrplanbezug') ){ ?>
					<ul>
						<?php while( have_rows('lehrplanbezug') ): the_row(); ?>
							<li><a href="<?php the_sub_field('bildungsplan_url'); ?>"><?php the_sub_field('kompetenzbereich'); ?></a> (<?php the_sub_field('country'); ?>)</li>
						<?php endwhile; ?>
					</ul>
					<?php
				}else{
					echo '<ul><li></li></ul>';
				}
			}
			return ob_get_clean();
		}else{
			return '';
		}

	}
	static function create_new_oer(){

		$exclude_terms = array('lehrerbildung');

		$title = wp_kses_stripslashes($_GET['oertitle']);
		$description = wp_kses_post($_GET['oerdesc']);
		//$lehrplan ='<ul><li></li></ul>';
		$content= '';
		$cloud_url = isset($_GET['cloud'])?$_GET['oercloud']:'https://cloud.rpi-virtuell.de/index.php/s/9fLPFgztSSKNpA6';
		$impuls_id = isset($_GET['oerimpuls'])?intval($_GET['oerimpuls']):false;

		if($impuls_id){
			$impuls = get_post($impuls_id);
			if($impuls){
				$link = '<a class="oer-impuls-link" href="'.get_the_permalink($impuls_id).'">'.$impuls->post_title.'</a>';
				//$lehrplan = self::relilab_get_lehrplanbezug($impuls);

			}
		}

		//Vorlage importieren
		$args = array(
			'name'        => PATTERN_DIDAKTIK_INFO_SLUG,
			'post_type'   => PATTERN_POST_TYPE,
			'post_status' => 'publish   ',
			'numberposts' => 1
		);

		$posts = get_posts($args);
		if( $posts ) {
			$vorlage = $posts[0];
			$content =  wp_slash($vorlage->post_content);
		}else{
			echo 'Es wurde keine Vorlage gefunden.<br>';
			echo 'Slug: '.PATTERN_DIDAKTIK_INFO_SLUG.'<br>';

			wp_die();
		}


		$search = ['[Kurzbeschreibung]','#cloudurl','[impuls]'];
		$repl = [$description,$cloud_url,$link];

		$content = str_replace($search,$repl,$content);

		//var_dump($content);die();

		$new = array(
			'post_name' => sanitize_title($title),
			'post_type' => 'material',
			'post_title' => $title,
			'post_content' => $content,
			'post_excerpt' => $description,
		);

		$post_id = wp_insert_post($new);

		if(!is_int($post_id)){
			var_dump($post_id); die();
		}

		self::copy_lehrplanbezug($impuls_id,$post_id);

		update_post_meta($post_id, 'cloud_url',$cloud_url);
		update_post_meta($post_id, 'impulse_id', $impuls_id);
		update_post_meta($post_id, 'excerpt', $description);



		//taxonomie from oer impuls

		$taxonomies = array('post_tag','themen','klassenstufe','lizenz');

		foreach ($taxonomies as $tax_slug){

			$term_ids = [];
			$terms = get_the_terms($impuls, $tax_slug);

			foreach ($terms as $term){
				if(!in_array($term->slug,$exclude_terms)){
					$term_ids[] = $term->term_id;
				}

			}

			wp_add_object_terms( $post_id, $term_ids, $tax_slug);

		}
		//add current user autoren taxonomie from user display name
		$taxonomy = 'autoren';
		$user = wp_get_current_user();
		$term = sanitize_title($user->display_name);


		$check = term_exists( $term, $taxonomy );
		if(!$check){
			$new = wp_insert_term($user->display_name,'autoren');
			if(isset($new['term_id'])){
				$id = $new['term_id'];
			}
		}else{
			if(isset($check['term_id'])){
				$id = $check['term_id'];
			}
		}
		if($id){
			$term = wp_set_object_terms( $post_id, array(intval($id)), 'autoren',true);
		}


		wp_redirect(home_url().'/wp-admin/post.php?post='.$post_id.'&action=edit');


		die();


	}

	/**
	 * SINGLE MATERIAL
	 * print Autoren and Datum after the Post Title on single material post
	 */

	static function print_autoren_top_of_content(){
		global $post;

		if(is_singular('material')){

			$autoren = get_post_meta($post->ID, 'autoren_slugs', true);

			$links =[];
			foreach ($autoren as $autor){
				$link =  get_term_link($autor, 'autoren');
				$term = get_term_by('slug', $autor,'autoren');



				$links[] = '<a href="'.$link.'" class="ct-meta-element-author">'.$term->name.'</a>';



			}
			echo ''.
			     '<div class="meta-author" itemprop="name">'.
			     'Von '. implode(', ', $links).' &bull; '.get_the_date().
			     '</div>';


		}
	}

	/** add oermaker to co-author pluss caps **/
	static function coauthors_plus_edit_material_authors($allowed){

		global $post;

		if(in_array($post->post_type,array('post','material')) && current_user_can('edit_materials')){
			$allowed = true;
		}

		return $allowed;

	}


	/*function modify_info_tabs($content){

			$content = get_the_content();

			$remove_tabs = ['Kurzbeschreibung','Beschreibung' ];
			$blocks = parse_blocks($content);

			foreach ($blocks as $i=>$block){

				if($block['blockName']=='kadence/tabs'){


					foreach ($block['attrs']['titles'] as $n=>$title){
						if(in_array($title['text'] , $remove_tabs)){

							unset ($block['innerBlocks'][$n] );
							unset ($block['attrs']['titles'] );
							$block['innerHTML'] = preg_replace('#<li.*>'.$title['text'].'</span></a></li>#','',$block['innerHTML']);
							$block['innerContent'][0] = preg_replace('#<li.*>'.$title['text'].'</span></a></li>#','',$block['innerContent'][0]);
							$blocks[$i] = $block;

						}
					}
					break;

				}
			}

			$content =serialize_blocks($blocks);


			$tabs = explode('<!-- /wp:kadence/tabs -->', $content.'X_X_X');
			$raw_tab = $tabs[0];


			$tabs[0] = $raw_tab;

			$content = str_replace('X_X_X','',implode('<!-- /wp:kadence/tabs -->', $tabs));

			return render_content_block($content);

		}*/




}


add_action('blocksy:single:top', array('class_material','blocksy_single_content_createoer'), 20);

add_action('blocksy:single:content:bottom', array('class_material','blocksy_single_content_lehrplan'), 20);

add_action( 'init', array('class_material','register_cpt_material') );

add_action( 'admin_init', array('class_material','add_theme_caps') );

add_filter('wp_insert_post_data', array('class_material','on_save'), 999,3);

add_action( 'wp_insert_post', array('class_material','after_save'), 999, 3 );

add_filter('the_content', array('class_material','the_content_filter'));

add_action('blocksy:hero:title:after', array('class_material','print_autoren_top_of_content'));

add_action('init', function (){
	if(isset($_GET['create-oer'])){
		class_material::create_new_oer();
	}
});

add_filter('coauthors_plus_edit_authors',array('class_material','coauthors_plus_edit_material_authors'));

add_shortcode('lehrplan', array('class_material','shortcode_lehrplan'));
add_shortcode('lehrplan_liste', array('class_material','shortcode_lehrplan_liste'));
