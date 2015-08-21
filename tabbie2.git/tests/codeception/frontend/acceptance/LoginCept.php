<?php
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\common\_pages\LoginPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login page works');

$loginPage = LoginPage::openBy($I);
/** @var \common\models\User $user */
$user = \common\models\User::findOne(1);

$I->amGoingTo('submit login form with no data');
$loginPage->login('', '');
$I->expectTo('see validations errors');
$I->see('Email cannot be blank.', '.help-block');
$I->see('Password cannot be blank.', '.help-block');

$I->amGoingTo('try to login with wrong credentials');
$I->expectTo('see validations errors');
$loginPage->login($user->email, 'wrong');
$I->expectTo('see validations errors');
$I->see('Incorrect username or password.', '.help-block');

$I->amGoingTo('try to login with correct credentials');
$loginPage->login($user->email, 'password_' . $user->id);
$I->expectTo('see that user is logged');
$I->seeLink(' Logout');
$I->dontSeeLink('Login');
$I->dontSeeLink('Signup');
$I->seeLink(" $user->givenname's Profile");
$I->seeLink(" $user->givenname's History");


/** Uncomment if using WebDriver
 * $I->click('Logout (erau)');
 * $I->dontSeeLink('Logout (erau)');
 * $I->seeLink('Login');
 */
