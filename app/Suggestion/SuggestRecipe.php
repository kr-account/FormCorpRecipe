<?php
/**
 *  * Created by PhpStorm.
 * User: Krishna Rao
 * Date: 12/7/17
 */

namespace App\Suggestion;


use PHPUnit\Runner\Exception;

class SuggestRecipe
{
    private $fridge_contents = [];
    private $recipes = [];

    private $acceptable_recipe;

    /**
     * @return array
     */
    public function getFridgeContents()
    {
        return $this->fridge_contents;
    }

    /**
     * @param array $fridge_contents
     */
    public function setFridgeContents(array $fridge_contents)
    {
        $this->fridge_contents = $fridge_contents;
    }

    /**
     * @return array
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param array $recipes
     */
    public function setRecipes(array $recipes)
    {
        $this->recipes = $recipes;
    }

    public function setUpFiles($fridgeContents, $recipeFile) {
        if(! file_exists( $fridgeContents) || !file_exists($recipeFile)) {
           return "Input files do not exist";
        }

        $fridgeArray = array_map('str_getcsv', file($fridgeContents));
        $json = file_get_contents($recipeFile);
        $recipeArray = json_decode($json, true);

        $this->setRecipes($recipeArray);
        $this->setFridgeContents($fridgeArray);

    }

    public function processData()
    {
        $today = strtotime(date('Y-m-d'));
        if(count($this->fridge_contents) == 0 || ! $this->fridge_contents) {
            return "Order Takeout";
        } elseif (count($this->recipes) == 0 || !$this->recipes) {
            return "Order Takeout";
        }
        else {
            // Loop through the recipes ..
            $test = [];
            foreach ($this->recipes as $recipe) {
                $all_items_available = false;
                $min_date = '';

                    foreach( $recipe["ingredients"] as $ingredient) {
                       $ingredient_found = false;
                       // Look if this ingredient is in the fridge ..
                       foreach ($this->fridge_contents as $fridge_item ) {
                           $expiry_date = strtotime(date_format(date_create_from_format('d/m/Y', $fridge_item[3]), 'Y-m-d'));

//                           print ($ingredient['item'] . " -- " . $fridge_item[0] ."\n");
//                           print ($ingredient['amount'] . " -- " . $fridge_item[1] ."\n");
//                           print ($ingredient['unit'] . " -- " . $fridge_item[2] ."\n");
//                           print ("$today <= $expiry_date\n");

                           if($ingredient['item'] == $fridge_item[0] &&
                              $ingredient['amount'] <= $fridge_item[1] &&
                              $ingredient['unit'] == $fridge_item[2] &&
                              $today <= $expiry_date
                           ) {
                               $ingredient_found = true;
                               if(!$min_date || $expiry_date <= $min_date) {
                                   $min_date = $expiry_date;
                               }
                               break;

                           }
                       } // end contents loop.

                       if( $ingredient_found ) {
                          $all_items_available = true;
                       } else {
                           $all_items_available = false;
                       }

                    } // end ingredients loop.

                    // If implementing recipe is possible ... find the greatest use by date
                    if($all_items_available == true ) {
                        $test[] = ['name' => $recipe["name"], 'max_date' => $min_date];
                        if( ! $this->acceptable_recipe ) {
                            $this->acceptable_recipe = ['name' => $recipe["name"], 'max_date' => $min_date];
                        } elseif ($min_date < $this->acceptable_recipe['max_date'] ) {
                            $this->acceptable_recipe = ['name' => $recipe["name"], 'max_date' => $min_date];
                        }
                    }
            }

            if( !$this->acceptable_recipe ) {
                return "Order Takeout";
            } else {
                return  $this->acceptable_recipe['name'];
            }

        }
    }

}