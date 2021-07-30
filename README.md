### Streamr PHP Bridge

Push data to [Streamr](https://streamr.network/) by using PHP. No *composer* required.

```php
include_once($_SERVER['DOCUMENT_ROOT']. '/streamr/streamr.class.php');

$streamrClient = new Streamr('STREAMR_PRIVATE_KEY', 'STREAMR_STREAM_ID');

$response = $streamrClient->publishData(array(
    'hello' => 'world'
));

print_r($response);
```

#### Based on
* [elliptic-php](https://github.com/simplito/elliptic-php)
* [php-keccak](https://github.com/kornrunner/php-keccak)
* 
#### Helpful links
* [Streamr developer docs](https://streamr.network/docs/getting-started)
* [Streamr API explorer](https://api-explorer.streamr.com)
