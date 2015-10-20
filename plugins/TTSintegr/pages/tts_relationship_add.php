<?php
	/**
	 * @author Lennard Bredenkamp, BFE
	 * target script for form in tts_relations_box.inc.php - process form data, store in Mantis DB, pass data to testlink
	 * some of the original TL code commented out, just in case we need to reactivate some parts
	 */
	 
	include_once( 'core.php' );
	include_once( 'relationship_api.php' );
	
	form_security_validate( 'tts_relationship_add' );
	
	$f_tts_exec_id = gpc_get_int( 'exec_id' );
	$f_tts_tproject_id = gpc_get_int( 'tproject_id' );
	$bug_id = gpc_get_string( 'bug_id' );
	
	# user has access to update the bug...
	// access_ensure_bug_level( config_get( 'update_bug_threshold' ), $f_src_bug_id );
	// $f_dest_bug_id_string = str_replace( ',', '|', $f_dest_bug_id_string );
	// $f_dest_bug_id_array = explode( '|', $f_dest_bug_id_string );
	
	if ( $f_tts_exec_id == '' || $f_tts_tproject_id == '' )
	{	
			// print_header_redirect_view( $bug_id );
			# Summary cannot be blank
			// if( is_blank( $this->summary ) ) {
				// error_parameters( lang_get( 'summary' ) );
				error_parameters( '(Ausf&uuml;hrungs-ID/Testprojekt-ID)' );
				trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}
	//create new TTS
	else
	{
		// $twin_relation = TTSrelation::loadRelation( $bug_id, $f_tts_exec_id );
		// $twin_relation = loadRelation( $bug_id, $f_tts_exec_id );
		
		if ( !tts_relation_isDuplicate( $f_tts_tproject_id, $f_tts_exec_id, $bug_id ) ) //create relation only if it doesn't already exist
		{
			$f_tts_relation = new TTSrelation($bug_id, $f_tts_exec_id, $f_tts_tproject_id); 
			$f_tts_relation->savetoDB();
			
			tts_log_history('add', $f_tts_tproject_id, $f_tts_exec_id, $bug_id ); //write log entry (code: TTSintegr.API.php)
			
			pass_to_testlink( $f_tts_tproject_id, $f_tts_exec_id, $bug_id );
		}
		else trigger_error( ERROR_DUPLICATE_FILE, ERROR );
		// else trigger_error( ERROR_DUPLICATE_FILE, "Diese Verkn&uuml;pfung existiert bereits!" );
	}
	form_security_purge( 'tts_relationship_add' );
	// print_header_redirect_view( $bug_id );

