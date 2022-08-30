<?php
/**
 * ZenEarth: Customizer
 */

if ( ! function_exists( 'zenearth_customize_register' ) ) :
	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	function zenearth_customize_register( $wp_customize ) {

		/**
		 * Add Slider Section
		 */
		$wp_customize->add_section(
			'zenearth_slider_section',
			array(
				'title'       => __( 'Slider', 'zenearth' ),
				'capability'  => 'edit_theme_options',
			)
		);
		
		// Add display slider option
		$wp_customize->add_setting(
				'zenearth_slider_display',
				array(
						'default'           => 0,
						'sanitize_callback' => 'zenearth_sanitize_checkbox',
				)
		);

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'zenearth_slider_display',
								array(
									'label'          => __( 'Display Slider on a Static Front Page', 'zenearth' ),
									'section'        => 'zenearth_slider_section',
									'settings'       => 'zenearth_slider_display',
									'type'           => 'checkbox',
								)
							)
		);

		for ($i = 1; $i <= 5; ++$i) {
		
			$slideImageId = 'zenearth_slide'.$i.'_image';
			$defaultSliderImagePath = get_template_directory_uri().'/images/slider/'.$i.'.jpg';
			
			// Add Slide Background Image
			$wp_customize->add_setting( $slideImageId,
				array(
					'default' => $defaultSliderImagePath,
					'sanitize_callback' => 'zenearth_sanitize_url'
				)
			);

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $slideImageId,
					array(
						'label'   	 => sprintf( esc_html__( 'Slide #%s Image', 'zenearth' ), $i ),
						'section' 	 => 'zenearth_slider_section',
						'settings'   => $slideImageId,
					) 
				)
			);
		}

		/**
		 * Add Footer Section
		 */
		$wp_customize->add_section(
			'zenearth_footer_section',
			array(
				'title'       => __( 'Footer', 'zenearth' ),
				'capability'  => 'edit_theme_options',
			)
		);
		
		// Add Footer Copyright Text
		$wp_customize->add_setting(
			'zenearth_footer_copyright',
			array(
			    'default'           => '',
			    'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'zenearth_footer_copyright',
	        array(
	            'label'          => __( 'Copyright Text', 'zenearth' ),
	            'section'        => 'zenearth_footer_section',
	            'settings'       => 'zenearth_footer_copyright',
	            'type'           => 'text',
	            )
	        )
		);

		/**
	     * Add Animations Section
	     */
	    $wp_customize->add_section(
	        'zenearth_animations_display',
	        array(
	            'title'       => __( 'Animations', 'zenearth' ),
	            'capability'  => 'edit_theme_options',
	        )
	    );

	    // Add display Animations option
	    $wp_customize->add_setting(
	            'zenearth_animations_display',
	            array(
	                    'default'           => 1,
	                    'sanitize_callback' => 'zenearth_sanitize_checkbox',
	            )
	    );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize,
	                        'zenearth_animations_display',
	                            array(
	                                'label'          => __( 'Enable Animations', 'zenearth' ),
	                                'section'        => 'zenearth_animations_display',
	                                'settings'       => 'zenearth_animations_display',
	                                'type'           => 'checkbox',
	                            )
	                        )
	    );
	}
endif; // zenearth_customize_register
add_action( 'customize_register', 'zenearth_customize_register' );

if ( ! function_exists( 'zenearth_sanitize_checkbox' ) ) :
	/**
	 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$checked`
	 * as a boolean value, either TRUE or FALSE.
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 */
	function zenearth_sanitize_checkbox( $checked ) {
		// Boolean check.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
endif; // zenearth_sanitize_checkbox

if ( ! function_exists( 'zenearth_sanitize_url' ) ) :

	function zenearth_sanitize_url( $url ) {
		return esc_url_raw( $url );
	}

endif; // zenearth_sanitize_url