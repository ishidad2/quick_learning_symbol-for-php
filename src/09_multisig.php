<?php
use SymbolSdk\Symbol\Models\AggregateBondedTransactionV2;

require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\MultisigRoutesApi;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\Models\EmbeddedMultisigAccountModificationTransactionV1;
use SymbolRestClient\Configuration;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\Symbol\IdGenerator;
use SymbolSdk\Symbol\Models\NamespaceId;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\UnresolvedMosaic;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\Models\HashLockTransactionV1;
use SymbolSdk\Symbol\Models\BlockDuration;
use SymbolSdk\Symbol\Models\Hash256;
use SymbolSdk\Symbol\Models\Signature;

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$apiInstance = new TransactionRoutesApi($client, $config);

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));
$namespaceIds = IdGenerator::generateNamespacePath('symbol.xym');
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);
/**
 * 秘密鍵からアカウント生成
 */
// $bobKey = $facade->createAccount(PrivateKey::random());
// $carolKey1 = $facade->createAccount(PrivateKey::random());
// $carolKey2 = $facade->createAccount(PrivateKey::random());
// $carolKey3 = $facade->createAccount(PrivateKey::random());
// $carolKey4 = $facade->createAccount(PrivateKey::random());
// $carolKey5 = $facade->createAccount(PrivateKey::random());

$bobKey = $facade->createAccount(new PrivateKey('D63C24DA8D43F064233B71CE0FC6904132BA7C16B2B2F7660EC15A539588FDF4'));
$carolKey1 = $facade->createAccount(new PrivateKey('E1A4510F74E9991B760FFA55A498FD2409FFEAF0764651ECE84B6DDA7B0013A6'));
$carolKey2 = $facade->createAccount(new PrivateKey('FD78E39DE879DB7575E77C23CA41D5EB6A6ED82D319C0A3878B595094C1F359D'));
$carolKey3 = $facade->createAccount(new PrivateKey('DFFC0BD7BCBC9AB2E14A5F2D60B5DA69C91BB3FAD7E5E24F32121564EB0891F9'));
$carolKey4 = $facade->createAccount(new PrivateKey('A4AC7AB846914021AFBFBE59BCF3B413C146101A60E77EE932EFB726CF12B116'));
$carolKey5 = $facade->createAccount(new PrivateKey('7CBA79757479402DDCDE6577F938CDE6FD9035ACADC1E343AE155EFA679D462A'));

echo "===秘密鍵と公開鍵の導出===" . PHP_EOL;
echo $bobKey->keyPair->privateKey() . PHP_EOL;
echo $carolKey1->keyPair->privateKey() . PHP_EOL;
echo $carolKey2->keyPair->privateKey() . PHP_EOL;
echo $carolKey3->keyPair->privateKey() . PHP_EOL;
echo $carolKey4->keyPair->privateKey() . PHP_EOL;
echo $carolKey5->keyPair->privateKey() . PHP_EOL;

echo "https://testnet.symbol.tools/?recipient=" . $bobKey->address . "&amount=20" . PHP_EOL;
echo "https://testnet.symbol.tools/?recipient=" . $carolKey1->address . "&amount=20" . PHP_EOL;

/**
 * マルチシグの登録
 */
// $multisigTx =  new EmbeddedMultisigAccountModificationTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,  // マルチシグ化したいアカウントの公開鍵を指定
//   minApprovalDelta: 3, // minApproval:承認のために必要な最小署名者数増分
//   minRemovalDelta: 3, // minRemoval:除名のために必要な最小署名者数増分
//   addressAdditions: [
//     $carolKey1->address,
//     $carolKey2->address,
//     $carolKey3->address,
//     $carolKey4->address,
//   ],
//   addressDeletions: [] // 除名対象アドレスリスト
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$multisigTx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートトランザクションの作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,  // マルチシグ化したいアカウントの公開鍵を指定
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100, 4);  // 手数料

// // マルチシグ化したいアカウントによる署名
// $sig = $bobKey->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// // 追加・除外対象として指定したアカウントによる連署
// $coSig1 = $facade->cosignTransaction($carolKey1->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig1);
// $coSig2 = $facade->cosignTransaction($carolKey2->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig2);
// $coSig3 = $facade->cosignTransaction($carolKey3->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig3);
// $coSig4 = $facade->cosignTransaction($carolKey4->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig4);

