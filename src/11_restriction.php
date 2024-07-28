<?php
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\RestrictionMosaicRoutesApi;
use SymbolRestClient\Model\EmbeddedMosaicGlobalRestrictionTransactionDTO;
use SymbolRestClient\Model\MosaicGlobalRestrictionEntryRestrictionDTO;
use SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2;
use SymbolSdk\Symbol\Models\EmbeddedMosaicGlobalRestrictionTransactionV1;
use SymbolSdk\Symbol\Models\MosaicAddressRestrictionTransactionV1;
use SymbolSdk\Symbol\Models\MosaicId;
use SymbolSdk\Symbol\Models\MosaicRestrictionType;
use SymbolSdk\Symbol\Models\TransferTransactionV1;
use SymbolSdk\Symbol\Models\UnresolvedMosaic;
use SymbolRestClient\Api\RestrictionAccountRoutesApi;
use SymbolSdk\Symbol\Models\Amount;
use SymbolSdk\Symbol\Models\BlockDuration;
use SymbolSdk\Symbol\Models\EmbeddedMosaicDefinitionTransactionV1;
use SymbolSdk\Symbol\Models\EmbeddedMosaicSupplyChangeTransactionV1;
use SymbolSdk\Symbol\Models\MosaicFlags;
use SymbolSdk\Symbol\Models\MosaicNonce;
use SymbolSdk\Symbol\Models\MosaicSupplyChangeAction;
use SymbolSdk\Symbol\Models\AccountAddressRestrictionTransactionV1;
use SymbolSdk\Symbol\Models\AccountRestrictionFlags;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolRestClient\Configuration;
use SymbolRestClient\Api\TransactionRoutesApi;
use SymbolSdk\Symbol\Models\Timestamp;
use SymbolSdk\Symbol\Models\NetworkType;
use SymbolSdk\Symbol\IdGenerator;
use SymbolSdk\Symbol\Models\NamespaceId;
use SymbolSdk\Symbol\Models\AccountMosaicRestrictionTransactionV1;
use SymbolSdk\Symbol\Models\UnresolvedMosaicId;
use SymbolSdk\Symbol\Models\AccountOperationRestrictionTransactionV1;
use SymbolSdk\Symbol\Models\TransactionType;
use SymbolSdk\Symbol\Metadata;

$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$apiInstance = new TransactionRoutesApi($client, $config);


$namespaceIds = IdGenerator::generateNamespacePath('symbol.xym');
$namespaceId = new NamespaceId($namespaceIds[count($namespaceIds) - 1]);

// $carolKey = $facade->createAccount(PrivateKey::random());
$carolKey = $facade->createAccount(new PrivateKey('BCBE85710DE196C5B7221D8359F0BCDDCEA9C7C37C2C52DF1354A55EEB6B482F') );
echo 'Carol' . PHP_EOL;
echo 'Address: ' . $carolKey->address . PHP_EOL;

// echo $carolKey->keyPair->privateKey() . PHP_EOL;

echo "https://testnet.symbol.tools/?recipient=" . $carolKey->address . "&amount=20" . PHP_EOL;

/**
 * 指定アドレスからの受信制限・指定アドレスへの送信制限
 */
$bobKey = $facade->createAccount(new PrivateKey('7CBA79757479402DDCDE6577F938CDE6FD9035ACADC1E343AE155EFA679D462A') );
$bobAddress = $bobKey->address;
echo 'Bob' . PHP_EOL;
echo 'Address: ' . $bobAddress . PHP_EOL;

// 制限設定
// $f = AccountRestrictionFlags::ADDRESS; // アドレス制限
// $f += AccountRestrictionFlags::BLOCK; // ブロック
// $flags = new AccountRestrictionFlags($f);

// // アドレス制限設定Tx作成
// $tx = new AccountAddressRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   restrictionFlags: $flags, // 制限フラグ
//   restrictionAdditions:[
//     $bobAddress
//   ],  // 設定アドレス
//   restrictionDeletions:[] // 削除アドレス
// );
// $facade->setMaxFee($tx, 1000);

// // 署名
// $sig = $carolKey->signTransaction($tx);
// $jsonPayload = $facade->attachSignature($tx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($jsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($tx) . PHP_EOL;

/**
 * 指定モザイクの受信制限
 */
// 制限設定
// $f = AccountRestrictionFlags::MOSAIC_ID; // モザイク制限
// $f += AccountRestrictionFlags::BLOCK; // ブロック
// $flags = new AccountRestrictionFlags($f);

