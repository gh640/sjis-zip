# `gh640/sjis-zip`

A utility to handle zip files encoded with Shift-JIS.

## Installation

```bash
composer require gh640/sjis-zip
```

## Usage

```php

use gh640\SjisZip\Extractor;

$extractor = Extractor::create('src.zip');

// List items.
$items = $extractor->items();

// Extract the file.
$extractor->extract('path/to/dest');
```

## License

MIT.