// // アナウンス
// $payload = ["payload" => strtoupper(bin2hex($aggregateTx->serialize()))];

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($aggregateTx) . PHP_EOL;

/**
 * 確認
 */
// $multisigApiInstance = new MultisigRoutesApi($client, $config);
// $multisigInfo = $multisigApiInstance->getAccountMultisig($bobKey->address);
// echo "===マルチシグ情報===" . PHP_EOL;
// echo $multisigInfo . PHP_EOL;

/**
 * 連署者アカウントの確認
 */
// $multisigInfo = $multisigApiInstance->getAccountMultisig($carolKey1->address);
// echo "===連署者1のマルチシグ情報===" . PHP_EOL;
// echo $multisigInfo . PHP_EOL;

/**
 * マルチシグ署名
 */
// $tx = new EmbeddedTransferTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,  //マルチシグ化したアカウントの公開鍵
//   recipientAddress: $aliceKey->address,
//   mosaics: [
//     new UnresolvedMosaic(
//       mosaicId: new UnresolvedMosaicId($namespaceId), // モザイクID
//       amount: new Amount(1000000) // 金額(1XYM)
//     )
//   ],
//   message: "\0test"
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートトランザクションの作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey1->publicKey,  // マルチシグ化したいアカウントの公開鍵を指定
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100, 2);  // 手数料

// // 起案者アカウントによる署名
// $sig = $carolKey1->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// // 追加・除外対象として指定したアカウントによる連署
// $coSig1 = $facade->cosignTransaction($carolKey2->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig1);
// $coSig2 = $facade->cosignTransaction($carolKey3->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig2);

// // アナウンス
// $payload = ["payload" => strtoupper(bin2hex($aggregateTx->serialize()))];

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($aggregateTx) . PHP_EOL;

/**
 * アグリゲートボンデッドトランザクションで送信
 */

// アグリゲートTxに含めるTxを作成
// $tx = new EmbeddedTransferTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,
//   recipientAddress: $aliceKey->address,
//   mosaics: [
//     new UnresolvedMosaic(
//       mosaicId: new UnresolvedMosaicId($namespaceId), // モザイクID
//       amount: new Amount(1000000) // 金額(1XYM)
//     )
//   ],
//   message: "\0test"
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$tx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートボンデッドTx作成
// $aggregateTx = new AggregateBondedTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey1->publicKey,  // 起案者アカウントの公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100, 2);  // 手数料

// // 署名
// $sig = $carolKey1->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// // ハッシュロックTx作成
// $hashLockTx = new HashLockTransactionV1(
//   signerPublicKey: $carolKey1->publicKey,  // 署名者公開鍵
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
// $hashLockSig = $carolKey1->signTransaction($hashLockTx);
// $hashLockJsonPayload = $facade->attachSignature($hashLockTx, $hashLockSig);

// /**
//  * ハッシュロックをアナウンス
//  */
// try {
//   $result = $apiInstance->announceTransaction($hashLockJsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($hashLockTx) . PHP_EOL;

// sleep(40);

// // ボンデッドTxのアナウンス

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

// sleep(10);

// /**
//  * 連署
//  */
// // トランザクションの取得
// $hash = "B8C1EF1EEEB517A049A7574CDEACD235104FB3D5F5F70C0184848E71616C74C5";
// $txInfo = $apiInstance->getPartialTransaction($facade->hashTransaction($aggregateTx));

// // $txInfo = $apiInstance->getPartialTransaction($hash);


// /**
//  * carolKey2の連署
//  */
// $signTxHash = new Hash256($txInfo->getMeta()->getHash());
// $signature = new Signature($carolKey2->keyPair->sign($signTxHash->binaryData));
// $body = [
//     'parentHash' => $signTxHash->__toString(),
//     'signature' => $signature->__toString(),
//     'signerPublicKey' => $carolKey2->publicKey->__toString(),
//     'version' => '0'
// ];

// //アナウンス
// try {
//   $result = $apiInstance->announceCosignatureTransaction($body);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $signTxHash->__toString() . PHP_EOL;
// sleep(2);

