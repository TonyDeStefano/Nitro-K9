<div class="wrap">

	<h1>Nitro K9 Pricing</h1>

	<form method="post" action="options.php">

		<?php

		settings_fields( 'nitro_k9_settings' );
		do_settings_sections( 'nitro_k9_settings' );

		/** @var \NitroK9\PriceGroup[] $price_groups */
		$price_groups = $this->price_groups;

		?>

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
					<td>
						<input type="checkbox"<?php if ( $price_group->isActive() ) {?> checked<?php } ?>>
					</td>
					<td colspan="2">
						<input class="form-control" value="<?php echo htmlspecialchars( $price_group->getTitle() ); ?>">
					</td>
					<td>
						<?php if ( count( $prices ) == 1 ) { ?>
							<input class="form-control price" value="<?php echo ( $prices[0]->getPrice() == 0 ) ? '' : '$' . number_format( $prices[0]->getPrice(), 2 ); ?>">
						<?php } ?>
					</td>
				</tr>
				<?php if ( count( $prices ) > 1 ) { ?>
					<?php foreach ( $prices as $index => $price ) { ?>
						<tr>
							<td>
								<input type="checkbox"<?php if ( $price->isActive() ) {?> checked<?php } ?>>
							</td>
							<td align="center"><span class="dashicons dashicons-arrow-right"></span></td>
							<td>
								<input class="form-control" value="<?php echo htmlspecialchars( $price->getTitle() ); ?>">
							</td>
							<td>
								<input class="form-control price" value="<?php echo ( $price->getPrice() == 0 ) ? '' : '$' . number_format( $price->getPrice(), 2 ); ?>">
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</table>

	</form>

</div>

