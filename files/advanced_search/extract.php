<?php

require_once '../vendor/autoload.php';

/**
 * Use this script to extract all documents from an existing index and put them in a bulk formated file (body_bulk.txt)
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/search_operations.html#_scrolling
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
 */

use Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\ProgressBar;

$client = ClientBuilder::create()
    ->setHosts(['10.82.0.16:9200'])
    ->build();

$fromIndex = 'search_ac';
$toIndex = 'search';

$finalSize = 2000;
$size = 0;
$step = 100;

$params = [
    'scroll' => '30s',          // how long between scroll requests. should be small!
    'size'   => $step,            // how many results *per shard* you want back
    'index'  => $fromIndex,
    'body'   => [
        'query' => [
            'match_all' => new \stdClass()
        ]
    ]
];

// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
$response = $client->search($params);

$fh = fopen('./body_bulk.txt', 'w');

$output = new ConsoleOutput();
$progress = new ProgressBar($output, $finalSize);

// Now we loop until the scroll "cursors" are exhausted
while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0 && ($size < $finalSize)) {

    foreach ($response['hits']['hits'] as $hit) {
        fwrite($fh, \json_encode([
            'index' => [
                '_index' => $toIndex,
                '_id' => $hit['_id']
            ]
        ]));
        fwrite($fh, "\n");

        $document = $hit['_source'];

        fwrite($fh, \json_encode($document));
        fwrite($fh, "\n");
    }

    // When done, get the new scroll_id
    // You must always refresh your _scroll_id!  It can change sometimes
    $scroll_id = $response['_scroll_id'];

    // Execute a Scroll request and repeat
    $response = $client->scroll([
        'body' => [
            'scroll_id' => $scroll_id,  //...using our previously obtained _scroll_id
            'scroll'    => '30s'        // and the same timeout window
        ]
    ]);

    $progress->advance($step);
    $size += $step;
}
$progress->finish();

fclose($fh);
