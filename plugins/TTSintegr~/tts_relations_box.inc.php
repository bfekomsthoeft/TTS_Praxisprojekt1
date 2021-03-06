<?php 
/**
 * @author Lennard Bredenkamp, BFE
 * displays test tracker relations box in bug view page
 * code taken from Mantis relationship_api.php and customized
 * 
 * depends on TTSintegr.API.php (included in TTSintegr.php)
 *
 * @param array $relation - array of objects (instances of TTSrelation class) - this is the Data of all the TTS relation to be displayed
 * @param int $p_bug_id - Mantis Bug ID
 */
function tts_relation_list_box( $relations, $p_bug_id ) {
?>
<a name="monitors" id="monitors" /><br />
	<div id="tts_relations_open">
		<table class="width100" cellspacing="1">
			<tr class="row-2" valign="top">
				<td width="15%" class="form-title" colspan="2">
					<a href="" onclick="ToggleDiv( 'tts_relations' ); return false;">
						<img border="0" src="images/minus.png" alt="-" /></a>
						&#160;TestTracker-Beziehungen 
				</td>
			</tr>
			<tr class="row-1">
				<td class="category" width="25%">Testausfuehrung hinzuf&uuml;gen</td> 
				<td>	
				<form method="post" action="<?php echo plugin_page('tts_relationship_add'); //function found in TTSintegr.API.php ?>"> 
					<b>Ausf&uuml;hrungs-ID</b> <input type="text" size="8" name="exec_id" id="exec_id" value="" maxlength="8">
					<b>Testprojekt-ID</b> <input type="text" size="8" name="tproject_id" id="tproject_id" value="" maxlength="8">
					<input type="hidden" name="bug_id" id="bug_id" value="<?php echo $p_bug_id; ?>">
					<input type="submit" name="abschicken3" value="Verknuepfen">
				</form>
				</td>
			</tr>
			
			<tr>
			<?php
			if (isset($relations)) 
			{
			?>
				<td colspan="2">
					<table border="0" width="100%" cellpadding="0" cellspacing="1">
						<tr>
							<th width="25%"><span class="nowrap">Link zum TTS</span>&#160;</th>
							<th width="15%">Ausf&uuml;hrungs-ID</th>
							<th width="15%">Testprojekt-ID</th>
							<th>Testplan-ID</th>
							<th>Build-ID</th>
							<th>Testprojekt-Name</th>
							<th>Testplan-Name</th>
							<th>Build-Name</th>
						</tr>
						<?php
						
							foreach ($relations as $i => $relation)
							{
						?>
							<tr <?php echo helper_alternate_class(); //alternate row colour (grey/light grey) ?> > 
								<td width="25%"><?php create_link_to_tc_execution( $relation->tts_tproject_id, $relation->tts_exec_id ); ?></td>
								<td><?php echo $relation->tts_exec_id; ?></td>
								<td width="15%"><?php echo $relation->tts_tproject_id; ?></td>
								
								<?php /* echo '[<a class="small" href="'.plugin_page('tts_relationship_delete').'?bug_id=' . $p_bug_id . '&amp;tproject_id=' . $relation->tts_tproject_id . '&amp;tproject_id=' . $relation->tts_tproject_id .  htmlspecialchars( form_security_param( 'tts_relationship_delete' ) ) . '">' . lang_get( 'delete_link' ) . '</a>]'; */ ?>
								<td width="15%"><?php echo $relation->tts_tplan_id; print_delete_from_testlink( $relation->tts_tproject_id, $relation->tts_exec_id, $p_bug_id ); ?></td>
								<td><?php echo $relation->tts_build_id; ?></td>
								<td><?php echo $relation->tts_tproject_name; ?></td>
								<td><?php echo $relation->tts_tplan_name; ?></td>
								<td><?php echo $relation->tts_build_name; ?></td>
							</tr>
							<?php
							}
			}
			?> 
					</table>
				</td>

			</tr>
		</table>
	</div>
	<div id="tts_relations_closed" class="hidden">
		<table class="width100" cellspacing="1">
			<tr>
				<td class="form-title">
					<a href="" onclick="ToggleDiv( 'tts_relations' ); return false;">
						<img border="0" src="images/plus.png" alt="+" /></a>
						&#160;TestTracker-Beziehungen 
				</td>
			</tr>
		</table>
	</div>
</a>
<?php
}
?>