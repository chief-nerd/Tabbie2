<?php
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\frontend\_pages\HowToPage;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that how-to works');
HowToPage::openBy($I);
$I->see('How-To', 'h1');
