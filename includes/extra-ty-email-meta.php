<?php

global $post;
$custom = get_post_custom( $post->ID );
$active_email = ( array_key_exists( 'nitro_k9_active_email', $custom ) ) ? $custom[ 'nitro_k9_active_email' ][0] : 0;

?>

<table class="form-table">
	<tr>
		<th>
			<label for="nitro-k9-ty-email-is-active">
				Is Active:
			</label>
		</th>
		<td>
			<select name="ty_email_is_active" id="nitro-k9-ty-email-is-active">
				<option value="0"<?php if ( $active_email == '0' ) { ?> selected<?php } ?>>No</option>
				<option value="1"<?php if ( $active_email == '1' ) { ?> selected<?php } ?>>Yes</option>
			</select>
		</td>
	</tr>
</table>
