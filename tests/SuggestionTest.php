<?php

/**
 *  * Created by PhpStorm.
 * User: Krishna Rao
 * Date: 13/7/17
 */
class SuggestionTest extends \PHPUnit_Framework_TestCase
{
    private $message = "Order Takeout";
    /**  @test */
    public function blankFridgeContentsReturnsOrderOut()
    {
       $suggest = new App\Suggestion\SuggestRecipe();
       $suggest->setFridgeContents([]);
       $suggest->setRecipes([]);

       $this->assertEquals($this->message, $suggest->processData());

    }

    /**  @test */
    public function blankRecipeReturnsOrderOut()
    {
       $suggest = new App\Suggestion\SuggestRecipe();
       $suggest->setFridgeContents([]);
       $suggest->setRecipes([]);


    }

    /** @test */
    public function expectErrorIfInputFilesDontExist() {

        $suggest = new App\Suggestion\SuggestRecipe();
        $this->assertEquals("Input files do not exist", $suggest->setUpFiles('dummy.csv', 'recipe.json'));

    }

}