// $tx = new AccountMosaicRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   restrictionFlags: $flags, // 制限フラグ
//   restrictionAdditions:[
//     new UnresolvedMosaicId($namespaceId)
//   ],  // 設定モザイク
//   restrictionDeletions:[] // 削除モザイク
// );
// $facade->setMaxFee($tx, 100);

// // 署名
// $sig = $carolKey->signTransaction($tx);
// $jsonPayload = $facade->attachSignature($tx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($jsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($tx) . PHP_EOL;

/**
 * 指定トランザクションの送信制限
 */
// $f = AccountRestrictionFlags::TRANSACTION_TYPE; // トランザクション制限
// $f += AccountRestrictionFlags::OUTGOING; // 送信
// $flags = new AccountRestrictionFlags($f);

// $tx = new AccountOperationRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   restrictionFlags: $flags, // 制限フラグ
//   restrictionAdditions:[
//     new TransactionType(TransactionType::ACCOUNT_OPERATION_RESTRICTION)
//   ],  // 設定トランザクション
//   restrictionDeletions:[] // 削除トランザクション
// );
// $facade->setMaxFee($tx, 100);

// // 署名
// $sig = $carolKey->signTransaction($tx);
// $jsonPayload = $facade->attachSignature($tx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($jsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($tx) . PHP_EOL;

/**
 * 確認
 */
// $restrictionAipInstance = new RestrictionAccountRoutesApi($client, $config);
// $res = $restrictionAipInstance->getAccountRestrictions($carolKey->address);
// echo $res . PHP_EOL;

/**
 * グローバルモザイク制限
 */

 $carolKey = $facade->createAccount(new PrivateKey('A4AC7AB846914021AFBFBE59BCF3B413C146101A60E77EE932EFB726CF12B116') );
echo 'Carol' . PHP_EOL;
echo 'Address: ' . $carolKey->address . PHP_EOL;

// モザイクフラグ設定
// $f = MosaicFlags::NONE;
// $f += MosaicFlags::SUPPLY_MUTABLE; // 供給量変更可能
// $f += MosaicFlags::TRANSFERABLE; // 第三者への譲渡可否
// $f += MosaicFlags::RESTRICTABLE; //制限設定の可否
// $f += MosaicFlags::REVOKABLE; //発行者からの還収可否
// $flags = new MosaicFlags($f);

// $mosaicId = IdGenerator::generateMosaicId($carolKey->address);
// echo 'MosaicId' . PHP_EOL;
// echo $mosaicId['id'] . PHP_EOL;

// // モザイク定義
// $mosaicDefTx = new EmbeddedMosaicDefinitionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey, // 署名者公開鍵
//   id: new MosaicId($mosaicId['id']), // モザイクID
//   divisibility: 2, // 分割可能性
//   duration: new BlockDuration(0), //duration:有効期限
//   nonce: new MosaicNonce($mosaicId['nonce']),
//   flags: $flags,
// );

// // モザイク変更
// $mosaicChangeTx = new EmbeddedMosaicSupplyChangeTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey, // 署名者公開鍵
//   mosaicId: new UnresolvedMosaicId($mosaicId['id']),
//   delta: new Amount(10000),
//   action: new MosaicSupplyChangeAction(MosaicSupplyChangeAction::INCREASE),
// );

// // キーの値と設定
// $keyId = Metadata::metadataGenerateKey("KYC"); // restrictionKey

// // グローバルモザイク制限
// $mosaicGlobalResTx = new EmbeddedMosaicGlobalRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   mosaicId: new UnresolvedMosaicId($mosaicId['id']),
//   restrictionKey: $keyId,
//   newRestrictionValue: 1,
//   newRestrictionType: new MosaicRestrictionType(MosaicRestrictionType::EQ),
// );
// // 更新する場合は以下も設定する必要あり
// //   - mosaicGlobalResTx.previousRestrictionValue
// //   - mosaicGlobalResTx.previousRestrictionType

// // マークルハッシュの算出
// $embeddedTransactions = [$mosaicDefTx, $mosaicChangeTx, $mosaicGlobalResTx];
// $merkleHash = $facade->hashEmbeddedTransactions($embeddedTransactions);

// // アグリゲートTx作成
// $aggregateTx = new AggregateCompleteTransactionV2(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   transactionsHash: $merkleHash,
//   transactions: $embeddedTransactions
// );
// $facade->setMaxFee($aggregateTx, 100);  // 手数料

// // 署名
// $sig = $carolKey->signTransaction($aggregateTx);
// $payload = $facade->attachSignature($aggregateTx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($payload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($aggregateTx) . PHP_EOL;

