<?php
use SymbolRestClient\Api\MetadataRoutesApi;
use SymbolSdk\Symbol\Models\MosaicId;
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\AccountRoutesApi;
use SymbolSdk\Merkle\MerkleHashBuilder;
use SymbolSdk\Symbol\Models\Importance;
use SymbolRestClient\Api\BlockRoutesApi;
use SymbolSdk\CryptoTypes\Signature;
use SymbolSdk\Symbol\Models\Hash256;
use SymbolSdk\Symbol\Models\TransactionFactory;
use SymbolRestClient\Configuration;
use SymbolSdk\CryptoTypes\PrivateKey;
use SymbolSdk\Merkle\Merkle;
use SymbolSdk\Symbol\Metadata;
use SymbolSdk\Symbol\Models\Address;
use SymbolSdk\Symbol\Models\BlockType;

$payload =
  "2802000000000000A5151FD55D82351DD488DB5563DD328DA72B2AD25B513C1D0F7F78AFF4D35BA094ABF505C74E6D6BE1FA19F3E5AC60A85E1A4EDC4AC07DECC0E56C59D5D24F0B69A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB0000000002984141A0D70000000000000EEAD6810500000062E78B6170628861B4FC4FCA75210352ACDBD2378AC0A447A3DCF63F969366BB1801000000000000540000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198544198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D04000000000000000074783100000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D6668A0DE72812AAE05000500746573743100000000000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329DBF85DADBFD54C48D050005007465737432000000000000000000000000000000662CEDF69962B1E0F1BF0C43A510DFB12190128B90F7FE9BA48B1249E8E10DBEEDD3B8A0555B4237505E3E0822B74BCBED8AA3663022413AFDA265BE1C55431ACAE3EA975AF6FD61DEFFA6A16CBA5174A16EF5553AE669D5803A0FA9D1424600";
$height = 686312;

$tx = TransactionFactory::deserialize(hex2bin($payload));
$hash = $facade->hashTransaction($tx);
// echo "===payload確認===" . PHP_EOL;
// print_r($tx);

/**
 * 署名の検証
 */

$signature = new Signature($tx->signature);
$res = $facade->verifyTransaction($tx, $signature);
// echo "===署名の検証===" . PHP_EOL;
// var_dump($res);

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
// echo "===マークルコンポーネントハッシュ===" . PHP_EOL;
// echo strtoupper($merkleComponentHash) . PHP_EOL;

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
// echo "===InBlockの検証===" . PHP_EOL;
// var_dump($resutl);

/**
 * ブロックヘッダーの検証
 */
 //normalブロックの検証

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

  // echo "===ブロックヘッダーの検証===" . PHP_EOL;
  // var_dump($hash === $blockInfo['meta']['hash']);
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

  // echo "===importanceブロックの検証===" . PHP_EOL;
  // var_dump($hash === $blockInfo['meta']['hash']);
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
// echo "===stateHashの検証===" . PHP_EOL;
// var_dump($hash === $blockInfo['block']['state_hash']);

/**
 * 13.3 アカウント・メタデータの検証
 */

 //検証用共通関数

function reverseHex($hex, $bytes = 1) {
  // 10進数を16進数に変換し、必要に応じてゼロパディング
  $hex = str_pad(dechex($hex), $bytes * 2, "0", STR_PAD_LEFT);
  // 16進数の文字列をバイナリデータに変換
  $bin = hex2bin($hex);
  // バイナリデータを逆順にする
  $reversed = strrev($bin);
  // バイナリデータを16進数の文字列に変換
  $reversedHex = bin2hex($reversed);
  return $reversedHex;
}

//葉のハッシュ値取得関数
function getLeafHash($encodedPath, $leafValue) {
  $hasher = hash_init('sha3-256');
  hash_update($hasher, hex2bin($encodedPath . $leafValue));
  $hash = strtoupper(bin2hex(hash_final($hasher, true)));
  return $hash;
}
// getLeafHash("200F84DD2830B37539EF766DD37A0DA6150FB8E14AEE2ED2773262F4AF14CF","39B9DF440E50AF995D7E8DD94FA38BF68033CC39053B8C9FA1BFC2AA25C99F91");

