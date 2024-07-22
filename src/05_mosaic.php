<?php
require_once(__DIR__ . '/util.php');

use SymbolSdk\Symbol\Models\MosaicFlags;
use SymbolSdk\Symbol\Models\MosaicNonce;
use SymbolSdk\Symbol\Models\BlockDuration;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\MosaicSupplyChangeAction;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\IdGenerator;
use SymbolSdk\Symbol\Models\MosaicDefinitionTransactionV1;
use SymbolSdk\Symbol\Models\MosaicSupplyChangeTransactionV1;
use SymbolSdk\Symbol\Models\MosaicId;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolRestClient\Configuration;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolRestClient\Api\TransactionStatusRoutesApi;


/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));

/**
 * 5.1 モザイク生成
 */

$f = MosaicFlags::NONE;
$f += MosaicFlags::SUPPLY_MUTABLE; // 供給量変更可能
// $f += MosaicFlags::TRANSFERABLE; // 第三者への譲渡可否
$f += MosaicFlags::RESTRICTABLE; //制限設定の可否
$f += MosaicFlags::REVOKABLE; //発行者からの還収可否
$flags = new MosaicFlags($f);

// ナンス設定
$size = MosaicNonce::size(); // 必要なバイト数を取得
$randomBytes = random_bytes($size); // ランダムなバイト列を生成
// バイト列を整数の配列に変換
$array = unpack("C*", $randomBytes);

// ナンス計算
$nonce = new MosaicNonce(
  $array[1] * 0x00000001 +
  $array[2] * 0x00000100 +
  $array[3] * 0x00010000 +
  $array[4] * 0x01000000
);
$mosaicId = IdGenerator::generateMosaicId($aliceKey->address, $nonce->value);

// モザイク定義
$mosaicDefTx = new MosaicDefinitionTransactionV1(
  signerPublicKey: $aliceKey->publicKey, // 署名者公開鍵
  id: new MosaicId($mosaicId["id"]), // モザイクID
  divisibility: 2, // 分割可能性
  duration: new BlockDuration(0), //duration:有効期限
  nonce: $nonce,
  flags: $flags,
);

echo "\n===モザイク定義===" . PHP_EOL;
var_dump($mosaicDefTx);


//モザイク変更
$mosaicChangeTx = new MosaicSupplyChangeTransactionV1(
  signerPublicKey: $aliceKey->publicKey, // 署名者公開鍵
  mosaicId: new UnresolvedMosaicId($mosaicDefTx->id->value),
  delta: new Amount(10000),
  action: new MosaicSupplyChangeAction(MosaicSupplyChangeAction::INCREASE),
);

echo "\n===モザイク変更===" . PHP_EOL;
var_dump($mosaicChangeTx);


// マークルハッシュの算出
$embeddedTransactions = [$mosaicDefTx, $mosaicChangeTx];
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

// 署名
$sig = $aliceKey->signTransaction($aggregateTx);
$payload = $facade->attachSignature($aggregateTx, $sig);

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
$hash = $facade->hashTransaction($aggregateTx);
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