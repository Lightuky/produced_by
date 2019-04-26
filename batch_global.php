<?php

// Add copyrights drop down menu to the batch manager
add_event_handler('loc_end_element_set_global', 'producedby_batch_global');
// Add handler to the submit event of the batch manager
add_event_handler('element_set_global_action', 'producedby_batch_global_submit', 50, 2);

function producedby_batch_global()
{
	global $template;

	load_language('plugin.lang', dirname(__FILE__).'/');

	// Assign the template for batch management
	$template->set_filename('PB_batch_global', dirname(__FILE__).'/batch_global.tpl');

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


	// Add info on the "choose action" dropdown in the batch manager
	$template->append('element_set_global_plugins_actions', array(
		'ID' => 'produced_by',				// ID of the batch manager action
		'NAME' => l10n('Définir lauteur'),	// Description of the batch manager action
		'CONTENT' => $template->parse('PB_batch_global', true)
		)
	);
}

// Process the submit action
function producedby_batch_global_submit($action, $collection)
{
	// If its our plugin that is called
	if ($action == 'produced_by')
	{
		$pbID = pwg_db_real_escape_string($_POST['producedbyID']);

		// Delete any previously assigned copyrights
		if (count($collection) > 0) {
			$query = sprintf(
				'DELETE
				FROM %s
				WHERE media_id IN (%s)
				;',
			PRODUCEDBY_MEDIA, implode(',', $collection));
			pwg_query($query);
		}

		// If you assign no copyright, dont put them in the table
		if ($pbID != '') {
			// Add the copyrights from the submit form to an array
			$edits = array();
			foreach ($collection as $image_id) {
				array_push(
					$edits,
					array(
						'media_id' => $image_id,
						'pb_id' => $pbID,
					)
				);
			}

			// Insert the array into the database
			mass_inserts(
				PRODUCEDBY_MEDIA,		// Table name
				array_keys($edits[0]),	// Columns
				$edits					// Data
			);
		}
	}
}

?>