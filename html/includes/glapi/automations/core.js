/**
  * Automation Core
  *
  * Theese functions provide automated object functions that
  * do not require server I/O.
  * 
  * See also:
  *   # automations/autowalk.js
  *   # map/manager/designer.js
  */

/**
  * Enable automation on an object
  *
  * This function is called by the map manager, whent it's about
  * to insert a new object on the grid.
  */ 
function auto_setup_object(uid,data) {

	// Check for automation path
	if ($defined(data.automate.path)) auto_path_register(uid, data.automate.path);

}

/**
  * Remove automation from an object
  *
  * This function is called by the map manager, whent it's about
  * to remove an object from the grid.
  */ 
function auto_remove_object(uid, data) {

	// Check for automation path
	if ($defined(data.automate.path)) auto_path_unregister(uid);

}

/**
  * Update automation object information
  *
  * This function is called by the map manager, whent it's about
  * to update an object on the grid.
  */ 
function auto_update_object(uid, data, old_data) {

	// Check for automation path
	if ($defined(data.automate.path)) auto_path_register(uid, data.automate.path);

}