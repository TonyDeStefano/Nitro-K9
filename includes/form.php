<?php

/** @var \NitroK9\Controller $this */

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

	<?php if ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_EMAIL ) { ?>

		<input type="hidden" name="nitro_k9_id" value="">
		<input type="hidden" name="nitro_k9_hash" value="">

	<?php } else { ?>

		<input type="hidden" name="nitro_k9_id" value="<?php echo $entry->getId(); ?>">
		<input type="hidden" name="nitro_k9_hash" value="<?php echo $entry->getHash(); ?>">

	<?php } ?>

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

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_OWNER ) { ?>

		<?php $owner = $entry->getOwners()[$entry->getCurrentOwner()]; ?>

		<h2>
			Additional Owner Info
			<?php if ( count( $entry->getOwners() ) > 1 ) { ?>
				( #<?php echo $entry->getCurrentOwner()+1; ?> / <?php echo count( $entry->getOwners() ); ?> )
			<?php } ?>
		</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'email', 'Email Address', TRUE, $owner->getInfoItem( 'email' ), 'email' );
		\NitroK9\Entry::drawFormRow( 'first_name', 'First Name', TRUE, $owner->getInfoItem( 'first_name' ) );
		\NitroK9\Entry::drawFormRow( 'last_name', 'Last Name', TRUE, $owner->getInfoItem( 'last_name' ) );
		\NitroK9\Entry::drawFormRow( 'address', 'Address', TRUE, $owner->getInfoItem( 'address' ) );
		\NitroK9\Entry::drawFormRow( 'city', 'City', TRUE, $owner->getInfoItem( 'city' ) );
		\NitroK9\Entry::drawFormRow( 'state', 'State', TRUE, $owner->getInfoItem( 'state' ) );
		\NitroK9\Entry::drawFormRow( 'zip', 'Zip', TRUE, $owner->getInfoItem( 'zip' ) );
		\NitroK9\Entry::drawFormRow( 'home_phone', 'Home Phone', FALSE, $owner->getInfoItem( 'home_phone' ) );
		\NitroK9\Entry::drawFormRow( 'work_phone', 'Work Phone', FALSE, $owner->getInfoItem( 'work_phone' ) );
		\NitroK9\Entry::drawFormRow( 'cell_phone', 'Cell Phone', FALSE, $owner->getInfoItem( 'cell_phone' ) );

		?>

		<div class="well">

			<?php

			\NitroK9\Entry::drawFormRow( 'em_contact', 'Emergency Contact', FALSE, $owner->getInfoItem( 'em_contact' ) );
			\NitroK9\Entry::drawFormRow( 'em_relationship', 'Relationship', FALSE, $owner->getInfoItem( 'em_relationship' ) );
			\NitroK9\Entry::drawFormRow( 'em_home_phone', 'Home Phone', FALSE, $owner->getInfoItem( 'em_home_phone' ) );
			\NitroK9\Entry::drawFormRow( 'em_work_phone', 'Work Phone', FALSE, $owner->getInfoItem( 'em_work_phone' ) );
			\NitroK9\Entry::drawFormRow( 'em_cell_phone', 'Cell Phone', FALSE, $owner->getInfoItem( 'em_cell_phone' ) );

			?>

		</div>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_COUNT ) { ?>

		<h2>How many pets would you like to enroll?</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'large_dogs', 'Large Dogs (>= 35lbs)', TRUE, $entry->getLargeDogs(), 'select', array( 0,1,2,3,4,5 ) );
		\NitroK9\Entry::drawFormRow( 'small_dogs', 'Small Dogs (< 35lbs)', TRUE, $entry->getSmallDogs(), 'select', array( 0,1,2,3,4,5 ) );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_INFO ) { ?>

		<?php $pet = $entry->getPets()[ $entry->getCurrentPet() ]; ?>

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

		<?php

		\NitroK9\Entry::drawFormRow( 'name', 'Name', TRUE, $pet->getInfoItem( 'name' ) );
		\NitroK9\Entry::drawFormRow( 'breed', 'Breed', TRUE, $pet->getInfoItem( 'breed' ) );
		\NitroK9\Entry::drawFormRow( 'color', 'Color', TRUE, $pet->getInfoItem( 'color' ) );
		\NitroK9\Entry::drawFormRow( 'gender', 'Gender', TRUE, $pet->getInfoItem( 'gender' ), 'select', array( 'Male' => 'M', 'Female' => 'F' ) );
		\NitroK9\Entry::drawFormRow( 'dob', 'Date of Birth', FALSE, $pet->getInfoItem( 'dob' ) );
		\NitroK9\Entry::drawFormRow( 'age', 'Age', FALSE, $pet->getInfoItem( 'age' ) );
		\NitroK9\Entry::drawFormRow( 'weight', 'Weight', FALSE, $pet->getInfoItem( 'weight' ) );
		\NitroK9\Entry::drawFormRow( 'is_aggressive', 'Aggressive?', TRUE, ( $pet->isAggressive() ) ? 1 : 0, 'select', array( 'No' => '0', 'Yes' => '1' ) );

		?>

		<h2>Identification</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'id_tag', 'ID Tag', FALSE, $pet->getInfoItem( 'id_tag' ) );
		\NitroK9\Entry::drawFormRow( 'tattoo', 'Tattoo', FALSE, $pet->getInfoItem( 'tattoo' ) );
		\NitroK9\Entry::drawFormRow( 'microchip', 'Microchip', FALSE, $pet->getInfoItem( 'microchip' ) );
		\NitroK9\Entry::drawFormRow( 'vet', 'Veterinarian', FALSE, $pet->getInfoItem( 'vet' ) );
		\NitroK9\Entry::drawFormRow( 'vet_phone', 'Vet Phone', FALSE, $pet->getInfoItem( 'vet_phone' ) );
		\NitroK9\Entry::drawFormRow( 'medical_conditions', 'Medical Conditions', FALSE, $pet->getInfoItem( 'medical_conditions' ), 'textarea' );
		\NitroK9\Entry::drawFormRow( 'medication', 'Medication and Dosage', FALSE, $pet->getInfoItem( 'medication' ), 'textarea' );

		?>

		<h2>Feeding</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'food_provided_by', 'Food Provided By', FALSE, $pet->getInfoItem( 'food_provided_by' ), 'select', array( 'Nitro K9' => 'Nitro K9', 'Client' => 'Client' ) );
		\NitroK9\Entry::drawFormRow( 'feed_instructions', 'Feeding Instructions', FALSE, $pet->getInfoItem( 'feed_instructions' ), 'textarea' );

		?>

		<h2>Behavior</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'problems', 'List any problems your pet has with people, pets or situations', FALSE, $pet->getInfoItem( 'problems' ), 'textarea' );
		\NitroK9\Entry::drawFormRow( 'snapped', 'Has your pet ever snapped at anyone?', FALSE, $pet->getInfoItem( 'snapped' ), 'select', array( 'No' => 'N', 'Yes' => 'Y' ) );
		\NitroK9\Entry::drawFormRow( 'bitten', 'Has your pet ever bitten another animal?', FALSE, $pet->getInfoItem( 'bitten' ), 'select', array( 'No' => 'N', 'Yes' => 'Y' ) );
		\NitroK9\Entry::drawFormRow( 'share', 'Will your pet share toys with other animals?', FALSE, $pet->getInfoItem( 'share' ), 'select', array( 'No' => 'N', 'Yes' => 'Y' ) );
		\NitroK9\Entry::drawFormRow( 'jumped', 'Has your pet ever jumped a fence or barrier?', FALSE, $pet->getInfoItem( 'jumped' ), 'select', array( 'No' => 'N', 'Yes' => 'Y' ) );
		\NitroK9\Entry::drawFormRow( 'restrictions', 'List any restrictions that should be placed on your pet\'s activities', FALSE, $pet->getInfoItem( 'restrictions' ), 'textarea' );
		\NitroK9\Entry::drawFormRow( 'mark_or_spray', 'Does your pet mark or spray inside the house?', FALSE, $pet->getInfoItem( 'mark_or_spray' ), 'select', array( 'No' => 'N', 'Yes' => 'Y' ) );
		\NitroK9\Entry::drawFormRow( 'anything_else', 'Anything else you would like to share?', FALSE, $pet->getInfoItem( 'anything_else' ), 'textarea' );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_SERVICES ) { ?>

		<?php $pet = $entry->getPets()[ $entry->getCurrentPet() ]; ?>

		<h2>
			Services for
			<?php echo $pet->getInfoItem( 'name' ); ?>
		</h2>

		<p>Please selected the services that you are interested in for your pet:</p>

		<?php if ( $pet->getType() == \NitroK9\Pet::TYPE_LARGE_DOG ) { ?>

			<h2>Nitro Dog Training</h2>

			<?php if ( $pet->isAggressive() ) { ?>

				<?php

				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_AGGRESSIVE_EVAL ], $pet );
				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_AGGRESSIVE_HOURLY ], $pet );
				
				?>

			<?php } else { ?>

				<?php

				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_STANDARD_EVAL ], $pet );
				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_STANDARD_HOURLY ], $pet );

				?>

			<?php } ?>

			<?php

			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_HANDS_ON_1 ], $pet );
			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_HANDS_ON_2 ], $pet );
			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_LG_HANDS_OFF_1 ], $pet );

			?>

		<?php } elseif ( $pet->getType() == \NitroK9\Pet::TYPE_SMALL_DOG ) { ?>

			<h2>Mini Heroes Training</h2>

			<?php if ( $pet->isAggressive() ) { ?>

				<?php

				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_AGGRESSIVE_EVAL ], $pet );
				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_AGGRESSIVE_HOURLY ], $pet );

				?>

			<?php } else { ?>

				<?php

				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_STANDARD_EVAL ], $pet );
				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_STANDARD_HOURLY ], $pet );

				?>

			<?php } ?>

			<?php

			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_HANDS_ON_1 ], $pet );
			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_HANDS_ON_2 ], $pet );
			\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_SM_HANDS_OFF_1 ], $pet );

			?>


		<?php } ?>

		<h2>Boarding</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_BOARDING_PER_NIGHT ], $pet );
		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_BOARDING_NIGHTS ], $pet );

		?>

		<h2>Board &amp; Train</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_BOOT_CAMP ], $pet );

		?>

		<h2>Pet Sitting</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::PET_SITTING ], $pet );
		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::PET_SITTING_VISITS ], $pet );

		?>

		<h2>Doggie Day Care</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_DAY_CARE ], $pet );
		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_DAY_CARE_PACKAGES ], $pet );

		?>

		<h2>Dog Walking</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_WALKING ], $pet );

		?>

		<h2>Personal Protection (Ring of Fire)</h2>

		<?php

		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_PERSONAL_PROTECTION_HOURLY ], $pet );
		\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ \NitroK9\PriceGroup::DOG_PERSONAL_PROTECTION ], $pet );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_AGGRESSION ) { ?>

		<?php $pet = $entry->getPets()[ $entry->getCurrentPet() ]; ?>

		<h2>
			Aggression Questions for
			<?php echo $pet->getInfoItem( 'name' ); ?>
		</h2>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_CONFIRM ) { ?>

		<h2>Confirmation</h2>

	<?php } ?>

	<div class="well clearfix nitro-k9-buttons">
		<div class="pull-right">
			<?php if ( $entry->getCurrentStep() != \NitroK9\Entry::STEP_CONFIRM ) { ?>
				<button class="btn btn-default" name="next_step">
					Next Step (<?php echo $entry->getNextStepName(); ?>)
					<i class="fa fa-chevron-right"></i>
				</button>
			<?php } ?>
			<?php if ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_BIO || $entry->getCurrentStep() == \NitroK9\Entry::STEP_OWNER ) { ?>
				<button class="btn btn-default" name="add_owner">
					<i class="fa fa-plus"></i>
					Add Another Owner
				</button>
			<?php } ?>
			<?php if ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_OWNER ) { ?>
				<button class="btn btn-default" name="remove_owner">
					<i class="fa fa-minus"></i>
					Remove This Owner
				</button>
			<?php } ?>
			<?php if ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_INFO ) { ?>
				<button class="btn btn-default" name="remove_pet">
					<i class="fa fa-minus"></i>
					Remove This Pet
				</button>
			<?php } ?>
			<?php if ( $entry->getCurrentStep() != \NitroK9\Entry::STEP_EMAIL ) { ?>
				<button class="btn btn-default" name="prior_step">
					<i class="fa fa-chevron-left"></i>
					Prior Step (<?php echo $entry->getPriorStepName(); ?>)
				</button>
			<?php } ?>
		</div>
	</div>

</form>
