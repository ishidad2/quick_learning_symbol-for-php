<?php
use SymbolSdk\Symbol\Models\TransferTransactionV1;
require_once(__DIR__ . '/util.php');

use SymbolRestClient\Configuration;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\BlockDuration;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\Symbol\Models\NamespaceRegistrationTransactionV1;
use SymbolSdk\Symbol\Models\AddressAliasTransactionV1;
use SymbolSdk\Symbol\Models\MosaicAliasTransactionV1;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolRestClient\Api\NetworkRoutesApi;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolRestClient\Api\NamespaceRoutesApi;
use SymbolRestClient\Api\ChainRoutesApi;
use SymbolRestClient\Api\BlockRoutesApi;
use SymbolRestClient\Api\ReceiptRoutesApi;
use SymbolSdk\Symbol\Models\NamespaceId;
use SymbolSdk\Symbol\Models\AliasAction;
use SymbolSdk\Symbol\Models\MosaicId;
use SymbolSdk\Symbol\Models\UnresolvedAddress;
use SymbolSdk\Symbol\Models\NamespaceRegistrationType;
use SymbolSdk\Symbol\Models\UnresolvedMosaic;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\IdGenerator;
use Carbon\Carbon;
use SymbolSdk\Utils\Converter;
use SymbolSdk\Symbol\Address;

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));

/**
 * 手数料の計算
 */
$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();

$networkApiInstance = new NetworkRoutesApi($client, $config);
$rootNsperBlock = $networkApiInstance->getRentalFees()->getEffectiveRootNamespaceRentalFeePerBlock();
$rentalDays = 365;
$rentalBlock = ($rentalDays * 24 * 60 * 60) / 30;
$rootNsRenatalFeeTotal = $rentalBlock * $rootNsperBlock;
echo "rentalBlock: " . $rentalBlock . PHP_EOL;
echo "Root Namespace Rental Fee: " . $rootNsRenatalFeeTotal . PHP_EOL;

/**
 * サブネームスペース取得の手数料
 */
$childNamespaceRentalFee = $networkApiInstance->getRentalFees()->getEffectiveChildNamespaceRentalFee();
echo "Child Namespace Rental Fee: " . $childNamespaceRentalFee . PHP_EOL;

/**
 * ルートネームスペースをレンタル
 */
// $name = "fugafuga";

// // Tx作成
// $tx = new NamespaceRegistrationTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   duration: new BlockDuration(86400), // 有効期限
//   id: new NamespaceId(IdGenerator::generateNamespaceId($name)), //必須
//   name: $name,
// );
// $facade->setMaxFee($tx, 100);

// // 署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// /**
//  * アナウンス
//  */
// $config = new Configuration();
// $config->setHost($NODE_URL);
// $client = new GuzzleHttp\Client();
// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * サブネームスペースをレンタル
 */
// $parnetNameId = IdGenerator::generateNamespaceId("hoge"); //ルートネームスペース名
// $name = "fuga"; //サブネームスペース名

// // Tx作成
// $tx = new NamespaceRegistrationTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   duration: new BlockDuration(86400), // 有効期限
//   parentId: new NamespaceId($parnetNameId),
//   id: new NamespaceId(IdGenerator::generateNamespaceId($name, $parnetNameId)),
//   registrationType: new NamespaceRegistrationType(NamespaceRegistrationType::CHILD),
//   name: $name,
// );
// $facade->setMaxFee($tx, 200);

// // 署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// /**
//  * アナウンス
//  */
// $config = new Configuration();
// $config->setHost($NODE_URL);
// $client = new GuzzleHttp\Client();
// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * 2階層目のサブネームスペースを作成したい場合は
 */
// $rootName = IdGenerator::generateNamespaceId("fugafuga"); //ルートネームスペース名
// $parnetNameId = IdGenerator::generateNamespaceId("hoge", $rootName); // 紐づけたい1階層目のサブネームスペース
// $name = "sai"; //サブネームスペース名

// // Tx作成
// $tx = new NamespaceRegistrationTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   duration: new BlockDuration(86400), // 有効期限
//   parentId: new NamespaceId($parnetNameId),
//   id: new NamespaceId(IdGenerator::generateNamespaceId($name, $parnetNameId)),
//   registrationType: new NamespaceRegistrationType(NamespaceRegistrationType::CHILD),
//   name: $name,
// );
// $facade->setMaxFee($tx, 200);

// // 署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// /**
//  * アナウンス
//  */
// $config = new Configuration();
// $config->setHost($NODE_URL);
// $client = new GuzzleHttp\Client();
// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * 有効期限の計算
 */
