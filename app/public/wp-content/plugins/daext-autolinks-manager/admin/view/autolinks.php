<?php

if ( ! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'daext-autolinks-manager'));
}

?>

<!-- process data -->

<?php

if (isset($_POST['form_submitted'])) {

    //Sanitization -----------------------------------------------------------------------------------------------------
	$update_id    = isset( $_POST['update_id'] ) ? intval( $_POST['update_id'] ) : '';
	$name         = sanitize_text_field( $_POST['name'] );
	$category_id  = intval( $_POST['category_id'], 10 );
	$keyword      = sanitize_text_field( $_POST['keyword'] );
	$url          = esc_url_raw( $_POST['url'] );
	$title        = sanitize_text_field( $_POST['title'] );
	$open_new_tab = intval( $_POST['open_new_tab'], 10 );
	$use_nofollow = intval( $_POST['use_nofollow'], 10 );

	if ( isset( $_POST['post_types'] ) and is_array( $_POST['post_types'] ) ) {
		$post_types = $_POST['post_types'];
	}else{
		$post_types = '';
    }

	if ( isset( $_POST['categories'] ) and is_array( $_POST['categories'] ) ) {
		$categories = $_POST['categories'];
	}else{
		$categories = '';
    }

	if ( isset( $_POST['tags'] ) and is_array( $_POST['tags'] ) ) {
		$tags = $_POST['tags'];
	}else{
		$tags = '';
    }

	$term_group_id         = intval( $_POST['term_group_id'], 10 );
	$case_sensitive_search = intval( $_POST['case_sensitive_search'], 10 );
	$left_boundary         = intval( $_POST['left_boundary'], 10 );
	$right_boundary        = intval( $_POST['right_boundary'], 10 );
	$keyword_before        = sanitize_text_field( $_POST['keyword_before'] );
	$keyword_after         = sanitize_text_field( $_POST['keyword_after'] );
	$limit                 = intval( $_POST['limit'], 10 );
	$priority              = intval( $_POST['priority'], 10 );

    //Validation -------------------------------------------------------------------------------------------------------

    $invalid_data_message = '';

    //validation on "name"
    if (mb_strlen(trim($name)) === 0 or mb_strlen(trim($name)) > 100) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Name" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "keyword"
    if (mb_strlen(trim($keyword)) === 0 or mb_strlen(trim($keyword)) > 255) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Keyword" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    /*
     * Do not allow only numbers as a keyword. Only numbers in a keyword would cause the index of the protected block to
     * be replaced. For example the keyword "1" would cause the "1" present in the index of the following protected
     * blocks to be replaced with an autolink:
     *
     * - [pb]1[/pb]
     * - [b]31[/pb]
     * - [pb]812[/pb]
     */
    if(preg_match('/^\d+$/', $keyword) === 1){
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('A keyword that includes only digits is not allowed.', 'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    /*
     * Do not allow to create specific keyword that would be able to replace the start delimiter or the protected block
     * [pb], part of the start delimiter, the end delimited [/pb] or part of the end delimiter.
     */
    if(preg_match('/^\[$|^\[p$|^\[pb$|^\[pb]$|^\[\/$|^\[\/p$|^\[\/pb$|^\[\/pb\]$|^\]$|^b\]$|^pb\]$|^\/pb\]$|^p$|^pb$|^pb\]$|^\/$|^\/p$|^\/pb$|^\/pb]$|^b$|^b\$]/i', $keyword) === 1){
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('The specified keyword is not allowed.', 'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
        $specified_keyword_not_allowed = true;
    }

    /*
     * Do not allow to create specific keyword that would be able to replace the start delimiter of the autolink [al],
     * part of the start delimiter, the end delimited [/al] or part of the end delimiter.
     */
    if(!isset($specified_keyword_not_allowed) and preg_match('/^\[$|^\[a$|^\[al$|^\[al]$|^\[\/$|^\[\/a$|^\[\/al$|^\[\/al\]$|^\]$|^l\]$|^al\]$|^\/al\]$|^a$|^al$|^al\]$|^\/$|^\/a$|^\/al$|^\/al]$|^l$|^l\$]/i', $keyword) === 1){
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('The specified keyword is not allowed.', 'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "url"
    if (mb_strlen(trim($url)) === 0 or mb_strlen(trim($url)) > 2083) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "URL" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "title"
    if (mb_strlen(trim($title)) > 255) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Title" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "limit"
    if (intval($limit, 10) === 0 or intval($limit, 10) > 1000000) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Limit" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "priority"
    if (intval($priority, 10) > 1000000) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Priority" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "keyword_before"
    if (mb_strlen(trim($keyword_before)) > 255) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Keyword Before" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

    //validation on "keyword_after"
    if (mb_strlen(trim($keyword_after)) > 255) {
        $invalid_data_message .= '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('Please enter a valid value in the "Keyword After" field.',
                'daext-autolinks-manager') . '</p></div>';
        $invalid_data         = true;
    }

}

//Update or add the record in the database
if (isset($_POST['form_submitted']) and ! isset($invalid_data)) {

    if ( isset( $update_id ) and ! empty( $update_id )){

	    //Update

	    global $wpdb;
	    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
	    $safe_sql   = $wpdb->prepare("UPDATE $table_name SET 
                name = %s,
                category_id = %d,
                keyword = %s,
                url = %s,
                title = %s,
                open_new_tab = %d,
                use_nofollow = %d,
                case_sensitive_search = %d,
                `limit` = %d,
                priority = %d,
                left_boundary = %d,
                right_boundary = %d,
                keyword_before = %s,
                keyword_after = %s,
                post_types = %s,
                categories = %s,
                tags = %s,
                term_group_id = %d
                WHERE autolink_id = %d",
		    $name,
		    $category_id,
		    $keyword,
		    $url,
		    $title,
		    $open_new_tab,
		    $use_nofollow,
		    $case_sensitive_search,
		    $limit,
		    $priority,
		    $left_boundary,
		    $right_boundary,
		    $keyword_before,
		    $keyword_after,
		    maybe_serialize($post_types),
		    maybe_serialize($categories),
		    maybe_serialize($tags),
		    $term_group_id,
		    $update_id);

	    $query_result = $wpdb->query($safe_sql);

	    if ($query_result !== false) {
		    $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_html__('The autolink has been successfully updated.', 'daext-autolinks-manager') . '</p></div>';
	    }

    }else{

	    //Add

        global $wpdb;
        $table_name = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
        $safe_sql   = $wpdb->prepare("INSERT INTO $table_name SET 
            name = %s,
            category_id = %d,
            keyword = %s,
            url = %s,
            title = %s,
            open_new_tab = %d,
            use_nofollow = %d,
            case_sensitive_search = %d,
            `limit` = %d,
            priority = %d,
            left_boundary = %d,
            right_boundary = %d,
            keyword_before = %s,
            keyword_after = %s,
            post_types = %s,
            categories = %s,
            tags = %s,
            term_group_id = %d",
            $name,
            $category_id,
            $keyword,
            $url,
            $title,
            $open_new_tab,
            $use_nofollow,
            $case_sensitive_search,
            $limit,
            $priority,
            $left_boundary,
            $right_boundary,
            $keyword_before,
            $keyword_after,
            maybe_serialize($post_types),
            maybe_serialize($categories),
            maybe_serialize($tags),
            $term_group_id
        );

        $query_result = $wpdb->query($safe_sql);

        if ($query_result !== false) {
            $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_html__('The autolink has been successfully added.', 'daext-autolinks-manager') . '</p></div>';
        }

    }

}

//delete an autolink
if (isset($_POST['delete_id'])) {

    global $wpdb;
    $delete_id = intval($_POST['delete_id'], 10);

    $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
    $safe_sql     = $wpdb->prepare("DELETE FROM $table_name WHERE autolink_id = %d ", $delete_id);
    $query_result = $wpdb->query($safe_sql);

    if ($query_result !== false) {
        $process_data_message = '<div class="updated settings-error notice is-dismissible below-h2"><p>' . esc_html__('The autolink has been successfully deleted.', 'daext-autolinks-manager') . '</p></div>';
    }

}

//clone the autolink
if (isset($_POST['clone_id'])) {

    global $wpdb;
    $clone_id = intval($_POST['clone_id'], 10);

    //clone the autolink
    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
    $wpdb->query("CREATE TEMPORARY TABLE daextam_temporary_table SELECT * FROM $table_name WHERE autolink_id = $clone_id");
    $wpdb->query("UPDATE daextam_temporary_table SET autolink_id = NULL");
    $wpdb->query("INSERT INTO $table_name SELECT * FROM daextam_temporary_table");
    $wpdb->query("DROP TEMPORARY TABLE IF EXISTS daextam_temporary_table");

}

//get the autolink data
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id'], 10);
    global $wpdb;
    $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
    $safe_sql     = $wpdb->prepare("SELECT * FROM $table_name WHERE autolink_id = %d ", $edit_id);
    $autolink_obj = $wpdb->get_row($safe_sql);
}

//Get the value of the custom filter
if (isset($_GET['cf']) and $_GET['cf'] != 'all') {
    $category_id_in_cf = intval($_GET['cf'], 10);
} else {
    $category_id_in_cf = false;
}

?>

<!-- output -->

<div class="wrap">

    <div id="daext-header-wrapper" class="daext-clearfix">

        <h2><?php esc_html_e('Autolinks Manager - Autolinks', 'daext-autolinks-manager'); ?></h2>

        <!-- Search Form -->

        <form action="admin.php" method="get" id="daext-search-form">

            <input type="hidden" name="page" value="daextam-autolinks">

            <p><?php esc_html_e('Perform your Search', 'daext-autolinks-manager'); ?></p>

            <?php
            if (isset($_GET['s']) and mb_strlen(trim($_GET['s'])) > 0) {
                $search_string = sanitize_text_field($_GET['s']);
            } else {
                $search_string = '';
            }

            //Custom Filter
            if ($category_id_in_cf !== false) {
                echo '<input type="hidden" name="cf" value="' . $category_id_in_cf . '">';
            }

            ?>

            <input type="text" name="s"
                   value="<?php echo esc_attr(stripslashes($search_string)); ?>" autocomplete="off" maxlength="255">
            <input type="submit" value="">

        </form>

        <!-- Filter Form -->

        <form method="GET" action="admin.php" id="daext-filter-form">

            <input type="hidden" name="page" value="<?php echo $this->shared->get('slug'); ?>-autolinks">

            <p><?php esc_html_e('Filter by Category', 'daext-autolinks-manager'); ?></p>

            <select id="cf" name="cf" class="daext-display-none">

                <option value="all" <?php if (isset($_GET['cf'])) {
                    selected(sanitize_text_field($_GET['cf']), 'all');
                } ?>><?php esc_html_e('All', 'daext-autolinks-manager'); ?></option>

                <?php

                global $wpdb;
                $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_category";
                $safe_sql     = "SELECT category_id, name FROM $table_name ORDER BY category_id DESC";
                $categories_a = $wpdb->get_results($safe_sql, ARRAY_A);

                foreach ($categories_a as $key => $category) {

                    if (isset($_GET['cf'])) {
                        echo '<option value="' . $category['category_id'] . '" ' . selected(sanitize_text_field($_GET['cf']),
                                $category['category_id'],
                                false) . '>' . esc_html(stripslashes($category['name'])) . '</option>';
                    } else {
                        echo '<option value="' . $category['category_id'] . '">' . esc_html(stripslashes($category['name'])) . '</option>';

                    }

                }

                ?>

            </select>

        </form>

    </div>

    <div id="daext-menu-wrapper">

        <?php if (isset($invalid_data_message)) {
            echo $invalid_data_message;
        } ?>
        <?php if (isset($process_data_message)) {
            echo $process_data_message;
        } ?>

        <!-- table -->

        <?php

        //custom filter
        if ($category_id_in_cf === false) {
            $filter = '';
        } else {
            global $wpdb;
            $filter = $wpdb->prepare("WHERE category_id = %d", $category_id_in_cf);
        }

        //create the query part used to filter the results when a search is performed
        if (isset($_GET['s']) and mb_strlen(trim($_GET['s'])) > 0) {

            $search_string = sanitize_text_field($_GET['s']);
            global $wpdb;

            //create the query part used to filter the results when a search is performed
            if ((mb_strlen(trim($filter)) > 0)) {
                $filter .= $wpdb->prepare(' AND (name LIKE %s OR keyword LIKE %s OR url LIKE %s)',
                    '%' . $search_string . '%',
                    '%' . $search_string . '%',
                    '%' . $search_string . '%');
            } else {
                $filter = $wpdb->prepare('WHERE (name LIKE %s OR keyword LIKE %s OR url LIKE %s)',
                    '%' . $search_string . '%',
                    '%' . $search_string . '%',
                    '%' . $search_string . '%');
            }

        }

        //retrieve the total number of autolinks
        global $wpdb;
        $table_name  = $wpdb->prefix . $this->shared->get('slug') . "_autolink";
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $filter");

        //Initialize the pagination class
        require_once($this->shared->get('dir') . '/admin/inc/class-daextam-pagination.php');
        $pag = new daextam_pagination();
        $pag->set_total_items($total_items);//Set the total number of items
        $pag->set_record_per_page(10); //Set records per page
        $pag->set_target_page("admin.php?page=" . $this->shared->get('slug') . "-autolinks");//Set target page
        $pag->set_current_page();//set the current page number from $_GET

        ?>

        <!-- Query the database -->
        <?php
        $query_limit = $pag->query_limit();
        $results     = $wpdb->get_results("SELECT * FROM $table_name $filter ORDER BY autolink_id DESC $query_limit",
            ARRAY_A); ?>

        <?php if (count($results) > 0) : ?>

            <div class="daext-items-container">

                <!-- list of tables -->
                <table class="daext-items">
                    <thead>
                    <tr>
                        <th>
                            <div><?php esc_html_e('Autolink ID', 'daext-autolinks-manager'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The ID of the autolink.', 'daext-autolinks-manager'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e('Name', 'daext-autolinks-manager'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The name of the autolink.', 'daext-autolinks-manager'); ?>"></div>
                        </th>
                        <th>
                            <div><?php esc_html_e('Category', 'daext-autolinks-manager'); ?></div>
                            <div class="help-icon"
                                 title="<?php esc_attr_e('The category of the autolink.', 'daext-autolinks-manager'); ?>"></div>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($results as $result) : ?>
                        <tr>
                            <td><?php echo intval($result['autolink_id'], 10); ?></td>
                            <td><?php echo esc_html(stripslashes($result['name'])); ?></td>
                            <td><?php echo esc_html(stripslashes($this->shared->get_category_name($result['category_id']))); ?></td>
                            <td class="icons-container">
                                <form method="POST"
                                      action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-autolinks">
                                    <input type="hidden" name="clone_id" value="<?php echo $result['autolink_id']; ?>">
                                    <input class="menu-icon clone help-icon" type="submit" value="">
                                </form>
                                <a class="menu-icon edit"
                                   href="admin.php?page=<?php echo $this->shared->get('slug'); ?>-autolinks&edit_id=<?php echo $result['autolink_id']; ?>"></a>
                                <form id="form-delete-<?php echo $result['autolink_id']; ?>" method="POST"
                                      action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-autolinks">
                                    <input type="hidden" value="<?php echo $result['autolink_id']; ?>"
                                           name="delete_id">
                                    <input class="menu-icon delete" type="submit" value="">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Display the pagination -->
            <?php if ($pag->total_items > 0) : ?>
                <div class="daext-tablenav daext-clearfix">
                    <div class="daext-tablenav-pages">
                        <span class="daext-displaying-num"><?php echo $pag->total_items; ?>
                            &nbsp<?php esc_html_e('items', 'daext-autolinks-manager'); ?></span>
                        <?php $pag->show(); ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else : ?>

            <?php

            if (mb_strlen(trim($filter)) > 0) {
                echo '<div class="error settings-error notice is-dismissible below-h2"><p>' . esc_html__('There are no results that match your filter.', 'daext-autolinks-manager') . '</p></div>';
            }

            ?>

        <?php endif; ?>

        <div>

            <form method="POST" action="admin.php?page=<?php echo $this->shared->get('slug'); ?>-autolinks"
                  autocomplete="off">

                <input type="hidden" value="1" name="form_submitted">

                <?php if (isset($_GET['edit_id'])) : ?>

                <!-- Edit an Autolink -->

                <div class="daext-form-container">

                    <h3 class="daext-form-title"><?php esc_html_e('Edit Autolink', 'daext-autolinks-manager'); ?>&nbsp<?php echo $autolink_obj->autolink_id; ?></h3>

                    <table class="daext-form daext-form-table">

                        <input type="hidden" name="update_id"
                               value="<?php echo $autolink_obj->autolink_id; ?>"/>

                        <!-- Name -->
                        <tr valign="top">
                            <th><label for="title"><?php esc_html_e('Name', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->name)); ?>" type="text"
                                       id="name" maxlength="100" size="30" name="name"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The name of the autolink.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Category ID -->
                        <tr>
                            <th scope="row"><label for="tags"><?php esc_html_e('Category', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <?php

                                $html = '<select id="category-id" name="category_id" class="daext-display-none">';

                                $html .= '<option value="0" ' . selected($autolink_obj->category_id, 0,
                                        false) . '>' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

                                global $wpdb;
                                $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
                                $sql        = "SELECT category_id, name FROM $table_name ORDER BY category_id DESC";
                                $category_a = $wpdb->get_results($sql, ARRAY_A);

                                foreach ($category_a as $key => $category) {
                                    $html .= '<option value="' . $category['category_id'] . '" ' . selected($autolink_obj->category_id,
                                            $category['category_id'],
                                            false) . '>' . esc_html(stripslashes($category['name'])) . '</option>';
                                }

                                $html .= '</select>';
                                $html .= '<div class="help-icon" title="' . esc_attr__('The category of the autolink.', 'daext-autolinks-manager') . '"></div>';

                                echo $html;

                                ?>
                            </td>
                        </tr>

                        <!-- Keyword -->
                        <tr>
                            <th scope="row"><label for="keyword"><?php esc_html_e('Keyword', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->keyword)); ?>" type="text"
                                       id="keyword" maxlength="255" size="30" name="keyword"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The keyword that will be converted to a link.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- URL -->
                        <tr>
                            <th scope="row"><label for="url"><?php esc_html_e('URL', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->url)); ?>" type="text"
                                       id="url" maxlength="2083" size="30" name="url"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The destination address of the link automatically generated on the keyword.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- HTML Options -------------------------------------------------------------------------- -->
                        <tr class="group-trigger" data-trigger-target="html-options">
                            <th class="group-title"><?php esc_html_e('HTML', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Title -->
                        <tr class="html-options">
                            <th scope="row"><label for="title"><?php esc_html_e('Title', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->title)); ?>" type="text"
                                       id="title" maxlength="255" size="30" name="title"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The title attribute of the link automatically generated on the keyword.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Open New Tab -->
                        <tr class="html-options">
                            <th scope="row"><?php esc_html_e('Open New Tab', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <select id="open-new-tab" name="open_new_tab" class="daext-display-none">
                                    <option value="0" <?php selected(intval($autolink_obj->open_new_tab),
                                        0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                    <option value="1" <?php selected(intval($autolink_obj->open_new_tab),
                                        1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title='<?php esc_attr_e('If you select "Yes" the link generated on the defined keyword opens the linked document in a new tab.',
                                         'daext-autolinks-manager'); ?>'></div>
                            </td>
                        </tr>

                        <!-- Use Nofollow -->
                        <tr class="html-options">
                            <th scope="row"><?php esc_html_e('Use Nofollow', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <select id="use-nofollow" name="use_nofollow" class="daext-display-none">
                                    <option value="0" <?php selected(intval($autolink_obj->use_nofollow),
                                        0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                    <option value="1" <?php selected(intval($autolink_obj->use_nofollow),
                                        1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title='<?php esc_attr_e('If you select "Yes" the link generated on the defined keyword will include the rel="nofollow" attribute.',
                                         'daext-autolinks-manager'); ?>'></div>
                            </td>
                        </tr>

                        <!-- Affected Posts Options -------------------------------------------------------------------------- -->
                        <tr class="group-trigger" data-trigger-target="affected-posts-options">
                            <th class="group-title"><?php esc_html_e('Affected Posts', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Post Types -->
                        <tr class="affected-posts-options">
                            <th scope="row"><label for="post-types"><?php esc_html_e('Post Types', 'daext-autolinks-manager'); ?></label>
                            </th>
                            <td>
                                <?php

                                $current_post_types_a = maybe_unserialize($autolink_obj->post_types);

                                $available_post_types_a = get_post_types(array(
                                    'public'  => true,
                                    'show_ui' => true
                                ));

                                //Remove the "attachment" post type
                                $available_post_types_a = array_diff($available_post_types_a, array('attachment'));

                                $html = '<select id="post-types" name="post_types[]" class="daext-display-none" multiple>';

                                foreach ($available_post_types_a as $key => $single_post_type) {
                                    if (is_array($current_post_types_a) and in_array($single_post_type,
                                            $current_post_types_a)) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    $post_type_obj = get_post_type_object($single_post_type);
                                    $html          .= '<option value="' . $single_post_type . '" ' . $selected . '>' . esc_html($post_type_obj->label) . '</option>';
                                }

                                $html .= '</select>';

                                $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which post types the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any post type.', 'daext-autolinks-manager') . '"></div>';

                                echo $html;

                                ?>
                            </td>
                        </tr>

                        <!-- Categories -->
                        <tr class="affected-posts-options">
                            <th scope="row"><label for="categories"><?php esc_html_e('Categories', 'daext-autolinks-manager'); ?></label>
                            </th>
                            <td>
                                <?php

                                $current_categories_a = maybe_unserialize($autolink_obj->categories);

                                $html = '<select id="categories" name="categories[]" class="daext-display-none" multiple>';

                                $categories = get_categories(array(
                                    'hide_empty' => 0,
                                    'orderby'    => 'term_id',
                                    'order'      => 'DESC'
                                ));

                                foreach ($categories as $key => $category) {
                                    if (is_array($current_categories_a) and in_array($category->term_id,
                                            $current_categories_a)) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                }

                                $html .= '</select>';
                                $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which categories the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any category.', 'daext-autolinks-manager') . '"></div>';

                                echo $html;

                                ?>
                            </td>
                        </tr>

                        <!-- Tags -->
                        <tr class="affected-posts-options">
                            <th scope="row"><label for="tags"><?php esc_html_e('Tags', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <?php

                                $current_tags_a = maybe_unserialize($autolink_obj->tags);

                                $html = '<select id="tags" name="tags[]" class="daext-display-none" multiple>';

                                $categories = get_categories(array(
                                    'hide_empty' => 0,
                                    'orderby'    => 'term_id',
                                    'order'      => 'DESC',
                                    'taxonomy'   => 'post_tag'
                                ));

                                foreach ($categories as $key => $category) {
                                    if (is_array($current_tags_a) and in_array($category->term_id, $current_tags_a)) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                }

                                $html .= '</select>';
                                $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which tags the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any tag.', 'daext-autolinks-manager') . '"></div>';

                                echo $html;

                                ?>
                            </td>
                        </tr>

                        <!-- Term Group -->
                        <tr class="affected-posts-options">
                            <th scope="row"><label for="tags"><?php esc_html_e('Term Group', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <?php

                                $html = '<select id="term-group-id" name="term_group_id" class="daext-display-none">';

                                $html .= '<option value="0">' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

                                global $wpdb;
                                $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_term_group";
                                $sql          = "SELECT term_group_id, name FROM $table_name ORDER BY term_group_id DESC";
                                $term_group_a = $wpdb->get_results($sql, ARRAY_A);

                                foreach ($term_group_a as $key => $term_group) {
                                    $html .= '<option value="' . $term_group['term_group_id'] . '" ' . selected($autolink_obj->term_group_id,
                                            $term_group['term_group_id'],
                                            false) . '>' . esc_html(stripslashes($term_group['name'])) . '</option>';
                                }

                                $html .= '</select>';
                                $html .= '<div class="help-icon" title="' . esc_attr__('The terms that will be compared with the ones available on the posts where the autolinks are applied. Please note that when a term group is selected the "Categories" and "Tags" options will be ignored.',
                                        'daext-autolinks-manager') . '"></div>';

                                echo $html;

                                ?>
                            </td>
                        </tr>

                        <!-- Advanced Match Options ---------------------------------------------------------------- -->
                        <tr class="group-trigger" data-trigger-target="advanced-match-options">
                            <th class="group-title"><?php esc_html_e('Advanced Match', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <div class="expand-icon"></div>
                            </td>
                        </tr>

                        <!-- Case Sensitive Search -->
                        <tr class="advanced-match-options">
                            <th scope="row"><?php esc_html_e('Case Sensitive Search', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <select id="case-sensitive-search" name="case_sensitive_search"
                                        class="daext-display-none">
                                    <option value="0" <?php selected(intval($autolink_obj->case_sensitive_search),
                                        0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                    <option value="1" <?php selected(intval($autolink_obj->case_sensitive_search),
                                        1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title='<?php esc_attr_e('If you select "No" the defined keyword will match both lowercase and uppercase variations.',
                                         'daext-autolinks-manager'); ?>'></div>
                            </td>
                        </tr>

                        <!-- Left Boundary -->
                        <tr class="advanced-match-options">
                            <th scope="row"><?php esc_html_e('Left Boundary', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <select id="left-boundary" name="left_boundary" class="daext-display-none">
                                    <option value="0" <?php selected(intval($autolink_obj->left_boundary),
                                        0); ?>><?php esc_html_e('Generic', 'daext-autolinks-manager'); ?></option>
                                    <option value="1" <?php selected(intval($autolink_obj->left_boundary),
                                        1); ?>><?php esc_html_e('White Space', 'daext-autolinks-manager'); ?></option>
                                    <option value="2" <?php selected(intval($autolink_obj->left_boundary),
                                        2); ?>><?php esc_html_e('Comma', 'daext-autolinks-manager'); ?></option>
                                    <option value="3" <?php selected(intval($autolink_obj->left_boundary),
                                        3); ?>><?php esc_html_e('Point', 'daext-autolinks-manager'); ?></option>
                                    <option value="4" <?php selected(intval($autolink_obj->left_boundary),
                                        4); ?>><?php esc_html_e('None', 'daext-autolinks-manager'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title='<?php esc_attr_e('Use this option to match keywords preceded by a generic boundary or by a specific character.', 'daext-autolinks-manager'); ?>'></div>
                            </td>
                        </tr>

                        <!-- Right Boundary -->
                        <tr class="advanced-match-options">
                            <th scope="row"><?php esc_html_e('Right Boundary', 'daext-autolinks-manager'); ?></th>
                            <td>
                                <select id="right-boundary" name="right_boundary" class="daext-display-none">
                                    <option value="0" <?php selected(intval($autolink_obj->right_boundary),
                                        0); ?>><?php esc_html_e('Generic', 'daext-autolinks-manager'); ?></option>
                                    <option value="1" <?php selected(intval($autolink_obj->right_boundary),
                                        1); ?>><?php esc_html_e('White Space', 'daext-autolinks-manager'); ?></option>
                                    <option value="2" <?php selected(intval($autolink_obj->right_boundary),
                                        2); ?>><?php esc_html_e('Comma', 'daext-autolinks-manager'); ?></option>
                                    <option value="3" <?php selected(intval($autolink_obj->right_boundary),
                                        3); ?>><?php esc_html_e('Point', 'daext-autolinks-manager'); ?></option>
                                    <option value="4" <?php selected(intval($autolink_obj->right_boundary),
                                        4); ?>><?php esc_html_e('None', 'daext-autolinks-manager'); ?></option>
                                </select>
                                <div class="help-icon"
                                     title='<?php esc_attr_e('Use this option to match keywords followed by a generic boundary or by a specific character.', 'daext-autolinks-manager'); ?>'></div>
                            </td>
                        </tr>

                        <!-- Keyword Before -->
                        <tr class="advanced-match-options">
                            <th scope="row"><label for="keyword-before"><?php esc_html_e('Keyword Before', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->keyword_before)); ?>"
                                       type="text" id="keyword-before" maxlength="255" size="30" name="keyword_before"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('Use this option to match occurences preceded by a specific string.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Keyword After -->
                        <tr class="advanced-match-options">
                            <th scope="row"><label for="keyword-after"><?php esc_html_e('Keyword After', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo esc_attr(stripslashes($autolink_obj->keyword_after)); ?>"
                                       type="text" id="keyword-after" maxlength="255" size="30" name="keyword_after"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('Use this option to match occurences followed by a specific string.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Limit -->
                        <tr class="advanced-match-options">
                            <th scope="row"><label for="limit"><?php esc_html_e('Limit', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo intval($autolink_obj->limit, 10); ?>" value="100" type="text"
                                       id="limit" maxlength="7" size="30" name="limit"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('With this option you can determine the maximum number of matches of the defined keyword automatically converted to a link.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                        <!-- Priority -->
                        <tr class="advanced-match-options">
                            <th scope="row"><label for="priority"><?php esc_html_e('Priority', 'daext-autolinks-manager'); ?></label></th>
                            <td>
                                <input value="<?php echo intval($autolink_obj->priority, 10); ?>" type="text"
                                       id="priority" maxlength="7" size="30" name="priority"/>
                                <div class="help-icon"
                                     title="<?php esc_attr_e('The priority value determines the order used to apply the autolinks on the post.', 'daext-autolinks-manager'); ?>"></div>
                            </td>
                        </tr>

                    </table>

                    <!-- submit button -->
                    <div class="daext-form-action">
                        <input class="button" type="submit"
                               value="<?php esc_html_e('Update Autolink', 'daext-autolinks-manager'); ?>">
                        <input id="cancel" class="button" type="submit"
                               value="<?php esc_attr_e('Cancel', 'daext-autolinks-manager'); ?>">
                    </div>

                    <?php else : ?>

                    <!-- Create New Autolink -->

                    <div class="daext-form-container">

                        <div class="daext-form-title"><?php esc_html_e('Create New Autolink', 'daext-autolinks-manager'); ?></div>

                        <table class="daext-form daext-form-table">

                            <!-- Name -->
                            <tr valign="top">
                                <th><label for="title"><?php esc_html_e('Name', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input type="text" id="name" maxlength="100" size="30" name="name"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The name of the autolink.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Category ID -->
                            <tr>
                                <th scope="row"><label for="tags"><?php esc_html_e('Category', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <?php

                                    $html = '<select id="category-id" name="category_id" class="daext-display-none">';

                                    $html .= '<option value="0" ' . selected(intval(get_option($this->shared->get('slug') . "_defaults_category_id")),
                                            0, false) . '>' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

                                    global $wpdb;
                                    $table_name = $wpdb->prefix . $this->shared->get('slug') . "_category";
                                    $sql        = "SELECT category_id, name FROM $table_name ORDER BY category_id DESC";
                                    $category_a = $wpdb->get_results($sql, ARRAY_A);

                                    foreach ($category_a as $key => $category) {
                                        $html .= '<option value="' . $category['category_id'] . '" ' . selected(intval(get_option($this->shared->get('slug') . "_defaults_category_id")),
                                                $category['category_id'],
                                                false) . '>' . esc_html(stripslashes($category['name'])) . '</option>';
                                    }

                                    $html .= '</select>';
                                    $html .= '<div class="help-icon" title="' . esc_attr__('The category of the autolink.', 'daext-autolinks-manager') . '"></div>';

                                    echo $html;

                                    ?>
                                </td>
                            </tr>

                            <!-- Keyword -->
                            <tr>
                                <th scope="row"><label for="keyword"><?php esc_html_e('Keyword', 'daext-autolinks-manager'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="keyword" maxlength="255" size="30" name="keyword"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The keyword that will be converted to a link.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- URL -->
                            <tr>
                                <th scope="row"><label for="url"><?php esc_html_e('URL', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input type="text" id="url" maxlength="2083" size="30" name="url"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The destination address of the link automatically generated on the keyword.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- HTML Options ---------------------------------------------------------------------- -->
                            <tr class="group-trigger" data-trigger-target="html-options">
                                <th class="group-title"><?php esc_html_e('HTML', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <div class="expand-icon"></div>
                                </td>
                            </tr>

                            <!-- Title -->
                            <tr class="html-options">
                                <th scope="row"><label for="title"><?php esc_html_e('Title', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input type="text" id="title" maxlength="255" size="30" name="title"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The title attribute of the link automatically generated on the keyword.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Open New Tab -->
                            <tr class="html-options">
                                <th scope="row"><?php esc_html_e('Open New Tab', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <select id="open-new-tab" name="open_new_tab" class="daext-display-none">
                                        <option value="0" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_open_new_tab")),
                                            0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                        <option value="1" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_open_new_tab")),
                                            1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                    </select>
                                    <div class="help-icon"
                                         title='<?php esc_attr_e('If you select "Yes" the link generated on the defined keyword opens the linked document in a new tab.',
                                             'daext-autolinks-manager'); ?>'></div>
                                </td>
                            </tr>

                            <!-- Use Nofollow -->
                            <tr class="html-options">
                                <th scope="row"><?php esc_html_e('Use Nofollow', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <select id="use-nofollow" name="use_nofollow" class="daext-display-none">
                                        <option value="0" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_use_nofollow")),
                                            0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                        <option value="1" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_use_nofollow")),
                                            1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                    </select>
                                    <div class="help-icon"
                                         title='<?php esc_attr_e('If you select "Yes" the link generated on the defined keyword will include the rel="nofollow" attribute.',
                                             'daext-autolinks-manager'); ?>'></div>
                                </td>
                            </tr>

                            <!-- Affected Posts Options ------------------------------------------------------------ -->
                            <tr class="group-trigger" data-trigger-target="affected-posts-options">
                                <th class="group-title"><?php esc_html_e('Affected Posts', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <div class="expand-icon"></div>
                                </td>
                            </tr>

                            <!-- Post Types -->
                            <tr class="affected-posts-options">
                                <th scope="row"><label for="post-types"><?php esc_html_e('Post Types', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <?php

                                    $defaults_post_types_a = get_option("daextam_defaults_post_types");

                                    $available_post_types_a = get_post_types(array(
                                        'public'  => true,
                                        'show_ui' => true
                                    ));

                                    //Remove the "attachment" post type
                                    $available_post_types_a = array_diff($available_post_types_a, array('attachment'));

                                    $html = '<select id="post-types" name="post_types[]" class="daext-display-none" multiple>';

                                    foreach ($available_post_types_a as $key => $single_post_type) {
                                        if (is_array($defaults_post_types_a) and in_array($single_post_type,
                                                $defaults_post_types_a)) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        $post_type_obj = get_post_type_object($single_post_type);
                                        $html          .= '<option value="' . $single_post_type . '" ' . $selected . '>' . esc_html($post_type_obj->label) . '</option>';
                                    }

                                    $html .= '</select>';

                                    $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which post types the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any post type.', 'daext-autolinks-manager') . '"></div>';

                                    echo $html;

                                    ?>
                                </td>
                            </tr>

                            <!-- Categories -->
                            <tr class="affected-posts-options">
                                <th scope="row"><label for="categories"><?php esc_html_e('Categories', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <?php

                                    $defaults_categories_a = get_option("daextam_defaults_categories");

                                    $html = '<select id="categories" name="categories[]" class="daext-display-none" multiple>';

                                    $categories = get_categories(array(
                                        'hide_empty' => 0,
                                        'orderby'    => 'term_id',
                                        'order'      => 'DESC'
                                    ));

                                    foreach ($categories as $key => $category) {
                                        if (is_array($defaults_categories_a) and in_array($category->term_id,
                                                $defaults_categories_a)) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                    }

                                    $html .= '</select>';
                                    $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which categories the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any category.', 'daext-autolinks-manager') . '"></div>';

                                    echo $html;

                                    ?>
                                </td>
                            </tr>

                            <!-- Tags -->
                            <tr class="affected-posts-options">
                                <th scope="row"><label for="tags"><?php esc_html_e('Tags', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <?php

                                    $defaults_tags_a = get_option("daextam_defaults_tags");

                                    $html = '<select id="tags" name="tags[]" class="daext-display-none" multiple>';

                                    $categories = get_categories(array(
                                        'hide_empty' => 0,
                                        'orderby'    => 'term_id',
                                        'order'      => 'DESC',
                                        'taxonomy'   => 'post_tag'
                                    ));

                                    foreach ($categories as $key => $category) {
                                        if (is_array($defaults_tags_a) and in_array($category->term_id,
                                                $defaults_tags_a)) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        $html .= '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                    }

                                    $html .= '</select>';
                                    $html .= '<div class="help-icon" title="' . esc_attr__('With this option you are able to determine in which tags the defined keywords will be automatically converted to a link. Leave this field empty to convert the keyword in any tag.', 'daext-autolinks-manager') . '"></div>';

                                    echo $html;

                                    ?>
                                </td>
                            </tr>

                            <!-- Term Group -->
                            <tr class="affected-posts-options">
                                <th scope="row"><label for="tags"><?php esc_html_e('Term Group', 'daext-autolinks-manager'); ?></label>
                                </th>
                                <td>
                                    <?php

                                    $html = '<select id="term-group-id" name="term_group_id" class="daext-display-none">';
                                    $temp = intval(get_option($this->shared->get('slug') . "_defaults_term_group_id"), 10);
                                    $html .= '<option value="0" ' . selected(intval(get_option($this->shared->get('slug') . "_defaults_term_group_id"), 10),
                                            0, false) . '>' . esc_html__('None', 'daext-autolinks-manager') . '</option>';

                                    global $wpdb;
                                    $table_name   = $wpdb->prefix . $this->shared->get('slug') . "_term_group";
                                    $sql          = "SELECT term_group_id, name FROM $table_name ORDER BY term_group_id DESC";
                                    $term_group_a = $wpdb->get_results($sql, ARRAY_A);

                                    foreach ($term_group_a as $key => $term_group) {
                                        $html .= '<option value="' . $term_group['term_group_id'] . '" ' . selected(intval(get_option($this->shared->get('slug') . "_defaults_term_group_id"), 10),
                                                $term_group['term_group_id'],
                                                false) . '>' . esc_html(stripslashes($term_group['name'])) . '</option>';
                                    }

                                    $html .= '</select>';
                                    $html .= '<div class="help-icon" title="' . esc_attr__('The terms that will be compared with the ones available on the posts where the autolinks are applied. Please note that when a term group is selected the "Categories" and "Tags" options will be ignored.',
                                            'daext-autolinks-manager') . '"></div>';

                                    echo $html;

                                    ?>
                                </td>
                            </tr>

                            <!-- Advanced Match Options ------------------------------------------------------------ -->
                            <tr class="group-trigger" data-trigger-target="advanced-match-options">
                                <th class="group-title"><?php esc_attr_e('Advanced Match', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <div class="expand-icon"></div>
                                </td>
                            </tr>

                            <!-- Case Sensitive Search -->
                            <tr class="advanced-match-options">
                                <th scope="row"><?php esc_html_e('Case Sensitive Search', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <select id="case-sensitive-search" name="case_sensitive_search"
                                            class="daext-display-none">
                                        <option value="0" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_case_sensitive_search")),
                                            0); ?>><?php esc_html_e('No', 'daext-autolinks-manager'); ?></option>
                                        <option value="1" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_case_sensitive_search")),
                                            1); ?>><?php esc_html_e('Yes', 'daext-autolinks-manager'); ?></option>
                                    </select>
                                    <div class="help-icon"
                                         title='<?php esc_attr_e('If you select "No" the defined keyword will match both lowercase and uppercase variations.',
                                             'daext-autolinks-manager'); ?>'></div>
                                </td>
                            </tr>

                            <!-- Left Boundary -->
                            <tr class="advanced-match-options">
                                <th scope="row"><?php esc_html_e('Left Boundary', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <select id="left-boundary" name="left_boundary" class="daext-display-none">
                                        <option value="0" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_left_boundary")),
                                            0); ?>><?php esc_html_e('Generic', 'daext-autolinks-manager'); ?></option>
                                        <option value="1" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_left_boundary")),
                                            1); ?>><?php esc_html_e('White Space', 'daext-autolinks-manager'); ?></option>
                                        <option value="2" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_left_boundary")),
                                            2); ?>><?php esc_html_e('Comma', 'daext-autolinks-manager'); ?></option>
                                        <option value="3" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_left_boundary")),
                                            3); ?>><?php esc_html_e('Point', 'daext-autolinks-manager'); ?></option>
                                        <option value="4" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_left_boundary")),
                                            4); ?>><?php esc_html_e('None', 'daext-autolinks-manager'); ?></option>
                                    </select>
                                    <div class="help-icon"
                                         title='<?php esc_attr_e('Use this option to match keywords preceded by a generic boundary or by a specific character.', 'daext-autolinks-manager'); ?>'></div>
                                </td>
                            </tr>

                            <!-- Right Boundary -->
                            <tr class="advanced-match-options">
                                <th scope="row"><?php esc_html_e('Right Boundary', 'daext-autolinks-manager'); ?></th>
                                <td>
                                    <select id="right-boundary" name="right_boundary" class="daext-display-none">
                                        <option value="0" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_right_boundary")),
                                            0); ?>><?php esc_html_e('Generic', 'daext-autolinks-manager'); ?></option>
                                        <option value="1" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_right_boundary")),
                                            1); ?>><?php esc_html_e('White Space', 'daext-autolinks-manager'); ?></option>
                                        <option value="2" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_right_boundary")),
                                            2); ?>><?php esc_html_e('Comma', 'daext-autolinks-manager'); ?></option>
                                        <option value="3" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_right_boundary")),
                                            3); ?>><?php esc_html_e('Point', 'daext-autolinks-manager'); ?></option>
                                        <option value="4" <?php selected(intval(get_option($this->shared->get('slug') . "_defaults_right_boundary")),
                                            4); ?>><?php esc_html_e('None', 'daext-autolinks-manager'); ?></option>
                                    </select>
                                    <div class="help-icon"
                                         title='<?php esc_attr_e('Use this option to match keywords followed by a generic boundary or by a specific character.', 'daext-autolinks-manager'); ?>'></div>
                                </td>
                            </tr>

                            <!-- Keyword Before -->
                            <tr class="advanced-match-options">
                                <th scope="row"><label for="keyword-before"><?php esc_attr_e('Keyword Before', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input type="text" id="keyword-before" maxlength="255" size="30"
                                           name="keyword_before"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('Use this option to match occurences preceded by a specific string.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Keyword After -->
                            <tr class="advanced-match-options">
                                <th scope="row"><label for="keyword-after"><?php esc_html_e('Keyword After', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input type="text" id="keyword-after" maxlength="255" size="30"
                                           name="keyword_after"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('Use this option to match occurences followed by a specific string.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Limit -->
                            <tr class="advanced-match-options">
                                <th scope="row"><label for="limit"><?php esc_html_e('Limit', 'daext-autolinks-manager'); ?></label></th>
                                <td>
                                    <input value="<?php echo intval(get_option($this->shared->get('slug') . "_defaults_limit"),
                                        10); ?>" type="text" id="limit" maxlength="7" size="30" name="limit"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('With this option you can determine the maximum number of matches of the defined keyword automatically converted to a link.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                            <!-- Priority -->
                            <tr class="advanced-match-options">
                                <th scope="row"><label for="priority"><?php esc_html_e('Priority', 'daext-autolinks-manager'); ?></label>
                                </th>
                                <td>
                                    <input value="<?php echo intval(get_option($this->shared->get('slug') . "_defaults_priority"),
                                        10); ?>" type="text" id="priority" maxlength="7" size="30" name="priority"/>
                                    <div class="help-icon"
                                         title="<?php esc_attr_e('The priority value determines the order used to apply the autolinks on the post.', 'daext-autolinks-manager'); ?>"></div>
                                </td>
                            </tr>

                        </table>

                        <!-- submit button -->
                        <div class="daext-form-action">
                            <input class="button" type="submit"
                                   value="<?php esc_attr_e('Add Autolink', 'daext-autolinks-manager'); ?>">
                        </div>

                        <?php endif; ?>

                    </div>

            </form>

        </div>

    </div>

</div>

<!-- Dialog Confirm -->
<div id="dialog-confirm" title="<?php esc_attr_e('Delete the autolink?', 'daext-autolinks-manager'); ?>" class="daext-display-none">
    <p><?php esc_html_e('This autolink will be permanently deleted and cannot be recovered. Are you sure?', 'daext-autolinks-manager'); ?></p>
