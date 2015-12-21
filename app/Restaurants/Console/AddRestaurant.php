<?php
namespace Groupeat\Restaurants\Console;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Restaurants\Entities\Address;
use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\FoodTag;
use Groupeat\Restaurants\Entities\FoodType;
use Groupeat\Restaurants\Entities\OpeningWindow;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Console\Abstracts\Command;
use Groupeat\Support\Values\PhoneNumber;
use Illuminate\Database\DatabaseManager;
use Phaza\LaravelPostgis\Geometries\Point;

class AddRestaurant extends Command
{
    protected $signature = 'restaurants:add
        {path : The relative path to the JSON file containing all needed information}';

    protected $description = "Parse a local JSON file to add a restaurant";

    private $db;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct();

        $this->db = $db;
    }

    public function handle()
    {
        $path = realpath($this->argument('path'));

        if (!$path) {
            $this->error('The path '.$this->argument('path').' does not exist.');
            return $this->fail();
        }

        $restaurantData = json_decode(file_get_contents($path));

        $this->db->connection()->transaction(function () use ($restaurantData) {
            $restaurant = new Restaurant;
            $restaurant->name = $restaurantData->name;
            $restaurant->phoneNumber = new PhoneNumber($restaurantData->phoneNumber);
            $restaurant->minimumGroupOrderPrice = $restaurantData->minimumGroupOrderPrice;
            $restaurant->deliveryCapacity = $restaurantData->deliveryCapacity;
            $restaurant->rating = $restaurantData->rating;
            $restaurant->pictureUrl = $restaurantData->pictureUrl;
            $restaurant->discountPolicy = $restaurantData->discountPolicy;
            $restaurant->save();

            $credentialsData = $restaurantData->credentials;
            $credentials = new UserCredentials;
            $credentials->user()->associate($restaurant);
            $credentials->email = $credentialsData->email;
            $credentials->locale = $credentialsData->locale;
            $credentials->password = '';
            $credentials->activate();

            $addressData = $restaurantData->address;
            $address = new Address;
            $address->restaurant()->associate($restaurant);
            $address->street = $addressData->street;
            $address->city = $addressData->city;
            $address->postcode = $addressData->postcode;
            $address->state = $addressData->state;
            $address->country = $addressData->country;
            $address->location = new Point($addressData->location->latitude, $addressData->location->longitude);
            $address->save();

            $categoryLabelToId = Category::lists('id', 'label')->all();
            $categoriesIds = [];
            foreach ($restaurantData->categories as $label) {
                $categoriesIds[] = $categoryLabelToId[$label];
            }
            $restaurant->categories()->sync($categoriesIds);

            foreach ($restaurantData->openingWindows as $openingWindowData) {
                $openingWindow = new OpeningWindow;
                $openingWindow->restaurant()->associate($restaurant);
                $openingWindow->dayOfWeek = $openingWindowData->dayOfWeek;
                $openingWindow->start = $openingWindowData->start;
                $openingWindow->end = $openingWindowData->end;
                $openingWindow->save();
            }

            $foodTypeLabelToId = FoodType::lists('id', 'label')->all();
            $foodTagLabelToId = FoodTag::lists('id', 'label')->all();
            foreach ($restaurantData->products as $productData) {
                $product = new Product;
                $product->restaurant()->associate($restaurant);
                $product->name = $productData->name;
                $product->description = $productData->description;
                $product->typeId = $foodTypeLabelToId[$productData->type];
                $product->save();

                $tagsIds = [];
                foreach ($productData->tags as $label) {
                    $tagsIds[] = $foodTagLabelToId[$label];
                }
                $product->tags()->sync($tagsIds);

                foreach ($productData->formats as $formatData) {
                    $format = new ProductFormat;
                    $format->product()->associate($product);
                    $format->name = $formatData->name;
                    $format->price = $formatData->price;
                    $format->save();
                }
            }
        });
    }
}
