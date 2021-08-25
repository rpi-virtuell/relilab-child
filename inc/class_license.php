<?php

class class_license {

	public $version = '4.0';
	public $langdom = 'deed.de';
	public $license;
	public $slug;

	function __construct() {

		global $post;

		$lizenz = get_the_term_list($post, 'lizenz');
		$this->lizenz =   strip_tags( $lizenz );
		$this->slug =str_replace('cc ', '',strtolower($this->lizenz));



	}

	function get_cc0(){

	    $src = 'https://licensebuttons.net/p/zero/1.0/88x31.png';
	    $url = 'https://creativecommons.org/publicdomain/zero/1.0/deed.de';
	    $lic= 'CC0 1.0';

		$format =
			'<a href="%1$s">
				<img src="%2$s" alt="%3$s">
			</a>
			<br>
			Weiternutzung als OER ausdrücklich erlaubt: Dieses Werk und dessen Inhalte sind - sofern nicht anders angegeben - lizenziert unter
			<a href="%1$s" rel="license" target="_blank">
				%3$s
			</a>.';

		return sprintf($format,$url,$src,$lic);

    }


	function get_image(){
		// https://licensebuttons.net/l/by/4.0/88x31.png
		// https://licensebuttons.net/l/by/4.0/88x31.png
	}
	function the_autoren(){

		global $post;

		$before = '<span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName dct:author">';
		$after = '</span>';

		echo get_the_term_list($post, 'autoren',$before,', ', $after);

	}

	function the_oer(){

		$link = '<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">';
		$link .= '<a href="%s" xmlns:cc="http://creativecommons.org/ns#" rel="cc:attributionURL dct:creator">%s</a>';
		$link .= ' (<a href="https://my.relilab.org" xmlns:cc="http://creativecommons.org/ns#" rel="cc:attributionURL dct:publisher"><em>my relilab</em></a>)';
		$link .= '</span>';

		echo sprintf($link, get_the_permalink(),get_the_title());

	}

	function the_base_info(){

		$format =
			'<a href="https://creativecommons.org/licenses/%1$s/%2$s/%3$s">
				<img src="https://licensebuttons.net/l/%1$s/%2$s/88x31.png" alt="%4$s %2$s">
			</a>
			<br>
			Weiternutzung als OER ausdrücklich erlaubt: Dieses Werk und dessen Inhalte sind - sofern nicht anders angegeben - lizenziert unter
			<a href="https://creativecommons.org/licenses/%1$s/%2$s/%3$s" rel="license" target="_blank">
				%4$s %2$s
			</a>.';

		echo sprintf($format,$this->slug,$this->version,$this->langdom, $this->lizenz);

	}

	function the_license(){

		$link = '<a href="https://creativecommons.org/licenses/%s/%s/%s" target="_blank">%s</a>';
		echo sprintf($link,$this->slug,$this->version,$this->langdom, $this->lizenz);
	}

	function get_box(){

	    if($this->lizenz=='CC0') return $this->get_cc0();

		ob_start();
		?>
		<div class="oer-cc-licensebox">
			<?php $this->the_base_info();?>
			Nennung gemäß <a href="https://open-educational-resources.de/oer-tullu-regel/">TULLU-Regel</a> bitte wie folgt:<br>
			<i>
				<?php $this->the_oer();?>

				von
				<?php $this->the_autoren(); ?>
				, Lizenz:
				<?php $this->the_license();?>
			</i>.
		</div>
		<?php
		return ob_get_clean();
	}
}
