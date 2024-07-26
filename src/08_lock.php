<?php
use SymbolSdk\Symbol\Models\ReceiptType;
require_once(__DIR__ . '/util.php');

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
use Symfony\Polyfill\Php70\Php70;
use SymbolRestClient\Api\ReceiptRoutesApi;
use SymbolRestClient\Api\SecretLockRoutesApi;
use SymbolSdk\Symbol\Models\LockHashAlgorithm;
use SymbolSdk\Symbol\Models\SecretLockTransactionV1;
use SymbolSdk\Symbol\Models\SecretProofTransactionV1;

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
$bobKey = $facade->createAccount(new PrivateKey("ED949592C90CA58A16CB5BEC303DB011A48373063DDB0C4CFD6DFD01F14A9007"));
$bobAddress = $bobKey->address;

$namespaceIds = IdGenerator::generateNamespacePath('symbol.xym');
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

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
// $hash = "A7E2950816E0A03A2BDED0711E2C9B85991EB83A2EDD69F553B33698B5C3351F";
// $txInfo = $apiInstance->getPartialTransaction($hash);

// $txInfo = $apiInstance->getPartialTransaction($facade->hashTransaction($aggregateTx));

// // // 連署者の連署
// $signTxHash = new Hash256($txInfo->getMeta()->getHash());
// $signature = new Signature($bobKey->keyPair->sign($signTxHash->binaryData));
// $body = [
//     'parentHash' => $signTxHash->__toString(),
//     'signature' => $signature->__toString(),
//     'signerPublicKey' => $bobKey->publicKey->__toString(),
//     'version' => '0'
// ];

// print_r($body);

// //アナウンス
// try {
//   $result = $apiInstance->announceCosignatureTransaction($body);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }

/**
 * シークレットロック
 */
// $proof = random_bytes(20); // 解除用キーワード
// $secret = hash('sha3-256', $proof, true); // ロック用キーワード

// echo "secret: " . bin2hex($secret) . PHP_EOL;
// echo "proof: " . bin2hex($proof) . PHP_EOL;

// // シークレットロックTx作成
// $lockTx = new SecretLockTransactionV1(
//   signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)), // 有効期限
//   network: new NetworkType(NetworkType::TESTNET),
//   mosaic: new UnresolvedMosaic(
//     mosaicId: new UnresolvedMosaicId($namespaceId), // モザイクID
//     amount: new Amount(1000000) // ロックするモザイク
//   ),
//   duration: new BlockDuration(480), //ロック期間
//   hashAlgorithm: new LockHashAlgorithm(LockHashAlgorithm::SHA3_256), // ハッシュアルゴリズム
//   secret: new Hash256($secret), // ロック用キーワード
//   recipientAddress: $bobAddress, // 解除時の転送先：Bob
// );
// $facade->setMaxFee($lockTx, 100);  // 手数料

// // 署名
// $lockSig = $aliceKey->signTransaction($lockTx);
// $payload = $facade->attachSignature($lockTx, $lockSig);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'シークレットロックTxHash' . PHP_EOL;
// echo $facade->hashTransaction($lockTx) . PHP_EOL;

// sleep(1);

// $secretAipInstance = new SecretLockRoutesApi($client, $config);
// $resutl = $secretAipInstance->searchSecretLock(secret: bin2hex($secret));
// echo 'シークレットロック情報' . PHP_EOL;
// echo $resutl . PHP_EOL;

// /**
//  * シークレットプルーフ
//  */
// // $proof = 'a9139a4fd2a92b74460749378f543d665b2044f1';

// $proofTx = new SecretProofTransactionV1(
//   signerPublicKey: $bobKey->publicKey,  // 署名者公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)), // 有効期限
//   network: new NetworkType(NetworkType::TESTNET),
//   hashAlgorithm: new LockHashAlgorithm(LockHashAlgorithm::SHA3_256), // ハッシュアルゴリズム
//   secret: new Hash256($secret), // ロック用キーワード
//   recipientAddress: $bobAddress, // 解除時の転送先：Alice
//   proof: $proof, // 解除用キーワード
// );
// $facade->setMaxFee($proofTx, 100);  // 手数料

// // 署名
// $proofSig = $bobKey->signTransaction($proofTx);
// $payload = $facade->attachSignature($proofTx, $proofSig);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'シークレットプルーフTxHash' . PHP_EOL;
// echo $facade->hashTransaction($proofTx) . PHP_EOL;

// sleep(30);

// /**
//  * 結果の確認
//  */
// $txInfo = $apiInstance->getConfirmedTransaction($facade->hashTransaction($proofTx));
// echo '承認確認' . PHP_EOL;
// echo $txInfo . PHP_EOL;

/**
 * レシート検索
 */

$receiptApiInstance = new ReceiptRoutesApi($client, $config);
$result = $receiptApiInstance->searchReceipts(
  receipt_type: new ReceiptType(ReceiptType::LOCK_SECRET_COMPLETED),
  target_address:$bobAddress
);
echo 'レシート' . PHP_EOL;
echo $result . PHP_EOL;