<div class="wrap">

	<h1>Nitro K9 Pricing</h1>

	<form id="nitro-k9-pricing-form" method="post" action="options.php" autocomplete="off">

		<?php

		settings_fields( 'nitro_k9_settings' );
		do_settings_sections( 'nitro_k9_settings' );

		/** @var \NitroK9\PriceGroup[] $price_groups */
		$price_groups = $this->price_groups;

		?>

		<input id="nitro-k9-pricing" type="hidden" value="" name="nitro_k9_pricing">

	</form>

	<form id="nitro-k9-pricing-form-fields" autocomplete="off">

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Active</th>
					<th colspan="2">Title</th>
					<th>Price</th>
				</tr>
			</thead>
			<?php foreach ( $price_groups as $id => $price_group ) { ?>
				<?php

				/** @var \NitroK9\Price[] $prices */
				$prices = $price_group->getPrices();

				?>
				<tr class="info">
					<td<?php if ( ! $price_group->isActive() ) { ?> class="danger"<?php } ?>>
						<select class="form-control narrow" name="pg_active_<?php echo $price_group->getId(); ?>">
							<option value="1"<?php if ( $price_group->isActive() ) {?> selected<?php } ?>>
								Active
							</option>
							<option value="0"<?php if ( ! $price_group->isActive() ) {?> selected<?php } ?>>
								Hidden
							</option>
						</select>
					</td>
					<td colspan="2">
						<input name="pg_title_<?php echo $price_group->getId(); ?>" class="form-control" value="<?php echo htmlspecialchars( $price_group->getTitle() ); ?>">
					</td>
					<td>
						<?php if ( count( $prices ) == 1 ) { ?>
							<input name="p_price_<?php echo $price_group->getId(); ?>_0" class="form-control narrow" value="<?php echo ( $prices[0]->getPrice() == 0 ) ? '' : '$' . number_format( $prices[0]->getPrice(), 2 ); ?>">
						<?php } ?>
					</td>
				</tr>
				<?php if ( count( $prices ) > 1 ) { ?>
					<?php foreach ( $prices as $index => $price ) { ?>
						<tr>
							<td<?php if ( ! $price->isActive() ) { ?> class="danger"<?php } ?>>
								<select class="form-control narrow" name="p_active_<?php echo $price_group->getId(); ?>_<?php echo $index; ?>">
									<option value="1"<?php if ( $price->isActive() ) {?> selected<?php } ?>>
										Active
									</option>
									<option value="0"<?php if ( ! $price->isActive() ) {?> selected<?php } ?>>
										Hidden
									</option>
								</select>
							</td>
							<td align="center"><span class="dashicons dashicons-arrow-right"></span></td>
							<td>
								<input name="p_title_<?php echo $price_group->getId(); ?>_<?php echo $index; ?>" class="form-control" value="<?php echo htmlspecialchars( $price->getTitle() ); ?>">
							</td>
							<td>
								<input name="p_price_<?php echo $price_group->getId(); ?>_<?php echo $index; ?>" class="form-control narrow" value="<?php echo ( $price->getPrice() == 0 ) ? '' : '$' . number_format( $price->getPrice(), 2 ); ?>">
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</table>

		<p>
			<input name="submit" id="nitro-k9-pricing-submit" class="button button-primary" value="<?php _e( 'Save Pricing', 'nitro-k9' ); ?>" type="submit">
		</p>

	</form>

</div>

