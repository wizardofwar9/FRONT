<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js?ver=3.3.1'></script>
<script type='text/javascript' src='http://wp.local/wp-content/themes/capitaine/js/select2.min.js?ver=4.0.6'></script>
<script type='text/javascript' src='http://wp.local/wp-content/themes/capitaine/js/script.js?ver=1.0'></script
    <?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
  <header class="header">
    <a href="<?php echo home_url( '/' ); ?>">
      <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="Logo">
    </a> 
    <?php 
if ( is_user_logged_in() ):
	$current_user = wp_get_current_user(); 
?>
	<p>
        <?php echo $current_user->user_firstname; ?>
        <a href="<?php echo wp_logout_url(); ?>"> Déconnexion </a>
	</p>
<?php else: ?>
    <p>
        <a href="<?php echo wp_login_url(); ?>"> Connexion </a>
	</p>
<?php endif; ?>
  </header>
    <?php wp_body_open(); ?>
    