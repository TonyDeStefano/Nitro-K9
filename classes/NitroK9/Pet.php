<?php

namespace NitroK9;

class Pet {

	const TYPE_LARGE_DOG = 'Large Dog';
	const TYPE_SMALL_DOG = 'Small Dog';
	
	private $type;
	private $is_aggressive = FALSE;
	private $info;
	private $services;
	private $aggression;

	/**
	 * Pet constructor.
	 *
	 * @param null $json
	 */
	public function __construct( $json=NULL )
	{
		$this->info = array();
		$this->services = array();
		$this->aggression = array();
		
		if ( $json !== NULL )
		{
			$array = json_decode( $json, TRUE );

			if ( is_array( $array ) )
			{
				if ( isset( $array['is_aggressive'] ) )
				{
					$this->setIsAggressive( $array['is_aggressive'] );
				}

				if ( isset( $array['type'] ) )
				{
					$this->setType( $array['type'] );
				}
				
				if ( isset( $array['info'] ) )
				{
					$this->info = $array['info'];
				}

				if ( isset( $array['services'] ) )
				{
					$this->services = $array['services'];
				}

				if ( isset( $array['aggression'] ) )
				{
					$this->aggression = $array['aggression'];
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param mixed $type
	 *
	 * @return Pet
	 */
	public function setType( $type )
	{
		$this->type = ( $type == self::TYPE_LARGE_DOG ) ? self::TYPE_LARGE_DOG : self::TYPE_SMALL_DOG;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isAggressive()
	{
		return ( $this->is_aggressive === TRUE );
	}

	/**
	 * @param boolean $is_aggressive
	 *
	 * @return Pet
	 */
	public function setIsAggressive( $is_aggressive )
	{
		$this->is_aggressive = ( $is_aggressive == 1 || $is_aggressive === TRUE );

		return $this;
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getInfoItem( $item )
	{
		return $this->getItem( 'info', $item );
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getServicesItem( $item )
	{
		return $this->getItem( 'services', $item );
	}

	/**
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getAggressionItem( $item )
	{
		return $this->getItem( 'aggression', $item );
	}

	/**
	 * @param $property
	 * @param $item
	 *
	 * @return mixed|string
	 */
	public function getItem( $property, $item )
	{
		if ( isset( $this->$property ) )
		{
			$array = $this->$property;
			
			if ( is_array( $array ) && isset( $array[ $item ] ) )
			{
				return $array[ $item ];
			}
		}

		return '';
	}

	/**
	 * @param $item
	 * @param $value
	 *
	 * @return $this
	 */
	public function setInfoItem( $item, $value )
	{
		$this->setItem( 'info', $item, $value );

		return $this;
	}

	/**
	 * I guess you can't assign a value like this `$this->$property[ $item ] = $value;`
	 * So that's why there is a temp array there.
	 *
	 * @param $property
	 * @param $item
	 * @param $value
	 *
	 * @return $this
	 */
	public function setItem( $property, $item, $value )
	{
		$array = $this->$property;
		if ( $array === NULL )
		{
			$array = array();
		}

		$array[ $item ] = $value;
		$this->$property = $array;

		return $this;
	}

	public function setInfoItemsFromPost()
	{
		$this
			->setInfo( NULL )
			->setItemsFromPost( 'info' );
	}

	public function setServicesItemsFromPost()
	{
		$this
			->setServices( NULL )
			->setItemsFromPost( 'services' );
	}

	public function setAggressionItemsFromPost()
	{
		$this
			->setAggression( NULL )
			->setItemsFromPost( 'aggression' );
	}

	public function setItemsFromPost( $property )
	{
		foreach ( $_POST as $key => $val )
		{
			if ( Entry::canAddItem( $key ) )
			{
				if ( strlen ( $val ) > 0 )
				{
					$this->setItem( $property, $key, $val );
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getInfo()
	{
		return ( $this->info === NULL ) ? array() : $this->info;
	}

	/**
	 * @param mixed $info
	 *
	 * @return Pet
	 */
	public function setInfo( $info )
	{
		$this->info = $info;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getServices()
	{
		return ( $this->services === NULL ) ? array() : $this->services;
	}

	/**
	 * @param mixed $services
	 *
	 * @return Pet
	 */
	public function setServices( $services )
	{
		$this->services = $services;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAggression()
	{
		return ( $this->aggression === NULL ) ? array() : $this->aggression;
	}

	/**
	 * @param mixed $aggression
	 *
	 * @return Pet
	 */
	public function setAggression( $aggression )
	{
		$this->aggression = $aggression;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'type' => $this->getType(),
			'is_aggressive' => ( $this->is_aggressive ) ? 1 : 0,
			'info' => $this->getInfo(),
			'services' => $this->getServices(),
			'aggression' => $this->getAggression()
		);
	}

	/**
	 * @param bool $for_confirmation
	 *
	 * @return array
	 */
	public function getInfoQuestions( $for_confirmation=FALSE )
	{
		$return = array(
			'' => array(
				array( 'name', 'Name', TRUE, $this->getInfoItem( 'name' ) ),
				array( 'breed', 'Breed', TRUE, $this->getInfoItem( 'breed' ) ),
				array( 'color', 'Color', TRUE, $this->getInfoItem( 'color' ) ),
				array( 'gender', 'Gender', TRUE, $this->getInfoItem( 'gender' ), 'select', array( 'Male' => 'Male', 'Female' => 'Female' ) ),
				array( 'dob', 'Date of Birth', FALSE, $this->getInfoItem( 'dob' ) ),
				array( 'age', 'Age', FALSE, $this->getInfoItem( 'age' ) ),
				array( 'weight', 'Weight', FALSE, $this->getInfoItem( 'weight' ) ),
				array( 'is_aggressive', 'Aggressive?', TRUE, ( $this->isAggressive() ) ? 1 : 0, 'select', array( 'No' => '0', 'Yes' => '1' ) ),
			),
			'Identification' => array(
				array( 'id_tag', 'ID Tag', FALSE, $this->getInfoItem( 'id_tag' ) ),
				array( 'tattoo', 'Tattoo', FALSE, $this->getInfoItem( 'tattoo' ) ),
				array( 'microchip', 'Microchip', FALSE, $this->getInfoItem( 'microchip' ) ),
				array( 'vet', 'Veterinarian', FALSE, $this->getInfoItem( 'vet' ) ),
				array( 'vet_phone', 'Vet Phone', FALSE, $this->getInfoItem( 'vet_phone' ) ),
				array( 'medical_conditions', 'Medical Conditions', FALSE, $this->getInfoItem( 'medical_conditions' ), 'textarea' ),
				array( 'medication', 'Medication and Dosage', FALSE, $this->getInfoItem( 'medication' ), 'textarea' ),
			),
			'Feeding' => array(
				array( 'food_provided_by', 'Food Provided By', FALSE, $this->getInfoItem( 'food_provided_by' ), 'select', array( 'Nitro K9' => 'Nitro K9', 'Client' => 'Client' ) ),
				array( 'feed_instructions', 'Feeding Instructions', FALSE, $this->getInfoItem( 'feed_instructions' ), 'textarea' ),
			),
			'Behavior' => array(
				array( 'problems', 'List any problems your pet has with people, pets or situations', FALSE, $this->getInfoItem( 'problems' ), 'textarea' ),
				array( 'snapped', 'Has your pet ever snapped at anyone?', FALSE, $this->getInfoItem( 'snapped' ), 'select', array( 'No' => 'No', 'Yes' => 'Yes' ) ),
				array( 'bitten', 'Has your pet ever bitten another animal?', FALSE, $this->getInfoItem( 'bitten' ), 'select', array( 'No' => 'No', 'Yes' => 'Yes' ) ),
				array( 'share', 'Will your pet share toys with other animals?', FALSE, $this->getInfoItem( 'share' ), 'select', array( 'No' => 'No', 'Yes' => 'Yes' ) ),
				array( 'jumped', 'Has your pet ever jumped a fence or barrier?', FALSE, $this->getInfoItem( 'jumped' ), 'select', array( 'No' => 'No', 'Yes' => 'Yes' ) ),
				array( 'restrictions', 'List any restrictions that should be placed on your pet\'s activities', FALSE, $this->getInfoItem( 'restrictions' ), 'textarea' ),
				array( 'mark_or_spray', 'Does your pet mark or spray inside the house?', FALSE, $this->getInfoItem( 'mark_or_spray' ), 'select', array( 'No' => 'No', 'Yes' => 'Yes' ) ),
				array( 'anything_else', 'Anything else you would like to share?', FALSE, $this->getInfoItem( 'anything_else' ), 'textarea' ),
			)
		);

		if ( $for_confirmation )
		{
			foreach ( $return as $category => $questions )
			{
				foreach ( $questions as $index => $question )
				{
					if ( strlen( $question[3] ) == 0 )
					{
						unset( $return[ $category ][ $index ] );
					}
				}

				if ( count( $return[ $category ] ) == 0 )
				{
					unset ( $return[ $category ] );
				}
			}
		}

		return $return;
	}

	/**
	 * @param bool $for_confirmation
	 *
	 * @return array
	 */
	public function getPricingQuestions( $for_confirmation=FALSE )
	{
		$categories = array();

		if ( $this->type == self::TYPE_LARGE_DOG )
		{
			if ( $this->isAggressive() )
			{
				$categories['Nitro Dog Training'] = array(
					PriceGroup::DOG_LG_AGGRESSIVE_EVAL,
					PriceGroup::DOG_LG_AGGRESSIVE_HOURLY
				);
			}
			else
			{
				$categories['Nitro Dog Training'] = array(
					PriceGroup::DOG_LG_STANDARD_EVAL,
					PriceGroup::DOG_LG_STANDARD_HOURLY
				);
			}

			$categories['Nitro Dog Training'][] = PriceGroup::DOG_LG_HANDS_ON_1;
			$categories['Nitro Dog Training'][] = PriceGroup::DOG_LG_HANDS_ON_2;
			$categories['Nitro Dog Training'][] = PriceGroup::DOG_LG_HANDS_OFF_1;
		}
		else
		{
			if ( $this->isAggressive() )
			{
				$categories['Mini Heroes Training'] = array(
					PriceGroup::DOG_SM_AGGRESSIVE_EVAL,
					PriceGroup::DOG_SM_AGGRESSIVE_HOURLY
				);
			}
			else
			{
				$categories['Mini Heroes Training'] = array(
					PriceGroup::DOG_SM_STANDARD_EVAL,
					PriceGroup::DOG_SM_STANDARD_HOURLY
				);
			}

			$categories['Mini Heroes'][] = PriceGroup::DOG_SM_HANDS_ON_1;
			$categories['Mini Heroes'][] = PriceGroup::DOG_SM_HANDS_ON_2;
			$categories['Mini Heroes'][] = PriceGroup::DOG_SM_HANDS_OFF_1;
		}

		$categories['Boarding'] = array(
			PriceGroup::DOG_BOARDING_PER_NIGHT,
			PriceGroup::DOG_BOARDING_NIGHTS
		);

		$categories['Board and Train'] = array(
			PriceGroup::DOG_BOOT_CAMP
		);

		$categories['Pet Sitting'] = array(
			PriceGroup::PET_SITTING,
			PriceGroup::PET_SITTING_VISITS
		);

		$categories['Doggie Day Care'] = array(
			PriceGroup::DOG_DAY_CARE,
			PriceGroup::DOG_DAY_CARE_PACKAGES
		);
		
		$categories['Dog Walking'] = array(
			PriceGroup::DOG_WALKING
		);

		$categories['Personal Protection (Ring of Fire)'] = array(
			PriceGroup::DOG_PERSONAL_PROTECTION_HOURLY,
			PriceGroup::DOG_PERSONAL_PROTECTION
		);

		if ( $for_confirmation )
		{
			foreach ( $categories as $category => $price_groups )
			{
				foreach ( $price_groups as $index => $price_group )
				{
					if ( strlen( $this->getServicesItem( 'price_' . $price_group ) ) == 0 )
					{
						unset ( $categories[ $category ][ $index ] );
					}
				}

				if ( count( $categories[ $category ] ) == 0 )
				{
					unset ( $categories[ $category ] );
				}
			}
		}
		
		return $categories;
	}

	/**
	 * @param int $section
	 * @param bool $for_confirmation
	 *
	 * @return array
	 */
	public function getAggressionQuestions( $section=1, $for_confirmation=FALSE )
	{
		switch ( $section )
		{
			case 1:
				$return = array(
					'Basic Information' => array(
						array( 'What is the main behavior problem or complaint?', 'main_problem' ),
						array( 'Additional Problems?', 'additional_problems' ),
						array( 'How frequently does the problem occur?', 'how_frequently' ),
					),
					'Chronology' => array(
						array( 'When did you first notice the main problem?', 'first_notice' ),
						array( 'When did it first become a serious concern?', 'when_concerned' ),
						array( 'In what general circumstances does the dog misbehave?', 'general_circumstances' ),
						array(
							'Please describe if and how the problem has changed in frequency:',
							'changed_in_frequency'
						),
						array(
							'Please describe if and how the problem has changed in intensity:',
							'changed_in_intensity'
						),
						array( 'Please describe if and how the problem has changed otherwise:', 'changed_otherwise' ),
						array( 'Describe several examples in detail (including date):', 'several_examples' ),
						array( 'What have you done so far to try to correct the problem?', 'what_have_you_done' ),
						array(
							'How do you discipline your dog for this and for other misbehavior?',
							'how_do_you_discipline'
						),
					),
					'Home Environment' => array(
						array(
							'Please list the name and age of all people, including yourself, living in your household',
							'people_at_home'
						),
						array(
							'Please list the name, species, breed, sex, age and age obtained for all animals living in your household',
							'animals_at_home'
						),
						array( 'In what sequence were the above animals obtained?', 'sequence_obtained' ),
						array(
							'Please describe your dog\'s relationship to the other animals:',
							'relationship_to_animals'
						),
						array(
							'What type of area do you live in?',
							'type_of_area',
							'select',
							array( '' => '', 'City/Town' => 'City/Town', 'Suburbs' => 'Suburbs', 'Rural' => 'Rural' )
						),
						array( 'Please describe your home:', 'describe_home' ),
						array(
							'Have you moved since acquiring your dog?',
							'have_moved',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'How many times?', 'how_many_moves', 'text' ),
						array(
							'Has your household (people or animals) changed since acquiring your dog?',
							'household_changed',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'If Yes, Please Describe:', 'describe_change' ),
					),
					'Background' => array(
						array( 'Why did you decide to get a dog?', 'why_decide' ),
						array( 'Why did you choose this breed?', 'why_chose_breed' ),
						array( 'Where did you get this dog?', 'where_gotten' ),
						array(
							'Have you owned dogs before',
							'owned_dogs_before',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'If known, how many female littermates?', 'female_littermates', 'text' ),
						array( 'If known, how many male littermates?', 'male_littermates', 'text' ),
						array( 'How many animals were there to choose from?', 'how_many_to_choose_from', 'text' ),
						array( 'Why did you choose this dog over the others? (Please be specific)', 'why_chose_this' ),
						array(
							'Was a temperament test performed?',
							'temperament_test',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' )
						),
						array( 'Result:', 'temperament_test_result' ),
						array( 'Describe your dog\'s behavior as a puppy:', 'behavior_as_puppy' ),
						array( 'Please describe any news about littermate behavior:', 'littermate_behavior' ),
						array(
							'Did you meet the parents?',
							'meet_the_parents',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'If yes, please describe their behavior:', 'parent_behavior' ),
						array(
							'Has this dog had other owners?',
							'other_owners',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'If yes, how many?', 'how_many_owners', 'text' ),
						array( 'Why was the dog given up?', 'why_given_up' ),
						array( 'At what age was your dog neutered/spayed?', 'what_age_spayed', 'text' ),
						array( 'If known, why was the dog neutered/spayed at this age?', 'why_spayed_at_this_age' ),
						array( 'Were there any behavior changes after neutering/spay?', 'changes_after_spayed' ),
						array(
							'Has your dog ever been bred?',
							'bred',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' )
						),
						array(
							'Are you planning to breed?',
							'planning_to_breed',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' )
						),
						array( 'If your dog is female and intact, when was her last heat? Was it normal?', 'heat' ),
					),
					'Diet and Feeding' => array(
						array(
							'Please describe, with specifics (e.g. brand name), what you feed your dog:',
							'describe_food'
						),
						array( 'Please describe any changes to your dog\'s appetite:', 'change_in_appetite' ),
						array( 'How many meals do you feed your dog and at what times?', 'meals_and_times' ),
						array( 'Who feeds the dog and where?', 'who_feed_where' ),
						array( 'What is your dog\'s favorite treat?', 'favorite_treat' ),
					),
					'Daily Schedule' => array(
						array(
							'Please describe, with detail, a typical 24-hour day in your dog\'s life:',
							'day_in_the_life'
						),
						array( 'How does the dog behave with familiar visitors?', 'familiar_visitors' ),
						array(
							'How does the dog behave with unfamiliar visitors (children or adults)?',
							'unfamiliar_visitors'
						),
						array( 'How do you exercise your dog?', 'exercise' ),
						array(
							'Is the dog free in a fenced yard?',
							'free_in_fenced_yard',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array(
							'Is the dog tied outside?',
							'tied_outside',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array(
							'Does the dog run free?',
							'run_free',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'How do you play with your dog?', 'how_play' ),
						array( 'What toys does the dog have?', 'dog_toys' ),
						array(
							'Is your dog housetrained?',
							'housetrained',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'How was the dog housetrained?', 'how_housetrained' ),
						array(
							'Does your dog ever urinate in the house?',
							'urinate',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array(
							'Does your dog ever defecate in the house?',
							'defecate',
							'select',
							array( '' => '', 'Yes' => 'Yes', 'No' => 'No' )
						),
						array( 'Where does your dog sleep at night?', 'sleep_at_night' ),
						array( 'Where is your dog when alone in the house?', 'where_when_alone' ),
						array( 'Where is your dog when you have guests?', 'where_when_guests' ),
						array( 'How does your dog behave while you are leaving the house?', 'behave_while_leaving' ),
						array( 'How does your dog behave when you return?', 'behave_when_return' ),
					),
					'Obedience Training' => array(
						array(
							'What basic obedience training has your dog had?',
							'obedience_training',
							'select',
							array(
								'' => '',
								'None' => 'None',
								'Trained at home' => 'Trained at home',
								'Started obedience classes but didn\'t finish' => 'Started obedience classes but didn\'t finish',
								'Graduated obedience class once' => 'Graduated obedience class once',
								'Graduated obedience class two or more levels' => 'Graduated obedience class two or more levels',
								'Private trainer' => 'Private trainer',
								'Other' => 'Other'
							)
						),
						array( 'If other, please describe:', 'other_training' ),
						array( 'How old was the dog when obedience training started?', 'how_old_started' ),
						array( 'Who in the family is the primary trainer?', 'primary_trainer' ),
						array( 'What, if any, awards or titles has your dog won?', 'awards_titles' ),
						array(
							'Has your dog had any hunting, herding, protection, attack or Schutzhund training, if so where when, with who?',
							'hunting_herding'
						),
					),
				);
				
				break;

			case 2:

				return array(
					'name' => 'Family Member Name',
					'sit' => 'Sit',
					'down' => 'Down',
					'stay' => 'Stay',
					'come' => 'Come',
					'heel' => 'Heel'
				);
			
			case 3:

				$return =  array(
					'Other Behaviors' => array(
						array( 'Please describe what tricks your dog knows, if any:', 'tricks' ),
						array( 'Does your dog jump up on you or others without permission?', 'jump_on_others', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Does your dog paw at you or at others?', 'paw_at_you', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Does your dog lick you?', 'lick_you', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Does your dog mount people?', 'mount_people', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'If yes, whom does your dog mount?', 'who_mount' ),
						array( 'Does your dog mount other animals or objects?', 'mount_animals', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'If yes, please describe:', 'describe_mounting' ),
						array( 'Does your dog ever bark at you?', 'bark_at_you', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'If yes, when?', 'when_bark' ),
						array( 'Does your dog bark at other times?', 'bark_other_times' ),
						array( 'What is your dog\'s activity level in general?', 'activity_level', 'select', array( '' => '', 'Low' => 'Low', 'Average' => 'Average', 'High' => 'High', 'Excessive' => 'Excessive' ) ),
					),
					'Medical History' => array(
						array( 'Is your dog on any medication now, for this or other problems?', 'on_meds_for_problem' ),
						array( 'Has your dog been on medication in the past?', 'on_meds_in_past', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Date of most recent rabies vaccination:', 'rabies_vax_date', 'text' ),
						array( 'Type of rabies shot:', 'rabies_vax_type', 'select', array( '' => '', '1 Year' => '1 Year', '3 Year' => '3 Year' ) ),
					),
				);
				
				break;

			case 4:

				return array(
					'Pet Dog',
					'Hug Dog',
					'Kiss Dog',
					'Lift Dog',
					'Call Off Furniture',
					'Push/Pull Off Furniture',
					'Approach on Furniture',
					'Disturb While Resting/Sleeping',
					'Approach While Eating',
					'Take Dog Food Away',
					'Take Human Food Away',
					'Take Water Dish Away',
					'Take Rawhide',
					'Take Biscuit/Cookie',
					'Take Real Bone',
					'Take Toy/Object',
					'Approach When Dog Has Any Object/Toy/Bone',
					'Verbally Punish',
					'Physically Punish',
					'Visual Threat',
					'Speak to Dog (Normal Tone)',
					'Stare at Dog',
					'Bend Over Dog',
					'Push on Shoulders or Back',
					'Approach Dog Near Spouse',
					'Enter Room',
					'Leave Room',
					'Reach Toward Dog',
					'Leash Restraint',
					'Scruff Restraint',
					'Put Leash On/Take Off',
					'Put Collar On',
					'Take Collar Off',
					'Bathe Dog',
					'Towel Dog',
					'Groom/Brush Dog',
					'Dog at Groomer\'s',
					'Trim Nails',
					'Leash/Collar Correction',
					'Response to "Sit"',
					'Response to "Down"',
					'Dog at Vet Clinic',
					'Unfamiliar Adult Enters House or Yard',
					'Unfamiliar Child Enters House or Yard',
					'Familiar Adult Enters House or Yard',
					'Familiar Child Enters House or Yard',
					'Response to Toddlers/Babies',
					'Dog in Car at Tollbooths/Gas Stations',
					'Unfamiliar Adult Approaches Owner, Dog on Leash',
					'Unfamiliar Child Approaches Owner, Dog on Leash',
					'Dog in House, Sees People Outside',
					'Response to Other Dogs While on Leash',
					'Response to Other Dogs While Not on Leash'
				);

			case 5:

				$return = array(
					'Aggression Towards People'	=> array(
						array( 'Are attacks sudden and surprising?', 'sudden_attacks', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Do the episodes appear unprovoked?', 'unprovoked', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Is your dog abruptly docile after an episode?', 'docile_after', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Does the dog appear "sorry" afterwards?', 'sorry_after', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Does the dog appear disoriented afterwards?', 'disoriented_after', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Are the episodes associated with a glazed or absent expression?', 'glazed_expression', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Can you usually tell what will set your dog off?', 'can_you_tell_what_set_off', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Is the aggressive behavior new and uncharacteristic?', 'behavior_new', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'Has your dog bitten and broken skin?', 'broken_skin', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'How many bites have broken skin?', 'how_many_broken_skin', 'text' ),
						array( 'How many bites total?', 'total_bites', 'text' ),
						array( 'Total number of episodes of aggression:', 'total_number_episodes', 'text' ),
						array( 'Describe typical episode, including circumstances and dog\'s response:', 'typical_episode' ),
						array( 'How reliably do the above circumstances cause that response in your dog?', 'cause_that_response' ),
						array( 'What parts of the body has the dog bitten and how severe were the injuries?', 'parts_of_body' ),
						array( 'Who are typically the targets of aggression?', 'typical_targets' ),
						array( 'Did your dog bite as a puppy?', 'bite_as_puppy', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
						array( 'If yes, please describe, including age:', 'describe_puppy_bites' ),
						array( 'At what age did your dog first growl at a person?', 'age_first_growl' ),
						array( 'What was the circumstance?', 'growling_circumstance' ),
						array( 'At what age did your dog first snap or bite at a person?', 'age_first_snap' ),
						array( 'What was the circumstance?', 'snap_circumstance' ),
					)
				);
				
				break;

			default:

				return array();
		}
		
		if ( $for_confirmation )
		{
			foreach ( $return as $category => $questions )
			{
				foreach ( $questions as $index => $question )
				{
					if ( strlen( $this->getAggressionItem( $question[1] ) ) == 0 )
					{
						unset ( $return[ $category ][ $index ] );
					}
				}
				
				if ( count( $return[ $category ] ) == 0 )
				{
					unset ( $return[ $category ] );
				}
			}
		}
		
		return $return;
	}

	/**
	 * @return bool
	 */
	public function hasPercentAnswers()
	{
		return $this->hasAggressionAnswers( 'percent_' );
	}

	/**
	 * @return bool
	 */
	public function hasScreenAnswers()
	{
		return $this->hasAggressionAnswers( 'screen_' );
	}

	/**
	 * @param $prefix
	 *
	 * @return bool
	 */
	public function hasAggressionAnswers( $prefix )
	{
		foreach( $this->getAggression() as $key => $val )
		{
			if ( substr( $key, 0, strlen( $prefix ) ) == $prefix )
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}