// /**
//  * carolKey3の連署
//  */
// $signature = new Signature($carolKey3->keyPair->sign($signTxHash->binaryData));
// $body = [
//     'parentHash' => $signTxHash->__toString(),
//     'signature' => $signature->__toString(),
//     'signerPublicKey' => $carolKey3->publicKey->__toString(),
//     'version' => '0'
// ];

// //アナウンス
// try {
//   $result = $apiInstance->announceCosignatureTransaction($body);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $signTxHash->__toString() . PHP_EOL;

/**
 * マルチシグ送信の確認
 */
// $aggregateHash = '6122124B72EEE31237F0CBF1A9A133E3D94AAB0C15B8582CCBA7187082E5DAD4';
// // $aggregateHash = $facade->hashTransaction($aggregateTx);

// $txInfo = $apiInstance->getConfirmedTransaction($aggregateHash);
// echo "\n===送信トランザクション===" . PHP_EOL;
// echo $txInfo . PHP_EOL;

/**
 * マルチシグの構成の縮小
 */
// $multisigTx = new EmbeddedMultisigAccountModificationTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $bobKey->publicKey,  // マルチシグ化したいアカウントの公開鍵を指定
//   minApprovalDelta: -1, // minApproval:承認のために必要な最小署名者数増分
//   minRemovalDelta: -1, // minRemoval:除名のために必要な最小署名者数増分
//   addressAdditions: [], //追加対象アドレスリスト
//   addressDeletions: [
//     $carolKey3->address,
//   ] // 除名対象アドレスリスト
// );

// // マークルハッシュの算出
// $embeddedTransactions = [$multisigTx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートトランザクションの作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey1->publicKey,  // 起案者アカウントの公開鍵
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100, 2);  // 手数料

// // 起案者アカウントによる署名
// $sig = $carolKey1->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// // 連署者アカウントによる連署
// $coSig1 = $facade->cosignTransaction($carolKey2->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig1);
// $coSig4 = $facade->cosignTransaction($carolKey4->keyPair, $aggregateTx);
// array_push($aggregateTx->cosignatures, $coSig4);

// // アナウンス
// $payload = ["payload" => strtoupper(bin2hex($aggregateTx->serialize()))];

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($aggregateTx) . PHP_EOL;

/**
 * 連署者構成の差替え
 */

 // マルチシグ設定Tx作成
$multisigTx = new EmbeddedMultisigAccountModificationTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $bobKey->publicKey,  // 構成変更したいマルチシグアカウントの公開鍵を指定
  minApprovalDelta: 0, // minApproval:承認のために必要な最小署名者数増分
  minRemovalDelta: 0, // minRemoval:除名のために必要な最小署名者数増分
  addressAdditions: [
    $carolKey5->address,
  ],
  addressDeletions: [
    $carolKey4->address,
  ] // 除名対象アドレスリスト
);

// マークルハッシュの算出
$embeddedTransactions = [$multisigTx];
$merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// アグリゲートトランザクションの作成
$aggregateTx = new AggregateCompleteTransactionV2(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $carolKey1->publicKey,  // 起案者アカウントの公開鍵
  deadline: new Timestamp($facade->now()->addHours(2)),
  transactionsHash: $merkleHash,
  transactions: $embeddedTransactions
);
$facade->setMaxFee($aggregateTx, 100, 2);  // 手数料

// 起案者アカウントによる署名
$sig = $carolKey1->signTransaction($aggregateTx);
$payload = $facade->attachSignature($aggregateTx, $sig);

// 連署者アカウントによる連署
$coSig2 = $facade->cosignTransaction($carolKey2->keyPair, $aggregateTx);
array_push($aggregateTx->cosignatures, $coSig2);
$coSig5 = $facade->cosignTransaction($carolKey5->keyPair, $aggregateTx);
array_push($aggregateTx->cosignatures, $coSig5);

// アナウンス
$payload = ["payload" => strtoupper(bin2hex($aggregateTx->serialize()))];

try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
echo 'TxHash' . PHP_EOL;
echo $facade->hashTransaction($aggregateTx) . PHP_EOL;