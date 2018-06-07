<?php
defined('ABSPATH') || exit;

if (version_compare(phpversion(), '5.3', '<')) {
    return;
}

class NewsletterWidgetMinimalGdpr extends WP_Widget {

    function __construct() {
        parent::__construct(false, $name = 'Newsletter Minimal With GDPR', array('description' => 'Newsletter widget to add a minimal subscription form with GDPR checkbox/notice'), array('width' => '350px'));
    }

    function widget($args, $instance) {

        $newsletter = Newsletter::instance();
        $newsletterSubscription = NewsletterSubscription::instance();

        extract($args);

        echo $before_widget;

        if (!is_array($instance)) {
            $instance = array();
        }
        // Filters are used for WPML
        if (!empty($instance['title'])) {
            $title = apply_filters('widget_title', $instance['title'], $instance);
            echo $before_title . $title . $after_title;
        }

        if (empty($instance['button'])) {
            $instance['button'] = 'Subscribe';
        }

        $options_profile = get_option('newsletter_profile');


        $form = '<div class="tnp tnp-widget-minimal tnp-widget tnp-widget-minimal-gdpr">';
        $form .= '<form action="' . esc_attr(home_url('/')) . '?na=s" method="post" onsubmit="return newsletter_check(this)">';
        if (isset($instance['nl']) && is_array($instance['nl'])) {
            foreach ($instance['nl'] as $a) {
                $form .= "<input type='hidden' name='nl[]' value='" . ((int) trim($a)) . "'>\n";
            }
        }
        // Referrer
        $form .= '<input type="hidden" name="nr" value="widget-minimal"/>';

        $form .= '<div class="tnp-field tnp-field-email">';
        $form .= '<input class="tnp-email" type="email" required name="ne" value="" placeholder="' . esc_attr($options_profile['email']) . '">';
        $form .= '</div>';

        $privacy_status = (int) $options_profile['privacy_status'];

        if ($privacy_status === 1 || $privacy_status === 2) {
            $form .= '<div class="tnp-field tnp-field-privacy">';
            $form .= '<label>';
            if ($privacy_status === 1) {
                $form .= '<input type="checkbox" name="ny" required class="tnp-privacy">&nbsp;';
            }
            $url = $newsletterSubscription->get_privacy_url();
            if (!empty($url)) {
                $form .= '<a target="_blank" href="' . esc_attr($url) . '">';
                $form .= esc_attr($options_profile['privacy']) . '</a>';
            } else {
                $form .= esc_html($options_profile['privacy']);
            }

            $form .= "</label></div>\n";
        }

        $form .= '<input class="tnp-submit" type="submit" value="' . esc_attr($instance['button']) . '">';

        $form .= '</form></div>';

        echo $form;
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        if (!is_array($instance)) {
            $instance = array();
        }
        $profile_options = NewsletterSubscription::instance()->get_options('profile');
        $instance = array_merge(array('title' => '', 'text' => '', 'button' => $profile_options['subscribe'], 'nl' => array()), $instance);
        $options_profile = get_option('newsletter_profile');
        if (!is_array($instance['nl'])) {
            $instance['nl'] = array();
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                Title:
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>">
            </label>

            <label for="<?php echo $this->get_field_id('button'); ?>">
                Button label:
                <input class="widefat" id="<?php echo $this->get_field_id('button'); ?>" name="<?php echo $this->get_field_name('button'); ?>" type="text" value="<?php echo esc_attr($instance['button']); ?>">
                Use a short one!
            </label>
        </p>

        <p>
            <?php _e('Automatically subscribe to', 'newsletter') ?>
            <br>
             <?php
            $lists = Newsletter::instance()->get_lists_public();
            foreach ($lists as $list) {
                ?>
                <label for="nl<?php echo $list->id ?>">
                    <input type="checkbox" value="<?php echo $list->id ?>" name="<?php echo $this->get_field_name('nl[]') ?>" <?php echo array_search($list->id, $instance['nl']) !== false ? 'checked' : '' ?>> <?php echo esc_html($list->name) ?>
                </label>
                <br>
            <?php } ?>
        </p>

        <?php
    }

}

add_action('widgets_init', function() {
    return register_widget("NewsletterWidgetMinimalGdpr");
});