// 枝のハッシュ値取得関数
function getBranchHash($encodedPath, $links) {
  $branchLinks = array_fill(0, 16, bin2hex(str_repeat(chr(0), 32)));
  foreach ($links as $link) {
      $index = hexdec($link['bit']);
      $branchLinks[$index] = $link['link'];
  }
  $concatenated = $encodedPath . implode("", $branchLinks);
  $hasher = hash_init('sha3-256');
  hash_update($hasher, hex2bin($concatenated));

  return strtoupper(bin2hex(hash_final($hasher, true)));
}
// $array = [
//   ["bit" => 2, "link" => "513DB50C2C5D5ADFEE727C4DAB2FD15D67D445DACE0EE4A91D2316BB6B5184DB"],
//   ["bit" => 4, "link" => "B6BFC203326047B79F350E8D397B7278B52B05B8061CBAE31A1E3E75EB374A11"],
//   ["bit" => 5, "link" => "A196EDBEDD6025B5A1C91D07033067FD7C231954FFD68CA7845053F6E0DECDE9"],
//   ["bit" => 6, "link" => "BE463563FE9855B88AC5EB5D4C648FCD0ACBF88FB470B4A156C6DF699469A7B0"],
//   ["bit" => 7, "link" => "3879965782C68D7BAFD9A3A5318606AC7720633E05F16A74998C979CE7CF6F4D"],
//   ["bit" => 8, "link" => "D92E9488B9A54EBF96760969B05E260B97E4FD0C4BCA87193ED88CC50C6A6610"],
//   ["bit" => 9, "link" => "D5AD2127DD636D531F26609A6F0055BB5B607C978BF4E8C7E3515125A76A956B"],
//   ["bit" => "A", "link" => "65DFF47A75A11595830C0B6E03E44DC3E869A57EFC51632350B4A999D69BB24E"],
//   ["bit" => "B", "link" => "38E66E2F0B029AE498F6EE0DC22C4F2836AF9F0E02B7E4EDD94A13EF0451FDA5"],
//   ["bit" => "C", "link" => "17F2795C8028161B541527BE46B12353CCADE3E01FCEF52C71FE5BC6AEDEA6B7"],
//   ["bit" => "E", "link" => "E64C24870C17F255F96E19120929AF366C81ADCDFB585D9976F3F26C855EF71C"],
//   ["bit" => "F", "link" => "AAC621E07225A53114FBDE5A4EA0C79860743C0122F25A26BAAD0638C697E5FD"]
// ];
// $res = getBranchHash('00', $array);
// echo $res . PHP_EOL;

// ワールドステートの検証

function checkState($stateProof, $stateHash, $pathHash, $rootHash) {
  $merkleLeaf = null;
  $merkleBranches = [];
  foreach($stateProof['tree'] as $n){
    if($n['type'] === 255){
      $merkleLeaf = $n;
    } else {
      $merkleBranches[] = $n;
    }
  }
  $merkleBranches = array_reverse($merkleBranches);
  $leafHash = getLeafHash($merkleLeaf['encoded_path'], $stateHash);

  $linkHash = $leafHash;  // リンクハッシュの初期値は葉ハッシュ
  $bit = "";
  for($i=0; $i <  count($merkleBranches); $i++){
    $branch = $merkleBranches[$i];
    $branchLink = array_filter($branch['links'], function($link) use ($linkHash) {
      return $link['link'] === $linkHash;
    });
    $branchLink = reset($branchLink); // 最初の要素を取得
    $linkHash = getBranchHash($branch['encoded_path'], $branch['links']);
    $bit = substr($merkleBranches[$i]['path'], 0, $merkleBranches[$i]['nibble_count']) . $branchLink['bit'] . $bit;
  }
  $treeRootHash = $linkHash; //最後のlinkHashはrootHash
  $treePathHash = $bit . $merkleLeaf['path'];
  if(strlen($treePathHash) % 2 == 1){
    $treePathHash = substr($treePathHash, 0, -1);
  }

  // 検証
  var_dump($treeRootHash === $rootHash);
  var_dump($treePathHash === $pathHash);

}

function hexToUint8($hex) {
  // 16進数文字列をバイナリデータに変換
  $binary = hex2bin($hex);
  // バイナリデータを配列に変換
  return array_values(unpack('C*', $binary));
}

/**
 * アカウント情報の検証
 */

$aliceRawAddress = "TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ";
$aliceAddress = new Address($aliceRawAddress);

$hasher = hash_init('sha3-256');
$alicePathHash = hash_update($hasher, hex2bin($aliceAddress));
$alicePathHash = strtoupper(bin2hex(hash_final($hasher, true)));

$hasher = hash_init('sha3-256');
$accountApiInstance = new AccountRoutesApi($client, $config);
$aliceInfo = $accountApiInstance->getAccountInfo($aliceRawAddress);
$aliceInfo = $aliceInfo["account"];

// アカウント情報から StateHash を導出
$format = (int)$aliceInfo['importance'] === 0 || strlen($aliceInfo['activity_buckets']) <5 ? 0x00 : 0x01;

