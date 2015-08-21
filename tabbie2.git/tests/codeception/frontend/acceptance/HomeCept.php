<?php
use tests\codeception\frontend\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage("/");
$I->see('Tabbie2');
$I->seeLink(' View Tournaments');
$I->seeLink(' Create Tournament');
