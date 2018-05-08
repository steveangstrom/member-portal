<?php

$id = get_the_ID();

$chosen_sidebar = get_post_meta(get_the_ID(), "qode_show-sidebar", true);
$default_array = array('default', '');

if(!in_array($chosen_sidebar, $default_array)){
	$sidebar = get_post_meta(get_the_ID(), "qode_show-sidebar", true);
}else{
	$sidebar = $qode_options_proya['blog_single_sidebar'];
}

$blog_hide_comments = "";
if (isset($qode_options_proya['blog_hide_comments']))
	$blog_hide_comments = $qode_options_proya['blog_hide_comments'];

if(get_post_meta($id, "qode_page_background_color", true) != ""){
	$background_color = get_post_meta($id, "qode_page_background_color", true);
}else{
	$background_color = "";
}

$content_style_spacing = "";
if(get_post_meta($id, "qode_margin_after_title", true) != ""){
	if(get_post_meta($id, "qode_margin_after_title_mobile", true) == 'yes'){
		$content_style_spacing = "padding-top:".esc_attr(get_post_meta($id, "qode_margin_after_title", true))."px !important";
	}else{
		$content_style_spacing = "padding-top:".esc_attr(get_post_meta($id, "qode_margin_after_title", true))."px";
	}
}

?>
<?php get_header(); ?>

    <div id="content">

        <div id="content_inner" class="wrap group cf">
        
        <div class="title_outer title_without_animation" data-height="150">
        <div class="" style="height:150px;">
        <div class="image not_responsive"></div>
        </div>
        </div><!--  bridge thing -->
        
        
           <div class="container">
           <div class="container_inner default_template_holder">
           
            <div class="two_columns_66_33 background_color_sidebar grid2 clearfix">
                <div class="column1">
                                
                        <div class="column_inner">

					<?php if (have_posts()) : while (have_posts()) : the_post(); 
                    $thisID=get_the_ID();	
          
                    ?>
                            
               		    <article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

      				              <header class="article-header">
                    <?php /*pheriche_breadcrumbs();*/?>
                   <!--   <h1 class="entry-title single-title " itemprop="headline"><?php echo the_title(); ?></h1> -->
                    </header>
    
                    <section class="entry-content cf group" itemprop="articleBody">
               
                      <?php
      
                        the_content();
    
                        wp_link_pages( array(
                          'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'pherichetheme' ) . '</span>',
                          'after'       => '</div>',
                          'link_before' => '<span>',
                          'link_after'  => '</span>',
                        ) );
                      ?>
                    </section> <?php // end article section ?>
    
            
                             <?php edit_post_link('Edit this entry','','.'); ?>
                    </article>
   		 </div><!-- close column 1 inner -->
    </div><!-- close column 1  -->

 			 	 <div class="column2">
                    <div class="column_inner">
                     <div class="sidebar-head">
                     <h3>Other Events</h2>
                     </div>
                     <?php 
					 
					// echo do_shortcode('[events show="6" excerptlength="5" ]');
					 
					  ?>
                     
                     
                    </div><!-- close column 2 inner -->
                </div><!-- close column 2  -->
                
						<?php endwhile; ?>

						<?php else : ?>

							<article id="post-not-found" class="hentry cf">
									<header class="article-header">
										<h1><?php _e( 'Oops, Not Found!', 'pherichetheme' ); ?></h1>
									</header>
									<section class="entry-content">
										<p><?php _e( 'Uh Oh. Something is missing. Please go back to the Homepage', 'pherichetheme' ); ?></p>
									</section>
									<footer class="article-footer">
											
									</footer>
							</article>

						<?php endif; ?>

					</div><!-- end two columns -->

					
			</div><!-- end container inner -->
                
            </div><!-- end container-->

			</div><!-- end content inner -->
       	</div><!-- end content -->     

<?php get_footer(); ?>
