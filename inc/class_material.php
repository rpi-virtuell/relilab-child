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
                        wp_update_term($id,'autoren',array('name'=>$author_name));
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

            $li = new class_license();


            $content = preg_replace('/\[Lizenz[^\]]*\]/', $li->get_box() ,$content);



            return self::get_impuls_video().$content;

        }else{
            return $content;
        }

    }

    static function get_impuls_video(){
        $url = get_post_meta(get_the_ID(),'impulsvideo_url',true);

        if($url){
            return  wp_oembed_get($url);
        }
        return '';
    }

    /**
     * Select Impuls H5P
     * @param $field
     *
     * @return mixed
     */
    static function acf_load_h5p_impuls_field_choices( $field ) {

        return $field;


        //havy errors!!!


        global $wpdb, $post;
        if(!$post){
            return $field ;
        }
        // reset choices
        $field['choices']=array();


        // get the textarea value from options page without any formatting
        $choiced = get_field('h5p_impuls', $post->ID, true);



        $h5ps = $wpdb->get_results("SELECT id,title FROM {$wpdb->prefix}h5p_contents", OBJECT );


        foreach ($h5ps as $h5p){

            $field['choices'][$h5p->id]= $h5p->title;
        }


        // return the field
        return $field;

    }



    /**
     * Displays a Create OER Buttom at the top of a single material post
     */
    static function blocksy_single_content_createoer(){
        global $post;
        if(is_singular('material') && !self::is_learnview()){
            echo '<div class="relilab-buttons">';
            $embed = self::shortcode_oer_embed_button();
            //echo '<a title="Dieses Material für SuS anzeigen" class="button learnview" target="_blank" href ="'.get_the_permalink().'learnview">Zeige Lern-Sicht</a>';
            echo $embed;
            echo '<span title="Dieses Material als Lernmedium einbetten oder verlinken" class="button learnview einbetten" target="_blank" href ="#embed"><span class="dashicons dashicons-welcome-view-site"></span></span>';

            if( self::is_oer_impulse()){
                //echo '<a title="OER von diesem Impuls ausgehend erstellen." class="button oercreate" href ="'.home_url().'/oer-creator/?impuls-title='.urlencode('Kopie von '.$post->post_title).'&impuls='.$post->ID.'">OER erstellen</a>';
                echo '<a title="OER von diesem Impuls ausgehend erstellen" class="button oercreate" href ="'.home_url().'/oer-creator/?impuls-title='.urlencode('Kopie von '.$post->post_title).'&impuls='.$post->ID.'"><span class="dashicons dashicons-welcome-write-blog"></span></a>';
            }else{

                echo '<a title="Kopieren und verändern" class="button oerclone" href ="'.home_url().'/oer-maker-eingabebestaetigung/?copy-oer=1&create-oer=1&oertitle='.urlencode('Kopie von '.$post->post_title).'&oerimpuls='.$post->ID.'"><span class="dashicons dashicons-admin-page"></span></a>';
            }
            echo '</div>';
        }

    }
    static function blocksy_single_content_impuls_video(){

        global $post;
        if($post && is_singular('material') && !self::is_learnview()){

            $impulsh5p = get_post_meta($post->ID,'h5p_impuls',true);
            if($impulsh5p){
                echo  do_shortcode( '<div class="entry-content" style="margin-bottom: 50px;">'.$impulsh5p.'</div>' );
            }
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
    static function shortcode_impulse(){
        global $post;

        $clone_id = get_post_meta(get_the_ID(),'impulse_id',true);

        if($clone_id) {

            $clone = get_post( $clone_id );

            return '"<a href="'.get_the_permalink($clone_id).'">' .$clone->post_title.'</a>"';
        }
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

        if(!is_user_logged_in()){
            //echo 'nicht angemeldet';
            return false;
        }

        $exclude_terms = array('lehrerbildung');
        $copy = false;

        if(isset($_GET['copy-oer'])){
            $copy = true;
        }

        $content= '';
        $cloud_url = isset($_GET['oercloud'])?$_GET['oercloud']:'';
        $impuls_id = isset($_GET['oerimpuls'])?intval($_GET['oerimpuls']):false;
        $title = wp_kses_stripslashes($_GET['oertitle']);


        $copy_content = null;

        if($impuls_id && $impuls_id>0){
            $impuls = get_post($impuls_id);
            if($impuls && $copy === true){

                $copy_content = $impuls->post_content;

            }elseif ($impuls){
                $link = '<a class="oer-impuls-link" href="'.get_the_permalink($impuls_id).'">'.$impuls->post_title.'</a>';
            }
        }

        if($copy_content === null){

            $description = $_GET['oerdesc'];

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
                $content =  $vorlage->post_content;
            }else{
                echo 'Es wurde keine Vorlage gefunden.<br>';
                echo 'Slug: '.PATTERN_DIDAKTIK_INFO_SLUG.'<br>';

                wp_die();
            }


            $pattern_id = get_post_meta($impuls_id,'vorlage',true);


            $pattern = get_post($pattern_id);
            if( $pattern ) {
                $content .=  $pattern->post_content;
            }

            $cloudtree = '';

            if($cloud_url){

                $cloudtree = '<!-- wp:paragraph --><p><strong>Cloudordner</strong></p><!-- /wp:paragraph -->'."\n\n";

                $cloudtree .= '<!-- wp:lazyblock/nextcloud-tree {"url":"'.$cloud_url.'","allowviewer":true,"blockId":"Z1fsOI4","blockUniqueClass":"lazyblock-nextcloud-tree-Z1fsOI4"} /-->';

            }


            $content = str_replace('<!-- wp:html -->'."\n".'cloud'."\n".'<!-- /wp:html -->', $cloudtree, $content);

            $search = ['[Kurzbeschreibung]','[impuls]'];
            $repl = [$description,$link];

            $content = str_replace($search,$repl,$content);

        }else{

            $content = $copy_content;
            $description = $impuls->post_excerpt;
        }

        //var_dump('<pre>',htmlentities($content));die();

        $new = array(
            'post_name' => sanitize_title($title),
            'post_type' => 'material',
            'post_title' => $title,
            'post_content' => wp_slash($content),
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
     * SINGLE MATERIAL (learnview):
     * print Autoren and Datum after the Post Title on single material post
     */

    static function print_autoren_top_of_content(){
        global $post;

        if(is_singular('material') && self::is_learnview()){

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

        $current_user = wp_get_current_user();

        $allowed = isset( $current_user->allcaps['edit_materials'] ) ? true : $allowed;

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


    static function shortcode_oer_embed_button(){

        $title = 'Dieses Lernmedium für Lernende';
        $id = 'oer-material-'.get_the_ID();
        $script = '<iframe style="border:0;" id="'.$id.'" frameBorder="0" scrolling="no"></iframe>';
        $script .= '<script src="'.get_stylesheet_directory_uri().'/js/cloudframe.js"></script>';
        $script .= "<script>document.getElementById('".$id."').src='".get_the_permalink()."learnview'; setTimeout(iFrameResize,1000);</script>";

        $block = '<!-- wp:bod/modal-block {"title":"'.$title.'","showOn":"selector","btnBackgdColor":"rgba(160,63,63,0.83)","textAlign":"right","triggerSelector":"einbetten","modalSize":"size-l","modalPadding":"5%","titleColor":"rgba(255,255,255,1)","titleBackgdColor":"rgba(61,0,94,1)","titlePadding":"5%","showCloseBtn":"yes","btnCloseLabel":"X","btnCloseBackgdColor":"rgba(108,0,0,0.1)","btnCloseAlign":"right","className":"bod-block-popup-overlay"} -->
                    <div class="wp-block-bod-modal-block bod-block-popup align-right bod-block-popup-overlay"><span class="bod-block-popup-trigger type_selector" data-selector="einbetten"></span><div style="background-color:rgba(0, 0, 0, 0.1)" class="bod-block-popup-overlay"></div><div role="dialog" aria-modal="false" aria-labelledby="" aria-describedby="" class="bod-block-popup-wrap"><div style="background-color:#ffffff;border-radius:10px " class="bod-block-popup size-l"><div id="" style="background-color:rgba(61,0,94,1);padding:5% " class="bod-modal-title"><h2 style="color:rgba(255,255,255,1)">'.$title.'</h2></div> <div id="" style="padding:5% " class="bod-modal-content">
                    <!-- wp:paragraph -->
                    <p><a title="Dieses Material für SuS anzeigen" style="float: right;" class="button learnview" target="_blank" href ="'.get_the_permalink().'learnview">Als Lernmedium öffnen</a></p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph -->
                    <p><strong>Einbetten:</strong></p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph -->
                    <p>Kopiere den folgenden <strong>HTML-Code</strong> und füge ihn in dein LMS, CMS oder deine Webseite ein</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:html -->
                    <textarea style="width:100%; height:125px">'.$script.'</textarea>
                    <p><br>Oder kopiere folgende <strong>Url zur Lernansicht</strong> und füge sie als Link in einen Arbeitsauftrag ein</p>
                    <input style="width:100%;font-family:Verdana;font-size: 14px;padding: 5px 15px; border:1px solid  var(--form-field-border-initial-color); border-radius: 3px;" value="'.get_the_permalink().'learnview">
                    <p><br>Zusätzlich kannst du diesen <strong>QR-Code</strong> kopieren. Lernende können ihn über Tablet oder Smartphone scannen und das Lernmedium öffnen</p>
                    [qrcode size="5"]'.get_the_permalink().'learnview[/qrcode]
                    <!-- /wp:html -->
                    <!-- wp:paragraph -->
                    
                    <hr>
                    <!-- /wp:paragraph -->
                    <div class="bod-block-close-btn align-right"><button type="button" style="background-color:rgba(108,0,0,0.8);color:#ffffff" class="type_btn bod-btn">X</button></div></div> </div> <div class="bod-block-popup-closer"></div></div></div>
                    <!-- /wp:bod/modal-block -->';



        return do_shortcode($block);


    }

    static function print_autoren_taxonomy_description_from_profile(){

        if(is_tax('autoren')){

            $q_object = get_queried_object();
            $user = get_user_by('login', $q_object->slug);
            $src = get_avatar_url($user->user_email);

            if($src){
                $img = '<img class="archive-avatar" src="'.$src.'">';
            }

            $author = '<h1>Materialien von '.$q_object->name.'</h1><p>'.$user->user_description.'</p>';

            echo '
                    <div class="archive-author-profile">
                            <div class="ap-col-left">'.$img.'</div>
                            <div class="ap-col-right">'.$author.'</div>
                    </div>';


        }


    }

    static function print_autoren_archive_title($title, $old_title,$prefix){

        if(is_tax('autoren')){
            return false;
        }elseif(is_tax('themen')){
            return 'Material im Bereich '. $old_title;
        }elseif(is_tax('lizenz')){
            return 'Material unter '. $old_title;
        }elseif(is_tax('klassenstufe')) {
            return 'Material für ' . $old_title;
        }elseif(is_archive()) {

            global $wp_query;

            $meta_query = (array) $wp_query->get( 'meta_query' );

            if(isset($meta_query[0]['key']) && $meta_query[0]['key'] === 'oer_impuls'){

                $page = get_page_by_path('impulse');
                $title = ($page)?$page->post_title:'OER Maker Impulse';

                return $title;
            }elseif ($prefix == 'Archive:'){

                if($wp_query->get( 'post_type' )=='material'){

                    if(isset($_GET['s'])){
                        return 'Suche in Materialien nach "'.$_GET['s'].'"';
                    }

                    return 'Freie Bildungsmedien (OER)';
                }

            }


        }
        return $title;



    }
    static function impulse_title($title) {
        if ( is_page( 'impulse' ) ) {
            return 'OER Maker Impulse';
        }
        return $title;
    }


    static function material_query_settings($query){
        if (  !is_search() && is_home() && $query->is_main_query() ) {

            //Materialkachel auf der Homes anzeigen, die OER-Impulse sind

            $meta_query = (array) $query->get( 'meta_query' );

            $meta_query[] = array(
                'key'     => 'oer_impuls',
                'value'   => 0,
                'compare' => '>',
            );
            $query->set( 'meta_query', $meta_query );
            $query->set( 'post_type', 'material' );

        }elseif ($query->is_main_query() && (is_tag())){

            //hack um die Anzeige aller Inhalte in der Materialkarten Ansicht zu erzwingen
            $tag = $query->get('tag');
            $taxquery = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'klassenstufe',
                    'field' => 'slug',
                    'terms' => 'stufe-gibt-es-nicht',
                    'operator'=> 'NOT IN'
                ),
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $tag,
                    'operator'=> 'IN'
                )
            );

            $query->set('tax_query',$taxquery);


            $query->tax_query->relation ='OR';
            $query->set( 'post_type', array('material', 'post') );


        }elseif ($query->is_main_query() && (is_search())){


            $s = $query->get('s');
            $query->set('s',false);


            $args = array(
                'post_type' => array('material','post'),
                'post_status' => 'publish',
                's' => $s
            );

            $posts = get_posts($args);
            $IDs = array();
            foreach ($posts as $post){
                array_push($IDs,$post->ID);
            }

            global $wp_query;

            $args = array(
                'post__in' => $IDs,
                'post_type' => 'material',
            );
            $wp_query = new WP_Query($args);

        }elseif ($query->is_main_query() && is_singular('page') && $query->get('pagename')==='impulse'){

            global $wp_query;

            $meta_query = array(array(
                'key'     => 'oer_impuls',
                'value'   => 0,
                'compare' => '>',
            ));

            $args = array(
                'post_type' => 'material',
                'meta_query' => $meta_query
            );

            $wp_query = new WP_Query($args);


        }elseif ($query->is_main_query() && is_singular('page') && $query->get('pagename')==='posts'){

            global $wp_query;

            $args = array(
                'post_type' => 'post',
            );

            $wp_query = new WP_Query($args);

        }
    }


    static function blocksy_single_content_cloned(){

        $clone_id = get_post_meta(get_the_ID(),'impulse_id',true);

        if($clone_id && !self::is_learnview()){

            $clone = get_post($clone_id);

            $html = '<div class="entry-content">';
            $html .=  '<p>Dieses Material basiert auf einer Kopie der OER "<a href="'.get_the_permalink($clone_id).'">'.$clone->post_title.'</a>".</p>';
            $html .=  '</div>';

            echo $html;

        }

    }


}


add_action('blocksy:single:top', array('class_material','blocksy_single_content_createoer'), 20);
add_action('blocksy:single:content:top', array('class_material','blocksy_single_content_impuls_video'), 20);

add_action('blocksy:single:content:bottom', array('class_material','blocksy_single_content_cloned'), 20);
add_action('blocksy:single:content:bottom', array('class_material','blocksy_single_content_lehrplan'), 30);

add_action( 'init', array('class_material','register_cpt_material') );

add_action( 'admin_init', array('class_material','add_theme_caps') );

add_filter('wp_insert_post_data', array('class_material','on_save'), 999,3);

add_action( 'wp_insert_post', array('class_material','after_save'), 999, 3 );

add_filter('the_content', array('class_material','the_content_filter'));

add_action('blocksy:hero:title:after', array('class_material','print_autoren_top_of_content'));
add_action('blocksy:hero:title:after', array('class_material','print_autoren_taxonomy_description_from_profile'));

add_filter('get_the_archive_title',array('class_material','print_autoren_archive_title'),9999,3);
add_filter('the_title',array('class_material','impulse_title'),9999,3);

add_action('init', function (){
    if(isset($_GET['create-oer'])){
        class_material::create_new_oer();
    }

});

add_action( 'init', function(){
    wp_enqueue_script('embedframe', get_stylesheet_directory_uri().'/js/cloudframe.content.js',null,null,true);
});

add_filter('coauthors_plus_edit_authors',array('class_material','coauthors_plus_edit_material_authors'));

add_shortcode('lehrplan', array('class_material','shortcode_lehrplan'));
add_shortcode('impuls', array('class_material','shortcode_impulse'));
add_shortcode('lehrplan_liste', array('class_material','shortcode_lehrplan_liste'));
add_shortcode('oer_embed_button', array('class_material','shortcode_oer_embed_button'));
add_action( 'pre_get_posts', array('class_material','material_query_settings') ,1,10);
add_filter('acf/load_field/name=h5p_impuls', array('class_material', 'acf_load_h5p_impuls_field_choices' ));
