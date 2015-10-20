<?php
	# bfe_clone_issue_to_project.php
	# wird vom benutzerdefinierten Button 'Klon in anderes Projekt...' aufgerufen.
	# Die Definition von 'Klon in anderes Projekt...' befindet sich in
	# custom_functions_inc.php

	# Diese Seite macht zunaechst einen Redirect auf die login_select_proj_page.php

	$g_allow_browser_cache = 1;
	require_once( 'core.php' );

	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );

	print_header_redirect( 'login_select_proj_page.php?ref=bfe_clone_issue_to_project.php?m_id='.$_POST[ 'm_id' ]  );

?>
