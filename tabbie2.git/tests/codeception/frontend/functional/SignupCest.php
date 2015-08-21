<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\frontend\_pages\SignupPage;
use common\models\User;

class SignupCest
{

    /**
     * This method is called before each cest class test method
     * @param \Codeception\Event\TestEvent $event
     */
    public function _before($event)
    {
    }

    /**
     * This method is called after each cest class test method, even if test failed.
     * @param \Codeception\Event\TestEvent $event
     */
    public function _after($event)
    {
        User::deleteAll([
			'email' => 'tester.email@example.local',
        ]);
    }

    /**
     * This method is called when test fails.
     * @param \Codeception\Event\FailEvent $event
     */
    public function _fail($event)
    {

    }

    /**
     *
     * @param \codeception_frontend\FunctionalTester $I
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
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->see('Email is not a valid email address.', '.help-block');

        $I->amGoingTo('submit signup form with correct email');
        $signupPage->submit([
			'email' => 'tester.email@example.local',
            'password' => 'tester_password',
        ]);

        $I->expectTo('see that user is created');
        $I->seeRecord('common\models\User', [
			'email' => 'tester.email@example.local',
        ]);

        $I->expectTo('see that user logged in');
		$I->seeLink(' Logout');
    }
}
