<?php
use SymbolSdk\Symbol\Models\AggregateBondedTransactionV2;
use SymbolSdk\Symbol\Models\BlockDuration;
use SymbolSdk\Symbol\Models\Cosignature;
use SymbolSdk\Symbol\Models\DetachedCosignature;
use SymbolSdk\Symbol\Models\Hash256;
use SymbolSdk\Symbol\Models\HashLockTransactionV1;
use SymbolSdk\Symbol\IdGenerator;
use SymbolSdk\Symbol\Models\NamespaceId;
use SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1;
use SymbolRestClient\Configuration;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\Symbol\Models\Signature;
use SymbolSdk\Symbol\Models\UnresolvedMosaic;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\Models\Timestamp;
require_once(__DIR__ . '/util.php');

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$apiInstance = new TransactionRoutesApi($client, $config);

/**
 * アグロゲートボンデットトランザクションの作成
 */
// $bobKey = $facade->createAccount(PrivateKey::random());
$bobKey = $facade->createAccount(new PrivateKey("ED949592C90CA58A16CB5BEC303DB011A48373063DDB0C4CFD6DFD01F1*******"));
$bobAddress = $bobKey->address;

// $namespaceIds = IdGenerator::generateNamespacePath('symbol.xym');
// $namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

// // アグリゲートTxに含めるTxを作成
// $tx1 = new EmbeddedTransferTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,
//   recipientAddress: $bobAddress,
//   mosaics: [
//     new UnresolvedMosaic(
//       mosaicId: new UnresolvedMosaicId($namespaceId), // モザイクID
//       amount: new Amount(1000000) // 金額(1XYM)
//     )
//   ],
//   message: "",  //メッセージなし
// );

// $tx2 = new EmbeddedTransferTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,
//   recipientAddress: $aliceKey->address,
//   message: "\0thank you!",
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx1, $tx2];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートボンデットTx作成
// $aggregateTx = new AggregateBondedTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100, 1);  // 手数料

// // 署名
// $sig = $aliceKey->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// /**
//  * ハッシュロック
//  */
// $hashLockTx = new HashLockTransactionV1(
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   network: new NetworkType(NetworkType::TESTNET),
//   deadline: new Timestamp($facade->now()->addHours(2)), // 有効期限
//   duration: new BlockDuration(480), // 有効期限
//   hash: new Hash256($facade->hashTransaction($aggregateTx)), // ペイロードのハッシュ
//   mosaic: new UnresolvedMosaic(
//     mosaicId: new UnresolvedMosaicId($namespaceId), // モザイクID
//     amount: new Amount(10 * 1000000) // 金額(10XYM)
//   )
// );
// $facade->setMaxFee($hashLockTx, 100);  // 手数料

// // 署名
// $hashLockSig = $aliceKey->signTransaction($hashLockTx);
// $hashLockJsonPayload = $facade->attachSignature($hashLockTx, $hashLockSig);

// /**
//  * ハッシュロックをアナウンス
//  */
// $config = new Configuration();
// $config->setHost($NODE_URL);
// $client = new GuzzleHttp\Client();
// $apiInstance = new TransactionRoutesApi($client, $config);

// try {
//   $result = $apiInstance->announceTransaction($hashLockJsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'ハッシュロックTxHash' . PHP_EOL;
// echo $facade->hashTransaction($hashLockTx) . PHP_EOL;

// sleep(40);

// /**
//  * アグリゲートボンデットTxをアナウンス
//  */
// try {
//   $result = $apiInstance->announcePartialTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }

// echo 'アグリゲートボンデットTxHash' . PHP_EOL;
// echo $facade->hashTransaction($aggregateTx) . PHP_EOL;

// sleep(5);
/**
 * 連署
 */
// トランザクションの取得
$hash = "0040C92D277AEE5349465C4A6F9A19F2D8503A9838A193F0F61AE3ECEE183C79";
$txInfo = $apiInstance->getPartialTransaction($hash);

// $txInfo = $apiInstance->getPartialTransaction($facade->hashTransaction($aggregateTx));

// 連署者の連署
$cosignature = new DetachedCosignature();
$signTxHash = new Hash256($txInfo->getMeta()->getHash());
$cosignature->parentHash = $signTxHash;
$cosignature->version = 0;
$cosignature->signerPublicKey = $bobKey->publicKey;
$cosignature->signature = new Signature($bobKey->keyPair->sign($signTxHash));

$body = [
    'parentHash' => bin2hex($cosignature->parentHash),
    'signature' => bin2hex($cosignature->signature),
    'signerPublicKey' => bin2hex($cosignature->signerPublicKey),
    'version' => $cosignature->version
];

//アナウンス
try {
  $result = $apiInstance->announceCosignatureTransaction($jsonBody);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}