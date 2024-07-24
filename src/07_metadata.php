<?php
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Configuration;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolRestClient\Api\MetadataRoutesApi;
use SymbolSdk\Symbol\Models\EmbeddedAccountMetadataTransactionV1;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\Symbol\Models\UnresolvedAddress;
use SymbolSdk\Symbol\Metadata;

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
$bobKey = $facade->createAccount(new PrivateKey('ED949592C90CA58A16CB5BEC303DB011A48373063DDB0C4CFD6DFD01xxxxxx'));
$targetAddress = $bobKey->address;  // メタデータ記録先アドレス
$sourceAddress = $aliceKey->address;  // メタデータ作成者アドレス

// キーと値の設定
$keyId = Metadata::metadataGenerateKey("key_account");
$newValue = "test";

// 同じキーのメタデータが登録されているか確認
$metadataInfo = $metaApiInstance->searchMetadataEntries(
  target_address: $targetAddress,
  source_address: $sourceAddress,
  scoped_metadata_key: strtoupper(dechex($keyId)),  // 16進数の大文字の文字列に変換
);

$oldValue = hex2bin($metadataInfo['data'][0]['metadata_entry']['value']); //16進エンコードされたバイナリ文字列をデコード
$updateValue = Metadata::metadataUpdateValue($oldValue, $newValue, true);

$tx = new EmbeddedAccountMetadataTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
  targetAddress: $targetAddress,  // メタデータ記録先アドレス
  scopedMetadataKey: $keyId,
  valueSizeDelta: strlen($newValue) - strlen($oldValue),
  value: $updateValue,
);

// マークルハッシュの算出
$embeddedTransactions = [$tx];
$merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// アグリゲートTx作成
$aggregateTx = new AggregateCompleteTransactionV2(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,
  deadline: new Timestamp($facade->now()->addHours(2)),
  transactionsHash: $merkleHash,
  transactions: $embeddedTransactions,
);
// 手数料
$facade->setMaxFee($aggregateTx, 100, 1);

// 作成者による署名
$sig = $aliceKey->signTransaction($aggregateTx);
$facade->attachSignature($aggregateTx, $sig);

// 記録先アカウントによる連署
$coSig = $bobKey->cosignTransaction($aggregateTx);
// $coSig = $facade->cosignTransaction($bobKey->keyPair, $aggregateTx);
array_push($aggregateTx->cosignatures, $coSig);

$payload = ['payload' => strtoupper(bin2hex($aggregateTx->serialize()))];

$apiInstance = new TransactionRoutesApi($client, $config);

// アナウンス
try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
$hash = $facade->hashTransaction($aggregateTx);
echo "\n===トランザクションハッシュ===" . PHP_EOL;
echo $hash . PHP_EOL;
