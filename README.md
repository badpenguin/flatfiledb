FlatFileDB
==========

PHP Flat File DB library with cache for CMS

More informations are available at http://www.badpenguin.org/


## Why?

On my VPS i uses Wordpress that is a fatty slow MySQL app.

Faster websites performs better also in SEO ranking.

## Technology

* Don't reinvent the wheel.

* Uses "dba" with "qdbm" format.

## Features

* DBA is a module so its faster then any self-made implementation.

* Table Locking.

* Library has a little "cache" system on top of DBA.

* Can store anything: strings, arrays, objects.

## Road Map

- [ ] Database Locking.

- [ ] Disable Locking.

- [ ] Implement "composer"


## Examples

### Opening the DB
```
$cms = FlatFile::open('db/cms.qdbm');
```

### Write Post
```php
/* Create an Object */
$post = new stdClass;
$post->id=5;
$post->title='my title';
$post->body='<p>my content</p>';
$post->last_modified_time = time();
$post->tags = array('featured','gallery');

$cms->set($post->id,$post);
if (!$post) die('Save failed');
```

### Get Post
```
$post = $cms->get($post_id);
if (!$post) die('Post not found');
```

### Check if Key exists
```
	if ($cms->is_valid('manteinance_mode')) die('Website is under manteinance');
```

### Delete a key
```
$cms->delete('manteinance_mode');
```


### Get all data
```
print_r($cms->get_all());
```

