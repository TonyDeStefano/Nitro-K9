<div class="wrap">

	<?php if ( isset( $_GET['action'] ) && $_GET['action'] == 'view' ) { ?>

		<?php

		$id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? intval( $_GET['id'] ) : NULL;
		$entry = new \NitroK9\Entry( $id );

		?>

		<?php if ( $entry->getId() === NULL ) { ?>

			<h1>
				Submission Not Found
				<a href="?page=nitro_k9_submissions" class="page-title-action">Back</a>
			</h1>

			<div class="alert alert-danger">
				The submission you are trying to view is not currently available.
			</div>

		<?php } else { ?>


			<h1>
				View Submission
				<a href="?page=nitro_k9_submissions" class="page-title-action">Back</a>
				<a href="#" class="page-title-action delete-nitro-k9-submission" data-id="<?php echo $entry->getId(); ?>">Delete</a>
			</h1>

			<?php if ( $entry->getCompletedAt() === NULL ) { ?>
				<div class="alert alert-danger">
					Incomplete submission started by <?php echo $entry->getFullName(); ?> on <?php echo $entry->getCreatedAt( 'l, F j, Y' ); ?>
				</div>
			<?php } else {?>
				<div class="alert alert-info">
					Submitted by <?php echo $entry->getFullName(); ?> on <?php echo $entry->getCompletedAt( 'l, F j, Y' ); ?>
				</div>
			<?php } ?>

			<h2>Information About <?php echo $entry->getFirstName(); ?></h2>

			<div class="well">

				<?php

				$questions = array_merge( $entry->getInfoQuestions( 1 ), $entry->getInfoQuestions( 2 ) );

				foreach ( $questions as $array )
				{
					\NitroK9\Entry::drawConfirmationRow(
						$array[1],
						$array[3]
					);
				}

				?>

			</div>

			<?php

			foreach ( $entry->getOwners() as $owner )
			{
				echo '
					<h2>Information About ' . $owner->getInfoItem( 'first_name' ) . '</h2>
					<div class="well">';

				$questions = array_merge( $owner->getInfoQuestions( 1 ), $owner->getInfoQuestions( 2 ) );

				foreach ( $questions as $array )
				{
					\NitroK9\Entry::drawConfirmationRow(
						$array[1],
						$array[3]
					);
				}

				echo '</div>';
			}

			foreach ( $entry->getPets() as $pet )
			{
				echo '
					<h2>Info About ' . $pet->getInfoItem( 'name' ) . '</h2>
					<div class="well">';

				$categories = $pet->getInfoQuestions( TRUE );

				foreach ( $categories as $category => $questions )
				{
					if ( strlen( $category ) > 0 )
					{
						echo '<h4>' . $category . '</h4>';
					}

					foreach ( $questions as $array )
					{
                        $answer = $array[3];

                        if ( $array[0] == 'is_aggressive' )
                        {
                            $answer = ( $pet->isAggressive() ) ? 'Yes' : 'No';
                        }
                        elseif( $array[0] == 'is_anxious' )
                        {
                            $answer = ( $pet->isAnxious() ) ? 'Yes' : 'No';
                        }
                        elseif( $array[0] == 'is_fixed' )
                        {
                            $answer = ( $pet->isFixed() ) ? 'Yes' : 'No';
                        }

						\NitroK9\Entry::drawConfirmationRow(
							$array[1],
                            $answer
						);
					}
				}

				echo '</div>';
			}

			foreach ( $entry->getPets() as $pet )
			{
				echo '
					<h2>Services for ' . $pet->getInfoItem( 'name' ) . '</h2>
					<div class="well">';

				$categories = $pet->getPricingQuestions( TRUE );

				foreach ( $categories as $category => $price_groups )
				{
					foreach( $price_groups as $price_group )
					{
						\NitroK9\Entry::drawConfirmationPriceRow( $this->price_groups[ $price_group ], $pet );
					}
				}

				echo '</div>';
			}

			foreach ( $entry->getPets() as $pet )
			{
				if ( $pet->isAggressive() || $pet->isAnxious() )
				{
					echo '
						<h2>' . ( ( $pet->isAggressive() ) ? 'Aggression' : 'Anxiety' ) . ' Questionnaire for ' . $pet->getInfoItem( 'name' ) . '</h2>
						<div class="well">';

					for ( $section=1; $section<=5; $section++ )
					{
						switch( $section )
						{
							case 1:
							case 3:
							case 5:

								$categories = $pet->getAggressionQuestions( $section, TRUE );

								foreach ( $categories as $category => $questions )
								{
									echo '<h4>' . $category . '</h4>';

									foreach ( $questions as $question )
									{
										\NitroK9\Entry::drawConfirmationRow(
											$question[0],
											$pet->getAggressionItem( $question[1] )
										);
									}
								}

								break;

							case 2:

								if ( $pet->hasPercentAnswers() )
								{
									echo
									'<h4>
											What percent of the time does your dog obey the following commands for each member of the family?
										</h4>';

									$commands = $pet->getAggressionQuestions( $section );

									echo '
										<table class="table table-bordered table-striped">
											<thead>
												<tr>';
									foreach ( $commands as $key => $command )
									{
										echo '<th>' . $command . '</th>';
									}
									echo '
												</tr>
											</thead>';
									for ( $x=1; $x<=10; $x++ )
									{
										$show = FALSE;
										foreach ( $commands as $key => $commmand )
										{
											if ( strlen( $pet->getAggressionItem( 'percent_' . $x . '_' . $key ) ) > 0 )
											{
												$show = TRUE;
												break;
											}
										}
										if ( $show )
										{
											echo '<tr>';
											foreach ( $commands as $key => $command )
											{
												echo '<td>' . $pet->getAggressionItem( 'percent_' . $x . '_' . $key ) . '</td>';
											}
											echo '</tr>';
										}
									}
									echo '</table>';
								}

								break;

							case 4:

								if ( $pet->hasScreenAnswers() )
								{
									echo '<h4>Aggression Screen</h4>';

									$responses = array(
										'growl' => 'Growl',
										'snarl' => 'Snarl / Bare Teeth',
										'snap' => 'Snap / Bite',
										'no' => 'No Reaction',
										'na' => 'N/A'
									);

									$causes = $pet->getAggressionQuestions( $section );

									echo
									'<table class="table table-bordered table-striped">
											<thead>
												<tr>
													<th>Action</th>';
									foreach ( $responses as $key => $response )
									{
										echo '<th>' .  $response . '</th>';
									}
									echo '
												</tr>
											</thead>';
									foreach ( $causes as $index => $cause )
									{
										$show = FALSE;
										foreach ( $responses as $key => $response )
										{
											if ( strlen( $pet->getAggressionItem( 'screen_' . $index . '_' . $key ) ) > 0 )
											{
												$show = TRUE;
												break;
											}
										}
										if ( $show )
										{
											echo '
													<tr>
														<th>' . $cause . '</th>';
											foreach ( $responses as $key => $response )
											{
												echo '<td style="text-align:center">';
												if ( strlen( $pet->getAggressionItem( 'screen_' . $index . '_' . $key ) ) )
												{
													echo 'X';
												}
												echo '</td>';
											}
											echo '</tr>';
										}
									}
									echo '</table>';
								}

								break;
						}
					}

					echo '</div>';
				}
			}

			?>

		<?php } ?>

	<?php } else { ?>

		<h1>
			Submissions
			<?php if ( isset( $_REQUEST['unfinished'] ) ) { ?>
				<a href="?page=<?php echo $_REQUEST['page']; ?>" class="page-title-action">
					View Completed Submissions
				</a>
			<?php } else { ?>
				<a href="?page=<?php echo $_REQUEST['page']; ?>&unfinished=true" class="page-title-action">
					View Incomplete Submissions
				</a>
			<?php } ?>
		</h1>

		<?php if ( isset( $_GET['delete'] ) ) { ?>

			<?php

			$id = ( isset( $_GET['delete'] ) && is_numeric( $_GET['delete'] ) ) ? intval( $_GET['delete'] ) : NULL;
			$entry = new \NitroK9\Entry( $id );

			?>

			<?php if ( $entry->getId() !== NULL ) { ?>

				<?php $entry->delete(); ?>

				<div class="alert alert-info">
					Submission has been deleted.
				</div>

			<?php } ?>

		<?php } ?>

		<?php

		$table = new \NitroK9\SubmissionsTable;
		$table->prepare_items();
		$table->display();

		?>

	<?php } ?>

</div>