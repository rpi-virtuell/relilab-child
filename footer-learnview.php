<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Blocksy relilab Child
 */

blocksy_after_current_template();
do_action('blocksy:content:bottom');

?>
	</main>
<footer>
    <p style="text-align: center; margin-top: 10px;">Dieses Lernmedium wurde in <a href="<?php echo home_url();?>">my·relilab</a>, dem religionspädagog. Lernlab erstellt. | <a href="https://rpi-virtuell.de/impressum">Impressum</a> | <a href="https://rpi-virtuell.de/impressum">Datenschutz</a></p>

</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
