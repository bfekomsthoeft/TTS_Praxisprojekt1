<?php
include_once("settings.php");

class TTSrelation 
{
	public $its_id;
	public $tts_exec_id;
	public $tts_tproject_id; 
	public $tts_tplan_id;
	public $tts_build_id; 
	public $tts_tproject_name; 
	public $tts_tplan_name;
	public $tts_build_name;
	
	
	function __construct( $its_id, $tts_exec_id, $tts_tproject_id )
	{
		$this->its_id = $its_id;
		$this->tts_exec_id = $tts_exec_id;
		$this->tts_tproject_id = $tts_tproject_id;
	}
	
	function savetoDB()
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
	 * Load an existing TimecardBug object from the database.
	 * @param int Bug ID
	 * @return object TimecardBug object
	 */
	static function loadRelation( $p_bug_id, $tts_exec_id) 
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

function deletefromDB( $bug_id, $exec_id, $tproject_id )
{
	$its_id = $bug_id;
	$t_project_table = plugin_table( 'project', 'TTSintegr' );
	//$c_relationship_id = db_prepare_int( $p_relationship_id );


	$query = "DELETE FROM $t_project_table
				WHERE its_id=" . db_param() .
				"AND tts_exec_id=" . db_param();
				"AND tts_tproject_id=" . db_param();
	$result = db_query_bound( $query, array( $its_id, $exec_id, $tproject_id ) );
	
}

function loadRelations($its_id)
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

function pass_to_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id )
{
	$settings = new Settings();
	//form using hidden fields in order to pass on data to TestLink
	/*
	echo $tts_tproject_id;
	echo $tts_exec_id;
	echo $p_bug_id;
	*/
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
	document.bfe_tts_delete_form.submit(); //send form (after click on bracketlink)
</script>
<?php
}
	
function print_delete_from_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id )
{
	//form using hidden fields in order to pass on data to TestLink
	// echo "Sende L&ouml;schanfrage an TestLink";
	// print_bracket_link( "onclick:bfeDeleteaction()", "L&ouml;schen" );
	
	 echo '[<a class="small" href="'.plugin_page('tts_relationship_delete').'&amp;bug_id=' . $p_bug_id . '&amp;exec_id=' . $tts_exec_id . '&amp;tproject_id=' . $tts_tproject_id .  htmlspecialchars( form_security_param( 'tts_relationship_delete' ) ) . '">' . lang_get( 'delete_link' ) . '</a>]';
	
	/*
	$t_tts_info_html = ' [<a class="small" href="bug_relationship_delete.php?bug_id=' . $p_bug_id . '&amp;rel_id=' . $p_relationship->id . htmlspecialchars( form_security_param( 'bug_relationship_delete' ) ) . '">' . lang_get( 'delete_link' ) . '</a>]';
	echo $t_tts_info_html;
	
	[ <a href='javascript:void(0)' onclick='bfeDeleteaction()'>L&ouml;schen</a> ]
	
	<form name="bfe_tts_delete_form" action="http://localhost/testlink/lib/execute/bugAddOrDeleteBFE.php" method="POST"> <!--  opens TestLink php file which then stores data in TestLink DB and redirects to Mantis  -->
		<input type="hidden" name="tproject_id" id="tproject_id" value="<?php echo $tts_tproject_id; ?>">
		<input type="hidden" name="exec_id" id="exec_id" value="<?php echo $tts_exec_id; ?>" >
		<input type="hidden" name="bug_id" id="bug_id" value="<?php echo $p_bug_id; ?>" >
		<input type="hidden" name="bug_delete" id="bug_delete" value="true" >
	</form>
	<script>
	function bfeDeleteaction()
	{
		document.bfe_tts_delete_form.submit(); //send form (after click on bracketlink)
	}
	</script>
	*/
}

function create_link_to_tc_execution($tproject_id, $exec_id)
{
	$settings = new Settings();
	// echo "<a href='http://localhost/testlink/lib/execute/BFE_mantis_toTL_linkprocessing.php?exec_id=".$exec_id."&tproject_id=".$tproject_id."'>".$exec_id."</a>";
	// echo $exec_id." ";
	echo "[ <a href='".$settings->tts_location."lib/execute/BFE_mantis_toTL_linkprocessing.php?exec_id=".$exec_id."&tproject_id=".$tproject_id."' target='_blank'>Im TestTracker &ouml;ffnen</a> ]";
}