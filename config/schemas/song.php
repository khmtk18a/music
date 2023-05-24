<?php

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;

return new Index('song', [
    'id' => new Field\IdentifierField('id'),
    'name' => new Field\TextField('name'),
    'artist' => new Field\TextField('artist', searchable: false),
    'thumbnail' => new Field\TextField('thumbnail', searchable: false),
    'url' => new Field\TextField('url', searchable: false)
]);
