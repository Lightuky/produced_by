<?php
/*
Plugin Name: Copyrights
Version: 2.9.a
Description: Create copyrights and assign them to your photos.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=537
Author: Mattias & J.Commelin (Deltaworks Online Foundation) <www.deltaworks.org>
Author URI: http://www.watergallery.nl/piwigo/plugins/copyrights/
*/
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('PRODUCEDBY_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)) . '/');  // The plugin path
define('PRODUCEDBY_WEB_PATH', get_root_url().'admin.php?page=plugin-produced_by'); // The path used in admin.php

global $prefixeTable;
define('PRODUCEDBY_ADMIN', $prefixeTable.'producedby_admin'); // The db
define('PRODUCEDBY_MEDIA', $prefixeTable.'producedby_media'); // The db

include_once(PRODUCEDBY_PATH . 'include/functions.inc.php');


/* +-----------------------------------------------------------------------+
 * | Plugin admin                                                          |
 * +-----------------------------------------------------------------------+ */

// Add an entry to the plugins menu
add_event_handler('get_admin_plugin_menu_links', 'producedby_admin_menu');
function producedby_admin_menu($menu) {
  array_push(
    $menu,
    array(
      'NAME'  => 'producedby',
      'URL'   => get_admin_plugin_menu_link(dirname(__FILE__)).'/admin.php'
    )
  );      
  return $menu;
}


/* +-----------------------------------------------------------------------+
 * | Plugin image                                                          |
 * +-----------------------------------------------------------------------+ */


// Add information to the picture's description (The copyright's name)
include_once(dirname(__FILE__).'/image.php');


/* +-----------------------------------------------------------------------+
 * | Plugin batchmanager                                                   |
 * +-----------------------------------------------------------------------+ */

// With the batchmanager, copyrights can be assigned to photos. There are two
// modes: Global mode, for mass assignment; Unit mode, for one by one
// assignment to the photos.

// The batch manager prefilters
include_once(dirname(__FILE__).'/filter.php');

// Global mode
include_once(dirname(__FILE__).'/batch_global.php');

// Unit mode
include_once(dirname(__FILE__).'/batch_single.php');

/* +-----------------------------------------------------------------------+
 * | Plugin picture_modify                                                 |
 * +-----------------------------------------------------------------------+ */

// Add the Copyrights dropdown menu to picture_modify
include_once(dirname(__FILE__).'/modify.php');


?>
