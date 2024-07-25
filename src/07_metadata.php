<?php
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\MosaicRoutesApi;
use SymbolRestClient\Configuration;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolRestClient\Api\MetadataRoutesApi;
use SymbolSdk\Symbol\Models\EmbeddedAccountMetadataTransactionV1;
use SymbolSdk\Symbol\Models\EmbeddedMosaicMetadataTransactionV1;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\EmbeddedNamespaceMetadataTransactionV1;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\Symbol\Models\UnresolvedAddress;
use SymbolSdk\Symbol\Metadata;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\IdGenerator;
use SymbolRestClient\Api\NamespaceRoutesApi;
use SymbolSdk\Symbol\Models\NamespaceId;

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$metaApiInstance = new MetadataRoutesApi($client, $config);

/**
 * メタデータの作成
 * アカウントに登録
 */
// $targetAddress = $aliceKey->address;  // メタデータ記録先アドレス
// $sourceAddress = $aliceKey->address;  // メタデータ作成者アドレス

// // キーと値の設定
// $keyId = Metadata::metadataGenerateKey("key_account");
// $newValue = "test";

// // 同じキーのメタデータが登録されているか確認
// $metadataInfo = $metaApiInstance->searchMetadataEntries(
//   source_address: $sourceAddress,
//   scoped_metadata_key: strtoupper(dechex($keyId)),  // 16進数の大文字の文字列に変換
// );

// $oldValue = hex2bin($metadataInfo['data'][0]['metadata_entry']['value']); //16進エンコードされたバイナリ文字列をデコード
// $updateValue = Metadata::metadataUpdateValue($oldValue, $newValue, true);

// $tx = new EmbeddedAccountMetadataTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   targetAddress: new UnresolvedAddress($targetAddress),  // メタデータ記録先アドレス
//   scopedMetadataKey: $keyId,
//   valueSizeDelta: strlen($newValue) - strlen($oldValue),
//   value: $updateValue,
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートTx作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions,
// );
// // 手数料
// $facade->setMaxFee($aggregateTx, 100);

// // 署名
// $sig = $aliceKey->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// // アナウンス
// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($aggregateTx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * 異なるアカウントのメタデータに記録
 */
// $bobKey = $facade->createAccount(new PrivateKey('ED949592C90CA58A16CB5BEC303DB011A48373063DDB0C4CFD6DFD01Fxxxxxx'));
// $targetAddress = $bobKey->address;  // メタデータ記録先アドレス
// $sourceAddress = $aliceKey->address;  // メタデータ作成者アドレス

// // キーと値の設定
// $keyId = Metadata::metadataGenerateKey("key_account");
// $newValue = "test";

// // 同じキーのメタデータが登録されているか確認
// $metadataInfo = $metaApiInstance->searchMetadataEntries(
//   target_address: $targetAddress,
//   source_address: $sourceAddress,
//   scoped_metadata_key: strtoupper(dechex($keyId)),  // 16進数の大文字の文字列に変換
// );

// $oldValue = hex2bin($metadataInfo['data'][0]['metadata_entry']['value']); //16進エンコードされたバイナリ文字列をデコード
// $updateValue = Metadata::metadataUpdateValue($oldValue, $newValue, true);

// $tx = new EmbeddedAccountMetadataTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   targetAddress: $targetAddress,  // メタデータ記録先アドレス
//   scopedMetadataKey: $keyId,
//   valueSizeDelta: strlen($newValue) - strlen($oldValue),
//   value: $updateValue,
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートTx作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions,
// );
// // 手数料
// $facade->setMaxFee($aggregateTx, 100, 1);

// // 作成者による署名
// $sig = $aliceKey->signTransaction($aggregateTx);
// $facade->attachSignature($aggregateTx, $sig);

// // 記録先アカウントによる連署
// $coSig = $bobKey->cosignTransaction($aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig);

// $payload = ['payload' => strtoupper(bin2hex($aggregateTx->serialize()))];

// $apiInstance = new TransactionRoutesApi($client, $config);

// // アナウンス
// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($aggregateTx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * モザイクに登録
 */
