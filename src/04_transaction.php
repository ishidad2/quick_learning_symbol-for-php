<?php
require_once(__DIR__ . '/util.php');

use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\TransferTransactionV1;
use SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\UnresolvedMosaic;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolRestClient\Api\TransactionStatusRoutesApi;
use SymbolRestClient\Configuration;

/**
 * 4.2 トランザクション作成
 */

/**
 * Bobへの転送トランザクション（と見せかけて自身への転送トランザクション）
 */

$bobKey = $facade->createAccount(new PrivateKey($alicePrivateKey));
$bob= $facade->createPublicAccount($bobKey->publicKey);
echo $bob->address . PHP_EOL;

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));

/**
 * トランザクション作成
 */
$messageData = "\0hello, symbol!";
$transferTransaction = new TransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET), // ネットワークタイプ
  signerPublicKey: $aliceKey->publicKey,  // 署名者公開鍵
	deadline: new Timestamp($facade->now()->addHours(2)), //Deadline:有効期限
  recipientAddress: $bob->address,  // 受信者アドレス
  mosaics: [
    new UnresolvedMosaic(
      mosaicId: new UnresolvedMosaicId('0x72C0212E67A08BCE'), // モザイクID
      amount: new Amount(1000000) // 金額(1XYM)
    )
  ],
  message: $messageData
);
$facade->setMaxFee($transferTransaction, 100);  // 手数料

echo "\n===トランザクション作成===" . PHP_EOL;
var_dump($transferTransaction);

/**
 * 署名
 */
$signature = $aliceKey->signTransaction($transferTransaction);
$payload = $facade->attachSignature($transferTransaction, $signature);
echo "\n===署名===" . PHP_EOL;
var_dump($payload);

/**
 * アナウンス
 */
$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$apiInstance = new TransactionRoutesApi($client, $config);

try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}

sleep(5);
/**
 * ステータスの確認
 */
$hash = $facade->hashTransaction($transferTransaction);
echo "\n===トランザクションハッシュ===" . PHP_EOL;
echo $hash . PHP_EOL;

$txStatusApi = new TransactionStatusRoutesApi($client, $config);

try {
  $txStatus = $txStatusApi->getTransactionStatus($hash);
  echo "\n===ステータスの確認===" . PHP_EOL;
  var_dump($txStatus);
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}

sleep(30);
/**
 * 承認確認
 */
try {
  $result = $apiInstance->getConfirmedTransaction($hash);
  echo "\n===承認確認===" . PHP_EOL;
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}

/**
 * トランザクション履歴
 */
try {
  $result = $apiInstance->searchConfirmedTransactions(
    address: $aliceKey->address,
    embedded: "true",
    page_size: 10,
    page_number: 1
  );
  echo "\n===トランザクション履歴===" . PHP_EOL;
  var_dump($result);
} catch (\Throwable $th) {
  echo 'Exception when calling TransactionRoutesApi->searchConfirmedTransactions: ', $th->getMessage(), PHP_EOL;
}

/**
 * アグリゲートトランザクション
 */
$bobKey = $facade->createAccount(PrivateKey::random());
$bobAddress = $bobKey->address;
$carolKey = $facade->createAccount(PrivateKey::random());
$carolAddress = $carolKey->address;

// アグリゲートTxに含めるTxを作成
$innerTx1 = new EmbeddedTransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,
  recipientAddress: $carolAddress,
  message: "\0hello, carol!"
);

$innerTx2 = new EmbeddedTransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,
  recipientAddress: $bobAddress,
  message: "\0hello, bob!"
);

// マークルハッシュの算出
$embeddedTransactions = [$innerTx1, $innerTx2];
$merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// アグリゲートTx作成
$aggregateTx = new AggregateCompleteTransactionV2(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,
  deadline: new Timestamp($facade->now()->addHours(2)),
  transactionsHash: $merkleHash,
  transactions: $embeddedTransactions
);
$facade->setMaxFee($aggregateTx, 100);  // 手数料

// アグリゲートトランザクションにおける最大手数料
// $requiredCosignatures = 1; // 必要な連署者の数を指定
// $calculatedCosignatures = $requiredCosignatures > count($aggregateTx->cosignatures)
//     ? $requiredCosignatures
//     : count($aggregateTx->cosignatures);
// $sizePerCosignature = 8 + 32 + 64;
// $calculatedSize = $aggregateTx->size() -
//     count($aggregateTx->cosignatures) * $sizePerCosignature +
//     $calculatedCosignatures * $sizePerCosignature;

// $aggregateTx->fee = new Amount($calculatedSize * 100);  // 手数料

// 署名
$sig = $aliceKey->signTransaction($aggregateTx);
$payload = $facade->attachSignature($aggregateTx, $sig);
// アナウンス
try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
