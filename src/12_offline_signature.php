<?php
use SymbolSdk\Symbol\Models\Cosignature;
use SymbolSdk\Symbol\Models\Hash256;
require_once(__DIR__ . '/util.php');
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1;
use SymbolSdk\Symbol\Models\TransactionFactory;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\IdGenerator;
use SymbolSdk\Symbol\Models\NamespaceId;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolRestClient\Configuration;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\CryptoTypes\Signature;

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$apiInstance = new TransactionRoutesApi($client, $config);

/**
 * 秘密鍵からアカウント生成
 */
$aliceKey = $facade->createAccount(new PrivateKey($alicePrivateKey));
// $bobPrivateKey= PrivateKey::random();
// echo "Bob's private key: " . $bobPrivateKey. PHP_EOL;
$bobPrivateKey = 'B34C8DEEADF5FE608CB2FD245C9ECF8A70DAD7F7E66CB22614BAF*********';
$bobKey = $facade->createAccount(new PrivateKey($bobPrivateKey));

$namespaceIds = IdGenerator::generateNamespacePath('symbol.xym');
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

// アグリゲートTxに含めるTxを作成
$innerTx1 = new EmbeddedTransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $aliceKey->publicKey,
  recipientAddress: $bobKey->address,
  mosaics:[],
  message: "\0tx1",
);

$innerTx2 = new EmbeddedTransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $bobKey->publicKey,
  recipientAddress: $aliceKey->address,
  mosaics:[],
  message: "\0tx2",
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
  transactions: $embeddedTransactions,
);
$facade->setMaxFee($aggregateTx, 100, 2);

// 署名
$signedHash = $aliceKey->signTransaction($aggregateTx);
$signedPayload = $facade->attachSignature($aggregateTx, $signedHash);
// echo "\n===payload===" . PHP_EOL;
// echo $signedPayload['payload'] . PHP_EOL;

/**
 * Bobによる連署
 */
$tx = TransactionFactory::deserialize(hex2bin($signedPayload['payload'])); // バイナリデータにする
// echo "\n===tx===" . PHP_EOL;
// print_r($tx);
// $signature = new Signature($tx->signature);
// $res = $facade->verifyTransaction($tx, $signature);
// var_dump($res);

$bobCosignature = $facade->cosignTransaction($bobKey->keyPair, $tx);
$bobSignedTxSignature = $bobCosignature->signature;
$bobSignedTxSignerPublicKey = $bobCosignature->signerPublicKey;

$recreatedTx = TransactionFactory::deserialize(hex2bin($signedPayload['payload']));

// 連署者の署名を追加
$cosignature = new Cosignature();
$signTxHash = $facade->hashTransaction($aggregateTx);
$cosignature->parentHash = new Hash256($signTxHash);
$cosignature->version = 0;
$cosignature->signerPublicKey = $bobSignedTxSignerPublicKey;
$cosignature->signature = $bobSignedTxSignature;
array_push($recreatedTx->cosignatures, $cosignature);

$signedPayload = ["payload" => strtoupper(bin2hex($recreatedTx->serialize()))];
echo $signedPayload;

try {
  $result = $apiInstance->announceTransaction($signedPayload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
echo 'TxHash' . PHP_EOL;
echo $facade->hashTransaction($recreatedTx) . PHP_EOL;
