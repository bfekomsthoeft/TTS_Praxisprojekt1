<?php
include_once("settings.php");
include_once( 'history_api.php' );


/**
 * @author Lennard Bredenkamp, BFE
 * provide data structure and functionality for TTSrelation objects (equivalent to single DB row and single row in tts relations box table in view.php)
 */
class TTSrelation 
{
	//Testcase-Name, Testplan-Name, Build-Name. Raus: Build-ID, Testplan-ID
	//only its_id, tts_exec_id and tts_tproject_id are used at the moment
	public $its_id;
	public $tts_exec_id;
	public $tts_tproject_id; 
	public $tts_tplan_id;
	public $tts_build_id; 
	public $tts_tproject_name; 
	public $tts_tplan_name;
	public $tts_build_name;
	
	public $tts_tcase_name; //added v1.1
	
	//needs to be extended if more data is needed in table
	function __construct( $its_id, $tts_exec_id, $tts_tproject_id )
	{
		$this->its_id = $its_id;
		$this->tts_exec_id = $tts_exec_id;
		$this->tts_tproject_id = $tts_tproject_id;
	}
	
	/**
	 * @author Lennard Bredenkamp, BFE
	 * save this object as a row in DB
	 */
	function savetoDB() //DB table needs to be extended: tcase_name
	{
		$t_project_table = plugin_table( 'project', 'TTSintegr' );				
		
		$t_query = "INSERT INTO $t_project_table ( its_id, tts_exec_id, tts_tproject_id, tts_tplan_id, tts_build_id, tts_tproject_name, tts_tplan_name, tts_build_name ) VALUES (
			" . db_param() . ',
			' . db_param() . ',
			' . db_param() . ',
			' . db_param() . ',
			' . db_param() . ',
			' . db_param() . ',
			' . db_param() . ',
			' . db_param() . ' )';
		db_query_bound( $t_query, array(
			$this->its_id,
			$this->tts_exec_id,
			$this->tts_tproject_id,
			NULL,
			NULL,
			'',
			'',
			'',
		) );
	}
	
	/**
	 * @author Lennard Bredenkamp, BFE
	 * NOT USED AT THE MOMENT (loadRelations is used to load multiple relations instead)
	 * get single tts_relation object from DB
	 * @param $p_bug_id
	 * @param $tts_exec_id
	 * @return TTSrelation object
	 */
	static function loadRelation( $p_bug_id, $tts_exec_id ) 
	{
		$t_project_table = plugin_table( 'project', 'TTSintegr' );
		$its_id = $p_bug_id;
		
		$t_query = "SELECT * FROM $t_project_table WHERE its_id=" . db_param() . " AND tts_exec_id=" . db_param();
		$t_result = db_query_bound( $t_query, array( $its_id, $tts_exec_id ) );
		
		$t_row = db_fetch_array( $t_result );
		$t_relation = new TTSrelation( $t_row[ 'its_id' ], $t_row[ 'tts_exec_id' ], $t_row[ 'tts_tproject_id' ]  );
		return $t_relation;
	}
	

}

/**
 * @author Lennard Bredenkamp, BFE
 * delete row from DB (single TTSrelation entry)
 * 
 */

function deletefromDB( $bug_id, $exec_id, $tproject_id )
{
	$its_id = $bug_id;
	$t_project_table = plugin_table( 'project', 'TTSintegr' );
	//$c_relationship_id = db_prepare_int( $p_relationship_id );


	$query = "DELETE FROM $t_project_table
				WHERE its_id=" . db_param() .
				" AND tts_exec_id=" . db_param() .
				" AND tts_tproject_id=" . db_param();
	$result = db_query_bound( $query, array( $its_id, $exec_id, $tproject_id ) );
	
}

/**
 * @author Lennard Bredenkamp, BFE
 * loads all relations linked to this issue from mantis_plugin_ttsintegr_project_table in bugtracker-DB
 * @return Array of TTSrelation objects
 */
function loadRelations( $its_id )
{
	$t_project_table = plugin_table( 'project', 'TTSintegr' );
	
	$query = "SELECT * FROM $t_project_table WHERE its_id = " . db_param();
	
	$result = db_query_bound( $query, array( $its_id ) ); /* $c_its_id */

	$t_bug_relationship_data = array();
	$t_relationship_count = db_num_rows( $result );
	
	for( $i = 0; $i < $t_relationship_count; $i++ ) 
	{
		$row = db_fetch_array( $result );
		//$t_bug_relationship_data[$i] = new BugRelationshipData;
		$t_relations[$i] = new TTSrelation( $row['its_id'], $row['tts_exec_id'], $row['tts_tproject_id']  );
		$t_bug_array[] = $row['source_bug_id'];
	}
	unset( $t_relations[$t_relationship_count] );//leeres Objekt löschen?
	
	return $t_relations;	
	
}

/**
 * @author Lennard Bredenkamp, BFE
 * test, whether new relation is a duplicate of an already existing TTTs relation. 
 * If relation already exists, tts_relationship_add will then trigger "Duplicate" Error
 * @return false if no duplicate is found, true if duplicate is found
 */
function tts_relation_isDuplicate ( $tproject_id, $exec_id, $bug_id )
{
	$relations = loadRelations( $bug_id );
		
	// var_dump( $relations );
	$twin = false;
	if ( $relations != null ) 
	{	
		foreach ( $relations as $relation )
		{
			// echo "<br>relation->tts_exec_id: $relation->tts_exec_id, f_tts_exec_id: $f_tts_exec_id, relation->tts_tproject_id: $relation->tts_tproject_id, f_tts_tproject_id: $f_tts_tproject_id <br>";
			if ( $relation->tts_exec_id == $exec_id && $relation->tts_tproject_id == $tproject_id ) 
			{
				$twin = true;
				return $twin; // abort search if one duplicate is found
			}
		}
	}
	return $twin;
}

