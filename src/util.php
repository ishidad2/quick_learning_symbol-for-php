<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/env.php');

use SymbolRestClient\Api\NodeRoutesApi;
use SymbolRestClient\Api\NetworkRoutesApi;
use SymbolRestClient\Configuration;
use SymbolSdk\Facade\SymbolFacade;

$NODE_URL = 'http://sym-test-03.opening-line.jp:3000'; // 以前のコードスニペットからのURL

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new \GuzzleHttp\Client();

// /node/info
$nodeInfoApiInstance = new NodeRoutesApi($client, $config);
$nodeInfo = $nodeInfoApiInstance->getNodeInfo();
// /network/properties
$networkType = $nodeInfo->getNetworkIdentifier();
$generationHash = $nodeInfo->getNetworkGenerationHashSeed();

$networkApiInstance = new NetworkRoutesApi($client, $config);
$networkProperties = $networkApiInstance->getNetworkProperties();

$epochAdjustment = $networkProperties->getNetwork()->getEpochAdjustment();
$identifier = $networkProperties->getNetwork()->getIdentifier();
$facade = new SymbolFacade('testnet');
