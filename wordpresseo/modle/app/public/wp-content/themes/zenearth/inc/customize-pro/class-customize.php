<?php

/**
 * Pro customizer section.
 */
if ( class_exists('WP_Customize_Section') ) {
	class zenearth_Customize_Section_Pro extends WP_Customize_Section {

		/**
		 * The type of customize section being rendered.
		 */
		public $type = 'zenearth';

		/**
		 * Custom button text to output.
		 */
		public $pro_text = '';

		/**
		 * Custom pro button URL.
		 */
		public $pro_url = '';

		/**
		 * Add custom parameters to pass to the JS via JSON.
		 */
		public function json() {
			$json = parent::json();

			$json['pro_text'] = $this->pro_text;
			$json['pro_url']  = esc_url( $this->pro_url );

			return $json;
		}

		/**
		 * Outputs the template.
		 */
		protected function render_template() { ?>

			<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">

				<h3 class="accordion-section-title">
					{{ data.title }}

					<# if ( data.pro_text && data.pro_url ) { #>
						<a href="{{ data.pro_url }}" class="button button-primary alignright" target="_blank">{{ data.pro_text }}</a>
					<# } #>
				</h3>
			</li>
		<?php }
	}
}

/**
 * Singleton class for handling the theme's customizer integration.
 */
final class zenearth_Customize {

	/**
	 * Returns the instance.
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 */
	public function sections( $manager ) {

		// Register custom section types.
		$manager->register_section_type( 'zenearth_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new zenearth_Customize_Section_Pro(
				$manager,
				'zenearth',
				array(
					'title'    => esc_html__( 'ZenEarthPro', 'zenearth' ),
					'pro_text' => esc_html__( 'Upgrade to Pro', 'zenearth' ),
					'pro_url'  => esc_url( 'https://zentemplates.com/product/zenearthpro' ),
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'zenearth-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/customize-pro/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'zenearth-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/customize-pro/customize-controls.css' );
	}
}

// Doing this customizer thang!
zenearth_Customize::get_instance();
