<?php

if ( ! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have sufficient capabilities to access this page.', 'daext-autolinks-manager'));
}

?>

<div class="wrap">

    <h2><?php esc_attr_e('Autolinks Manager - Options', 'daext-autolinks-manager'); ?></h2>

    <?php

    //settings errors
    if (isset($_GET['settings-updated']) and $_GET['settings-updated'] == 'true') {
        settings_errors();
    }

    ?>

    <div id="daext-options-wrapper">

        <?php
        //get current tab value
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'defaults_options';
        ?>

        <div class="nav-tab-wrapper">
            <a href="?page=daextam-options&tab=defaults_options"
               class="nav-tab <?php echo $active_tab == 'defaults_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Defaults', 'daext-autolinks-manager'); ?></a>
            <a href="?page=daextam-options&tab=analysis_options"
               class="nav-tab <?php echo $active_tab == 'analysis_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Analysis', 'daext-autolinks-manager'); ?></a>
            <a href="?page=daextam-options&tab=advanced_options"
               class="nav-tab <?php echo $active_tab == 'advanced_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Advanced', 'daext-autolinks-manager'); ?></a>
        </div>

        <form method="post" action="options.php" autocomplete="off">

            <?php

            if ($active_tab == 'defaults_options') {

                settings_fields($this->shared->get('slug') . '_defaults_options');
                do_settings_sections($this->shared->get('slug') . '_defaults_options');

            }

            if ($active_tab == 'analysis_options') {

                settings_fields($this->shared->get('slug') . '_analysis_options');
                do_settings_sections($this->shared->get('slug') . '_analysis_options');

            }

            if ($active_tab == 'advanced_options') {

                settings_fields($this->shared->get('slug') . '_advanced_options');
                do_settings_sections($this->shared->get('slug') . '_advanced_options');

            }

            ?>

            <div class="daext-options-action">
                <input type="submit" name="submit" id="submit" class="button"
                       value="<?php esc_attr_e('Save Changes', 'daext-autolinks-manager'); ?>">
            </div>

        </form>

    </div>

