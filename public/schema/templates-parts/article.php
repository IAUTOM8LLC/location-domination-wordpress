<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "NewsArticle",
		"mainEntityOfPage": {
			"@type": "WebPage",
			"@id": "<?php echo the_permalink(); ?>"
		},
		"headline": "<?php echo the_title_attribute(); ?>",
		"image": [
			"<?php echo get_the_post_thumbnail( get_the_id(), 'thumbnail' ); ?>"
		],
		"datePublished": "<?php echo get_the_date('c');?>",
		"dateModified": "<?php echo the_modified_date('c');?>",
		"author": {
			"@type": "Person",
			"name": "<?php echo get_the_author(); ?>"
		},
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo bloginfo( 'name' ); ?>",
			"logo": {
				"@type": "ImageObject",
				"url": "<?php echo wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ); ?>"
			}
		},
		"description": "<?php echo get_the_excerpt(get_the_id()); ?>"
	}
</script>
