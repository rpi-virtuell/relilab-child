<?php
/*
Template Name: Full-width learnview
Template Post Type: material
*/
get_header('learnview');

if (have_posts()) {
	the_post();
}

/**
 * Note to code reviewers: This line doesn't need to be escaped.
 * Function blocksy_output_hero_section() used here escapes the value properly.
 */
if (apply_filters('blocksy:single:has-default-hero', true)) {
	echo blocksy_output_hero_section([
		'type' => 'type-2'
	]);
}

$page_structure = blocksy_get_page_structure();

$container_class = 'ct-container-full';
$data_container_output = '';

if ($page_structure === 'none' || blocksy_post_uses_vc()) {
	$container_class = 'ct-container';

	if ($page_structure === 'narrow') {
		$container_class = 'ct-container-narrow';
	}
} else {
	$data_container_output = 'data-content="' . $page_structure . '"';
}

$container_class .= ' learnview-container'
?>

<div
		class="<?php echo trim($container_class) ?>"
		<?php //echo wp_kses_post(blocksy_sidebar_position_attr()); ?>
		<?php echo $data_container_output; ?>>

		<?php do_action('blocksy:single:container:top'); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php

do_action( 'blocksy:single:top' );

echo blocksy_get_featured_image_output();
echo blocksy_output_hero_section( [
	'type' => 'type-1'
] );

do_action('blocksy:single:content:top');
?>

		<div class="entry-content">
			<?php

			$blocks = parse_blocks(get_the_content());

			foreach ($blocks as $i=>$block){
				$firsttabs = 10000;
				if ($block ["blockName"]=='kadence/tabs' && $firsttabs > $i){
					$firsttabs = $i;
					$first_tab_block = $block['innerBlocks'][0]["innerBlocks"][0];

					$description = render_block($first_tab_block);
					$label = $block['attrs']['titles'][0]['text'];

					$short_description = '<p><strong>'.$label.':</strong> '.$description.'</p>';

				}else{
					echo apply_filters( 'the_content', render_block( $block ) );
				}

			}
			?>
		</div>
	</article>

<?php
do_action('blocksy:single:content:bottom');
//blocksy_display_page_elements('separated');
have_posts();
wp_reset_query();
get_footer('learnview');
