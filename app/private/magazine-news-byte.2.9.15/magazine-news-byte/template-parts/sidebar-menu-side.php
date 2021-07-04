<?php
// Dispay Sidebar if sidebar has widgets
if ( is_active_sidebar( 'hoot-menu-side' ) ) :

	?>
	<div <?php hoot_attr( 'menu-side-box', '', 'inline-nav js-search' ); ?>>
		<?php dynamic_sidebar( 'hoot-menu-side' ); ?>
	</div>
	<?php

endif;