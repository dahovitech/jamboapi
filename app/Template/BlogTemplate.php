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

class BlogTemplate
{

    public static function create(Project $project)
    {
        $pages = Collection::create(['name' => 'Pages', 'slug' => 'pages', 'project_id' => $project->id, 'order' => 1]);
        $posts = Collection::create(['name' => 'Posts', 'slug' => 'posts', 'project_id' => $project->id, 'order' => 2]);
        $categories = Collection::create(['name' => 'Categories', 'slug' => 'categories', 'project_id' => $project->id, 'order' => 3]);
        $users = Collection::create(['name' => 'users', 'slug' => 'users', 'project_id' => $project->id, 'order' => 4]);
        $tags = Collection::create(['name' => 'Tags', 'slug' => 'tags', 'project_id' => $project->id, 'order' => 5]);
        $comments = Collection::create(['name' => 'Comments', 'slug' => 'comments', 'project_id' => $project->id, 'order' => 6]);
        $globals = Collection::create(['name' => 'Globals', 'slug' => 'globals', 'project_id' => $project->id, 'order' => 7]);

        $pages_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $pages->id, 'order' => 1,
            'type' => 'text', 'label' => 'Title', 'name' => 'title', 'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false, "message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $pages_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $pages->id, 'order' => 2,
            'type' => 'slug', 'label' => 'URL', 'name' => 'url', 'options' => '{"slug": {"field": "title"},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $pages_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $pages->id, 'order' => 3,
            'type' => 'richtext', 'label' => 'Content', 'name' => 'content', 'options' => '{"slug": [],"media": [], "relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $pages_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $pages->id, 'order' => 4,
            'type' => 'enumeration', 'label' => 'Menu Position', 'name' => 'menu-position', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": ["MainMenu","FooterMenu","Both"],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 1,
            'type' => 'text', 'label' => 'Title', 'name' => 'title', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 2,
            'type' => 'slug', 'label' => 'URL', 'name' => 'url', 'options' => '{"slug": {"field": "title"},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 3,
            'type' => 'longtext', 'label' => 'Excerpt', 'name' => 'excerpt', 'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 4,
            'type' => 'richtext', 'label' => 'Content', 'name' => 'content', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 5,
            'type' => 'media', 'label' => 'Cover Image', 'name' => 'cover-image', 'options' => '{"slug": [],"media": {"type": 1},"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 6,
            'type' => 'relation', 'label' => 'Category', 'name' => 'category', 'options' => '{"slug": [],"media": [],"relation": {"type": "1","collection": ' . $categories->id . '},"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 7,
            'type' => 'relation', 'label' => 'Author', 'name' => 'author', 'options' => '{"slug": [],"relation": {"type": 1,"collection": ' . $users->id . '},"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $posts_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $posts->id, 'order' => 8,
            'type' => 'relation', 'label' => 'Tags', 'name' => 'tags', 'options' => '{"slug": [],"media": [],"relation": {"type": "2","collection": ' . $tags->id . '},"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $categories_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $categories->id, 'order' => 1,
            'type' => 'text', 'label' => 'Title', 'name' => 'title', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $categories_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $categories->id, 'order' => 2,
            'type' => 'slug', 'label' => 'URL', 'name' => 'url', 'options' => '{"slug": {"field": "title"},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

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

        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 4,
            'type' => 'longtext', 'label' => 'Bio', 'name' => 'bio', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 5,
            'type' => 'media', 'label' => 'Avatar', 'name' => 'avatar', 'options' => '{"slug": [],"media": {"type": 1},"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 6,
            'type' => 'text', 'label' => 'Facebook', 'name' => 'facebook', 'options' => '{"slug": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 7,
            'type' => 'text', 'label' => 'Instagram', 'name' => 'instagram', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 8,
            'type' => 'text', 'label' => 'Twitter', 'name' => 'twitter', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $users_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $users->id, 'order' => 9,
            'type' => 'text', 'label' => 'Linkedin', 'name' => 'linkedin', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $tags_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $tags->id, 'order' => 1,
            'type' => 'text', 'label' => 'Tag', 'name' => 'tag', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $comments_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $comments->id, 'order' => 2,
            'type' => 'text', 'label' => 'Name', 'name' => 'name', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $comments_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $comments->id, 'order' => 3,
            'type' => 'email', 'label' => 'E-mail', 'name' => 'e-mail', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $comments_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $comments->id, 'order' => 4,
            'type' => 'longtext', 'label' => 'Comment', 'name' => 'comment', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $comments_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $comments->id, 'order' => 5,
            'type' => 'relation', 'label' => 'Post', 'name' => 'post', 'options' => '{"slug": [],"relation": {"type": 1,"collection": ' . $posts->id . '},"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": false,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);

        $globals_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $globals->id, 'order' => 1,
            'type' => 'slug', 'label' => 'Label', 'name' => 'label', 'options' => '{"slug": {"field": null},"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": true,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
        $globals_field = CollectionField::create([
            'project_id' => $project->id, 'collection_id' => $globals->id, 'order' => 2,
            'type' => 'text', 'label' => 'Value', 'name' => 'value', 'options' => '{"slug": [],"media": [],"relation": [],"enumeration": [],"hideInContentList": false}', 'validations' => '{"unique": {"status": false,"message": null},"required": {"status": true,"message": null},"charcount": {"max": null,"min": null,"type": "Between","status": false,"message": null}}',
        ]);
    }
}
