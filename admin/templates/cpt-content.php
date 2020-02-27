<?php


class cpt_content {

	function cptInnerCustomBox($post) {
		$api = new LocationDominationAPI();
        $options = get_option( 'mpb_location_type' );


		if ( isset( $options ) && $options == 2 ) {
			?>

            <div class="col-half">
                <div class="loading-content spinner">

                </div>
                <select class="by_select hidden" data-id="<?php echo $post->ID ?>" name="by_option" id="by_option">
					<?php
					echo '<option value="2" >By State</option>';
					?>
                </select>
            </div>

            <div class="col-half left state-content" data-id="<?php echo $post->ID ?>">
				<?php
				if ( get_post_meta( get_the_ID(), '_selected_states', true ) != null ) {
					$state_value = get_post_meta( get_the_ID(), '_selected_states', true );

					$results = $api->get_all_states();

					?>
                    <fieldset>
                        <label for="selected_states">Select a State</label>
                        <select name="selected_states" class="state_select" id="selected_states">
                            <option value="0">Select a State</option>
							<?php
							foreach ( $results as $result ) {
								echo '<option value="' . $result->id . '"' . selected( $state_value, $result->id ) . '>' . $result->state . '</option>';
							}
							?>
                        </select>
                    </fieldset>
					<?php
				}
				?>

                <br/>
            </div>
            <div class="col-half left county-content" data-id="<?php echo $post->ID ?>">
				<?php
				if ( get_post_meta( get_the_ID(), '_selected_counties', true ) != null ) {
					$county_value = get_post_meta( get_the_ID(), '_selected_counties', true );
					$state_id = get_post_meta( get_the_ID(), '_selected_states', true );

					$results = $api->get_counties( $state_id );

					?>
                    <fieldset>
                        <label for="selected_counties">Select Counties</label>
                        <select name="selected_counties[]" class="county_select" id="selected_counties" multiple>

							<?php
							foreach ( $results as $result ) {
								$selected = ( in_array( $result->id, $county_value ) ) ? 'selected="selected"' :  '';
								echo '<option value="' . $result->id . '"' . $selected . '>' . $result->county . '</option>';
							}
							?>
                        </select>
                    </fieldset>
                    <p>Note: A Page for Each City in Each County You Select Will Be Created On Submit. You May Repeat This Process as Many Times As Necessary.</p>
					<?php
				}
				?>

                <br/>
            </div>
            <div class="col-half left city-content" data-id="<?php echo $post->ID ?>">
				<?php
				if ( get_post_meta( get_the_ID(), '_selected_cities', true ) != null ) {
					$city_value = get_post_meta( get_the_ID(), '_selected_cities', true );

					$county_ids = get_post_meta( get_the_ID(), '_selected_counties', true );


					$results = $api->get_selected_cities($county_ids);
					?>
                    <fieldset>
                        <select name="_selected_cities" class="city_select" id="_selected_cities" multiple>
                            <option value="0">Select a City</option>
							<?php
							foreach ( $results as $result ) {
								echo '<option value="' . $result->id . '"' . selected( $city_value, $result->id ) . '>' . $result->name . '</option>';
							}
							?>
                        </select>
                    </fieldset>
					<?php
				}
				?>

                <br/>
            </div>

            <script>
                (function ($) {
                    $(document).ready(function () {
                        if(!$('.state_select').length) {
                            updateLocationSelection();
                        }
                        $(".by_select").change(function () {
                            var str = $(this).children("option:selected").val();
                            if (str == 1) {
                                $('.state_select').remove();
                                $('.county_select').remove();
                            } else {
                                updateLocationSelection();
                            }
                        })
                        function updateLocationSelection(str) {
                            $.ajax({
                                url: '<?php echo site_url() ?>/wp-admin/admin-ajax.php',
                                data: {action: 'setlocation', state_id: str}, // form data
                                type: "post", // POST

                                beforeSend: function (xhr) {
                                    $('.loading-content').addClass('is-active');
                                },
                                success: function (data) {
                                    //$('.fwx-loader-wrap').hide();  // changing the button label back
                                    $('.state-content').html(data); // insert data
                                    $('.loading-content').removeClass('is-active');
                                },
                                error: function(){
                                    $('.loading-content').removeClass('is-active');
                                },
                            });
                            return false;
                        }

                        $(document).on("change", ".state_select", function () {
                            var str = $(this).children("option:selected").val();
                            console.log(str);
                            $('.county-content').html('');
                            $('.city-content').html('');
                            updateCounties(str);
                        })
                        $(document).on("change", ".county_select", function () {
                            var str = $(this).children("option:selected").val();
                            console.log(str);

                            //updateCities(str);
                        })

                        function updateCounties(str) {
                            $.ajax({
                                url: '<?php echo site_url() ?>/wp-admin/admin-ajax.php',
                                data: {action: 'setcounty', state_id: str}, // form data
                                type: "post", // POST

                                beforeSend: function (xhr) {
                                    $('.loading-content').addClass('is-active');
                                },
                                success: function (data) {
                                    //$('.fwx-loader-wrap').hide();  // changing the button label back
                                    $('.county-content').html(data); // insert data
                                    $('.loading-content').removeClass('is-active');
                                },
                                error: function(){
                                    $('.loading-content').removeClass('is-active');
                                },
                            });
                            return false;
                        }

                        function updateCities(str) {

                            $.ajax({
                                url: '<?php echo site_url() ?>/wp-admin/admin-ajax.php',
                                data: {action: 'setcity', county_id: str}, // form data
                                type: "post", // POST

                                beforeSend: function (xhr) {
                                    $('.loading-content').addClass('is-active');
                                },
                                success: function (data) {
                                    //$('.fwx-loader-wrap').hide();  // changing the button label back
                                    $('.city-content').html(data); // insert data
                                    $('.loading-content').addClass('is-active');
                                },
                                error: function(){
                                    $('.loading-content').removeClass('is-active');
                                },
                            });
                            return false;
                        }

                        //Require post title when adding/editing Project Summaries
                        $('body').on('submit.edit-post', '#post', function () {

                            // If the title isn't set
                            if ($("#title").val().replace(/ /g, '').length === 0) {

                                // Show the alert
                                window.alert('A title is required.');

                                // Hide the spinner
                                $('#major-publishing-actions .spinner').hide();

                                // The buttons get "disabled" added to them on submit. Remove that class.
                                $('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');

                                // Focus on the title field.
                                $("#title").focus();

                                return false;
                            }
                        });
                    });
                }(jQuery));
            </script>
			<?php
		}
		else if( isset($options) && $options == 1 ){
		    echo 'Locations Settings Set to All Cities. Note: It may take a few minutes to generate/update all of the pages when saving this template.';
        }
		else{

			echo '<h2>Locations Settings Have Not Been Configured.</h2>'.
                 'Please Set Location Settings Here: <a href="'. admin_url() .'?page=location-domination-page.php">Location Settings</a>';
        }
	}
}

$cptContent = new cpt_Content();