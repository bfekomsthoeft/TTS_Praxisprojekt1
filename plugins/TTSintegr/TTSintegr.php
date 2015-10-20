<?php

class TTSintegrPlugin extends MantisPlugin 
{	
	
	function register() 
	{
		$this->name        = 'TTS-Verknuepfung';
		$this->description = 'Ermoeglicht die einfache Zusammenarbeit von TestLink und Mantis';
		$this->version     = '1.1';
		$this->requires    = array(
		  'MantisCore'       => '1.2.0',
		);
		$this->author      = 'Lennard Bredenkamp';
		$this->contact     = 'lennard.bredenkamp@gmail.com';
		$this->url         = 'www.bfe.tv';
	}

	/**
	 * Default plugin configuration.
	 */
	function hooks( ) {
		$hooks = array(
			'EVENT_VIEW_BUG_EXTRA' => 'tts_relations_view', //Darstellung der verknüpften Ausführungen
			'EVENT_REPORT_BUG_FORM_TOP' => 'data_input_from_tl', // von TestLink per POST übertragenen Daten in Mantis-form einbauen
			'EVENT_REPORT_BUG' => 'store_ttslink', // store TTS relation in Mantis-DB and pass on data to TestLink
			//'EVENT_BUG_DELETED' => 'delete_relation_on_bugdelete', //delete multiple relations? - delete function not needed (not needed for standard workflow)
		);
		return $hooks;
	}

	/**
	 * @author Lennard Bredenkamp, BFE
	 * (NOT USED AT THE MOMENT!)
	 * when bug is deleted in ITS, automatically delete Relation in Mantis-DB as well and trigger deletion in TestLink-DB
	 */
	function delete_relation_on_bugdelete( $p_bug_id )
	{
		//loadRelations
		//must be able to delete multiple relations! 
		// - loop here, trigger more than once (loop in PHP) - might create tab-mess (when opened in multiple tabs (redirect might not work with multiple delete processes))
		// - (more elegant): trigger only once, pass array of relations to bugAddOrDeleteBFE.php
		deletefromDB( $bug_id, $d_tts_exec_id, $d_tts_tproject_id );
		delete_in_testlink( $d_tts_tproject_id, $d_tts_exec_id, $bug_id );
	}
	
	function init ()
	{
		include_once( 'TTSintegr.API.php' );
		include_once( 'helper_api.php' ); // used for: alternate color (GUI, grey, light-grey)
		include_once( 'tts_relations_box.inc.php' ); 
	}
	

	/**
	 * @author Lennard Bredenkamp, BFE
	 * import the data which came from TestLink via POST
	 * and show data on bug report page
	 */
	function data_input_from_tl() //Importiere die Daten, die bei der Erzeugung eines neuen Bugtickets aus TestLink heraus übergeben wurden.
	{
		$args = $_POST;
		if ( isset( $args[ 'exec_id' ], $args[ 'tproject_id' ] ) )
		{
		$tts_exec_id = filter_var( gpc_get_string( 'exec_id' ), FILTER_SANITIZE_NUMBER_INT ); //only allow int to prevent code injections
		$tts_tproject_id =  filter_var( gpc_get_string( 'tproject_id' ), FILTER_SANITIZE_NUMBER_INT ); //only allow int to prevent code injections
		?>
		  
		  
		<tr <?php echo helper_alternate_class() ?>>
		<td class="category">
			TestTracker-Beziehung
		</td>
		<td>
			<table>
				<tr>
					<td style="padding-right: 30px">
						<b>Ausf&uuml;hrungs-ID:</b> <?php echo $tts_exec_id; ?>
					</td>
					<td >
						<b>Testprojekt-ID:</b> <?php echo $tts_tproject_id; ?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="tts_exec_id" id="tts_exec_id" value="<?php echo $tts_exec_id; ?>">
			<input type="hidden" name="tts_tproject_id" id="tts_tproject_id" value="<?php echo $tts_tproject_id; ?>">
		</td>
		</tr>
		<?php
		}
	}
	
	/**
	 * @author Lennard Bredenkamp, BFE
	 * store TTS relation in Mantis-DB and pass on data to TestLink
	 */
	function store_ttslink( $p_event, $p_data, $p_bug_id ) 
	{
		$args = $_POST;
		if ( isset( $args[ 'tts_exec_id' ], $args[ 'tts_tproject_id' ] ) )
		{
			//extract data from form POST
			$tts_exec_id = gpc_get_string( 'tts_exec_id' ); 
			$tts_tproject_id = gpc_get_string( 'tts_tproject_id' );
			
			//create new TTS relation Object and store in DB
			$tts_relation = new TTSrelation($p_bug_id, $tts_exec_id, $tts_tproject_id);
			$tts_relation->savetoDB();
			
			pass_to_testlink( $tts_tproject_id, $tts_exec_id, $p_bug_id );
		}
	}
	
	
	
	/**
	 * @author Lennard Bredenkamp, BFE
	 * display tts relationships box in bug view page
	 */
	function tts_relations_view ( $p_event, $p_bug_id )
	{
		$relations = loadRelations($p_bug_id); //loadRelations in file tts_relations_box_inc.php
		tts_relation_list_box( $relations, $p_bug_id );
	}
	
	/**
	 * @author Lennard Bredenkamp, BFE
	 * create DB table in which plugin data will be stored
	 * @return array
	 */
	function schema() { 
		return array(
			array( 'CreateTableSQL', array( plugin_table( 'project' ), "
				id					I		AUTOINCREMENT PRIMARY,
				its_id				I		NOTNULL UNSIGNED,
				tts_exec_id			I		NOTNULL UNSIGNED,
				tts_tproject_id		I		NOTNULL UNSIGNED,
				tts_tplan_id 		I		UNSIGNED,
				tts_build_id 		I		UNSIGNED,
				tts_tproject_name 	C(64)	DEFAULT \" '' \",
				tts_tplan_name 		C(64)	DEFAULT \" '' \",
				tts_build_name 		C(64)	DEFAULT \" '' \"
				" ) ),
		);
	}
}