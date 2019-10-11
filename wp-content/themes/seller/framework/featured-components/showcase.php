<?php 
if ( get_theme_mod('seller_main_showcase_enable' ) && is_front_page() ) :  ?>
	
	    <div id="showcase">
	    <div class="container">
	    	<?php
			  		for ( $i = 1; $i <= 3; $i++ ) :
			  		
			  				$url = esc_url ( get_theme_mod('seller_showcase_url'.$i) );
				  			$img = esc_url ( get_theme_mod('seller_showcase_img'.$i) );
				  			$title = esc_html( get_theme_mod('seller_showcase_title'.$i));
				  			$desc = esc_html( get_theme_mod('seller_showcase_desc'.$i));
				  			
							echo "<div class='col-md-4 col-sm-4 showcase'><figure><div><a href='".$url."'><img alt= '".$title."' src='".$img."'>";
							if ( $desc || $title ) :
								echo "<div class='showcase-caption'><div class='showcase-caption-title'>".$title."</div><div class='showcase-caption-desc'>".$desc."</div></div>";
							endif;
							echo "</a></div></figure></div>";  
					endfor;
	           ?>
	     </div>   
		</div><!--.showcase-->
	    
<?php 
endif; ?>