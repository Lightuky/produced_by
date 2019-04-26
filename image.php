<?php

// Add an event handler for a prefilter
add_event_handler('loc_begin_picture', 'producedby_set_prefilter_add_to_pic_info', 55 );

// Change the variables used by the function that changes the template
add_event_handler('loc_begin_picture', 'producedby_add_image_vars_to_template');

// Add the prefilter to the template
function producedby_set_prefilter_add_to_pic_info()
{
	global $template;
	$template->set_prefilter('picture', 'producedby_add_to_pic_info');
}

// Insert the template for the copyright display
function producedby_add_to_pic_info($content, &$smarty)
{
	// Add the information after the author - so before the createdate
	$search = '#class="imageInfoTable">#';
	
	$replacement = 'class="imageInfoTable">
	<div id="producedby_name" class="imageInfo">
		<dt>{\'Produit par\'|@translate}</dt>
		<dd>
{if $PB_INFO_NAME}
			<a target="_blanc" href="{$PB_INFO_URL}" title="{$PB_INFO_NAME}: {$PB_INFO_DESCR}">{$PB_INFO_NAME}</a>
{else}
      {\'N/A\'|@translate}
{/if}
    </dd>
	</div>';

	return preg_replace($search, $replacement, $content, 1);
}

// Assign values to the variables in the template
function producedby_add_image_vars_to_template()
{
	global $page, $template, $prefixeTable;

	// Show block only on the photo page
	if ( !empty($page['image_id']) )
	{
		// Get the copyright name, url and description that belongs to the current media_item
		$query = sprintf('
		  select pb_id, name
		  FROM %s NATURAL JOIN %s
		  WHERE media_id = %s
		;',
		PRODUCEDBY_ADMIN, PRODUCEDBY_MEDIA, $page['image_id']);
		$result = pwg_query($query);
		$row = pwg_db_fetch_assoc($result);
		$name = '';
		if (isset($row) and count($row) > 0) {
			// If its the authors default copyright, get the data from the author table, instead of the copyright table
			if ($row['pb_id'] == -1) {
				// Check if the extended author plugin is active
				$query = '
					SELECT *
					FROM '.$prefixeTable.'plugins
					WHERE id=\'Extended_author\'
					;';
				$result = pwg_query($query);
				$row = pwg_db_fetch_assoc($result);
				
				// Only get the authors default copyright when it is active.
				if (count($row) > 0) {
					$query = sprintf('
						SELECT name
						FROM %s
						WHERE pb_id IN (
							SELECT a.producedby
							FROM '.$prefixeTable.'images i, '.$prefixeTable.'author_extended a
							WHERE i.id = %d
							AND i.author = a.name
						)
						;',
						PRODUCEDBY_ADMIN, $page['image_id']);
					$result = pwg_query($query);
					$row = pwg_db_fetch_assoc($result);
				}
			}
		}
		// Get the data from the chosen row
		if (isset($row) and count($row) > 0) {
			$name = $row['name'];
		}
			
		// Sending data to the template
    $template->assign(
      array	(
        'PB_INFO_NAME' => $name,
      )
    );
	}
}

?>