/**
 * アカウントへのモザイク制限適用
 */
$keyId = Metadata::metadataGenerateKey("KYC"); // restrictionKey

// carolに適用
// $carolMosaicAddressResTx = new MosaicAddressRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   mosaicId: new UnresolvedMosaicId('0x51E212A3D485C85F'),
//   restrictionKey: $keyId,
//   previousRestrictionValue: -1, // 以前のリストリクション値がなく、新規に値を設定する場合
//   newRestrictionValue: 1,
//   targetAddress: $carolKey->address,
// );
// $facade->setMaxFee($carolMosaicAddressResTx, 100);


// // 署名
// $sig = $carolKey->signTransaction($carolMosaicAddressResTx);
// $jsonPayload = $facade->attachSignature($carolMosaicAddressResTx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($jsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($carolMosaicAddressResTx) . PHP_EOL;

// // bobに適用
// $bobMosaicAddressResTx = new MosaicAddressRestrictionTransactionV1(
//   network: new NetworkType(NetworkType::TESTNET),
//   signerPublicKey: $carolKey->publicKey,
//   deadline: new Timestamp($facade->now()->addHours(2)),
//   mosaicId: new UnresolvedMosaicId('0x51E212A3D485C85F'),
//   restrictionKey: $keyId,
//   previousRestrictionValue: -1, // 以前のリストリクション値がなく、新規に値を設定する場合
//   newRestrictionValue: 1,
//   targetAddress: $bobKey->address,
// );
// $facade->setMaxFee($bobMosaicAddressResTx, 100);

// // 署名
// $sig = $carolKey->signTransaction($bobMosaicAddressResTx);
// $jsonPayload = $facade->attachSignature($bobMosaicAddressResTx, $sig);

// try {
//   $result = $apiInstance->announceTransaction($jsonPayload);
//   echo $result . PHP_EOL;
// } catch (Exception $e) {
//   echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
// }
// echo 'TxHash' . PHP_EOL;
// echo $facade->hashTransaction($bobMosaicAddressResTx) . PHP_EOL;

/**
 * 制限状態確認
 */

$restrictionAipInstance = new RestrictionMosaicRoutesApi($client, $config);
$res = $restrictionAipInstance->searchMosaicRestrictions(
  mosaic_id: '51E212A3D485C85F'
);
echo 'MosaicRestrictions' . PHP_EOL;
echo $res . PHP_EOL;

/**
 * 送信確認
 */
// 成功（CarolからBobに送信）
$tx = new TransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $carolKey->publicKey,  // 署名者公開鍵
  deadline: new Timestamp($facade->now()->addHours(2)), // 有効期限
  recipientAddress: $bobKey->address, // 受信者アドレス
  mosaics: [
    new UnresolvedMosaic(
      mosaicId: new UnresolvedMosaicId('0x51E212A3D485C85F'),  // モザイクID
      amount: new Amount(1) // 金額
    )
  ],
  message: '',
);
$facade->setMaxFee($tx, 100);  // 手数料
$sig = $carolKey->signTransaction($tx);
$payload = $facade->attachSignature($tx, $sig);
try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
echo 'TxHash' . PHP_EOL;
echo $facade->hashTransaction($tx) . PHP_EOL;

// 失敗（CarolからDaveに送信）
$daveKey = $facade->createAccount(PrivateKey::random());

$tx = new TransferTransactionV1(
  network: new NetworkType(NetworkType::TESTNET),
  signerPublicKey: $carolKey->publicKey,  // 署名者公開鍵
  deadline: new Timestamp($facade->now()->addHours(2)), // 有効期限
  recipientAddress: $daveKey->address, // 受信者アドレス
  mosaics: [
    new UnresolvedMosaic(
      mosaicId: new UnresolvedMosaicId('0x51E212A3D485C85F'),  // モザイクID
      amount: new Amount(1) // 金額
    )
  ],
  message: '',
);
$facade->setMaxFee($tx, 100);  // 手数料
$sig = $carolKey->signTransaction($tx);
$payload = $facade->attachSignature($tx, $sig);
try {
  $result = $apiInstance->announceTransaction($payload);
  echo $result . PHP_EOL;
} catch (Exception $e) {
  echo 'Exception when calling TransactionRoutesApi->announceTransaction: ', $e->getMessage(), PHP_EOL;
}
echo 'TxHash' . PHP_EOL;
echo $facade->hashTransaction($tx) . PHP_EOL;