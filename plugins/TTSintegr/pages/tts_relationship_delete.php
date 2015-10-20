<?php
	/**
	 * @author Lennard Bredenkamp, BFE
	 * target script for delete link - delete from Mantis DB, pass data to testlink
	 * some of the original TL code commented out, just in case we need to reactivate some parts
	 */
	 
	include_once( 'core.php' );
	include_once( 'relationship_api.php' );

	form_security_validate( 'tts_relationship_delete' );
	
	$d_tts_exec_id = gpc_get_int( 'exec_id' );
	$d_tts_tproject_id = gpc_get_int( 'tproject_id' );
	$bug_id = gpc_get_string( 'bug_id' );

	# user has access to update the bug...
	// access_ensure_bug_level( config_get( 'update_bug_threshold' ), $f_src_bug_id );
	// $f_dest_bug_id_string = str_replace( ',', '|', $f_dest_bug_id_string );
	// $f_dest_bug_id_array = explode( '|', $f_dest_bug_id_string );
	
	// $t_relationship_info_html .= ' [<a class="small" href="'.plugin_page('tts_relationship_delete').'&amp;bug_id=' . $p_bug_id . '&amp;rel_id=' . $p_relationship->id . htmlspecialchars( form_security_param( 'bug_relationship_delete' ) ) . '">' . lang_get( 'delete_link' ) . '</a>]';
	
	//delete from TTS
	// $f_tts_relation = new TTSrelation($bug_id, $d_tts_exec_id, $d_tts_tproject_id); 
	
	// echo "Loesche: bug_id: $bug_id, d_tts_exec_id: $d_tts_exec_id, d_tts_tproject_id: $d_tts_tproject_id ";
	deletefromDB( $bug_id, $d_tts_exec_id, $d_tts_tproject_id );
	
	tts_log_history('delete', $d_tts_tproject_id, $d_tts_exec_id, $bug_id ); //write log entry (siehe TTSintegr.API.php)
	
	delete_in_testlink( $d_tts_tproject_id, $d_tts_exec_id, $bug_id );
	
	form_security_purge( 'tts_relationship_delete' );
	// print_header_redirect_view( $bug_id );

	// Loesche: bug_id: 103, d_tts_exec_id: 44, d_tts_tproject_id: 13 Löschanfrage an TestLink..
