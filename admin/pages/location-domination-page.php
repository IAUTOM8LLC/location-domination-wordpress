<?php
$options = get_option( 'mpb_location_type' )
?>
<div class="contacts-upload wrap">
    <div class="contacts-uploader">
        <h2 class="title">Location Domination Settings</h2>
        <div class="alert-box"></div>
        <div class="top-box summary-box">
            <div class="col-half">
                <div style="width: 100%;">
                    <h3>Settings</h3>

                </div>
            </div>
            <div class="col-half left">
                <ul class="dev-list bold">
                    <li>
                        <div>
                            <span class="list-label">Plugin Version</span>
                            <span class="list-detail">
                                <span><?php echo $this->version; ?></span>
                            </span>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="list-label">PHP Version</span>
                            <span class="list-detail">
                                <span class="def-tag tag-success"><?php echo phpversion(); ?></span>
			                </span>
                        </div>
                    </li>
                    <li>
                        <div>
                            <span class="list-label">Max Upload Size</span>
                            <span class="list-detail lastLockout"><?php echo size_format( wp_max_upload_size() ); ?></span>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <div class="row is_multiline">
            <div class="col-half">
                <div class="dev-box uploader-form">
                    <div class="box-title">
                        <span class="dashicons-before dashicons-admin-generic span-icon"></span>
                        <h3>API Settings</h3>
                        <?php


                        ?>
                    </div>
                    <form name="import" method="post" action="options.php">
                        <?php settings_fields( 'mpb-settings-group' ); ?>
                        <?php do_settings_sections( 'mpb-settings-group' ); ?>
                        <?php wp_nonce_field( 'update-options', 'my_nonce_field' ); ?>
                        <span class="input-group">
                            <label for="mpb-api-key" class="inline-label">API Key</label>
							<input
                                    type="text" name="mpb_api_key"
                                    value="<?php echo esc_attr( get_option( 'mpb_api_key' ) ); ?>" />
                            <br />
                        </span>
                        <br />
                        <input type='hidden' name='action' value='update'>
                        <input type="hidden" name="page_options" value="mpb_api_key" />
                        <div class="row end">
                            <div class="col-third tl">
                                <?php submit_button(); ?>
                            </div>
                            <div class="col-two-third tr">
                                <p class="status-text"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-half">
                <div class="dev-box alert-image">
                    <div class="box-title">
                        <span class="dashicons-before dashicons-admin-site-alt span-icon"></span>
                        <h3>Location Settings</h3>
                    </div>
                    <p>You can pull in all cities in the U.S. for all of the services you create, or you can choose to
                        drill-down to city level by service. This option <em>cannot</em> be changed once you save it, so
                        please plan accordingly.</p>
                    <form name="location_type" method="post" action="options.php" class="location-settings">
						<?php settings_fields( 'mpb-locations-group' ); ?>
						<?php do_settings_sections( 'mpb-locations-group' ); ?>
						<?php wp_nonce_field( 'update-options', 'mpb-locations-nonce' ); ?>
                        <span class="input-group">
                            <label for="mpb_location_type" class="inline-label">Set Location Type</label>

                            <select name="mpb_location_type" id="mpb_location_type" <?php if ( $options ) {
	                            echo 'disabled';
                            } ?>>
                                <option value="1" <?php if ( $options == 1 ) {
	                                echo 'selected="selected"';
                                } ?>>All Cities in the U.S.</option>
                                <option value="2" <?php if ( $options == 2 ) {
	                                echo 'selected="selected"';
                                } ?>>Drill Down by State, County, or City</option>
                            </select>

                            <br/>
                            <p class="dd-message"></p>
                            <p>
                                <?php

                                if ( isset( $options ) && $options == 1 ) {
	                                echo 'You have choosen to pull in the entire cities data.  All services created from this point on will create a page for each city.  That is roughly ~19k pages per service.  Please ensure that your server specifications can handle this.';
                                } elseif ( isset( $options ) && $options == 2 ) {
	                                echo 'You will determine the States, Counties, and/or Cities you wish to drill down to from the service template pages you create.';
                                } else {

                                }
                                ?>
                            </p>
                        </span>
                        <br/>
                        <input type='hidden' name='action' value='update'>
                        <input type="hidden" name="page_options" value="mpb_location_type"/>
                        <div class="row end">
                            <div class="col-third tl loc-submit-outer" style="display: inline-block;">
                                <input type="submit" name="submit" id="submit"
                                       class="button button-primary location-submit"
                                       value="Save Settings" <?php if ( $options  != null ) {
									echo 'disabled';
								} ?>>
                                <div class="spinner"></div>

                            </div>

                        </div>
                    </form>
                    <script>
                        (function ($) {
                            $(document).ready(function () {
                                // display spinner
                                $('.location-settings').submit(function () {
                                    /*return confirm('Are You Sure You Want to Continue? This Cannot be Undone!');*/
                                    $('.loc-submit-outer .spinner').addClass('is-active');
                                    $('.location-submit').attr("disabled", true);

                                });
                            });
                        }(jQuery));
                    </script>
                </div>
            </div>
        </div>

        <div class="col-half">
            <div class="dev-box alert-image">
                <div class="box-title">
                    <span class="dashicons-before dashicons-share-alt span-icon"></span>
                    <h3>Shortcodes</h3>

                </div>
                <p>You can use these shortcodes on any page or title created with Location Domination.</p>
                <div>
                    <ul>
                        <li><code>[city]</code>Use to get current city</li>
                        <li><code>[state]</code>Use to get current state</li>
                        <li><code>[county]</code>Use to get current county</li>
                        </li>

                    </ul>

                </div>
            </div>
        </div>

    </div>
</div>
