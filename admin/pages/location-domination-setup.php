<?php
$my_postid    = $_GET['id'];//This is page id or post id
$content_post = get_post( $my_postid );
$content      = $content_post->post_content;

if ( get_option('mpb_location_type' ) == 1 ) {
	$total_records = 18908;
}
else {
	$api   = new LocationDominationAPI();
	$query = $api->get_cities_count( $my_postid );

	$total_records = $query[0]->total;
}
?>
<div class="contacts-upload wrap">
    <div class="contacts-uploader">
        <h2 class="title"><?php echo $_GET['title'] ?> Creation Page</h2>
        <div class="alert-box"></div>
        <div class="dev-box summary-box alert-image">
            <div class="box-title" style="color:red;">
                <span class="dashicons-before dashicons-warning span-icon"></span>
                <h3 style="color:red;">Warning</h3>
            </div>
            <div class="content">
                <div style="width: 100%;">
                    <h4>You are about to create or update <?php echo $total_records; ?> posts
                        for <?php echo $_GET['title'] ?>. Please click submit to continue. This process may take several
                        minutes depending on your server setup and internet speed. DO NOT navigate away from this page
                        during this process!</h4>
                    <form class="ajax-params">
                        <input name="title" type="hidden" value="<?php echo $_GET['title'] ?>">
                        <input name="post_ID" type="hidden" value="<?php echo $my_postid ?>">
                        <input type="hidden" name="post_content" value="<?php echo htmlentities($content); ?>">
                        <input type="submit" value="Submit" class="wisdom-run-query button button-primary">
                    </form>
                </div>
            </div>

        </div>
        <div class="row is_multiline progress-div" style="display: none;">
            <div class="col" style="width: 100%;">
                <div class="dev-box alert-image">
                    <div class="box-title">
                        <span class="dashicons-before dashicons-controls-repeat span-icon"></span>
                        <h3>Progress</h3>
                    </div>

                    <div class="wisdom-batch-progress">
                        <progress id="progressBar" max="100" value="0" data-label="0%"></progress>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    (function ($) {
        $('body').on('submit', '.ajax-params', function (e) {
            e.preventDefault();
            $('.wisdom-run-query').prop('disabled', true);
            var params = $(this).serialize();
            //$('.wisdom-batch-progress').append( '<div id="myProgress"><div id="myBar">0%</div></div><div class="spinner is-active"></div>' );
            // start the process
            self.process_offset(0, 0, params, self);
        });

        process_offset = function (offset, percentage, params, self) {
            var progressbar = $('#progressBar');
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url() ?>/wp-admin/admin-ajax.php',
                data: {
                    params: params,
                    action: '_do_batch_query',
                    offset: offset,
                    percentage: percentage,
                },
                dataType: "json",
                beforeSend: function () {
                    $('.progress-div').show();

                },
                success: function (response) {
                    if ($.isNumeric(parseInt(response.offset))) {


                        self.process_offset(parseInt(response.offset), parseInt(response.percentage), params, self);
                        progressbar.val(parseInt(response.percentage));
                        progressbar.attr('data-label', parseInt(response.percentage) + '%');

                    } else {

                        var messagestr = 'A total of ' + response.totalrecords + ' posts have been created for <?php echo $_GET['title'];?>. Click <a href="<?php echo admin_url(); ?>edit.php?post_type=<?php echo strtolower( str_replace( ' ', '', $_GET['title'] ) ); ?>">here</a> to view your new posts.';
                        $('.wisdom-batch-progress').html(messagestr);
                        $('.wisdom-run-query').hide();
                    }


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                    $('.wisdom-batch-progress').html('Error: Something went wrong.  If the problem persists please submit a ticket.');
                    $('.wisdom-run-query').prop('disabled', false);
                }
            });

        }
    }(jQuery));
</script>
<style>
    progress {
        background-color: #f3f3f3;
        border: 0;
        height: 24px;
        border-radius: 5px;
        width: 90%;

    }

    progress::-webkit-progress-bar {
        background-color: #f3f3f3;
        border: 0;
        height: 24px;
        border-radius: 5px;
        width: 100%;
    }

    progress::-webkit-progress-value {
        background-color: #19b4cf;
        border-radius: 5px;
    }

    progress::-moz-progress-bar {
        background-color: #f3f3f3;
        border: 0;
        height: 18px;
        border-radius: 9px;
        width: 90%;
    }

    progress[value]::before {
        content: '';
        position: absolute;
        top: 84px;
        left: 27px;
        border: 18px solid #19b4cf;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    progress[value]::after {
        content: attr(data-label);
        line-height: 40px;
        position: absolute;
        top: 84px;
        right: 27px;
        border: 18px solid #f3f3f3;
        width: 40px;
        height: 40px;
        border-radius: 50%;

    }

    .wisdom-batch-progress {
        text-align: center;
        padding: 20px;
    }

</style>