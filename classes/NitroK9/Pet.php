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
	 * @return array
	 */
	public static function getAgressionQuestions()
	{
		return array(
			'Basic Information' => array(
				array( 'What is the main behavior problem or complaint?', 'main_problem' ),
				array( 'Additional Problems?', 'additional_problems' ),
				array( 'How frequently does the problem occur?', 'how_frequently' ),
			),
			'Chronology' => array(
				array( 'When did you first notice the main problem?', 'first_notice' ),
				array( 'When did it first become a serious concern?', 'when_concerned' ),
				array( 'In what general circumstances does the dog misbehave?', 'general_circumstances' ),
				array( 'Please describe if and how the problem has changed in frequency:', 'changed_in_frequency' ),
				array( 'Please describe if and how the problem has changed in intensity:', 'changed_in_intensity' ),
				array( 'Please describe if and how the problem has changed otherwise:', 'changed_otherwise' ),
				array( 'Describe several examples in detail (including date):', 'several_examples' ),
				array( 'What have you done so far to try to correct the problem?', 'what_have_you_done' ),
				array( 'How do you discipline your dog for this and for other misbehavior?', 'how_do_you_discipline' ),
			),
			'Home Environment' => array(
				array( 'Please list the name and age of all people, including yourself, living in your household', 'people_at_home' ),
				array( 'Please list the name, species, breed, sex, age and age obtained for all animals living in your household', 'animals_at_home' ),
				array( 'In what sequence were the above animals obtained?', 'sequence_obtained' ),
				array( 'Please describe your dog\'s relationship to the other animals:', 'relationship_to_animals' ),
				array( 'What type of area do you live in?', 'type_of_area', 'select', array( '' => '', 'City/Town' => 'City/Town', 'Suburbs' => 'Suburbs', 'Rural' => 'Rural' ) ),
				array( 'Please describe your home:', 'describe_home' ),
				array( 'Have you moved since acquiring your dog?', 'have_moved', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'How many times?', 'how_many_moves', 'text' ),
				array( 'Has your household (people or animals) changed since acquiring your dog?', 'household_changed', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'If Yes, Please Describe:', 'describe_change' ),
			),
			'Background' => array(
				array( 'Why did you decide to get a dog?', 'why_decide' ),
				array( 'Why did you choose this breed?', 'why_chose_breed' ),
				array( 'Where did you get this dog?', 'where_gotten' ),
				array( 'Have you owned dogs before', 'owned_dogs_before', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'If known, how many female littermates?', 'female_littermates', 'text' ),
				array( 'If known, how many male littermates?', 'male_littermates', 'text' ),
				array( 'How many animals were there to choose from?', 'how_many_to_choose_from', 'text' ),
				array( 'Why did you choose this dog over the others? (Please be specific)', 'why_chose_this' ),
				array( 'Was a temperament test performed?', 'temperament_test', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' ) ),
				array( 'Result:', 'temperament_test_result' ),
				array( 'Describe your dog\'s behavior as a puppy:', 'behavior_as_puppy' ),
				array( 'Please describe any news about littermate behavior:', 'littermate_behavior' ),
				array( 'Did you meet the parents?', 'meet_the_parents', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'If yes, please describe their behavior:', 'parent_behavior' ),
				array( 'Has this dog had other owners?', 'other_owners', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'If yes, how many?', 'how_many_owners', 'text' ),
				array( 'Why was the dog given up?', 'why_given_up' ),
				array( 'At what age was your dog neutered/spayed?', 'what_age_spayed', 'text' ),
				array( 'If known, why was the dog neutered/spayed at this age?', 'why_spayed_at_this_age' ),
				array( 'Were there any behavior changes after neutering/spay?', 'changes_after_spayed' ),
				array( 'Has your dog ever been bred?', 'bred', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' ) ),
				array( 'Are you planning to breed?', 'planning_to_breed', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No', 'Unsure' => 'Unsure' ) ),
				array( 'If your dog is female and intact, when was her last heat? Was it normal?', 'heat' ),
			),
			'Diet and Feeding' => array(
				array( 'Please describe, with specifics (e.g. brand name), what you feed your dog:', 'describe_food' ),
				array( 'Please describe any changes to your dog\'s appetite:', 'change_in_appetite' ),
				array( 'How many meals do you feed your dog and at what times?', 'meals_and_times' ),
				array( 'Who feeds the dog and where?', 'who_feed_where' ),
				array( 'What is your dog\'s favorite treat?', 'favorite_treat' ),
			),
			'Daily Schedule' => array(
				array( 'Please describe, with detail, a typical 24-hour day in your dog\'s life:', 'day_in_the_life' ),
				array( 'How does the dog behave with familiar visitors?', 'familiar_visitors' ),
				array( 'How does the dog behave with unfamiliar visitors (children or adults)?', 'unfamiliar_visitors' ),
				array( 'How do you exercise your dog?', 'exercise' ),
				array( 'Is the dog free in a fenced yard?', 'free_in_fenced_yard', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'Is the dog tied outside?', 'tied_outside', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'Does the dog run free?', 'run_free', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'How do you play with your dog?', 'how_play' ),
				array( 'What toys does the dog have?', 'dog_toys' ),
				array( 'Is your dog housetrained?', 'housetrained', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'How was the dog housetrained?', 'how_housetrained' ),
				array( 'Does your dog ever urinate in the house?', 'urinate', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'Does your dog ever defecate in the house?', 'defecate', 'select', array( '' => '', 'Yes' => 'Yes', 'No' => 'No' ) ),
				array( 'Where does your dog sleep at night?', 'sleep_at_night' ),
				array( 'Where is your dog when alone in the house?', 'where_when_alone' ),
				array( 'Where is your dog when you have guests?', 'where_when_guests' ),
				array( 'How does your dog behave while you are leaving the house?', 'behave_while_leaving' ),
				array( 'How does your dog behave when you return?', 'behave_when_return' ),
			),
			'Obedience Training' => array(
				array( 'What basic obedience training has your dog had?', 'obedience_training', 'select', array(
					'' => '',
					'None' => 'None',
					'Trained at home' => 'Trained at home',
					'Started obedience classes but didn\'t finish' => 'Started obedience classes but didn\'t finish',
					'Graduated obedience class once' => 'Graduated obedience class once',
					'Graduated obedience class two or more levels' => 'Graduated obedience class two or more levels',
					'Private trainer' => 'Private trainer',
					'Other' => 'Other'
				) ),
				array( 'If other, please describe:', 'other_training' ),
				array( 'How old was the dog when obedience training started?', 'how_old_started' ),
				array( 'Who in the family is the primary trainer?', 'primary_trainer' ),
				array( 'What, if any, awards or titles has your dog won?', 'awards_titles' ),
				array( 'Has your dog had any hunting, herding, protection, attack or Schutzhund training, if so where when, with who?', 'hunting_herding' ),
			),
		);
	}
}