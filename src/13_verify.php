<?php
use SymbolRestClient\Api\AccountRoutesApi;
use SymbolSdk\Merkle\MerkleHashBuilder;
use SymbolSdk\Symbol\Models\Importance;
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\BlockRoutesApi;
use SymbolSdk\CryptoTypes\Signature;
use SymbolSdk\Symbol\Models\Hash256;
use SymbolSdk\Symbol\Models\TransactionFactory;
use SymbolRestClient\Configuration;
use SymbolSdk\Merkle\Merkle;
use SymbolSdk\Symbol\Models\Address;
use SymbolSdk\Symbol\Models\BlockType;

$payload =
  "2802000000000000A5151FD55D82351DD488DB5563DD328DA72B2AD25B513C1D0F7F78AFF4D35BA094ABF505C74E6D6BE1FA19F3E5AC60A85E1A4EDC4AC07DECC0E56C59D5D24F0B69A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB0000000002984141A0D70000000000000EEAD6810500000062E78B6170628861B4FC4FCA75210352ACDBD2378AC0A447A3DCF63F969366BB1801000000000000540000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198544198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D04000000000000000074783100000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D6668A0DE72812AAE05000500746573743100000000000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329DBF85DADBFD54C48D050005007465737432000000000000000000000000000000662CEDF69962B1E0F1BF0C43A510DFB12190128B90F7FE9BA48B1249E8E10DBEEDD3B8A0555B4237505E3E0822B74BCBED8AA3663022413AFDA265BE1C55431ACAE3EA975AF6FD61DEFFA6A16CBA5174A16EF5553AE669D5803A0FA9D1424600";
$height = 686312;

$tx = TransactionFactory::deserialize(hex2bin($payload));
$hash = $facade->hashTransaction($tx);
echo "===payload確認===" . PHP_EOL;
// print_r($tx);

/**
 * 署名の検証
 */

$signature = new Signature($tx->signature);
$res = $facade->verifyTransaction($tx, $signature);
echo "===署名の検証===" . PHP_EOL;
var_dump($res);

/**
 * マークルコンポーネントハッシュの計算
 */
$merkleComponentHash = $hash;

if (isset($tx->cosignatures) && count($tx->cosignatures) > 0) {
  $hasher = new MerkleHashBuilder();
  $hash = new Hash256($hash);
  $hasher->update($hash);
  foreach ($tx->cosignatures as $cosignature) {
    $hasher->update(new Hash256($cosignature->signerPublicKey));
  }
  $merkleComponentHash = $hasher->final();
}
echo "===マークルコンポーネントハッシュ===" . PHP_EOL;
echo strtoupper($merkleComponentHash) . PHP_EOL;

/**
 * InBlockの検証
 */

$leafhash = new Hash256($merkleComponentHash);

// ノードから取得
$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$blockApiInstance = new BlockRoutesApi($client, $config);

$HRoot = $blockApiInstance->getBlockByHeight($height);
$HRootHash = new Hash256($HRoot["block"]["transactions_hash"]);

$merkleProof = $blockApiInstance->getMerkleTransaction($height, $leafhash);
$merklePath = $merkleProof["merkle_path"];

$resutl = Merkle::proveMerkle($leafhash, $merklePath, $HRootHash);
echo "===InBlockの検証===" . PHP_EOL;
var_dump($resutl);

/**
 * ブロックヘッダーの検証
 */
//normalブロックの検証
/**
 * Converts a given number to a reversed hex string of specified byte length.
 *
 * @param int|string $number The number to convert.
 * @param int $bytes The byte length of the resulting hex string.
 * @return string The reversed hex string.
 */
function reverseHex($number, $bytes = 1) {
  // 10進数を16進数に変換し、必要に応じてゼロパディング
  $hex = str_pad(dechex($number), $bytes * 2, "0", STR_PAD_LEFT);
  // 16進数の文字列をバイナリデータに変換
  $bin = hex2bin($hex);
  // バイナリデータを逆順にする
  $reversed = strrev($bin);
  // バイナリデータを16進数の文字列に変換
  $reversedHex = bin2hex($reversed);
  return $reversedHex;
}

$blockInfo = $blockApiInstance->getBlockByHeight($height);
$block = $blockInfo["block"];
$previousBlockHash = $blockApiInstance->getBlockByHeight($height - 1);
$previousBlockHash = $previousBlockHash["meta"]["hash"];

