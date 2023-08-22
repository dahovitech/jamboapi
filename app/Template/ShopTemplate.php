<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Template;

use App\Models\Project;
use App\Models\Collection;
use App\Models\CollectionField;

class ShopTemplate
{
 
    public static function create(Project $project){

        $products = Collection::create(['name' => 'Products', 'slug' => 'products', 'project_id' => $project->id, 'order' => 1]);
        $productVariations= Collection::create(['name' => 'ProductVariations', 'slug' => 'product-variations', 'project_id' => $project->id, 'order' => 2]);
        $categories = Collection::create(['name' => 'Categories', 'slug' => 'categories', 'project_id' => $project->id, 'order' => 3]);
        $users = Collection::create(['name' => 'Users', 'slug' => 'users', 'project_id' => $project->id, 'order' => 4]);
        $orders = Collection::create(['name' => 'Orders', 'slug' => 'orders', 'project_id' => $project->id, 'order' => 5]);
        $orderDetails = Collection::create(['name' => 'OrderDetails', 'slug' => 'order-details', 'project_id' => $project->id, 'order' => 6]);
        $productReviews = Collection::create(['name' => 'ProductReviews', 'slug' => 'product-reviews', 'project_id' => $project->id, 'order' => 7]);
        $globals = Collection::create(['name' => 'Globals', 'slug' => 'globals', 'project_id' => $project->id, 'order' => 8]);


        //Products
        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 1,
            'type' => 'text', 
            'label' => 'Name', 
            'name' => 'name',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": true, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 2,
            'type' => 'slug', 
            'label' => 'Slug', 
            'name' => 'slug', 
            'options' => '{"slug": {"field": "name"},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 3,
            'type' => 'number', 
            'label' => 'Price', 
            'name' => 'price',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 4,
            'type' => 'relation', 
            'label' => 'Category', 
            'name' => 'category', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$categories->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 5,
            'type' => 'relation', 
            'label' => 'Product reviews', 
            'name' => 'product-reviews', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "2","collection": '.$productReviews->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 6,
            'type' => 'relation', 
            'label' => 'Product variations', 
            'name' => 'product-variations', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "2","collection": '.$productVariations->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 7,
            'type' => 'richtext', 
            'label' => 'Description', 
            'name' => 'description', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        
        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 8,
            'type' => 'media', 
            'label' => 'Main image', 
            'name' => 'mainImage', 
            'options' => '{"slug": [],"media": {"type": 1},"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
   
        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 9,
            'type' => 'media', 
            'label' => 'Images products', 
            'name' => 'images', 
            'options' => '{"slug": [],"media": {"type": 2},"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $products_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $products->id, 
            'order' => 10,
            'type' => 'boolean', 
            'label' => 'Is varaible', 
            'name' => 'isVariable',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

       
       


        //ProduitVariation

        $productVariations_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $productVariations->id, 
            'order' => 1,
            'type' => 'number', 
            'label' => 'Price', 
            'name' => 'price',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $productVariations_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $productVariations->id, 
            'order' => 2,
            'type' => 'media', 
            'label' => 'Images products', 
            'name' => 'images', 
            'options' => '{"slug": [],"media": {"type": 2},"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $productVariations_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $productVariations->id, 
            'order' => 3,
            'type' => 'relation', 
            'label' => 'Product', 
            'name' => 'product', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$products->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

           
        

        //Categories
        $categories_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $categories->id, 
            'order' => 1,
            'type' => 'text', 
            'label' => 'Name', 
            'name' => 'name', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $categories_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $categories->id, 
            'order' => 2,
            'type' => 'slug', 
            'label' => 'Slug', 
            'name' => 'slug', 
            'options' => '{"slug": {"field": "name"},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $categories_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $categories->id, 
            'order' => 3,
            'type' => 'relation', 
            'label' => 'Product', 
            'name' => 'product', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "2","collection": '.$products->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        //Users
        $users_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $users->id, 
            'order' => 1,
            'type' => 'text', 
            'label' => 'Username', 
            'name' => 'username', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $users_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $users->id, 
            'order' => 2,
            'type' => 'email', 
            'label' => 'Email', 
            'name' => 'email', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $users_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $users->id, 
            'order' => 3,
            'type' => 'password', 
            'label' => 'Password', 
            'name' => 'password', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);


        //Orders

        $orders_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orders->id, 
            'order' => 1,
            'type' => 'relation', 
            'label' => 'User', 
            'name' => 'user', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$users->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orders_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orders->id, 
            'order' => 2,
            'type' => 'enumeration', 
            'label' => 'Status', 
            'name' => 'status', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration":  ["pending","processing","shipped","delivered","canceled","returned","completed"],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orders_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orders->id, 
            'order' => 3,
            'type' => 'number', 
            'label' => 'Total amount', 
            'name' => 'totalAmount',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);


        //OrderDetails
        
        $orderDetails_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orderDetails->id, 
            'order' => 1,
            'type' => 'number', 
            'label' => 'Quantity', 
            'name' => 'quantity',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orderDetails_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orderDetails->id, 
            'order' => 2,
            'type' => 'number', 
            'label' => 'Unit price', 
            'name' => 'unitPrice',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orderDetails_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $orderDetails->id, 
            'order' => 3,
            'type' => 'number', 
            'label' => 'Sub total', 
            'name' => 'subPrice',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orderDetails_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' =>  $orderDetails->id, 
            'order' => 4,
            'type' => 'relation', 
            'label' => 'Order', 
            'name' => 'order', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$orders->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $orderDetails_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' =>  $orderDetails->id, 
            'order' => 5,
            'type' => 'relation', 
            'label' => 'Product', 
            'name' => 'product', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$products->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);


        //ProductReview

        $productReviews_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' =>  $productReviews->id, 
            'order' => 1,
            'type' => 'relation', 
            'label' => 'Product', 
            'name' => 'product', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$products->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $productReviews_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' =>  $productReviews->id, 
            'order' => 2,
            'type' => 'relation', 
            'label' => 'User', 
            'name' => 'user', 
            'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": '.$users->id.'},"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $productReviews_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $productReviews->id, 
            'order' => 3,
            'type' => 'number', 
            'label' => 'Rating', 
            'name' => 'rating',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": 5,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $productReviews_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $productReviews->id, 
            'order' => 3,
            'type' => 'longtext', 
            'label' => 'Comment', 
            'name' => 'comment',
            'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}',
            'validations' => '{"unique": {"status": false, "message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        //Globals

        $globals_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $globals->id, 
            'order' => 1,
            'type' => 'slug', 
            'label' => 'Label', 
            'name' => 
            'label', 
            'options' => '{"slug": {"field": null},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $globals_field = CollectionField::create([
            'project_id' => $project->id, 
            'collection_id' => $globals->id, 
            'order' => 1,
            'type' => 'text', 
            'label' => 
            'Value', 
            'name' => 'value', 
            'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 
            'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);



        }
}