<?php
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

function plugin_install() {
	global $prefixeTable;
	$query = '
		CREATE TABLE IF NOT EXISTS '.$prefixeTable.'producedby_admin (
			pb_id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(255) UNIQUE NOT NULL,
			PRIMARY KEY (pb_id)
		) ENGINE=MyISAM DEFAULT CHARACTER SET utf8
		;';
	pwg_query($query);

  $query = '
    CREATE TABLE IF NOT EXISTS '.$prefixeTable.'producedby_media (
      media_id int(11) NOT NULL,
      pb_id int(11) NOT NULL,
      PRIMARY KEY (media_id)
    ) ENGINE = MyISAM DEFAULT CHARACTER SET utf8
    ;';
  pwg_query($query);
}

function plugin_activate() {
  global $prefixeTable;

  $query = '
    SELECT COUNT(*)
    FROM '.$prefixeTable.'producedby_admin
    ;';
  list($counter) = pwg_db_fetch_row(pwg_query($query));
  if (0 == $counter) {
    producedby_create_default();
  }
}

function plugin_uninstall() {
  global $prefixeTable;

  $query = '
    DROP TABLE '.$prefixeTable.'producedby_admin
    ;';
  pwg_query($query);

  $query = '
    DROP TABLE '.$prefixeTable.'producedby_media
    ;';
  pwg_query($query);
}

function producedby_create_default() {
  global $prefixeTable;

  // Insert the copyrights of Creative Commons
  $inserts = array(
    array(
      'name' => 'auteur 1'
    ),
    array(
      'name' => 'auteur 2'
    ),
    array(
      'name' => 'auteur 3'
    ),
    array(
      'name' => 'auteur 4'
    )
  );

  mass_inserts(
    $prefixeTable.'producedby_admin',
    array_keys($inserts[0]),
    $inserts
  );
}

?>
