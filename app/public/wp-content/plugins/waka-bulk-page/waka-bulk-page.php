<?php
/**
 * @since             1.0.0
 * @package           Waka Bulk Page
 *
 * @wordpress-plugin
 * Plugin Name:       Waka Bulk Page
 * Plugin URI:        https://github.com/QuentinChx/waka-bulk-page.git
 * Description:       Bulk page creation for setting up quickly your website. Intuitive and easy to use.
 * Version:           1.0.0
 * Author:            Waka
 * Author URI:        http://agence-waka.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       waka-bulk-page
 * Domain Path:       /languages
 *
 *
 *  This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License, version 2, as
 *   published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit('denied');
}

define( 'WAKA_BULK_PAGE_VERSION', '1.0.2' );
define( 'WAKA_BULK_PAGE_FILE', __FILE__ ); // this file
define( 'WAKA_BULK_PAGE_BASENAME', plugin_basename( WAKA_BULK_PAGE_FILE ) );

$plugin_dir_path = plugin_dir_path( __FILE__ );

require $plugin_dir_path . 'includes/class-waka-bulk-page-walker.php';
require $plugin_dir_path . 'includes/class-waka-bulk-page-admin.php';
require $plugin_dir_path . 'includes/class-waka-bulk-page.php';

$plugin = new Waka_Bulk_Page();
