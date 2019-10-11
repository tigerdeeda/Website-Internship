<?php
for ($i = 1; $i < 4; $i++) :
$title= esc_html(get_theme_mod('seller_showcase_title'.$i))?>
<div id="showcase">
    <div class="container">
			<div class='col-md-4 col-sm-4 showcase'><figure><div><a href='".esc_url($showcase['url'))."'><img alt= "<?php echo $title;?>" src='<?php echo get_template_directory_uri()."/defaults/images/dimg7.jpg"; ?>'><div class='showcase-caption'><div class='showcase-caption-title'><?php esc_html_e('Retina Ready','seller') ?></div><div class='showcase-caption-desc'><?php esc_html_e('Works Like a Charm on Super HD Devices.','seller') ?></div></div></a></div></figure></div>
			<div class='col-md-4 col-sm-4 showcase'><figure><div><a href='".esc_url($showcase['url'))."'><img alt= "<?php echo $title;?>" src='<?php echo get_template_directory_uri()."/defaults/images/dimg5.jpg"; ?>'><div class='showcase-caption'><div class='showcase-caption-title'><?php esc_html_e('Translation Ready','seller') ?></div><div class='showcase-caption-desc'><?php esc_html_e('Convert the Theme Easily in Your Own Language.','seller') ?></div></div></a></div></figure></div>
			<div class='col-md-4 col-sm-4 showcase'><figure><div><a href='".esc_url($showcase['url'))."'><img alt= "<?php echo $title;?>" src='<?php echo get_template_directory_uri()."/defaults/images/dimg4.jpg"; ?>'><div class='showcase-caption'><div class='showcase-caption-title'><?php esc_html_e('Retina Ready','seller') ?></div><div class='showcase-caption-desc'><?php esc_html_e('View it on iPhone','seller') ?></div></div></a></div></figure></div>
     </div>   
</div><!--.showcase-->
<?php endfor;?>