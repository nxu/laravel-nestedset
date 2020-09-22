![Tests](https://github.com/nXu/laravel-nestedset/workflows/Tests/badge.svg)

# Nested Sets for Laravel 
Implementation of the 
[Nested Set Model](https://en.wikipedia.org/wiki/Nested_set_model) for 
Laravel Eloquent ORM.

Heavily inspired by the now deprecated [Baum](https://github.com/etrepat/baum)
package.

## What are nested sets?
The nested set model is a pattern for representing hierarchical data (commonly
known as trees) in relational databases. It excels when the data changes 
relatively rarely. 

**The typical use cases** for this pattern include multi-level categories in
online shops or complex menu systems.  

### Classic tree structures
Classic tree structures typically only contain a `parent_id` column to represent
the parent of each node. This is perfectly enough to store the hierarchical
structure, but it makes queries to the database that traverse the tree on 
multiple levels very costly. 

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

This pattern also has a big drawback: modifying the table requires complicated
and somewhat expensive logic. The goal of this package is to implement this
logic so you can use nested sets as easily as possible. 

### Database design
The goal of this package is to make queries to the hierarchical data as easy as
possible. Therefore each node has 4 attributes to describe its position in 
the hierarchy. While this is mostly redundant, it also gives a lot more
flexibility to make every query to the table as effective as possible.

The 4 columns used by the package:
- `parent_id` - The primary key of the parent node. `null` for root nodes
- `left` and `right` - Left and right edges of the node
- `depth` - The depth of the node in the tree. Level 0 for root nodes

### Automatic nested set building
This package automatically processes all changes to the table and builds the
nested set accordingly. It utilizes the `parent_id` attribute as a single source
of truth and builds the hierarchy according to this column. 

## Requirements
This package requires PHP 7.2+ and Laravel 6+.

## Usage

### Install
```
composer require nxu/laravel-nestedset
```

### Migrations
Add the required columns to your table containing the hierarchical data.

#### Use the migration helper macro
To add the required columns, you may use the following Blueprint macro:

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

    // Foreign key for the parent id
    $table->foreignId('parent_id')
          ->nullable()
          ->constrained('my_tree')
          ->onDelete('cascade');

    // Optional
    $table->index(['left', 'right', 'parent_id']);
});
```