// $targetMosaic = '6FA40B0B8B9E392F';
// $mosaicApiInstance = new MosaicRoutesApi($client, $config);
// $mosaicInfo = $mosaicApiInstance->getMosaic($targetMosaic);
// $sourceAddress = $mosaicInfo['mosaic']['owner_address']; // モザイク作成者アドレス

// $keyId = Metadata::metadataGenerateKey("key_mosaic");
// $newValue = 'test';

// // 同じキーのメタデータが登録されているか確認
// $metadataInfo = $metaApiInstance->searchMetadataEntries(
//   target_id: $targetMosaic,
//   source_address: new UnresolvedAddress($sourceAddress),
//   scoped_metadata_key: strtoupper(dechex($keyId)),  // 16進数の大文字の文字列に変換
//   metadata_type: 1,
// );

// $oldValue = hex2bin($metadataInfo['data'][0]['metadata_entry']['value']); //16進エンコードされたバイナリ文字列をデコード
// $updateValue = Metadata::metadataUpdateValue($oldValue, $newValue, true);

// $tx = new EmbeddedMosaicMetadataTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   targetMosaicId: new UnresolvedMosaicId(hexdec($targetMosaic)),
//   targetAddress: new UnresolvedAddress($sourceAddress),
//   scopedMetadataKey: $keyId,
//   valueSizeDelta: strlen($newValue) - strlen($oldValue),
//   value: $updateValue,
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートTx作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions,
// );
// // 手数料
// $facade->setMaxFee($aggregateTx, 100);

// // 作成者による署名
// $sig = $aliceKey->signTransaction($aggregateTx);
// $payload =$facade->attachSignature($aggregateTx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// // アナウンス
// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($aggregateTx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * ネームスペースに登録
 */

//ターゲットと作成者アドレスの設定
// $targetNamespace = new NamespaceId(IdGenerator::generateNamespaceId("fugafuga"));

// $namespaceApiInstance = new NamespaceRoutesApi($client, $config);
// $namespaceInfo = $namespaceApiInstance->getNamespace(substr($targetNamespace, 2));

// $sourceAddress = new UnresolvedAddress($namespaceInfo['namespace']['owner_address']); // ネームスペース作成者アドレス

// $keyId = Metadata::metadataGenerateKey("key_namespace");
// $newValue = 'test';

// // 同じキーのメタデータが登録されているか確認
// $metadataInfo = $metaApiInstance->searchMetadataEntries(
//   target_id: substr($targetNamespace, 2),
//   source_address: $sourceAddress,
//   scoped_metadata_key: strtoupper(dechex($keyId)),  // 16進数の大文字の文字列に変換
//   metadata_type: 2,
// );

// $oldValue = hex2bin($metadataInfo['data'][0]['metadata_entry']['value']); //16進エンコードされたバイナリ文字列をデコード
// $updateValue = Metadata::metadataUpdateValue($oldValue, $newValue, true);

// $tx = new EmbeddedNamespaceMetadataTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   targetNamespaceId: new NamespaceId($targetNamespace),
//   targetAddress: $sourceAddress,
//   scopedMetadataKey: $keyId,
//   valueSizeDelta: strlen($newValue) - strlen($oldValue),
//   value: $updateValue,
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートTx作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions,
// );
// // 手数料
// $facade->setMaxFee($aggregateTx, 100);

// // 作成者による署名
// $sig = $aliceKey->signTransaction($aggregateTx);
// $payload =$facade->attachSignature($aggregateTx, $sig);

// $apiInstance = new TransactionRoutesApi($client, $config);

// // アナウンス
// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// $hash = $facade->hashTransaction($aggregateTx);
// echo "\n===トランザクションハッシュ===" . PHP_EOL;
// echo $hash . PHP_EOL;

/**
 * 確認
 */
$metaApiInstance = new MetadataRoutesApi($client, $config);
$metadataInfo = $metaApiInstance->searchMetadataEntries(
  target_address: $aliceKey->address,
  source_address: $aliceAddress,
);
echo "\n===メタデータ一覧===" . PHP_EOL;
echo $metadataInfo . PHP_EOL;