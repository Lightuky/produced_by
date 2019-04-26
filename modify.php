<?php

// Add a prefilter
add_event_handler('loc_begin_admin', 'PB_set_prefilter_modify', 50 );
add_event_handler('loc_begin_admin_page', 'PB_modify_submit', 45 );

// Change the variables used by the function that changes the template
add_event_handler('loc_begin_admin_page', 'PB_add_modify_vars_to_template');

function PB_set_prefilter_modify()
{
	global $template;
	$template->set_prefilter('picture_modify', 'PB_modify');
}

function PB_modify($content, &$smarty)
{
	$search = "#<strong>{'Creation date'#"; // Not ideal, but ok for now :)

	// We use the <tr> from the Creation date, and give them a new <tr>
	$replacement = '<strong>{\'Auteur\'|@translate}</strong>
		<br>
			<select id="producedbyID" name="producedbyID">
				<option value="">--</option>
				{html_options options=$PBoptions selected=$PBid}
			</select>
		</p>
	
	</p>
  <p>
		<strong>{\'Date de création\'';

    return preg_replace($search, $replacement, $content);
}

function PB_add_modify_vars_to_template()
{
	if (isset($_GET['page']) and 'photo' == $_GET['page'] and isset($_GET['image_id']))
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
		
		// Get the current Copyright
		$image_id = $_GET['image_id'];
		$query = sprintf(
			'SELECT `media_id`, `pb_id`
			FROM %s
			WHERE `media_id` = %d
			;',
			PRODUCEDBY_MEDIA, $image_id);
		$result = pwg_query($query);
		
		$PBid = 0; // Default is '--'
		while ($row = pwg_db_fetch_assoc($result)) {
			$PBid = $row['pb_id'];
		}
		$template->assign('PBid', $PBid);
	}
}

function PB_modify_submit()
{
  if (isset($_GET['page']) and 'photo' == $_GET['page'] and isset($_GET['image_id']))
	{
		if (isset($_POST['submit']))
		{
			// The data from the submit
			$image_id = $_GET['image_id'];
			$PBid = $_POST['producedbyID'];

			// Delete the Copyright if it allready exists
			$query = sprintf(
				'DELETE
				FROM %s
				WHERE `media_id` = %d
				;',
				PRODUCEDBY_MEDIA, $image_id);
			pwg_query($query);

			// If you assign no copyright, dont put it in the table
			if ($PBid != '') {
				// Insert the Copyright
				$query = sprintf(
					'INSERT INTO %s
					VALUES (%d, %d)
					;',
					PRODUCEDBY_MEDIA, $image_id, $PBid);
				pwg_query($query);
			}
		}
	}
}

?>