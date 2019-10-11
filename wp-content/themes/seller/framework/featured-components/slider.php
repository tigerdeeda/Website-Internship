<?php
if ( get_theme_mod('seller_main_slider_enable' ) || is_front_page() || is_home()) :

	$count_x = $count = get_theme_mod('seller_main_slider_count'); ?>
	
	    <div id="slider-wrapper">
	    <ul class="bxslider">
	    	<?php
			  		for ( $i = 1; $i <= $count; $i++ ) :
				  		
				  			$url = esc_url ( get_theme_mod('seller_slide_url'.$i) );
				  			$img =  get_theme_mod('seller_slide_img'.$i);
				  			$title = esc_html( get_theme_mod('seller_slide_title'.$i));
				  			$desc = esc_html( get_theme_mod('seller_slide_desc'.$i));
				  			
			  				if ($count > 5) { break; }
							echo "<li><a class='slideurl' href='".$url."'><img alt='".$title."' src='".$img."'></a>";
							if( ($desc != "") || ($title != "") ) :
								echo "<div class='slider-caption container'><div class='slider-caption-title'>".$title."</div><div class='slider-caption-desc'>".$desc."</div></div>";
							endif;
							echo "</li>";  
							if ($count > 5) 
								break;
					endfor;
	           ?>
	     </ul>   
		</div>
	    
	<?php 
endif;?>