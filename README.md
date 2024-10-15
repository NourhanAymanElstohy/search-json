# SearchJson Laravel Package

## Overview

The `SearchJson` package provides robust search functionality for JSON fields within a Laravel application. It allows you to search for terms in multiple languages across different JSON fields, while also supporting flexible regex patterns to handle Arabic character variations.

## Features

- Search within JSON fields across multiple languages (e.g., `ar`, `en`).
- Supports character normalization for Arabic, so variations of characters like `ا`, `أ`, `إ`, and `آ` are all matched.

## Installation

To install the SearchJson package, simply run the following command:

```bash
composer require nourayman/search-json
```

## Usage

1. **Add Searchable Trait to your Model**

In your Model, Add trait`Searhable` to Article Model:

```php
use Nourayman\SearchJson\Searchable;
class Article {
    use Searchable;
    // Your Model Code
}
```

In your Controller, use the function `searchJson` directly:

```php
$field = 'title';
$langs = ['ar', 'en'];
$text = 'ألنفسيه';

$results = Article::searchJson($field, $text, $langs);
```

2. **Customizing Character Variations**

The package automatically handles Arabic character normalization. You can further customize this by modifying the `buildRegexPattern` method in the service class to add new character variations or optional prefixes as needed.

## Methods

### `searchJson($field, $term, array $langs)`

- **Description**: Searches through specified JSON fields in multiple languages, handling variations in Arabic characters.
- **Parameters**:
  - `$field`: THe JSON field to search within (e.g., `title`).
  - `$term`: The search term, which can include any form of Arabic characters.
  - `$langs`: Languages to search within the JSON fields (e.g., `['ar', 'en']`).
- **Returns**: Collection of matched results.

## Example Query

For a given term like `'ألنفسيه'`, the package will match:

- `النفسية`
- `ألنفسية`
- `إلنفسية`
- `النفسيه`
- `ألنفسيه`
- `إلنفسية`

This search flexibility is ideal for applications needing robust multi-language support in JSON fields.

## Contributing

Contributions are welcome! If you have any bug reports, feature requests, or pull requests, please submit them to the [GitHub repository](https://github.com/nourayman/searchjson).

## License

This package is open-sourced software licensed under the MIT license.
