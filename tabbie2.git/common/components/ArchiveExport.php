<?php
	/**
	 * ArchiveExport.php File
	 * @package  Tabbie2
	 * @author   Étienne Beaulé
	 * @version
	 */

	namespace common\components;


	use yii;
	use yii\base\Component;
	use common\models;

	class ArchiveExport extends Component
	{
		const BREAK_CAT_PREFIX = 'BC';
		const DEBATE_PREFIX = 'D';
		const VENUE_PREFIX = 'V';
		const MOTION_PREFIX = 'M';
		const TEAM_PREFIX = 'T';
		const SPEAKER_PREFIX = 'S';
		const SOCIETY_PREFIX = 'I';
		const ADJ_PREFIX = 'A';
		const QUESTION_PREFIX = 'Q';

		public function createXML($tournament)
		{
			$xml = new \SimpleXMLElement(
				'<tournament style="bp" />'
			);
			$xml->addAttribute('name', $tournament->fullname);
			$xml->addAttribute('short', $tournament->name);
			$xml->addAttribute('host', $tournament->hostedby->fullname);
			$xml->addAttribute('start-date', $tournament->start_date);
			$xml->addAttribute('end-date', $tournament->end_date);

			foreach ($tournament->rounds as $round)
			{
				$roundXML = $xml->addChild('round');
				$roundXML->addAttribute('name', $round->getName());
				$isInround = $round->type == models\Round::TYP_IN;
				$roundXML->addAttribute('elimination', $isInround ? 'false' : 'true');
				if (!$isInround) $roundXML->addAttribute('break-category', self::BREAK_CAT_PREFIX . $round->type);

				foreach ($round->debates as $debate)
				{
					$result = $debate->result;
					$debateXML = $roundXML->addChild('debate');
					$debateXML->addAttribute('id', self::DEBATE_PREFIX . $debate->id);
					$debateXML->addAttribute('venue', self::VENUE_PREFIX . $debate->venue_id);
					$debateXML->addAttribute('motion', self::MOTION_PREFIX . $round->id);

					list($adjs, $chair) = self::getAdjudicatorsString($debate);
					$debateXML->addAttribute('adjudicators', $adjs);
					$debateXML->addAttribute('chair', $chair);

					foreach ($debate->getTeams() as $k => $team)
					{
						$sideXML = $debateXML->addChild('side');
						$sideXML->addAttribute('team', self::TEAM_PREFIX . $team->id);

						$sideBallotXML = $sideXML->addChild('ballot', $result->getSpeaks($k));

						$sideBallotXML->addAttribute('adjudicators', $adjs);
						$sideBallotXML->addAttribute('ignored', 'false');
						$sideBallotXML->addAttribute('rank', $result->{$k . '_place'});

						foreach (['A', 'B'] as &$speaker)
						{
							$speechXML = $sideXML->addChild('speech');
							$speechXML->addAttribute('reply', 'false');

							$ballotXML = $speechXML->addChild('ballot', $result->{$k . '_' . $speaker . '_speaks'});
							$ballotXML->addAttribute('adjudicators', $adjs);

							# Test for iron-people to keep the correct speaker listed
							$irregularKey = $k . '_irregular';
							if ($speaker == 'A' && $result->$irregularKey == models\Team::IRREGULAR_A_NOSHOW) $speaker = 'B';
							if ($speaker == 'B' && $result->$irregularKey == models\Team::IRREGULAR_B_NOSHOW) $speaker = 'A';
							$speechXML->addAttribute('speaker', self::SPEAKER_PREFIX . $team->{'speaker' . $speaker . '_id'});
						}
					}
				}
			}


			$participantsXML = $xml->addChild('participants');
			$allInstitutions = [];

			foreach ($tournament->teams as $team)
			{
				$teamXML = $participantsXML->addChild('team');
				$teamXML->addAttribute('id', $team->id);
				$teamXML->addAttribute('name', $team->name);
				$teamXML->addAttribute('swing', $team->isSwing ? 'true' : 'false');

				$breaks = [self::BREAK_CAT_PREFIX . models\User::LANGUAGE_ENL];
				if ($team->language_status == models\User::LANGUAGE_ESL && $tournament->has_esl)
					array_push($breaks, self::BREAK_CAT_PREFIX . models\User::LANGUAGE_ESL);
				if ($team->language_status == models\User::LANGUAGE_EFL && $tournament->has_efl)
					array_push($breaks, self::BREAK_CAT_PREFIX . models\User::LANGUAGE_EFL);
				if ($team->language_status == models\User::LANGUAGE_EFL && $tournament->has_esl)
					array_push($breaks, self::BREAK_CAT_PREFIX . models\User::LANGUAGE_ESL);
				$teamXML->addAttribute('break-eligibilities', join(' ', $breaks));

				foreach (['A', 'B'] as $speaker)
				{
					$user = $team->{'speaker' . $speaker};

					$userXML = $teamXML->addChild('speaker', $user->getName());
					$userXML->addAttribute('id', self::SPEAKER_PREFIX . $user->id);

					$institutions = ['I'. $team->society_id];
					foreach ($user->getCurrentSocieties() as $inst)
					{
						array_push($institutions, self::SOCIETY_PREFIX . $inst->id);
						array_push($allInstitutions, $inst);
					}
					$userXML->addAttribute('institutions', join(' ', array_unique($institutions)));
				}
			}

			$cores = [];
			foreach ($tournament->cAs as $ca) array_push($cores, $ca->id);

			foreach ($tournament->adjudicators as $adjudicator)
			{
				$institutions = [];
				foreach ($adjudicator->getSocieties() as $inst)
				{
					array_push($institutions, self::SOCIETY_PREFIX . $inst->id);
					array_push($allInstitutions, $inst);
				}
				$adjXML = $participantsXML->addChild('adjudicator');
				$adjXML->addAttribute('id', self::ADJ_PREFIX . $adjudicator->id);
				$adjXML->addAttribute('core', in_array($adjudicator->user_id, $cores) ? 'true' : 'false');
				$adjXML->addAttribute('score', $adjudicator->strength);
				$adjXML->addAttribute('institutions', join(' ', $institutions));
				$adjXML->addAttribute('name', $adjudicator->user->getName());

				$feedbacks = $adjudicator->hasMany(models\Feedback::className(), ['to_id' => 'id']);
				foreach ($feedbacks as $fb)
				{
					$feedbackXML = $adjXML->addChild('feedback');
					$feedbackXML->addAttribute('debate', self::DEBATE_PREFIX . $fb->debate_id);
					if ($fb->to_type == models\Feedback::TO_CHAIR_FROM_TEAM)
						$feedbackXML->addAttribute('source-team', self::TEAM_PREFIX . $fb->to_id);
					else
						$feedbackXML->addAttribute('source-adjudicator', self::ADJ_PREFIX . $fb->to_id);

					foreach ($fb->answers as $answer)
					{
						$answerXML = $feedbackXML->addChild('answer', $answer->value);
						$answerXML->addAttribute('question', self::QUESTION_PREFIX . $answer->question_id);
					}
				}
			}


			// The break categories (language status) corresponds between User::LANGUAGE_ and Round::TYP_
			$bcXML = $xml->addChild('break-category', Yii::t("app", "Open"));
			$bcXML->addAttribute('id', self::BREAK_CAT_PREFIX . models\Round::TYP_OUT);
			if ($tournament->has_esl)
			{
				$bcXML = $xml->addChild('break-category', Yii::t("app", "ESL"));
				$bcXML->addAttribute('id', self::BREAK_CAT_PREFIX . models\Round::TYP_ESL);
			}
			if ($tournament->has_efl)
			{
				$bcXML = $xml->addChild('break-category', Yii::t("app", "EFL"));
				$bcXML->addAttribute('id', self::BREAK_CAT_PREFIX . models\Round::TYP_EFL);
			}


			foreach ($allInstitutions as $inst)
			{
				$instXML = $xml->addChild('institution', $inst->name);
				$instXML->addAttribute('id', $inst->id);
				$instXML->addAttribute('reference', $inst->abr);
				$instXML->addAttribute('region', $inst->country->name);
			}


			foreach ($tournament->rounds as $round)
			{
				$motionXML = $xml->addChild('motion', $round->motion);
				$motionXML->addAttribute('id', self::MOTION_PREFIX . $round->id);
				$motionXML->addAttribute('reference', $round->motionTags[0]->name);

				if ($round->infoslide !== NULL)
					$infoslide = $motionXML->addChild('info-slide', $round->infoslide);
			}


			foreach ($tournament->venues as $venue)
			{
				$venueXML = $xml->addChild('venue', $venue->name);
				$venueXML->addAttribute('id', self::VENUE_PREFIX . $venue->id);
				if ($venue->group !== NULL)
					$venueXML->addAttribute('categories', $venue->group);
			}


			$questionTypes = [
				models\Question::TYPE_STAR => 'is',
				models\Question::TYPE_INPUT => 't',
				models\Question::TYPE_TEXT => 'tl',
				models\Question::TYPE_NUMBER => 'f',
				models\Question::TYPE_CHECKBOX => 'bc'
			];
			foreach ($tournament->getAllQuestions() as $question)
			{
				if ($question !== NULL)
				{
					$questionXML = $xml->addChild('question', $question->text);
					$questionXML->addAttribute('id', self::QUESTION_PREFIX . $question->id);
					if ($question->type !== NULL)
						$questionXML->addAttribute('type', $questionTypes[$question->type]);
					$questionXML->addAttribute('from-teams', $question->apply_T2C ? 'true' : 'false');
					$questionXML->addAttribute('from-adjudicators', $question->apply_W2C || $question->apply_C2W ? 'true' : 'false');
				}
				else break;
			}

			return $xml->asXML();
		}

		private static function getAdjudicatorsString($debate) {
			$chair = '';
			$adjs = [];

			foreach ($debate->getAdjudicatorsInPanel() as $adj)
			{
				$adjID = self::ADJ_PREFIX . $adj->adjudicator_id;
				array_push($adjs, $adjID);
				if ($adj->is_chair()) $chair = $adjID;
			}
			return [join(' ', $adjs), $chair];
		}
	}