$supplementalPublicKeysMask = 0x00;
$linkedPublicKey = [];
if($aliceInfo['supplemental_public_keys']['linked'] !== null){
  $supplementalPublicKeysMask |= 0x01;  // OR 演算子と代入演算子を組み合わせ
  $linkedPublicKey = hexToUint8($aliceInfo['supplemental_public_keys']['linked']['public_key']);
}

$nodePublicKey = [];
if($aliceInfo['supplemental_public_keys']['node'] !== null){
  $supplementalPublicKeysMask |= 0x02;
  $nodePublicKey = hexToUint8($aliceInfo['supplemental_public_keys']['node']['public_key']);
}

$vrfPublicKey = [];
if($aliceInfo['supplemental_public_keys']['vrf'] !== null){
  $supplementalPublicKeysMask |= 0x04;
  $vrfPublicKey = hexToUint8($aliceInfo['supplemental_public_keys']['vrf']['public_key']);
}

$votingPublicKeys = [];
if($aliceInfo['supplemental_public_keys']['voting'] !== null){
  foreach($aliceInfo['supplemental_public_keys']['voting']['public_key'] as $key){
    $votingPublicKeys = array_merge($votingPublicKeys, hexToUint8($key['public_key']));
  }
}

$importanceSnapshots = [];
if((int)$aliceInfo['importance'] !== 0){
  $importanceSnapshots = array_merge(
    hexToUint8(reverseHex($aliceInfo['importance_snapshot'], 8)),
    hexToUint8(reverseHex($aliceInfo['importance_snapshot_height'], 8))
  );
}

$activityBuckets = [];
if((int)$aliceInfo['importance'] > 0){
  for ($idx = 0; $idx < count($aliceInfo['activity_buckets']) || $idx < 5; $idx++) {
    $bucket = $aliceInfo['activity_buckets'][$idx];
    $activityBuckets = array_merge(
      $activityBuckets,
      hexToUint8(reverseHex($bucket['start_height'], 8)),
      hexToUint8(reverseHex($bucket['total_fees_paid'], 8)),
      hexToUint8(reverseHex($bucket['beneficiary_count'], 4)),
      hexToUint8(reverseHex($bucket['raw_score'], 8))
    );
  }
}

$balances = [];
if(count($aliceInfo['mosaics']) > 0){
  foreach($aliceInfo['mosaics'] as $mosaic){
    $balances = array_merge(
      $balances,
      hexToUint8(bin2hex(strrev(hex2bin($mosaic['id'])))),
      hexToUint8(reverseHex($mosaic['amount'], 8))
    );
  }
}

$accountInfoBytes = array_merge(
  hexToUint8(reverseHex($aliceInfo['version'], 2)),
  hexToUint8($aliceInfo['address']),
  hexToUint8(reverseHex($aliceInfo['address_height'], 8)),
  hexToUint8($aliceInfo['public_key']),
  hexToUint8(reverseHex($aliceInfo['public_key_height'], 8)),
  hexToUint8(reverseHex($aliceInfo['account_type'], 1)),
  hexToUint8(reverseHex($format, 1)),
  hexToUint8(reverseHex($supplementalPublicKeysMask, 1)),
  hexToUint8(reverseHex(count($votingPublicKeys), 1)),
  $linkedPublicKey,
  $nodePublicKey,
  $vrfPublicKey,
  $votingPublicKeys,
  $importanceSnapshots,
  $activityBuckets,
  hexToUint8(reverseHex(count($aliceInfo['mosaics']), 2)),
  $balances,
);
// var_dump($accountInfoBytes);

$accountInfoBytesString = implode('', array_map('chr', $accountInfoBytes));

hash_update($hasher, $accountInfoBytesString);
$aliceStateHash = strtoupper(bin2hex(hash_final($hasher, true)));

// var_dump($aliceStateHash);

// require_once(__DIR__ . '/13_sample_data.php');
// checkState($stateProofSample, $stateHashSample, $pathHashSample, $rootHashSample);

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
$blockInfo = $blockApiInstance->searchBlocks(order: 'desc');
$rootHash = $blockInfo['data'][0]['meta']['state_hash_sub_cache_merkle_roots'][0];

//サービス提供者を含む任意のノードからマークル情報を取得
$stateProof = $accountApiInstance->getAccountInfoMerkle($aliceRawAddress);

//検証
checkState($stateProof, $aliceStateHash, $alicePathHash, $rootHash);

/**
 * モザイクへ登録したメタデータの検証
 */