/**
 * @author Lennard Bredenkamp, BFE
 * pass on data to TestLink in order to create new bug relation in TestLink, uses form with hidden fields, form sent using javascript
 */
function pass_to_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id )
{
	$settings = new Settings();
	//form using hidden fields in order to pass on data to TestLink
	
	echo "Leite Daten weiter an TestLink...";
	?>
	<form name="bfe_tts_form" action="<?php echo $settings->tts_location; ?>lib/execute/bugAddOrDeleteBFE.php" method="POST"> <!--  opens TestLink php file which then stores data in TestLink DB and redirects to Mantis  -->
		<input type="hidden" name="tproject_id" id="tproject_id" value="<?php echo $tts_tproject_id; ?>">
		<input type="hidden" name="exec_id" id="exec_id" value="<?php echo $tts_exec_id; ?>" >
		<input type="hidden" name="bug_id" id="bug_id" value="<?php echo $p_bug_id; ?>" >
	</form>
	<script>
	document.bfe_tts_form.submit(); //automatically send form
	</script>
	<?php
}

/**
 * @author Lennard Bredenkamp, BFE
 * ask TestLink to delete data for this specific tts relation, using hidden fields in HTML form, automatically sent using javascript
 * @return Array of TTSrelation objects
 */
function delete_in_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id )
{
$settings = new Settings();
?>
L&ouml;schanfrage an TestLink..
<form name="bfe_tts_delete_form" action="<?php echo $settings->tts_location; ?>lib/execute/bugAddOrDeleteBFE.php" method="POST"> <!--  opens TestLink php file which then stores data in TestLink DB and redirects to Mantis  -->
	<input type="hidden" name="tproject_id" id="tproject_id" value="<?php echo $tts_tproject_id; ?>">
	<input type="hidden" name="exec_id" id="exec_id" value="<?php echo $tts_exec_id; ?>" >
	<input type="hidden" name="bug_id" id="bug_id" value="<?php echo $p_bug_id; ?>" >
	<input type="hidden" name="bug_delete" id="bug_delete" value="true" >
</form>
<script>
	document.bfe_tts_delete_form.submit(); //send form
</script>
<?php
}

/**
 * @author Lennard Bredenkamp, BFE
 * provide link for deletion, trigger delete process
 * 
 */
function print_delete_from_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id )
{
	//uses form security function from mantis
	echo '[<a class="small" href="'.plugin_page('tts_relationship_delete').'&amp;bug_id=' . $p_bug_id . '&amp;exec_id=' . $tts_exec_id . '&amp;tproject_id=' . $tts_tproject_id .  htmlspecialchars( form_security_param( 'tts_relationship_delete' ) ) . '">' . lang_get( 'delete_link' ) . '</a>]';
}

/**
 * @author Lennard Bredenkamp, BFE
 * creates link to TestLink, exec_id and tproject_id via GET (plain text)
 * 
 */
function create_link_to_tc_execution($tproject_id, $exec_id)
{
	$settings = new Settings();
	// echo "<a href='http://localhost/testlink/lib/execute/BFE_mantis_toTL_linkprocessing.php?exec_id=".$exec_id."&tproject_id=".$tproject_id."'>".$exec_id."</a>";
	// echo $exec_id." ";
	// echo "[ <a href='".$settings->tts_location."lib/execute/BFE_mantis_toTL_linkprocessing.php?exec_id=".$exec_id."&tproject_id=".$tproject_id."' target='_blank'>Im TestTracker &ouml;ffnen</a> ]"; //v1.0
	echo " [ <a class='small' href='".$settings->tts_location."lib/execute/BFE_mantis_toTL_linkprocessing.php?exec_id=".$exec_id."&tproject_id=".$tproject_id."' target='_blank'>Im TTS &ouml;ffnen</a> ]"; //v1.1
}


/**
 * @author Lennard Bredenkamp, BFE
 * compose and store issue history log entry, action ( 'add' or 'delete') determines the text
 */
function tts_log_history ( $action, $tproject_id, $exec_id, $bug_id )
{
	switch ( $action )
	{
		case 'add': //ADD
			$change_text = "mit [ Testprojekt-ID: $tproject_id Ausf&uuml;hrungs-ID: $exec_id ]";

			$field_text = "Testausf&uuml;hrung verkn&uuml;pft";
			break;
			
		case 'delete': //DELETE
			$change_text = "Verkn&uuml;pfung gel&ouml;scht: [ Testprojekt-ID: $tproject_id Ausf&uuml;hrungs-ID: $exec_id ]";
			//man könnte hier noch einen Link zur Ausführung einfügen (besonders, falls gelöscht?) .create_link_to_tc_execution($f_tts_tproject_id, $f_tts_exec_id);
			
			$field_text = "Testausf&uuml;hrung abgekoppelt";
			break;
	}
	// function history_log_event_direct( $p_bug_id, $p_field_name, $p_old_value, $p_new_value, $p_user_id = null, $p_type = 0 )
	//p_type: ENUMERATION/CONSTANT -> number (defined in core/constant_inc.php), standard: normal, 0)
	history_log_event_direct( $bug_id, $field_text, '', $change_text );	
}