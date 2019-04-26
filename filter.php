<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

add_event_handler('get_batch_manager_prefilters', 'PB_add_bm_filter');
add_event_handler('perform_batch_manager_prefilters', 'PB_perform_bm_filter', 50, 2);
add_event_handler('element_set_global_action', 'PB_element_set_bm_global_action');

// Add the filter to the list
function PB_add_bm_filter($prefilters)
{
	load_language('plugin.lang', dirname(__FILE__).'/');

	array_push($prefilters,
		array('ID' => 'avec auteur', 'NAME' => l10n('sans auteur')),
		array('ID' => 'sans auteur', 'NAME' => l10n('sans auteur'))
	);

	return $prefilters;
}

// Put the right photos in the prefilter array
function PB_perform_bm_filter($filter_sets, $prefilter)
{
	if ('avec auteur' == $prefilter) {
		// Select all photos in the copyrights table who´s CR not is 0
		$query = sprintf('
			SELECT media_id
			FROM %s
			WHERE pb_id <> 0
			;',
			PRODUCEDBY_MEDIA);
		array_push($filter_sets, array_from_query($query, 'media_id'));
	}

	if ('sans auteur' == $prefilter) {
		// Select all photo's, except the ones that have a copyright
		$query = sprintf('
			SELECT id
			FROM %s
			WHERE id NOT IN (
				SELECT media_id
				FROM %s
				WHERE pb_id <> 0
			)
			;',
			IMAGES_TABLE, PRODUCEDBY_MEDIA);
		array_push($filter_sets, array_from_query($query, 'id'));
	}

	return $filter_sets;
}

// Test if the filters aren't set incorrectly
function PB_element_set_bm_global_action($action)
{
	if (in_array(@$_SESSION['bulk_manager_filter']['prefilter'], array('avec auteur', 'sans auteur')) and $action == 'produced_by') {
		// let's refresh the page because we the current set might be modified
		redirect(get_root_url().'admin.php?page='.$_GET['page']);
	}
}

?>