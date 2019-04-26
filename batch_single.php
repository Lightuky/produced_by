<?php

// Add event handlers for the prefilter
add_event_handler('loc_end_element_set_unit', 'PB_set_prefilter_batch_single', 55 );
add_event_handler('loc_begin_element_set_unit', 'PB_batch_single_submit', 50 );

// Change the variables used by the function that changes the template
add_event_handler('loc_end_element_set_unit', 'PB_add_batch_single_vars_to_template');

// Add a prefilter to the template
function PB_set_prefilter_batch_single()
{
	global $template;
	$template->set_prefilter('batch_manager_unit', 'PB_batch_single');
}

// Insert the copyright selector to the template
function PB_batch_single($content, &$smarty)
{
	$search = "#<td><strong>{'Date de création'#";

	// We use the <tr> from the Creation date, and give them a new <tr>
	$replacement = '<td><strong>{\'Auteur\'|@translate}</strong></td>
		<td>
			<select id="producedby-{$element.ID}" name="producedby-{$element.ID}">
				<option value="">--</option>
				{html_options options=$PBoptions selected=$PBproducedby[$element.ID]}
			</select>
		</td>
	</tr>
	
	<tr>
		<td><strong>{\'Date de création\'';

  return preg_replace($search, $replacement, $content);
}

// Assign the variables to the Smarty template
function PB_add_batch_single_vars_to_template()
{
	global $template;

	load_language('plugin.lang', dirname(__FILE__).'/');

	// Fetch all the copyrights and assign them to the template
	$query = sprintf(
		'SELECT `pb_id`,`name`
		FROM %s
		ORDER BY pb_id ASC
		;',
		PRODUCEDBY_ADMIN);
	$result = pwg_query($query);

	$PBoptions = array();
	while ($row = pwg_db_fetch_assoc($result)) {
		$PBoptions[$row['pb_id']] = $row['name'];
	}
	$template->assign('PBoptions', $PBoptions);
	
	// Get the copyright for each element
	$query = sprintf(
		'SELECT `media_id`, `pb_id`
		FROM %s
		;',
		PRODUCEDBY_MEDIA);
	$result = pwg_query($query);
	
	$PBproducedby = array();
	while ($row = pwg_db_fetch_assoc($result)) {
		$PBproducedby[$row['media_id']] = $row['pb_id'];
  }

  // Assign the copyrights to the template
	$template->assign('PBproducedby', $PBproducedby);
}

// Catch the submit and update the copyrights tables
function PB_batch_single_submit()
{
	if (isset($_POST['submit']))
	{
		// The image id's:
		$collection = explode(',', $_POST['element_ids']);

		// Delete all existing id's of which the copyright is going to be set
		if (count($collection) > 0) {
			$query = sprintf(
				'DELETE
				FROM %s
				WHERE media_id IN (%s)
				;',
				PRODUCEDBY_MEDIA, implode(',', $collection));
			pwg_query($query);
		}

		// Add all copyrights to an array
		$edits = array();
		foreach ($collection as $image_id) {
			// The copyright id's
			$pbID = pwg_db_real_escape_string($_POST['producedby-'.$image_id]);

			// If you assign no copyright, dont put them in the table
			if ($pbID != '') {
				array_push(
					$edits,
					array(
						'media_id' => $image_id,
						'pb_id' => $pbID,
					)
				);
			}
		}

		if (count($edits) > 0) {
			// Insert the array to the database
			mass_inserts(
				PRODUCEDBY_MEDIA,        // Table name
				array_keys($edits[0]),   // Columns
				$edits                   // Data
			);
		}
	}
}

?>
