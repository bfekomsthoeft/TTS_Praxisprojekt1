<?php
	if ( is_page_name( 'login_page' ) ) {
		$t_align = 'center';
	} else {
		$t_align = 'right'; /*BFE_DK_von left auf right geändert*/
	}

	echo '<div align="', $t_align, '">';
	if ( is_page_name( 'login_page' ) ) {
		echo '<a href="http://www.bfe.tv" title="BFE Homepage"><img border="0" alt="BFE Homepage" src="images/bfe_logo.jpg" /></a>';
	} else {
		echo '<a href="/" title="BFE ITS Homepage"><img border="0" alt="BFE ITS Homepage" src="images/bfe_logo.jpg" /></a>';
	}
/*	if ( is_page_name( 'login_page' ) ) {
		echo '<br />';
        	echo '<div class="menu" style="background-color:red;"><p><b>Achtung, dieses System ist nicht im produktiven Einsatz!</b></p>';
		echo '<p><b>Wenn Sie durch &quot;raten&quot; hier gelandet sind, sind Sie hier falsch!</b></p>';
		echo '<p><b>Sie m&ouml;chten wahrscheinlich nach: </b><a href="https://issuetracking.bfe.tv">https://issuetracking.bfe.tv</a></p></div>';
	}*/
	echo '</div>';
?>