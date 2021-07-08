<?php
namespace ILJ\Core\Options;

use ILJ\Helper\Options as OptionsHelper;

/**
 * Option: Blacklist also child pages of blacklisted pages
 *
 * @since   1.2.15
 * @package ILJ\Core\Options
 */
class BlacklistChildPages extends AbstractOption
{
    /**
     * @inheritdoc
     */
    public static function getKey()
    {
        return self::ILJ_OPTIONS_PREFIX . 'blacklist_child_pages';
    }

    /**
     * @inheritdoc
     */
    public static function getDefault()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function isPro()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return __('Blacklist also child pages of blacklisted pages', 'internal-links');
    }

    /**
     * @inheritdoc
     */
    public function renderField($value)
    {
        $checked = checked(1, $value, false);
        OptionsHelper::renderToggle($this, $checked);
    }

    /**
     * @inheritdoc
     */
    public function isValidValue($value)
    {
	    return 1 === (int) $value || 0 === (int) $value;
    }
}
