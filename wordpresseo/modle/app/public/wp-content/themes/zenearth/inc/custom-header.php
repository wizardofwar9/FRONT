<?php
/**
 * Custom header implementation
 *
 * @link https://codex.wordpress.org/Custom_Headers
 */

if ( ! function_exists( 'zenearth_custom_header_setup' ) ) :
  /**
   * Set up the WordPress core custom header feature.
   */
  function zenearth_custom_header_setup() {

  	add_theme_support( 'custom-header', array (
                         'default-image'          => '',
                         'flex-height'            => true,
                         'flex-width'             => true,
                         'uploads'                => true,
                         'width'                  => 900,
                         'height'                 => 100,
                         'default-text-color'     => '#434343',
                         'wp-head-callback'       => 'zenearth_header_style',
                      ) );
  }
endif; // zenearth_custom_header_setup
add_action( 'after_setup_theme', 'zenearth_custom_header_setup' );

if ( ! function_exists( 'zenearth_header_style' ) ) :
  /**
   * Styles the header image and text displayed on the blog.
   *
   * @see zenearth_custom_header_setup().
   */
  function zenearth_header_style() {

  	$header_text_color = get_header_textcolor();

      if ( ! has_header_image()
          && ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color
               || 'blank' === $header_text_color ) ) {

          return;
      }

      $headerImage = get_header_image();
  ?>
      <style id="zenearth-custom-header-styles" type="text/css">

          <?php if ( has_header_image() ) : ?>

                  #hdr-main {background-image: url("<?php echo esc_url( $headerImage ); ?>");}

          <?php endif; ?>

          <?php if ( get_theme_support( 'custom-header', 'default-text-color' ) !== $header_text_color
                      && 'blank' !== $header_text_color ) : ?>

                  #hdr-main {color: #<?php echo sanitize_hex_color_no_hash( $header_text_color ); ?>;}

          <?php endif; ?>
      </style>
  <?php
  }
endif; // End of zenearth_header_style.

