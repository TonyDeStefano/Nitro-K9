<?php

$make_new_entry = TRUE;

if ( isset( $_GET['nitro_k9_id'] ) && isset( $_GET['nitro_k9_hash'] ) )
{
	$entry = new \NitroK9\Entry( $_GET['nitro_k9_id'] );
	if ( strlen( $entry->getHash() ) > 0 && $entry->getHash() == $_GET['nitro_k9_hash'] )
	{
		$make_new_entry = FALSE;
	}
}

if ( $make_new_entry )
{
	$entry = new \NitroK9\Entry();
}

?>

<?php if ( $this->getErrorCount() > 0 ) { ?>

	<div class="alert alert-danger">
		<ul style="margin-bottom:0">
			<?php foreach ( $this->getErrors() as $error ) { ?>
				<li>
					<?php echo $error; ?>
				</li>
			<?php } ?>
		</ul>
	</div>

<?php } ?>

<form method="post" class="form-horizontal nitro-k9-form">

	<?php wp_nonce_field(); ?>
	
	<input type="hidden" name="nitro_k9_id" value="<?php echo $entry->getId(); ?>">
	<input type="hidden" name="nitro_k9_hash" value="<?php echo $entry->getHash(); ?>">

	<?php if ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_EMAIL ) { ?>

		<h2>Let's start with your email address ...</h2>
		<p>
			If you have to stop half-way through filling this out, or if lose your internet connection,
			you can always come back to this page and enter your email to pick up where you left off.
		</p>

		<?php \NitroK9\Entry::drawFormRow( 'email', 'Email Address', TRUE, $entry->getEmail(), 'email'  ); ?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_BIO ) { ?>

		<h2>A little about you ...</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'email', 'Email Address', TRUE, $entry->getEmail(), 'email' );
		\NitroK9\Entry::drawFormRow( 'first_name', 'First Name', TRUE, $entry->getFirstName() );
		\NitroK9\Entry::drawFormRow( 'last_name', 'Last Name', TRUE, $entry->getLastName() );
		\NitroK9\Entry::drawFormRow( 'address', 'Address', TRUE, $entry->getAddress() );
		\NitroK9\Entry::drawFormRow( 'city', 'City', TRUE, $entry->getCity() );
		\NitroK9\Entry::drawFormRow( 'state', 'State', TRUE, $entry->getState() );
		\NitroK9\Entry::drawFormRow( 'zip', 'Zip', TRUE, $entry->getZip() );
		\NitroK9\Entry::drawFormRow( 'home_phone', 'Home Phone', FALSE, $entry->getHomePhone() );
		\NitroK9\Entry::drawFormRow( 'work_phone', 'Work Phone', FALSE, $entry->getWorkPhone() );
		\NitroK9\Entry::drawFormRow( 'cell_phone', 'Cell Phone', FALSE, $entry->getCellPhone() );

		?>

		<div class="well">

			<?php

			\NitroK9\Entry::drawFormRow( 'em_contact', 'Emergency Contact', FALSE, $entry->getEmContact() );
			\NitroK9\Entry::drawFormRow( 'em_relationship', 'Relationship', FALSE, $entry->getEmRelationship() );
			\NitroK9\Entry::drawFormRow( 'em_home_phone', 'Home Phone', FALSE, $entry->getEmHomePhone() );
			\NitroK9\Entry::drawFormRow( 'em_work_phone', 'Work Phone', FALSE, $entry->getEmWorkPhone() );
			\NitroK9\Entry::drawFormRow( 'em_cell_phone', 'Cell Phone', FALSE, $entry->getEmCellPhone() );

			?>

		</div>

		<?php

		\NitroK9\Entry::drawFormRow( 'how_heard', 'How did you hear about us?', FALSE, $entry->getHowHeard(), 'select', \NitroK9\Entry::getAllHowHeards() );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_COUNT ) { ?>

		<h2>How many pets would you like to enroll?</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'large_dogs', 'Large Dogs (>= 35lbs)', TRUE, $entry->getLargeDogs(), 'select', array(0,1,2,3,4,5) );
		\NitroK9\Entry::drawFormRow( 'small_dogs', 'Small Dogs (< 35lbs)', TRUE, $entry->getSmallDogs(), 'select', array(0,1,2,3,4,5) );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_INFO ) { ?>

		<?php $pet = $entry->getPets()[$entry->getCurrentPet()]; ?>

		<h2>
			Tell us about
			<?php if ( $pet->getInfoItem( 'name' ) == '' ) { ?>
				your <?php echo $pet->getType(); ?>
				<?php if ( count( $entry->getPets() ) > 1 ) { ?>
					( pet #<?php echo $entry->getCurrentPet()+1; ?> / <?php echo count( $entry->getPets() ); ?> )
				<?php } ?>
			<?php } else { ?>
				<?php echo $pet->getInfoItem( 'name' ); ?>
			<?php } ?>
		</h2>

	<?php } ?>

	<div class="well clearfix nitro-k9-buttons">
		<div class="pull-right">
			<?php if ( $entry->getCurrentStep() != \NitroK9\Entry::STEP_CONFIRM ) { ?>
				<button class="btn btn-default" name="next_step">
					Next Step
					<i class="fa fa-chevron-right"></i>
				</button>
			<?php } ?>
			<?php if ( $entry->getCurrentStep() != \NitroK9\Entry::STEP_EMAIL ) { ?>
				<button class="btn btn-default" name="prior_step">
					<i class="fa fa-chevron-left"></i>
					Prior Step
				</button>
			<?php } ?>
		</div>
	</div>

</form>
