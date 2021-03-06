<?php

// init
require_once dirname(__FILE__).'/../bootstrap/unit.php';
$t = new lime_test(34);

// load test data
echo 'Loading test data ...'.PHP_EOL;
$data = new Doctrine_Data();
$data->purge(array('Rate', 'RatableTestObject'));
Doctrine_Core::loadData(dirname(__FILE__).'/../fixtures');

// test object
$obj = new RatableTestObject();
$non_rated_obj = kRateTestTools::getNonRatedObject();
$rated_obj = kRateTestTools::getRatedObject();
$multi_rated_obj = kRateTestTools::getMultiRatedObject();


// begin: tests

$t->comment('The object is ratable (rating methods are available)');
$t->ok(is_callable(array($obj, 'getModel')), 'the getModel() method exists');
$t->ok(is_callable(array($obj, 'getItemId')), 'the getItemId() method exists');
$t->ok(is_callable(array($obj, 'hasRates')), 'the hasRates() method exists');
$t->ok(is_callable(array($obj, 'getAvgRating')), 'the getAvgRating() method exists');
$t->ok(is_callable(array($obj, 'getNbRates')), 'the getNbRates() method exists');
$t->ok(is_callable(array($obj, 'addRate')), 'the addRate() method exists');
$t->ok(is_callable(array($obj, 'getRate')), 'the getRate() method exists');
$t->ok(is_callable(array($obj, 'getAllRates')), 'the getAllRates() method exists');


$t->comment('->getModel() returns the right model name.');
$t->is($obj->getModel(), 'RatableTestObject', '->getModelName() returns the right model name');


$t->comment('A new (and non rated) object behaves correctly');
$t->is($obj->hasRates(), false, '->hasRates() returns false');
$t->is($obj->getAvgRating(), 0, '->getAvgRating() returns 0');
$t->is($obj->getNbRates(), 0, '->getNbRates() returns 0');
$t->is(count($obj->getAllRates()), 0, '->getAllRates() returns 0 record');


$t->comment('A non rated object behaves correctly');
$t->is($non_rated_obj->hasRates(), false, '->hasRates() returns false');
$t->is($non_rated_obj->getAvgRating(), 0, '->getAvgRating() returns 0');
$t->is($non_rated_obj->getNbRates(), 0, '->getNbRates() returns 0');
$t->is(count($non_rated_obj->getAllRates()), 0, '->getAllRates() returns 0 record');


$t->comment('A rated object behaves correctly');
$t->is($rated_obj->hasRates(), true, '->hasRates() returns true');
$t->is($rated_obj->getAvgRating(), 4.0, '->getAvgRating() returns 4.0');
$t->is($rated_obj->getNbRates(), 1, '->getNbRates() returns 1');
$t->is(count($rated_obj->getAllRates()), 1, '->getAllRates() returns 1 record');


$t->comment('A multi rated object behaves correctly');
$t->is($multi_rated_obj->hasRates(), true, '->hasRates() returns true');
$t->is($multi_rated_obj->getAvgRating(), 3.0, '->getAvgRating() returns 3.0');
$t->is($multi_rated_obj->getNbRates(), 3, '->getNbRates() returns 3');
$t->is(count($multi_rated_obj->getAllRates()), 3, '->getAllRates() returns 3 record');


$t->comment('->addRate() fails on a non-saved object');
$r = new Rate();
$r->setValue(2.4);

try {
  $obj->addRate($r);
  $t->fail('->addRate() should fail when rating a non-saved object');
} catch (LogicException $e) {
  $t->pass('->addRate() fails when rating a non-saved object');
}


$t->comment('->addRate() works when used without user, on a rated object');
$r = new Rate();
$r->setValue(2.4);
$nb_rates = $multi_rated_obj->getNbRates() + 1;
$rating = (2.4 + $multi_rated_obj->getAvgRating()) / $nb_rates;
$multi_rated_obj->addRate($r);

$t->is($multi_rated_obj->hasRates(), true, '->hasRates() returns true');
$t->is($multi_rated_obj->getAvgRating(), $rating, sprintf('->getAvgRating() returns %.2f', $rating));
$t->is($multi_rated_obj->getNbRates(), $nb_rates, sprintf('->getNbRates() returns %d', $nb_rates));
$t->is(count($multi_rated_obj->getAllRates()), $nb_rates, sprintf('->getAllRates() returns %d record', $nb_rates));


$t->comment('->addRate() works when used without user, on a NON rated object');
$rating = 3.3;
$r = new Rate();
$r->setValue($rating);
$non_rated_obj->addRate($r);

$t->is($non_rated_obj->hasRates(), true, '->hasRates() returns true');
$t->is($non_rated_obj->getAvgRating(), $rating, sprintf('->getAvgRating() returns %.2f', $rating));
$t->is($non_rated_obj->getNbRates(), 1, '->getNbRates() returns 1');
$t->is(count($non_rated_obj->getAllRates()), 1, '->getAllRates() returns 1 record');
