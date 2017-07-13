<?php
/**
 *  * Created by PhpStorm.
 * User: Krishna Rao
 */
include('vendor/autoload.php');

use App\Suggestion\SuggestRecipe;

$count = count($argv);
if( $count != 3 ) {
    print "Usage: $argv[0] fridge.csv recipes.json\n";
    die();
}


$suggestion = new SuggestRecipe();

$result = $suggestion->setUpFiles($argv[1], $argv[2]);
if($result == "Input files do not exist") {
    print "$result\n";
    exit();
}

print $suggestion->processData() . "\n";