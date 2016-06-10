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

		$questions = $entry->getInfoQuestions( 1 );

		foreach ( $questions as $array )
		{
			\NitroK9\Entry::drawFormRow(
				$array[0],
				$array[1],
				$array[2],
				$array[3],
				( isset( $array[4] ) ) ? $array[4] : 'text',
				( isset( $array[5] ) ) ? $array[5] : array()
			);
		}

		?>

		<div class="well">

			<?php

			$questions = $entry->getInfoQuestions( 2 );

			foreach ( $questions as $array )
			{
				\NitroK9\Entry::drawFormRow(
					$array[0],
					$array[1],
					$array[2],
					$array[3],
					( isset( $array[4] ) ) ? $array[4] : 'text',
					( isset( $array[5] ) ) ? $array[5] : array()
				);
			}

			?>

		</div>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_OWNER ) { ?>

		<?php $owner = $entry->getOwners()[$entry->getCurrentOwner()]; ?>

		<h2>
			Additional Owner Info
			<?php if ( count( $entry->getOwners() ) > 1 ) { ?>
				( #<?php echo $entry->getCurrentOwner()+1; ?> / <?php echo count( $entry->getOwners() ); ?> )
			<?php } ?>
		</h2>

		<?php

		$questions = $owner->getInfoQuestions( 1 );

		foreach ( $questions as $question )
		{
			\NitroK9\Entry::drawFormRow(
				$questions[0],
				$questions[1],
				$questions[2],
				$questions[3],
				( isset( $questions[4] ) ) ? $questions[4] : 'text',
				( isset( $questions[5] ) ) ? $questions[5] : array()
			);
		}

		?>

		<div class="well">

			<?php

			$questions = $owner->getInfoQuestions( 2 );

			foreach ( $questions as $question )
			{
				\NitroK9\Entry::drawFormRow(
					$questions[0],
					$questions[1],
					$questions[2],
					$questions[3],
					( isset( $questions[4] ) ) ? $questions[4] : 'text',
					( isset( $questions[5] ) ) ? $questions[5] : array()
				);
			}

			?>

		</div>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_COUNT ) { ?>

		<h2>How many pets would you like to enroll?</h2>

		<?php

		\NitroK9\Entry::drawFormRow( 'large_dogs', 'Large Dogs (>= 35lbs)', TRUE, $entry->getLargeDogs(), 'select', array( 0,1,2,3,4,5 ) );
		\NitroK9\Entry::drawFormRow( 'small_dogs', 'Small Dogs (< 35lbs)', TRUE, $entry->getSmallDogs(), 'select', array( 0,1,2,3,4,5 ) );

		?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_INFO ) { ?>

		<?php

		$pet = $entry->getPets()[ $entry->getCurrentPet() ];
		$categories = $pet->getInfoQuestions();

		?>

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

		<?php foreach ( $categories as $category => $questions ) { ?>

			<?php if ( strlen( $category ) > 0 ) { ?>
				<h2><?php echo $category; ?></h2>
			<?php } ?>

			<?php

			foreach ( $questions as $question )
			{
				\NitroK9\Entry::drawFormRow(
					$question[0],
					$question[1],
					$question[2],
					$question[3],
					( isset( $question[4] ) ) ? $question[4] : 'text',
					( isset( $question[5] ) ) ? $question[5] : array()
				);
			}

			?>

		<?php } ?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_SERVICES ) { ?>

		<?php

		$pet = $entry->getPets()[ $entry->getCurrentPet() ];
		$categories = $pet->getPricingQuestions();

		?>

		<h2>
			Services for
			<?php echo $pet->getInfoItem( 'name' ); ?>
		</h2>

		<p>Please selected the services that you are interested in for your pet:</p>

		<?php foreach ( $categories as $category => $price_groups ) { ?>
			<h2><?php echo $category; ?></h2>
			<?php

			foreach ( $price_groups as $price_group )
			{
				\NitroK9\Entry::drawFormPriceRow( $this->price_groups[ $price_group ], $pet );
			}

			?>
		<?php } ?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_PET_AGGRESSION ) { ?>

		<?php $pet = $entry->getPets()[ $entry->getCurrentPet() ]; ?>

		<h2>
			Aggression Questions for
			<?php echo $pet->getInfoItem( 'name' ); ?>
		</h2>

		<p>Please answer the following questions to the best of your ability:</p>

		<?php $categories = \NitroK9\Pet::getAggressionQuestions( 1 ); ?>

		<?php foreach ( $categories as $category => $questions ) { ?>

			<h2><?php echo $category; ?></h2>

			<?php

			foreach ( $questions as $question )
			{
				\NitroK9\Entry::drawFormRow(
					$question[1],
					$question[0],
					FALSE,
					$pet->getAggressionItem( $question[1] ),
					( isset( $question[2] ) ) ? $question[2] : 'textarea',
					( isset( $question[3] ) ) ? $question[3] : array(),
					TRUE
				);
			}

			?>

		<?php } ?>

		<h2>
			What percent of the time does your dog obey the following commands for each member of the family?
		</h2>

		<?php $commands = \NitroK9\Pet::getAggressionQuestions( 2 ); ?>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<?php foreach ( $commands as $key => $command ) { ?>
						<th><?php echo $command; ?></th>
					<?php } ?>
				</tr>
			</thead>
			<?php for ( $x=1; $x<=10; $x++ ) { ?>
				<tr>
					<?php foreach ( $commands as $key => $command ) { ?>
						<td>
							<label>
								<input
									class="form-control"
									name="percent_<?php echo $x; ?>_<?php echo $key; ?>"
									value="<?php echo esc_html( $pet->getAggressionItem( 'percent_'.$x.'_'.$key ) ); ?>"
									<?php if ( $key != 'name' ) { ?>
										style="max-width:70px"
									<?php } ?>
								>
							</label>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
		</table>

		<?php $categories = \NitroK9\Pet::getAggressionQuestions( 3 ); ?>

		<?php foreach ( $categories as $category => $questions ) { ?>

			<h2><?php echo $category; ?></h2>

			<?php

			foreach ( $questions as $question )
			{
				\NitroK9\Entry::drawFormRow(
					$question[1],
					$question[0],
					FALSE,
					$pet->getAggressionItem( $question[1] ),
					( isset( $question[2] ) ) ? $question[2] : 'textarea',
					( isset( $question[3] ) ) ? $question[3] : array(),
					TRUE
				);
			}

			?>

		<?php } ?>

		<h2>Aggression Screen</h2>
		<p>Check all that apply:</p>

		<?php

		$responses = array(
			'growl' => 'Growl',
			'snarl' => 'Snarl / Bare Teeth',
			'snap' => 'Snap / Bite',
			'no' => 'No Reaction',
			'na' => 'N/A'
		);
		$causes = \NitroK9\Pet::getAggressionQuestions( 4 );

		?>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Action</th>
					<?php foreach ( $responses as $key => $response ) { ?>
						<th><?php echo $response; ?></th>
					<?php } ?>
				</tr>
			</thead>
			<?php foreach ( $causes as $index => $cause ) { ?>
				<tr>
					<th><?php echo $cause; ?></th>
					<?php foreach ( $responses as $key => $response ) { ?>
						<td style="text-align:center">
							<label>
								<input type="checkbox" value="1" name="screen_<?php echo $index; ?>_<?php echo $key; ?>"<?php if ( strlen( $pet->getAggressionItem( 'screen_'.$index.'_'.$key ) ) ) { ?> checked<?php } ?> >
							</label>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
		</table>

		<?php $categories = \NitroK9\Pet::getAggressionQuestions( 5 ); ?>

		<?php foreach ( $categories as $category => $questions ) { ?>

			<h2><?php echo $category; ?></h2>

			<?php

			foreach ( $questions as $question )
			{
				\NitroK9\Entry::drawFormRow(
					$question[1],
					$question[0],
					FALSE,
					$pet->getAggressionItem( $question[1] ),
					( isset( $question[2] ) ) ? $question[2] : 'textarea',
					( isset( $question[3] ) ) ? $question[3] : array(),
					TRUE
				);
			}

			?>

		<?php } ?>

	<?php } elseif ( $entry->getCurrentStep() == \NitroK9\Entry::STEP_CONFIRM ) { ?>

		<h2>Confirmation</h2>
		<p>Please confirm that the information below is accurate:</p>

		<h2>Information About You</h2>

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
