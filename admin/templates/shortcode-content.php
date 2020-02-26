<?php


class shortcode_content {
	function shortcodeCustomBox( $post ) { ?>
		<p>You can use these shortcodes on any page or title created with the MPBUilder.</p>
		<div>
			<ul>
				<li><code>[city]</code>Use to get current city</li>
				<li><code>[state]</code>Use to get current state</li>
				<li><code>[county]</code>Use to get current county</li>
				<li><code>[breadcrumb]</code>Use to display a breadcrumb by city, state, county for
					current page.
				</li>

			</ul>

		</div>
	<?php }

}

$shortcodeContent = new shortcode_content();