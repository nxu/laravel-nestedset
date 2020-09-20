![Tests](https://github.com/nXu/laravel-nestedset/workflows/Tests/badge.svg)

# Nested Sets for Laravel 
Implementation of the 
[Nested Set Model](https://en.wikipedia.org/wiki/Nested_set_model) for 
Laravel Eloquent ORM.

Heavily inspired by the now deprecated [Baum](https://github.com/etrepat/baum)
package.

## Theory
The nested set model is a pattern for representing hierarchical data (commonly
known as trees) in relational databases.

### Classic tree structures
Classic tree structures typically only contain a `parent_id` column to represent
the parent of each node. This is perfectly enough to store the hierarchical
structure, but it makes queries to the database very costly. 

For example, to query all descendants of a single node, you need multiple, 
recursive queries.

### Nested sets
Nested sets are another structure for the same hierarchical data. They make
querying the tree very simple, basically all nodes are available by making one
single query.

This is done by representing the data as
[sets](https://en.wikipedia.org/wiki/Set_(mathematics)) instead of trees.

Instead of a single `parent_id` column, each set has two attributes: `left`
and `right`. By comparing these attributes, you can query all hierarchy data.

Example:

![https://i.imgur.com/coToNY7.png](https://i.imgur.com/coToNY7.png)

This pattern also has drawbacks: modifying the table requires complicated
and somewhat expensive logic. The goal of this package is to implement this
logic so you can use nested sets as easily as possible. 

## Usage

### Requirements
This package requires PHP 7.2+ and Laravel 6+.

### Install
```
composer require nxu/laravel-nestedset
```

### Database design
The goal of this package is to make queries to the hierarchical data as easy as
possible. Therefore it utilizes the traditional `parent_id` as a single source
of truth and builds the hierarchy according to this column. It also adds a
fourth column called `depth` for querying the depth of any given node.

#### Use the migration helper macro
To add these columns, you can use the following Blueprint macro:

```php
Schema::create('my_tree', function (Blueprint $table) {
    $table->nestedSet();
});
```

> NOTE!
> This macro assumes two things:
> - Your table has an unsigned BIGINT primary key called `id`
> - You want to use your `parent_id` foreign key with ON DELETE CASCADE  

#### Add columns manually
Alternatively, you can add the columns manually:

```php
Schema::create('my_tree', function (Blueprint $table) {
    $table->unsignedBigInteger('left')->nullable();
    $table->unsignedBigInteger('right')->nullable();
    $table->unsignedBigInteger('depth')->nullable();

    // Foreign key - use the same type as your primary key
    $table->unsignedBigInteger('parent_id')->nullable();

    $table->foreign('parent_id')
        ->references('id')
        ->on('my_tree')
        ->onDelete('CASCADE');

    // Optional, but this will make your queries faster
    $table->index(['left', 'right', 'parent_id']);
});
```