$namespaceIds = IdGenerator::generateNamespacePath("fugafuga"); // ルートネームスペース
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$namespaceApiInstance = new NamespaceRoutesApi($client, $config);
try {
  $nsInfo = $namespaceApiInstance->getNamespace(substr($namespaceId, 2));
  // echo $nsInfo['namespace']. PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}

$chainApiInstance = new ChainRoutesApi($client, $config);
try {
  $chainInfo = $chainApiInstance->getChainInfo(substr($namespaceId, 2));
  // echo $chainInfo . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
$lastHeight = (int)$chainInfo['height'];

$blockApiInstance = new BlockRoutesApi($client, $config);
try {
  $lastBlock = $blockApiInstance->getBlockByHeight($lastHeight);
  // echo $lastBlock . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
$remainHeight = (int)$nsInfo['namespace']['end_height'] - $lastHeight;

$endDate = Carbon::createFromTimestampMs((int)$lastBlock['block']['timestamp'] + $remainHeight * 30000 + $epochAdjustment * 1000);
echo "End Date: " . $endDate . PHP_EOL;

/**
 * アカウントへのリンク
 */
// $namespaceId = IdGenerator::generateNamespaceId("fugafuga"); // ルートネームスペース
// $address = $aliceKey->address;

// //Tx作成
// $tx = new AddressAliasTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   namespaceId: new NamespaceId($namespaceId),
//   address: new Address($address),
//   aliasAction: new AliasAction(AliasAction::LINK),
// );
// $facade->setMaxFee($tx, 100);

// //署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * モザイクへリンク
 */
// $namespaceIds = IdGenerator::generateNamespacePath("fugafuga.hoge"); // ルートネームスペース
// $namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);
// $mosaicId = new MosaicId("0x12679808DC2A1493");

// //Tx作成
// $tx = new MosaicAliasTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   namespaceId: new NamespaceId($namespaceId),
//   mosaicId: $mosaicId,
//   aliasAction: new AliasAction(AliasAction::LINK),
// );
// $facade->setMaxFee($tx, 100);

// //署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * 未解決で使用
 */

// UnresolvedAccount 導出
$namespaceId = IdGenerator::generateNamespaceId("fugafuga"); // ルートネームスペース
$address = Address::fromNamespaceId(new NamespaceId($namespaceId), $facade->network->identifier);

// Tx作成
$tx = new TransferTransactionV1(
  signerPublicKey: $aliceKey->publicKey,
  network: new NetworkType($networkType),
  deadline: new Timestamp($facade->now()->addHours(2)),
  recipientAddress: new UnresolvedAddress($address),
  message: ''
);
$facade->setMaxFee($tx, 100);

//署名
$sig = $aliceKey->signTransaction($tx);
$payload = $facade->attachSignature($tx, $sig);

$apiInstance = new TransactionRoutesApi($client, $config);

try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
$hash = $facade->hashTransaction($tx);
echo "\n===トランザクションハッシュ===" . PHP_EOL;
echo $hash . PHP_EOL;

/**
 * 送信モザイク
 */
// $namespaceIds = IdGenerator::generateNamespacePath("fugafuga.hoge"); // ルートネームスペース
// $namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

// $tx = new TransferTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   recipientAddress: $aliceKey->address,
//   mosaics: [
//     new UnresolvedMosaic(
//       mosaicId: new UnresolvedMosaicId($namespaceId),
//       amount: new Amount(100)
//     ),
//   ],
// );
// $facade->setMaxFee($tx, 100);

// //署名
// $sig = $aliceKey->signTransaction($tx);
// $payload = $facade->attachSignature($tx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($tx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

// $namespaceIds = IdGenerator::generateNamespacePath("symbol.xym");
// $namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);
// var_dump($namespaceId);


/**
 * 参照 アカウント
 */
$namespaceId = new NamespaceId(IdGenerator::generateNamespaceId("fugafuga"));
$namespaceInfo = $namespaceApiInstance->getNamespace(substr($namespaceId, 2));
// var_dump($namespaceInfo);

/**
 * 参照 モザイク
 */
$namespaceIds = IdGenerator::generateNamespacePath("fugafuga.hoge");
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);
$namespaceInfo = $namespaceApiInstance->getNamespace(substr($namespaceId, 2));
// var_dump($namespaceInfo);

/**
 * 逆引き アドレス
 */
$addresses = ["addresses"=> ["TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ"]];
$accountNames = $namespaceApiInstance->getAccountsNames($addresses);
// var_dump($accountNames);

/**
 * 逆引き モザイク
 */
$mosaicIds = ["mosaicIds"=> ["72C0212E67A08BCE"]];
$mosaicNames = $namespaceApiInstance->getMosaicsNames($mosaicIds);
// var_dump($mosaicNames);

/**
 * レシートの参照
 */
$receiptApiInstance = new ReceiptRoutesApi($client, $config);

$state = $receiptApiInstance->searchAddressResolutionStatements(
  height: 1600481
);
echo $state . PHP_EOL;

/**
 * モザイクの場合
 */
$state = $receiptApiInstance->searchMosaicResolutionStatements(
  height: 1601155
);
// echo $state . PHP_EOL;