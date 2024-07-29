<?php
use SymbolSdk\Merkle\MerkleHashBuilder;
require_once(__DIR__ . '/util.php');
use SymbolRestClient\Api\BlockRoutesApi;
use SymbolSdk\CryptoTypes\Signature;
use SymbolSdk\Symbol\Models\Hash256;
use SymbolSdk\Symbol\Models\TransactionFactory;
use SymbolRestClient\Configuration;

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

function validateTransactionInBlock($leaf, $HRoot, $merkleProof) {
  if (count($merkleProof) === 0) {
      // There is a single item in the tree, so HRoot' = leaf.
      return strtoupper($leaf) === strtoupper($HRoot);
  }

  $HRoot0 = array_reduce($merkleProof, function($proofHash, $pathItem) {
    $hasher = new MerkleHashBuilder();
    if ($pathItem['position'] === 'left') {
      $hasher->update(new Hash256(hex2bin($pathItem['hash'])));
      $hasher->update($proofHash);
    } else {
      $hasher->update($proofHash);
      $hasher->update(new Hash256(hex2bin($pathItem['hash'])));
    }
    return $hasher->final();
  }, $leaf);

  return strtoupper($HRoot) === strtoupper($HRoot0);
}

$leaf = new Hash256($merkleComponentHash);

// ノードから取得
$config = new Configuration();
$config->setHost($NODE_URL);
$client = new GuzzleHttp\Client();
$blockApiInstance = new BlockRoutesApi($client, $config);

$HRoot = $blockApiInstance->getBlockByHeight($height);
$HRootHash = new Hash256($HRoot["block"]["transactions_hash"]);

$merkleProof = $blockApiInstance->getMerkleTransaction($height, $leaf);

$result = validateTransactionInBlock($leaf, $HRootHash, $merkleProof["merkle_path"]);
echo "===InBlockの検証===" . PHP_EOL;
var_dump($result);



