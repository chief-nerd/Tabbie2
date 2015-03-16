<?php
//This is a list of the functions that I'd like to put in place for testing the energy of a distribution
$energy_functions = array();

$energy_functions[] = 'panel_diversity';
function panel_diversity(){
	/*	Get the regions of each of the judges on the panel, and then for each region, subtract 1 from the frequency of it, and then sum them and add the result to the energy level for the debate.
		Thus, if there is perfect diversity, the score is zero. If it's worse than that, it'll be 4,9,16 etc
	*/
		foreach ($adjudicator){
        $regions[] = $adjudicator->region;
        }
    $region_diversity = array_count_values($regions);
    $panel_homogeneity = '0';
    foreach ($region_diversity as $region => $adjud_count){
        $panel_homogeneity += (pow($adjud_count,2) - 1);
        }
    $panel_homogeneity_energy += $panel_homogeneity_penalty;
}

$energy_functions[] = 'university_conflict';
function university_conflict($adjudicator_id_lookup){
	/* Get the university of the adjudicators, and see if they match the teams in the debate. If they do, then add this (arbitrarily large) number to the energy level for the debate.  
	*/
	$adjudicator_check = new Adjudicator;
	$adjudicator_institutions = array();

	$adjudicator_institutions = $adjudicator_check->society_id;

	/* Get the institutions of the teams in the debate */

	/* Is there a clash? If so */
	foreach($institution)
	if(in_array($adjudicator_institutions)){
		$university_conflict_energy += $university_conflict_penalty;
	}
}

$energy_functions[] = 'team_conflict';
function team_conflict(){
	/* Get the clashed teams of the adjudicators */ /*see if they match the teams in the debate. If they do, then add this (arbitrarily large) number to the energy level for the debate.  
	*/
	$adjudicator_check = new Adjudicator;
	$adjudicator_team_clashes = array();

	$adjudicator_clashes = /* Get clashes from database */

	/* Get the institutions of the teams in the debate */

	/* Is there a clash? If so */
	foreach($team_clash)
	if(in_array($adjudicator_team_clashes)){
		$university_conflict_energy += $university_conflict_penalty;
	}
}

$energy_functions[] = 'adjudicator_conflict';
function adjudicator_conflict(){
	/* Get the clashed adjudicators of the adjudicators, and see if they match the other adjudicators in the debate. If they do, then add this (arbitrarily large) number to the energy level for the debate.  
	*/
 
/*See above.*/

}

$energy_functions[] = 'chair_not_chair';
function chair_not_chair(){
	/* Gets the chair_id, and checks whether they have the 'can chair' tag. If they don't, add this number to the energy level for the debate.
	*/
	$can_chair = $adjudicator->chair_tag;

	if(!$can_chair){
		$chair_not_chair += $chair_not_chair_penalty;
	}
}

$energy_functions[] = 'watcher_not_watched';
function watcher_not_watched(){
	/* Gets the ids of each of the panel (including the chair) and checks whether they're watched. If they 
	are, check whether there are any watchers. If not, then add this number to the energy level for the debate.
	*/

	//Get all of the adjudicators in the debate.
	foreach($adjudicator){
		if($adjudicator->watchee){
			foreach($adjudicator){
				if($adjudicator->watcher){
					$watchee_watched = true;
					break;
				}
			}
			if($watchee_watched){
				break;
			}
			else{
				$watcher_not_watched += $watcher_not_watched_penalty;
			}
		}
	}
}

$energy_functions[] = 'chair_not_perfect';
function chair_not_perfect(){
	foreach($adjudicator){
		$adjudicator_ranking = $adjudicator->ranking;
		$penalty = 100 - $adjudicator_ranking;
	}
}

$energy_functions[] = 'panel_strength_not_perfect';
function panel_strength_not_perfect(){
	/* Gets the total strength of the panel, and compares it to the ideal strength for a panel at this level. It then multiples the difference by the weighting for this, and adds this number to the energy level for the debate.
	*/

}

$energy_functions[] = 'chair_not_ciaran_perfect';
function chair_not_ciaran_perfect(){
	/* Works out the ideal chair strength for a debate of this potential, and then gets the Quadratical difference with the actual value, as per this allocation. It adds this number fo the energy level for the debate.
	For example, there are 5 rooms, with energy levels of 3,3,2,2,1.
	The 5 judges are ranked 100,80,60,40,20
	The judges in each room should be 100/80, 100/80, 60/40, 60/40, 20. Penalty for each point outside those bounds.
	*/

	//Get the number of rooms in each point bracket, and then look up the ranking of the n'th active judge

	//SORT ROOMS BY POTENTIAL
	//SORT ACTIVE JUDGES
	//GET NUMBER OF ROOMS ON EACH POTENTIAL:

		//THIS IS SOMETHING WE NEED TO TALK ABOUT

	//For each of those numbers, return the rank of that judge
	//MAKE EACH OF THOSE RANKS A SPREAD, with a $high_boundary and a $low_boundary

	foreach($adjudicator){
		if($adjudicator->ranking > $high_boundary || $adjudicator->ranking > $low_boundary)
	}


}

$energy_functions[] = 'rotation_not_applied';
function rotation_not_applied(){
	/* Maybe this should just be a tie-breaker, but I'm not sure. Where this is on, penalty for each time that this (chair?) judge has judged a room of this potential / points bracket?
	*/
	foreach($adjudicator){
		$adjudicator_rotation = array();
		$adjudicator_rotation = $adjudicator->room_levels;
		$adjudicator_rotation_energy = pow($adjudicator_rotation_penalty, $adjudicator_rotation[/*level of the current debate*/]
			//This is meant to penalise allocations that have the same judge always judging the same quality of rooms. Ideally, you want some level of adjudicator rotation.
	}
}

$energy_functions[] = 'judge_met_team';
function judge_met_team(){
	/* Check each of the judges' history, and check whether they've judged this team before. If they have, add this penalty to the energy level for the debate.
	*/
	foreach($adjudicator){
		$team_history = array();
		$team_history = $adjudicator->history;
		foreach($team){
			$team_id = $team->id;
			if ($team_history[$team_id]){
				$adjudicator_met_team += pow($adjudicator_met_team_penalty,$team_history[$team_id]);
			}
		}
	}
}

$energy_functions = 'judge_met_judge'
function judge_met_judge(){
	/* Check each of the judges' history, and check whether they've judged with each other judge before. If they have, add this penalty to the energy level for the debate.
	*/
		foreach($adjudicator){
		$adjudicator_history = array();
		$adjudicator_history = $adjudicator->history;
		foreach($adjudiator){
			$adjudiator = $adjudiator->id;
			if ($adjudicator_history[$adjudicator_id]){
				$adjudicator_met_team += pow($team_$adjudicator_met_adjudicator_penalty,$adjudicator_history[$adjudicator_id]);
			}
		}
	}
}

function get_energy_total(){
foreach //debate{
foreach($energy_functions as $energy_check){
	$energy_total += $energy_check(/*$debate*/);
}
//}
}

function switch_two_people(){
	/*Get the energy levels of their two debates, and then work out the energy levels if they were to switch. If it would be better to have them switch, do it.
	 */
}z