<?php
namespace tests\codeception\frontend;

use tests\codeception\backend\FunctionalTester;
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\frontend\_pages\MotiontagPage;

class MotionsCest
{
	public function _before(AcceptanceTester $I)
	{
		MotiontagPage::openBy($I);
	}

	public function _after(AcceptanceTester $I)
	{
	}

	// tests
	public function viewIndex(AcceptanceTester $I)
	{
		$I->wantTo('ensure that the motiontag system works');
		$I->seeInCurrentUrl('motions');
		$I->see('Motion Archive', 'h1');
		$I->seeNumberOfElements('div.list-view div.motion_group', [1, 10]);
	}

	// tests
	public function filterByTag(AcceptanceTester $I)
	{
		$I->wantTo('see if filtering for tags work');
		$I->seeNumberOfElements('.tags', [1, 30]);
		$I->click("a", '.tags');
		$I->seeInCurrentUrl("motiontag/");
	}

	// tests
	public function addNewMotion(AcceptanceTester $I)
	{
		$I->wantTo('add a legacy motion - not logged in');
		$I->seeInCurrentUrl('motions');
		$I->seeLink(' Add third-party Motion');
		$I->click(' Add third-party Motion');
		$I->seeInCurrentUrl("login");

		$I->fillField('LoginForm[email]', 'Claudine.Abbott@example.local');
		$I->fillField('LoginForm[password]', 'password_1');
		$I->click('Login');
		$I->dontSeeInCurrentUrl("login");
	}

	/*public function goToTournament(AcceptanceTester $I)
	{
		$I->wantTo('go to a tournament');
		$I->click(".tournament_link:nth-of-type(1) a");
		$I->see("div.tournament-view");
	}*/
}