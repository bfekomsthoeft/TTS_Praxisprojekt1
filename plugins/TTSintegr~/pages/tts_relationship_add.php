<?php
	/**
	 * @author Lennard Bredenkamp, BFE
	 * target script for form in tts_relations_box.inc.php - process form data, store in Mantis DB, pass data to testlink
	 * some of the original TL code commented out, just in case we need to reactivate some parts
	 */
	 
	include_once( 'core.php' );
	include_once( 'relationship_api.php' );

	// form_security_validate( 'bug_relationship_add' );
	
	$f_tts_exec_id = gpc_get_int( 'exec_id' );
	$f_tts_tproject_id = gpc_get_int( 'tproject_id' );
	$bug_id = gpc_get_string( 'bug_id' );

	# user has access to update the bug...
	// access_ensure_bug_level( config_get( 'update_bug_threshold' ), $f_src_bug_id );
	// $f_dest_bug_id_string = str_replace( ',', '|', $f_dest_bug_id_string );
	// $f_dest_bug_id_array = explode( '|', $f_dest_bug_id_string );
	
	//create new TTS
	$f_tts_relation = new TTSrelation($bug_id, $f_tts_exec_id, $f_tts_tproject_id); 
	$f_tts_relation->savetoDB();
	
	pass_to_testlink( $f_tts_tproject_id, $f_tts_exec_id, $bug_id );
	
	// form_security_purge( 'bug_relationship_add' );
	// print_header_redirect_view( $bug_id );
