<?php

namespace tests\codeception\frontend\acceptance;

use common\models\Country;
use common\models\InSociety;
use common\models\Society;
use tests\codeception\frontend\_pages\SignupPage;
use common\models\User;
use yii\helpers\ArrayHelper;
use tests\codeception\frontend\AcceptanceTester;
use Faker;

class SignupCest
{
	/** @var Faker\Generator */
	private $f;
    /**
     * This method is called before each cest class test method
     * @param \Codeception\Event\TestEvent $event
     */
    public function _before($event)
    {
		$this->f = Faker\Factory::create();
    }

    /**
     * This method is called after each cest class test method, even if test failed.
     * @param \Codeception\Event\TestEvent $event
     */
    public function _after($event)
    {
		/** @var User $user */
		$user = User::findOne(['email' => 'tester.email@example.local']);
		foreach ($user->inSocieties as $in) {
			$in->delete();
		}
		$user->delete();
    }

    /**
     * This method is called when test fails.
     * @param \Codeception\Event\FailEvent $event
     */
    public function _fail($event)
    {
    }

    /**
	 * @param AcceptanceTester $I
     * @param \Codeception\Scenario $scenario
     */
    public function testUserSignup($I, $scenario)
    {
        $I->wantTo('ensure that signup works');

        $signupPage = SignupPage::openBy($I);
        $I->see('Signup', 'h1');
        $I->see('Please fill out the following fields to signup:');

        $I->amGoingTo('submit signup form with no data');

        $signupPage->submit([]);

        $I->expectTo('see validation errors');
		$I->see('Email cannot be blank.', '.help-block');
		$I->see('Password cannot be blank.', '.help-block');
		$I->see('Password Repeat cannot be blank.', '.help-block');
		$I->see('Givenname cannot be blank.', '.help-block');
		$I->see('Surename cannot be blank.', '.help-block');
		$I->see('Current Society cannot be blank.', '.help-block');

        $I->amGoingTo('submit signup form with not correct email');
        $signupPage->submit([
            'email' => 'tester.email',
            'password' => 'tester_password',
        ]);

        $I->expectTo('see that email address is wrong');
		$I->dontSee('Email cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->see('Email is not a valid email address.', '.help-block');

        $I->amGoingTo('submit signup form with correct email');
		$I->submitForm("#form-signup", [
			'SignupForm[societies_id]'    => Society::find()->all()[0]->id,
			'SignupForm[email]'           => 'tester.email@example.local',
			'SignupForm[password]'        => 'tester_password',
			'SignupForm[password_repeat]' => 'tester_password',
			'SignupForm[givenname]' => $this->f->firstName,
			'SignupForm[surename]'  => $this->f->lastName,
		],
			"signup-button");

		$this->checkLoggedIn($I);
    }

	/**
	 * @param AcceptanceTester $I
	 */
	private function checkLoggedIn($I)
	{
		$I->expectTo('see that user logged in');
		$I->seeLink(' Logout');
		$I->dontSeeLink('Login');
		$I->dontSeeLink('Signup');
	}

	/**
	 * @param AcceptanceTester      $I
	 * @param \Codeception\Scenario $scenario
	 */
	public function testNewSociety($I, $scenario)
	{
		$I->wantTo('see if signup works with a new society');

		$societyName = $this->f->city . " Debating Society";
		$first = $this->f->firstName;
		$signupPage = SignupPage::openBy($I);
		$I->see('Signup', 'h1');
		$I->see('Please fill out the following fields to signup:');

		$I->submitForm("#form-signup", [
			'SignupForm[societies_id]'    => $societyName,
			'SignupForm[email]'           => 'tester.email@example.local',
			'SignupForm[password]'        => 'tester_password',
			'SignupForm[password_repeat]' => 'tester_password',
			'SignupForm[givenname]'       => $first,
			'SignupForm[surename]'        => $this->f->lastName,
		],
			"signup-button");

		$I->seeInCurrentUrl('add-new-society');
		$I->seeInField("#society-fullname", $societyName);

		$I->submitForm("#w0", [
			"Society[fullname]" => $societyName,
		]);
		$I->see("Country cannot be blank.", ".help-block");
		$I->see("Abbrevation cannot be blank.", ".help-block");

		$I->submitForm("#w0", [
			"Society[fullname]"   => $societyName,
			"Society[abr]"        => Society::generateAbr($societyName),
			"Society[country_id]" => Country::COUNTRY_UNKNOWN_ID,
		]);

		$I->dontSeeElement(".alert-danger");
		$this->checkLoggedIn($I);
		$I->see("User Profile", "h1");
		$I->see($societyName, "td");
		$I->see($first, "td");

		/* clean up */
		$soc = Society::find()->where(["fullname" => $societyName])->one();
		foreach ($soc->inSocieties as $in) {
			$in->delete();
		}
		$soc->delete();
	}
}
