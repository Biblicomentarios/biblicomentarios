<?php
function irp_ui_whats_new() {
    ?>
    <style>
        .irp-grid {
            margin-left: auto;
            margin-right: auto;
            border-spacing: 10px;
        }
        .irp-grid td {
            text-align: center;
        }
        .irp-headline {
            font-size:40px;
            font-weight:bold;
            text-align:center;
            margin: 5px!important;
        }
    </style>

    <p class="irp-headline">Welcome in Inline Related Posts (v.<?php echo IRP_PLUGIN_VERSION ?>)</p>
    <p class="irp-headline" style="font-size: 35px;">Get even more page views with box themes</p>
    <div style="clear:both; height:30px;"></div>

    <div style="text-align:center; width:auto;">
        <img src="<?php echo IRP_PLUGIN_ASSETS ?>landing/mac-irl-wahtsnew.png" />
    </div>
    <div style="clear:both; height:30px;"></div>

    <table border="0" class="irp-grid">
        <tr>
            <td><img src="<?php echo IRP_PLUGIN_ASSETS ?>landing/img-whatsnew.001.jpg" /></td>
            <td><img src="<?php echo IRP_PLUGIN_ASSETS ?>landing/img-whatsnew.002.jpg" /></td>
            <td><img src="<?php echo IRP_PLUGIN_ASSETS ?>landing/img-whatsnew.003.jpg" /></td>
        </tr>
        <tr>
            <td>Box Themes</td>
            <td>More colors</td>
            <td>Featured Image</td>
        </tr>
    </table>
    <div style="clear:both"></div>

    <hr/>

    <p class="irp-headline">Get gorgeous themes with Inline Related Posts PRO</p>
    <table border="0" class="irp-grid">
        <tr>
            <td style="text-align:left;">
                <?php irp_notice_pro_features() ?>
            </td>
            <td>
                <div style="border:1px dashed red; padding:10px;">
                    <iframe width="560" height="350" src="https://www.youtube.com/embed/CjdTr14Nd1g" frameborder="0" allowfullscreen></iframe>
                </div>
            </td>
        </tr>
    </table>

    <table border="0" class="irp-grid">
        <tr>
            <td style="text-align: right; vertical-align: top;">
                <form method="get" action="<?php echo IRP_PAGE_SETTINGS?>">
                    <input type="hidden" name="page" value="<?php echo IRP_PLUGIN_SLUG?>" />
                    <input type="submit" class="button" value="CONTINUE USING FREE VERSION" />
                </form>
            </td>
            <td style="text-align: left; vertical-align: top;">
                <form method="get" action="<?php echo IRP_PAGE_PREMIUM?>">
                    <input type="hidden" name="utm_source" value="free-users" />
                    <input type="hidden" name="utm_medium" value="irp-whatsnew" />
                    <input type="hidden" name="utm_campaign" value="IRP" />
                    <input type="submit" class="button-primary" value="UPGRADE TO PREMIUM NOW ››" />
                </form>
                <a href="<?php echo irp_preview_link()?>" target="_blank">or preview your posts with PRO themes</a>
            </td>
        </tr>
    </table>
<?php }

function irp_preview_link() {
    global $irp;

    $options=array(
        'count'=>1
        , 'array'=>TRUE
        , 'numberposts'=>5
    );
    $posts=wp_get_recent_posts($options);
    $ids=array();
    foreach($posts as $p) {
        $ids[]=$p['ID'];
    }
    shuffle($ids);
    $options=irp_ui_get_box($ids, $options);

    $options=array(
        'ctaText'=>urlencode($options['ctaText'])
        , 'postTitle'=>urlencode($options['postTitle'])
        , 'postImageUrl'=>urlencode($options['postImageUrl'])
    );
    $uri=IRP_PAGE_PREMIUM;
    $uri=add_query_arg($options, $uri);
    return $uri;
}