$srcAddress = new Address('TDSSDPIPAJHVRZTQUAR36OQU6O7MV4BIAOLL5UA');
$targetAddress = new Address('TDSSDPIPAJHVRZTQUAR36OQU6O7MV4BIAOLL5UA');

$scopeKey = Metadata::metadataGenerateKey('key_mosaic'); //メタデータキー
$scopeKey = strtoupper(dechex($scopeKey));
$targetId = '6FA40B0B8B9E392F'  ; //モザイクID

$hasher = hash_init('sha3-256');
hash_update($hasher, $srcAddress->binaryData);
hash_update($hasher, $targetAddress->binaryData);
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($scopeKey))));
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($targetId))));
hash_update($hasher, chr(1));

$compositeHash = hash_final($hasher, true);

$hasher = hash_init('sha3-256');
hash_update($hasher, $compositeHash);
$pathHash1 = strtoupper(bin2hex(hash_final($hasher, true)));

// echo "Path Hash 1: " . $pathHash1 . PHP_EOL;

//stateHash(Value値)
$hasher = hash_init('sha3-256');
$version = 1;
hash_update($hasher, pack('C*', ...hexToUint8(reverseHex($version, 2)))); //version
hash_update($hasher, $srcAddress->binaryData);
hash_update($hasher, $targetAddress->binaryData);
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($scopeKey))));
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($targetId))));
hash_update($hasher, chr(1));

$value = "test";
$length = strlen($value);
$hexLength = dechex($length);
$paddedHex = str_pad($hexLength, 4, "0", STR_PAD_LEFT);

hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($paddedHex))));
hash_update($hasher, $value);

$stateHash1 = strtoupper(bin2hex(hash_final($hasher, true)));
// echo "State Hash 1: " . $stateHash . PHP_EOL;

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
$blockInfo = $blockApiInstance->searchBlocks(order: 'desc');
$rootHash1 = $blockInfo['data'][0]['meta']['state_hash_sub_cache_merkle_roots'][8];

//サービス提供者を含む任意のノードからマークル情報を取得
$metadataApiInstance = new MetadataRoutesApi($client, $config);
$stateProof1 = $metadataApiInstance->getMetadataMerkle(bin2hex($compositeHash));

//検証

// checkState($stateProof1, $stateHash1, $pathHash1, $rootHash1);

/**
 * アカウントへ登録したメタデータの検証
 */
$srcAddress = new Address('TDNH6IMNTNWAYRM7MXBFNGNGZRCFOQY5MSPTZUI');
$targetAddress = new Address('TDNH6IMNTNWAYRM7MXBFNGNGZRCFOQY5MSPTZUI');

//compositePathHash(Key値)
$scopeKey = Metadata::metadataGenerateKey('key_account'); //メタデータキー
$scopeKey = strtoupper(dechex($scopeKey));
$targetId = '0000000000000000';

$hasher = hash_init('sha3-256');
hash_update($hasher, $srcAddress->binaryData);
hash_update($hasher, $targetAddress->binaryData);
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($scopeKey))));
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($targetId))));
hash_update($hasher, chr(0)); // account

$compositeHash = hash_final($hasher, true);

$hasher = hash_init('sha3-256');
hash_update($hasher, $compositeHash);
$pathHash2 = strtoupper(bin2hex(hash_final($hasher, true)));

// echo "Path Hash 1: " . $pathHash1 . PHP_EOL;

//stateHash(Value値)
$hasher = hash_init('sha3-256');
$version = 1;
hash_update($hasher, pack('C*', ...hexToUint8(reverseHex($version, 2)))); //version
hash_update($hasher, $srcAddress->binaryData);
hash_update($hasher, $targetAddress->binaryData);
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($scopeKey))));
hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($targetId))));
hash_update($hasher, chr(0)); // account

$value = "test";
$length = strlen($value);
$hexLength = dechex($length);
$paddedHex = str_pad($hexLength, 4, "0", STR_PAD_LEFT);

hash_update($hasher, pack('C*', ...array_reverse(hexToUint8($paddedHex))));
hash_update($hasher, $value);

$stateHash2 = strtoupper(bin2hex(hash_final($hasher, true)));

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
$blockInfo = $blockApiInstance->searchBlocks(order: 'desc');
$rootHash2 = $blockInfo['data'][0]['meta']['state_hash_sub_cache_merkle_roots'][8];

//サービス提供者を含む任意のノードからマークル情報を取得
$metadataApiInstance = new MetadataRoutesApi($client, $config);
$stateProof2 = $metadataApiInstance->getMetadataMerkle(bin2hex($compositeHash));

//検証

checkState($stateProof2, $stateHash2, $pathHash2, $rootHash2);