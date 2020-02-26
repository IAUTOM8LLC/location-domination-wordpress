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
	    //$url = 'https://masspage.aen.technology/wp-json/mpbuilder/v1/get_selected_cities?filter='. implode( ",", $county_ids);
        //echo $url;
		?>
        <p>The below fields are used to capture the service's <em>Meta Data</em>. If you leave a field blank it will use the default value.</p>
        <fieldset >
            <label for="_service_type">Schema Type</label>
            <select name="_service_type" id="_service_type">
                <option value="7" <?php echo selected( $service_type, 7 ) ?>>Article</option>
                <option value="1" <?php echo selected( $service_type, 1 ) ?>>Event</option>
                <option value="2" <?php echo selected( $service_type, 2 ) ?>>Job</option>
                <option value="3" <?php echo selected( $service_type, 3 ) ?>>Product</option>
                <option value="4" <?php echo selected( $service_type, 4 ) ?>>Review</option>
                <option value="5" <?php echo selected( $service_type, 5 ) ?>>Recipe</option>
                <option value="6" <?php echo selected( $service_type, 6 ) ?>>Creative Work</option>
            </select>
            <br/>

            <br/>
            <label for="_service_title">Service Title</label>
            <input type="text" name="_service_title" id="_service_title" value="<?php echo $service_title; ?>">
            <span style="font-size: 80%;">This Will Be Appended to The Pages Title</span>
            <br/>
            <br/>
            <label for="_service_description">Service Description</label>
            <input type="text" name="_service_description" id="_service_description" value="<?php echo $service_description; ?>">
            <br/>
        </fieldset>
        <p>Note: Many of the schema fields are derived automatically for each page when the template is saved depending on the schema type selected. Location information is based on the associated pages location information.</p>
        <style>
            fieldset label, fieldset input{display:block;}
        </style>
		<?php
	}

}

$metaContent = new meta_content();