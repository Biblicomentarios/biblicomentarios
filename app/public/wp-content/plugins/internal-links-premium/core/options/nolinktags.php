<?php
namespace ILJ\Core\Options;

use ILJ\Enumeration\TagExclusion;

/**
 * Option: Html tags that don't get linked
 *
 * @since   1.1.3
 * @package ILJ\Core\Options
 */
class NoLinkTags extends AbstractOption
{
    /**
     * @inheritdoc
     */
    public static function getKey()
    {
        return self::ILJ_OPTIONS_PREFIX . 'no_link_tags';
    }

    /**
     * @inheritdoc
     */
    public static function getDefault()
    {
        return [TagExclusion::HEADLINE];
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return __('Exclude HTML areas from linking', 'internal-links');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return __('Content within the HTML tags that are configured here do not get used for linking.', 'internal-links');
    }

    /**
     * @inheritdoc
     */
    public function renderField($value)
    {
        if ($value == "") {
            $value = [];
        }
        echo '<select name="' . self::getKey() . '[]" id="' . self::getKey() . '" multiple="multiple">';
        foreach (TagExclusion::getValues() as $tag_exclusion) {
            $is_pro = (bool) !(TagExclusion::getRegex($tag_exclusion));
            echo '<option value="' . $tag_exclusion . '"' . (!$is_pro && in_array($tag_exclusion, $value) ? ' selected' : '') . ($is_pro ? ' disabled' : '') . '>' . TagExclusion::translate($tag_exclusion) . ($is_pro ? ' - ' . __('Pro feature', 'internal-links') : '') . '</option>';
        }
        echo '</select>';
    }

    /**
     * @inheritdoc
     */
    public function getHint()
    {
        if (\ILJ\ilj_fs()->is__premium_only()) {
            if (\ILJ\ilj_fs()->can_use_premium_code() ) {
                return '<p>' . __('In order to exclude variable areas, simply use our provided shortcode when entering text:', 'internal-links') . '<br><code style="display:inline-block;margin-top:10px;">[ilj_no_linking]' . __('This text does not get linked automatically.', 'internal-links') . '[/ilj_no_linking]</code></p>';
            }
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isValidValue($value)
    {
        $values = $value;
        $validValues = [
        TagExclusion::HEADLINE,
        TagExclusion::STRONG
        ];

        if (\ILJ\ilj_fs()->can_use_premium_code__premium_only()) {
            $validValues = TagExclusion::getValues();
        }

        foreach($values as $value) {
            if (!in_array($value, $validValues)) {
                return false;
            }
        }

        return true;
    }
}
