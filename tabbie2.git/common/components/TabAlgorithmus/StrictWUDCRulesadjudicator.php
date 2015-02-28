<?php
//This is a list of the functions that I'd like to put in place for testing the energy of a distribution
$energy_functions = array();

$energy_functions[] = 'panel_diversity';
function panel_diversity(){
	/*	Get the regions of each of the judges on the panel, and then for each region, subtract 1 from the frequency of it, and then sum them and add the result to the energy level for the debate.
		Thus, if there is perfect diversity, the score is zero. If it's worse than that, it'll be 4,9,16 etc
	*/

}

$energy_functions[] = 'university_conflict';
function university_conflict(){
	/* Get the university of the adjudicators, and see if they match the teams in the debate. If they do, then add this (arbitrarily large) number to the energy level for the debate.  
	*/

}

$energy_functions[] = 'team_conflict';
function team_conflict(){
	/* Get the clashed teams of the adjudicators, and see if they match the teams in the debate. If they do, then add this (arbitrarily large) number to the energy level for the debate.  
	*/

}

$energy_functions[] = 'chair_not_chair';
function chair_not_chair(){
	/* Gets the chair_id, and checks whether they have the 'can chair' tag. If they don't, add this number to the energy level for the debate.
	*/
}

$energy_functions[] = 'watcher_not_watched';
function watcher_not_watched(){
	/* Gets the ids of each of the panel (including the chair) and checks whether they're watched. If they 
	are, check whether there are any watchers. If not, then add this number to the energy level for the debate.
	*/
}

$energy_functions[] = 'chair_not_perfect';
function chair_not_perfect(){
	/* Gets the score of the chair, and subtracts their ranking from 100. Then, it multiplies this by the weighting for this, and adds this number to the energy level for the debate.
	*/
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
}

$energy_functions[] = 'rotation_not_applied';
function rotation_not_applied(){
	/* Maybe this should just be a tie-breaker, but I'm not sure. Where this is on, penalty for each time that this (chair?) judge has judged a room of this potential / points bracket?
	*/
}

$energy_functions[] = 'judge_met_team';
function judge_met_team(){
	/* Check each of the judges' history, and check whether they've judged this team before. If they have, add this penalty to the energy level for the debate.
	*/
}

$energy_functions = 'judge_met_judge'
function judge_met_judge(){
	/* Check each of the judges' history, and check whether they've judged with each other judge before. If they have, add this penalty to the energy level for the debate.
	*/
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
}