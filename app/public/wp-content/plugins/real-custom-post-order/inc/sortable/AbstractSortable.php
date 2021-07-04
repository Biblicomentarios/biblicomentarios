<?php

namespace DevOwl\RealCustomPostOrder\sortable;

use DevOwl\RealCustomPostOrder\base\UtilsProvider;
use WP_Error;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Abstract sortable implementation. Each type of sortable should get an own subclass
 * of this abstract one. For example tags, media or posts.
 */
abstract class AbstractSortable {
    use UtilsProvider;
    /**
     * The registered type name.
     *
     * @var string
     */
    private $type;
    /**
     * Available sortable types.
     *
     * @var AbstractSortable[]
     */
    private static $register = [];
    /**
     * C'tor.
     *
     * @param string $type
     * @codeCoverageIgnore
     */
    public function __construct($type) {
        $this->type = $type;
    }
    /**
     * See updateByIntSequence().
     *
     * @param int[] $sequence
     * @return boolean|WP_Error
     */
    public function doUpdateByIntSequence($sequence) {
        if (!\is_array($sequence)) {
            return new \WP_Error('rcpo_update_sequence_error', __('Sequence array not valid.', RCPO_TD));
        }
        foreach ($sequence as $key => $value) {
            if (!\is_numeric($value)) {
                return new \WP_Error('rcpo_update_sequence_error', __('Sequence value not a number.', RCPO_TD));
            }
            $sequence[$key] = \intval($value);
        }
        if (\count($sequence) === 0) {
            return \true;
        }
        $this->recreateIndex($sequence);
        return $this->updateByIntSequence($sequence);
    }
    /**
     * Checks if the current screen allows to modify the order.
     *
     * @param boolean $respectOption If true the user option is respected
     * @return boolean
     */
    abstract public function isAvailable($respectOption = \true);
    /**
     * Checks if the current screen is sortable. For example disallow if a
     * filter is active. This boolean is localized to the frontend.
     *
     * @return boolean
     */
    abstract public function isSortable();
    /**
     * Localize frontend.
     *
     * @return array
     */
    abstract public function localize();
    /**
     * Update a set of entries by an ordered array of IDs.
     * Do not use this method directly, instead use doUpdateByIntSequence() so validation is done.
     *
     * You do not need to pass all post entries in the sequence, only the visible part of UI.
     *
     * @param int[] $sequence
     * @return boolean|WP_Error
     */
    abstract protected function updateByIntSequence($sequence);
    /**
     * Recreate index the sortable column. This is done before each reorder.
     *
     * @param number[] $sequence
     */
    abstract public function recreateIndex($sequence = null);
    /**
     * Get type.
     *
     * @codeCoverageIgnore
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Register a new abstract sortable.
     *
     * @param string $type Unique type name
     * @param string $class FQN to class
     * @return AbstractSortable
     */
    public static function register($type, $class) {
        self::$register[$type] = new $class($type);
        return self::$register[$type];
    }
    /**
     * Get a new instance of a registered type.
     *
     * @param string $type
     * @return AbstractSortable
     * @codeCoverageIgnore
     */
    public static function getTypeInstance($type) {
        return self::$register[$type];
    }
    /**
     * Checks if any of the available sortables is available.
     *
     * @return AbstractSortable[]
     */
    public static function someAvailable() {
        $found = [];
        foreach (self::$register as $obj) {
            if ($obj->isAvailable()) {
                $found[] = $obj;
            }
        }
        return $found;
    }
    /**
     * Get all available types.
     *
     * @return string[]
     * @codeCoverageIgnore
     */
    public static function getTypes() {
        return \array_keys(self::$register);
    }
}
