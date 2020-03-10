<?php


class meta_content {
	function metaCustomBox( $post ) {

	    $service_type = get_post_meta( get_the_ID(), '_service_type', true );
	    $service_title = get_post_meta( get_the_ID(), '_service_title', true );
	    $service_description = get_post_meta( get_the_ID(), '_service_description', true );
		$counties     = get_post_meta( get_the_ID(), '_selected_counties', true );

		//$county_ids = array();
        if($counties) {
	        foreach ( $counties as $county ) {
		        $county_ids[] = $county;
	        }
        }
		?>
        <p>The below fields are used to capture the service's <em>Meta Data</em>. If you leave a field blank it will use the default value.</p>
        <fieldset >
            <input name="_service_type" type="hidden" value="2" />
            <label for="_service_title">Service Title</label>
            <input type="text" name="_service_title" id="_service_title" value="<?php echo sanitize_text_field($service_title); ?>">
            <span style="font-size: 80%;">This Will Be Appended to The Pages Title</span>
            <br/>
            <br/>
            <label for="_service_description">Service Description</label>
            <input type="text" name="_service_description" id="_service_description" value="<?php echo sanitize_text_field($service_description); ?>">
            <br/>
        </fieldset>
        <p>Note: for an enriched experience consider using <a href="https://locationdomination.net/" target="_blank">Location Domination.</a></p>
        <style>
            fieldset label, fieldset input{display:block;}
        </style>
		<?php
	}

}

$metaContent = new meta_content();