if ($block['type'] === BlockType::NORMAL) {
  $hasher = hash_init('sha3-256');

  hash_update($hasher, hex2bin($block['signature'])); // signature
  hash_update($hasher, hex2bin($block['signer_public_key'])); // publicKey

  hash_update($hasher, hex2bin(reverseHex($block['version'],1)));
  hash_update($hasher, hex2bin(reverseHex($block['network'], 1)));
  hash_update($hasher, hex2bin(reverseHex($block['type'], 2)));
  hash_update($hasher, hex2bin(reverseHex($block['height'], 8)));
  hash_update($hasher, hex2bin(reverseHex($block['timestamp'], 8)));
  hash_update($hasher, hex2bin(reverseHex($block['difficulty'], 8)));

  hash_update($hasher, hex2bin($block['proof_gamma']));
  hash_update($hasher, hex2bin($block['proof_verification_hash']));
  hash_update($hasher, hex2bin($block['proof_scalar']));
  hash_update($hasher, hex2bin($previousBlockHash));
  hash_update($hasher, hex2bin($block['transactions_hash']));
  hash_update($hasher, hex2bin($block['receipts_hash']));
  hash_update($hasher, hex2bin($block['state_hash']));
  hash_update($hasher, hex2bin($block['beneficiary_address']));
  hash_update($hasher, hex2bin(reverseHex($block['fee_multiplier'], 4)));

  $hash = strtoupper(bin2hex(hash_final($hasher, true)));

  echo "===ブロックヘッダーの検証===" . PHP_EOL;
  var_dump($hash === $blockInfo['meta']['hash']);
}

/**
 * importanceブロックの検証
 */
$height = 1440;
// height = Importance Block のブロック高
$blockInfo = $blockApiInstance->getBlockByHeight($height);
$block = $blockInfo["block"];
$previousBlockHash = $blockApiInstance->getBlockByHeight($height - 1);
$previousBlockHash = $previousBlockHash["meta"]["hash"];

if($block['type'] === BlockType::IMPORTANCE){
  $hasher = hash_init('sha3-256');

  hash_update($hasher, hex2bin($block['signature'])); // signature
  hash_update($hasher, hex2bin($block['signer_public_key'])); // publicKey

  hash_update($hasher, hex2bin(reverseHex($block['version'],1)));
  hash_update($hasher, hex2bin(reverseHex($block['network'],1)));
  hash_update($hasher, hex2bin(reverseHex($block['type'],1)));
  hash_update($hasher, hex2bin(reverseHex($block['height'], 8)));
  hash_update($hasher, hex2bin(reverseHex($block['timestamp'], 8)));
  hash_update($hasher, hex2bin(reverseHex($block['difficulty'], 8)));

  hash_update($hasher, hex2bin($block['proof_gamma']));
  hash_update($hasher, hex2bin($block['proof_verification_hash']));
  hash_update($hasher, hex2bin($block['proof_scalar']));
  hash_update($hasher, hex2bin($previousBlockHash));
  hash_update($hasher, hex2bin($block['transactions_hash']));
  hash_update($hasher, hex2bin($block['receipts_hash']));
  hash_update($hasher, hex2bin($block['state_hash']));
  hash_update($hasher, hex2bin($block['beneficiary_address']));
  hash_update($hasher, hex2bin(reverseHex($block['fee_multiplier'], 4)));
  hash_update($hasher, hex2bin(reverseHex($block['voting_eligible_accounts_count'], 4)));
  hash_update($hasher, hex2bin(reverseHex($block['harvesting_eligible_accounts_count'], 8)));
  hash_update($hasher, hex2bin(reverseHex($block['total_voting_balance'], 8)));
  hash_update($hasher, hex2bin($block['previous_importance_block_hash']));

  $hash = strtoupper(bin2hex(hash_final($hasher, true)));

  echo "===importanceブロックの検証===" . PHP_EOL;
  var_dump($hash === $blockInfo['meta']['hash']);
}

/**
 * stateHashの検証
 */
// print_r($blockInfo);

$hasher = hash_init('sha3-256');
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][0]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][1]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][2]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][3]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][4]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][5]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][6]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][7]));
hash_update($hasher, hex2bin($blockInfo['meta']['state_hash_sub_cache_merkle_roots'][8]));
$hash = strtoupper(bin2hex(hash_final($hasher, true)));
echo "===stateHashの検証===" . PHP_EOL;
var_dump($hash === $blockInfo['block']['state_hash']);
