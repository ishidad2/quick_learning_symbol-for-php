# 13.検証
ブロックチェーン上に記録されたさまざまな情報を検証します。
ブロックチェーンへのデータ記録は全ノードの合意を持って行われますが、
ブロックチェーンへの**データ参照**はノード単体からの情報取得であるため、
信用できないノードの情報を元にして新たな取引を行いたい場合は、ノードから取得したデータに対して検証を行う必要があります。


## 13.1 トランザクションの検証

トランザクションがブロックヘッダーに含まれていることを検証します。この検証が成功すれば、トランザクションがブロックチェーンの合意によって承認されたものとみなすことができます。

### 検証するペイロード

今回検証するトランザクションペイロードとそのトランザクションが記録されているとされるブロック高です。

```
payload =
  "2802000000000000A5151FD55D82351DD488DB5563DD328DA72B2AD25B513C1D0F7F78AFF4D35BA094ABF505C74E6D6BE1FA19F3E5AC60A85E1A4EDC4AC07DECC0E56C59D5D24F0B69A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB0000000002984141A0D70000000000000EEAD6810500000062E78B6170628861B4FC4FCA75210352ACDBD2378AC0A447A3DCF63F969366BB1801000000000000540000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198544198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D04000000000000000074783100000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329D6668A0DE72812AAE05000500746573743100000000000000590000000000000069A31A837EB7DE323F08CA52495A57BA0A95B52D1BB54CEA9A94C12A87B1CADB000000000198444198A8D76FEF8382274D472EE377F2FF3393E5B62C08B4329DBF85DADBFD54C48D050005007465737432000000000000000000000000000000662CEDF69962B1E0F1BF0C43A510DFB12190128B90F7FE9BA48B1249E8E10DBEEDD3B8A0555B4237505E3E0822B74BCBED8AA3663022413AFDA265BE1C55431ACAE3EA975AF6FD61DEFFA6A16CBA5174A16EF5553AE669D5803A0FA9D1424600";
height = 686312;
```


### payload確認

トランザクションの内容を確認します。

```php
$tx = TransactionFactory::deserialize(hex2bin($payload));
$hash = $facade->hashTransaction($tx);
echo "\n===payload確認===" . PHP_EOL;
echo $hash . PHP_EOL;
print_r($tx);
```
###### 出力例
```
4A1C88BBFE6EB46111C2B02F7C7355DAE186E54132197C2CD6D51297846A1824
SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2 Object
(
    [transactionsHash] => SymbolSdk\Symbol\Models\Hash256 Object
        (
            [binaryData] => b�apb�a��O�u!R���7���G���?��f�
        )

    [transactions] => Array
        (
            [0] => SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1 Object
                (
                    [recipientAddress] => SymbolSdk\Symbol\Models\UnresolvedAddress Object
                        (
                            [binaryData] => ���o'MG.�w��3���2�
                        )

                    [mosaics] => Array
                        (
                        )

                    [message] => tx1
                    [transferTransactionBodyReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1:private] => 0
                    [transferTransactionBodyReserved_2:SymbolSdk\Symbol\Models\EmbeddedTransferTransactionV1:private] => 0
                    [signerPublicKey] => SymbolSdk\Symbol\Models\PublicKey Object
                        (
                            [binaryData] => i��~��2�RIZW�
��-LꚔ�*����
                        )

                    [version] => 1
                    [network] => SymbolSdk\Symbol\Models\NetworkType Object
                        (
                            [value] => 152
                        )

                    [type] => SymbolSdk\Symbol\Models\TransactionType Object
                        (
                            [value] => 16724
                        )

                    [embeddedTransactionHeaderReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                    [entityBodyReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                )

            [1] => SymbolSdk\Symbol\Models\EmbeddedAccountMetadataTransactionV1 Object
                (
                    [targetAddress] => SymbolSdk\Symbol\Models\UnresolvedAddress Object
                        (
                            [binaryData] => ���o'MG.�w��3���2�
                        )

                    [scopedMetadataKey] => -5896758431726933914
                    [valueSizeDelta] => 5
                    [value] => test1
                    [signerPublicKey] => SymbolSdk\Symbol\Models\PublicKey Object
                        (
                            [binaryData] => i��~��2�RIZW�
��-LꚔ�*����
                        )

                    [version] => 1
                    [network] => SymbolSdk\Symbol\Models\NetworkType Object
                        (
                            [value] => 152
                        )

                    [type] => SymbolSdk\Symbol\Models\TransactionType Object
                        (
                            [value] => 16708
                        )

                    [embeddedTransactionHeaderReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                    [entityBodyReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                )

            [2] => SymbolSdk\Symbol\Models\EmbeddedAccountMetadataTransactionV1 Object
                (
                    [targetAddress] => SymbolSdk\Symbol\Models\UnresolvedAddress Object
                        (
                            [binaryData] => ���o'MG.�w��3���2�
                        )

                    [scopedMetadataKey] => -8231360769634433601
                    [valueSizeDelta] => 5
                    [value] => test2
                    [signerPublicKey] => SymbolSdk\Symbol\Models\PublicKey Object
                        (
                            [binaryData] => i��~��2�RIZW�
��-LꚔ�*����
                        )

                    [version] => 1
                    [network] => SymbolSdk\Symbol\Models\NetworkType Object
                        (
                            [value] => 152
                        )

                    [type] => SymbolSdk\Symbol\Models\TransactionType Object
                        (
                            [value] => 16708
                        )

                    [embeddedTransactionHeaderReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                    [entityBodyReserved_1:SymbolSdk\Symbol\Models\EmbeddedTransaction:private] => 0
                )

        )

    [cosignatures] => Array
        (
            [0] => SymbolSdk\Symbol\Models\Cosignature Object
                (
                    [version] => 0
                    [signerPublicKey] => SymbolSdk\Symbol\Models\PublicKey Object
                        (
                            [binaryData] => f,���b���
�                                                    C�߱!��������I��
                        )

                    [signature] => SymbolSdk\Symbol\Models\Signature Object
                        (
                            [binaryData] => �Ӹ�U[B7P^"�K�튣f0"A:��e�UC���Z��a����l�Qt�n�U:�iՀ:��BF
                        )

                )

        )

    [aggregateTransactionHeaderReserved_1:SymbolSdk\Symbol\Models\AggregateCompleteTransactionV2:private] => 0
    [signature] => SymbolSdk\Symbol\Models\Signature Object
        (
            [binaryData] => ��]�5Ԉ�Uc�2��+*�[Q<x���[�����Nmk����`�^N�J�}���lY��O

        )

    [signerPublicKey] => SymbolSdk\Symbol\Models\PublicKey Object
        (
            [binaryData] => i��~��2�RIZW�
��-LꚔ�*����
        )

    [version] => 2
    [network] => SymbolSdk\Symbol\Models\NetworkType Object
        (
            [value] => 152
        )

    [type] => SymbolSdk\Symbol\Models\TransactionType Object
        (
            [value] => 16705
        )

    [fee] => SymbolSdk\Symbol\Models\Amount Object
        (
            [size] => 8
            [value] => 55200
        )

    [deadline] => SymbolSdk\Symbol\Models\Timestamp Object
        (
            [size] => 8
            [value] => 23653181966
        )

    [verifiableEntityHeaderReserved_1:SymbolSdk\Symbol\Models\Transaction:private] => 0
    [entityBodyReserved_1:SymbolSdk\Symbol\Models\Transaction:private] => 0
)
```

### 署名者の検証

トランザクションがブロックに含まれていることが確認できれば自明ですが、  
念のため、アカウントの公開鍵でトランザクションの署名を検証しておきます。

```php
$signature = new Signature($tx->signature);
$res = $facade->verifyTransaction($tx, $signature);
echo "\n===署名の検証===" . PHP_EOL;
var_dump($res);
console.log(res);
```
```
true
```

### マークルコンポーネントハッシュの計算

トランザクションのハッシュ値には連署者の情報が含まれていません。
一方でブロックヘッダーに格納されるマークルルートはトランザクションのハッシュに連署者の情報が含めたものが格納されます。
そのためトランザクションがブロック内部に存在しているかどうかを検証する場合は、トランザクションハッシュをマークルコンポーネントハッシュに変換しておく必要があります。

```php
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
echo strtoupper($merkleComponentHash) . PHP_EOL;
```

###### 出力例

```
C61D17F89F5DEBC74A98A1321DB71EB7DC9111CDF1CF3C07C0E9A91FFE305AC3
```

### InBlockの検証

ノードからマークルツリーを取得し、先ほど計算したmerkleComponentHashからブロックヘッダーのマークルルートが導出できることを確認します。

```php
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
```
```
bool(true)
```

トランザクションの情報がブロックヘッダーに含まれていることが確認できました。

## 13.2 ブロックヘッダーの検証

既知のブロックハッシュ値（例：ファイナライズブロック）から、検証中のブロックヘッダーまでたどれることを検証します。


### normalブロックの検証

```js
block = await blockRepo.getBlockByHeight(height).toPromise();
previousBlock = await blockRepo.getBlockByHeight(height - 1).toPromise();
if(block.type ===  sym.BlockType.NormalBlock){
    
  hasher = sha3_256.create();
  hasher.update(Buffer.from(block.signature,'hex')); //signature
  hasher.update(Buffer.from(block.signer.publicKey,'hex')); //publicKey
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.version, 1));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.networkType, 1));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.type, 2));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.height.lower    ,block.height.higher]));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.timestamp.lower ,block.timestamp.higher]));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.difficulty.lower,block.difficulty.higher]));
  hasher.update(Buffer.from(block.proofGamma,'hex'));
  hasher.update(Buffer.from(block.proofVerificationHash,'hex'));
  hasher.update(Buffer.from(block.proofScalar,'hex'));
  hasher.update(Buffer.from(previousBlock.hash,'hex'));
  hasher.update(Buffer.from(block.blockTransactionsHash,'hex'));
  hasher.update(Buffer.from(block.blockReceiptsHash,'hex'));
  hasher.update(Buffer.from(block.stateHash,'hex'));
  hasher.update(sym.RawAddress.stringToAddress(block.beneficiaryAddress.address));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.feeMultiplier, 4));
  hash = hasher.hex().toUpperCase();
  console.log(hash === block.hash);
}
```

true が出力されればこのブロックハッシュは前ブロックハッシュ値の存在を認知していることになります。  
同様にしてn番目のブロックがn-1番目のブロックを存在を確認し、最後に検証中のブロックにたどり着きます。  

これで、どのノードに問い合わせても確認可能な既知のファイナライズブロックが、  
検証したいブロックの存在に支えられていることが分かりました。  

### importanceブロックの検証

importanceBlockは、importance値の再計算が行われるブロック(720ブロック毎、テストネットは180ブロック毎)です。  
NormalBlockに加えて以下の情報が追加されています。  

- votingEligibleAccountsCount
- harvestingEligibleAccountsCount
- totalVotingBalance
- previousImportanceBlockHash

```js
block = await blockRepo.getBlockByHeight(height).toPromise();
previousBlock = await blockRepo.getBlockByHeight(height - 1).toPromise();
if(block.type ===  sym.BlockType.ImportanceBlock){

  hasher = sha3_256.create();
  hasher.update(Buffer.from(block.signature,'hex')); //signature
  hasher.update(Buffer.from(block.signer.publicKey,'hex')); //publicKey
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.version, 1));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.networkType, 1));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.type, 2));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.height.lower    ,block.height.higher]));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.timestamp.lower ,block.timestamp.higher]));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.difficulty.lower,block.difficulty.higher]));
  hasher.update(Buffer.from(block.proofGamma,'hex'));
  hasher.update(Buffer.from(block.proofVerificationHash,'hex'));
  hasher.update(Buffer.from(block.proofScalar,'hex'));
  hasher.update(Buffer.from(previousBlock.hash,'hex'));
  hasher.update(Buffer.from(block.blockTransactionsHash,'hex'));
  hasher.update(Buffer.from(block.blockReceiptsHash,'hex'));
  hasher.update(Buffer.from(block.stateHash,'hex'));
  hasher.update(sym.RawAddress.stringToAddress(block.beneficiaryAddress.address));
  hasher.update(cat.GeneratorUtils.uintToBuffer(   block.feeMultiplier, 4));
  hasher.update(cat.GeneratorUtils.uintToBuffer(block.votingEligibleAccountsCount,4));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.harvestingEligibleAccountsCount.lower,block.harvestingEligibleAccountsCount.higher]));
  hasher.update(cat.GeneratorUtils.uint64ToBuffer([block.totalVotingBalance.lower,block.totalVotingBalance.higher]));
  hasher.update(Buffer.from(block.previousImportanceBlockHash,'hex'));

  hash = hasher.hex().toUpperCase();
  console.log(hash === block.hash);
}
```

後述するアカウントやメタデータの検証のために、stateHashSubCacheMerkleRootsを検証しておきます。

### stateHashの検証
```js
console.log(block);
```
```js
> NormalBlockInfo
    height: UInt64 {lower: 59639, higher: 0}
    hash: "B5F765D388B5381AC93659F501D5C68C00A2EE7DF4548C988E97F809B279839B"
    stateHash: "9D6801C49FE0C31ADE5C1BB71019883378016FA35230B9813CA6BB98F7572758"
  > stateHashSubCacheMerkleRoots: Array(9)
        0: "4578D33DD0ED5B8563440DA88F627BBC95A174C183191C15EE1672C5033E0572"
        1: "2C76DAD84E4830021BE7D4CF661218973BA467741A1FC4663B54B5982053C606"
        2: "259FB9565C546BAD0833AD2B5249AA54FE3BC45C9A0C64101888AC123A156D04"
        3: "58D777F0AA670440D71FA859FB51F8981AF1164474840C71C1BEB4F7801F1B27"
        4: "C9092F0652273166991FA24E8B115ACCBBD39814B8820A94BFBBE3C433E01733"
        5: "4B53B8B0E5EE1EEAD6C1498CCC1D839044B3AE5F85DD8C522A4376C2C92D8324"
        6: "132324AF5536EC9AA85B2C1697F6B357F05EAFC130894B210946567E4D4E9519"
        7: "8374F46FBC759049F73667265394BD47642577F16E0076CBB7B0B9A92AAE0F8E"
        8: "45F6AC48E072992343254F440450EF4E840D8386102AD161B817E9791ABC6F7F"
```
```js
hasher = sha3_256.create();
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[0],'hex')); //AccountState
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[1],'hex')); //Namespace
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[2],'hex')); //Mosaic
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[3],'hex')); //Multisig
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[4],'hex')); //HashLockInfo
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[5],'hex')); //SecretLockInfo
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[6],'hex')); //AccountRestriction
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[7],'hex')); //MosaicRestriction
hasher.update(Buffer.from(block.stateHashSubCacheMerkleRoots[8],'hex')); //Metadata
hash = hasher.hex().toUpperCase();
console.log(block.stateHash === hash);
```
```js
> true
```

ブロックヘッダーの検証に利用した9個のstateがstateHashSubCacheMerkleRootsから構成されていることがわかります。


## 13.3 アカウント・メタデータの検証

マークルパトリシアツリーを利用して、トランザクションに紐づくアカウントやメタデータの存在を検証します。  
サービス提供者がマークルパトリシアツリーを提供すれば、利用者は自分の意志で選択したノードを使ってその真偽を検証することができます。

### 検証用共通関数

```js
//葉のハッシュ値取得関数
function getLeafHash(encodedPath, leafValue){
    const hasher = sha3_256.create();
    return hasher.update(sym.Convert.hexToUint8(encodedPath + leafValue)).hex().toUpperCase();
}

//枝のハッシュ値取得関数
function getBranchHash(encodedPath, links){
    const branchLinks = Array(16).fill(sym.Convert.uint8ToHex(new Uint8Array(32)));
    links.forEach((link) => {
        branchLinks[parseInt(`0x${link.bit}`, 16)] = link.link;
    });
    const hasher = sha3_256.create();
    const bHash = hasher.update(sym.Convert.hexToUint8(encodedPath + branchLinks.join(''))).hex().toUpperCase();
    return bHash;
}

//ワールドステートの検証
function checkState(stateProof,stateHash,pathHash,rootHash){

  const merkleLeaf = stateProof.merkleTree.leaf;
  const merkleBranches = stateProof.merkleTree.branches.reverse();
  const leafHash = getLeafHash(merkleLeaf.encodedPath,stateHash);

  let linkHash = leafHash; //最初のlinkHashはleafHash
  let bit="";
  for(let i = 0; i < merkleBranches.length; i++){
      const branch = merkleBranches[i];
      const branchLink = branch.links.find(x=>x.link === linkHash)
      linkHash = getBranchHash(branch.encodedPath,branch.links);
      bit = merkleBranches[i].path.slice(0,merkleBranches[i].nibbleCount) + branchLink.bit + bit ;
  }

  const treeRootHash = linkHash; //最後のlinkHashはrootHash
  let treePathHash = bit + merkleLeaf.path;

  if(treePathHash.length % 2 == 1){
    treePathHash = treePathHash.slice( 0, -1 );
  }
 
  //検証
  console.log(treeRootHash === rootHash);
  console.log(treePathHash === pathHash);
}
```


### 13.3.1 アカウント情報の検証

アカウント情報を葉として、
マークルツリー上の分岐する枝をアドレスでたどり、
ルートに到着できるかを確認します。

```js
stateProofService = new sym.StateProofService(repo);

aliceAddress = sym.Address.createFromRawAddress("TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ");

hasher = sha3_256.create();
alicePathHash = hasher.update(
  sym.RawAddress.stringToAddress(aliceAddress.plain())
).hex().toUpperCase();

hasher = sha3_256.create();
aliceInfo = await accountRepo.getAccountInfo(aliceAddress).toPromise();
aliceStateHash = hasher.update(aliceInfo.serialize()).hex().toUpperCase();

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
blockInfo = await blockRepo.search({order:"desc"}).toPromise();
rootHash = blockInfo.data[0].stateHashSubCacheMerkleRoots[0];

//サービス提供者を含む任意のノードからマークル情報を取得
stateProof = await stateProofService.accountById(aliceAddress).toPromise();

//検証
checkState(stateProof,aliceStateHash,alicePathHash,rootHash);
```


### 13.3.2 モザイクへ登録したメタデータの検証

モザイクに登録したメタデータValue値を葉として、
マークルツリー上の分岐する枝をメタデータキーで構成されるハッシュ値でたどり、
ルートに到着できるかを確認します。

```js
srcAddress = Buffer.from(
    sym.Address.createFromRawAddress("TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ").encoded(),
    'hex'
)

targetAddress = Buffer.from(
    sym.Address.createFromRawAddress("TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ").encoded(),
    'hex'
)

hasher = sha3_256.create();    
hasher.update(srcAddress);
hasher.update(targetAddress);
hasher.update(sym.Convert.hexToUint8Reverse("CF217E116AA422E2")); // scopeKey
hasher.update(sym.Convert.hexToUint8Reverse("1275B0B7511D9161")); // targetId
hasher.update(Uint8Array.from([sym.MetadataType.Mosaic])); // type: Mosaic 1
compositeHash = hasher.hex();

hasher = sha3_256.create();   
hasher.update( Buffer.from(compositeHash,'hex'));

pathHash = hasher.hex().toUpperCase();

//stateHash(Value値)
hasher = sha3_256.create(); 
hasher.update(cat.GeneratorUtils.uintToBuffer(1, 2)); //version
hasher.update(srcAddress);
hasher.update(targetAddress);
hasher.update(sym.Convert.hexToUint8Reverse("CF217E116AA422E2")); // scopeKey
hasher.update(sym.Convert.hexToUint8Reverse("1275B0B7511D9161")); // targetId
hasher.update(Uint8Array.from([sym.MetadataType.Mosaic])); //mosaic

value = Buffer.from("test");

hasher.update(cat.GeneratorUtils.uintToBuffer(value.length, 2)); 
hasher.update(value); 
stateHash = hasher.hex();

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
blockInfo = await blockRepo.search({order:"desc"}).toPromise();
rootHash = blockInfo.data[0].stateHashSubCacheMerkleRoots[8];

//サービス提供者を含む任意のノードからマークル情報を取得
stateProof = await stateProofService.metadataById(compositeHash).toPromise();

//検証
checkState(stateProof,stateHash,pathHash,rootHash);
```

### 13.3.3 アカウントへ登録したメタデータの検証

アカウントに登録したメタデータValue値を葉として、
マークルツリー上の分岐する枝をメタデータキーで構成されるハッシュ値でたどり、
ルートに到着できるかを確認します。

```js
srcAddress = Buffer.from(
    sym.Address.createFromRawAddress("TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ").encoded(),
    'hex'
)

targetAddress = Buffer.from(
    sym.Address.createFromRawAddress("TBIL6D6RURP45YQRWV6Q7YVWIIPLQGLZQFHWFEQ").encoded(),
    'hex'
)

//compositePathHash(Key値)
hasher = sha3_256.create();    
hasher.update(srcAddress);
hasher.update(targetAddress);
hasher.update(sym.Convert.hexToUint8Reverse("9772B71B058127D7")); // scopeKey
hasher.update(sym.Convert.hexToUint8Reverse("0000000000000000")); // targetId
hasher.update(Uint8Array.from([sym.MetadataType.Account])); // type: Account 0
compositeHash = hasher.hex();

hasher = sha3_256.create();   
hasher.update( Buffer.from(compositeHash,'hex'));

pathHash = hasher.hex().toUpperCase();

//stateHash(Value値)
hasher = sha3_256.create(); 
hasher.update(cat.GeneratorUtils.uintToBuffer(1, 2)); //version
hasher.update(srcAddress);
hasher.update(targetAddress);
hasher.update(sym.Convert.hexToUint8Reverse("9772B71B058127D7")); // scopeKey
hasher.update(sym.Convert.hexToUint8Reverse("0000000000000000")); // targetId
hasher.update(Uint8Array.from([sym.MetadataType.Account])); //account
value = Buffer.from("test");
hasher.update(cat.GeneratorUtils.uintToBuffer(value.length, 2)); 
hasher.update(value); 
stateHash = hasher.hex();

//サービス提供者以外のノードから最新のブロックヘッダー情報を取得
blockInfo = await blockRepo.search({order:"desc"}).toPromise();
rootHash = blockInfo.data[0].stateHashSubCacheMerkleRoots[8];

//サービス提供者を含む任意のノードからマークル情報を取得
stateProof = await stateProofService.metadataById(compositeHash).toPromise();

//検証
checkState(stateProof,stateHash,pathHash,rootHash);
```

## 13.4 現場で使えるヒント

### トラステッドウェブ

トラステッドウェブを簡単に説明すると、全てをプラットフォーマーに依存せず、かつ全てを検証せずに済むWebの実現です。

本章の検証で分かることは、ブロックチェーンが持つすべての情報はブロックヘッダーのハッシュ値によって検証可能ということです。
ブロックチェーンはみんなが認め合うブロックヘッダーの共有とそれを再現できるフルノードの存在で成り立っています。
しかし、ブロックチェーンを活用したいあらゆるシーンでこれらを検証するための環境を維持しておくことは非常に困難です。
最新のブロックヘッダーが複数の信頼できる機関から常時ブロードキャストされていれば、検証の手間を大きく省くことができます
このようなインフラが整えば、都会などの数千万人が密集する超過密地帯、あるいは基地局が十分に配置できない僻地や災害時の広域ネットワーク遮断時など
ブロックチェーンの能力を超えた場所においても信頼できる情報にアクセスできるようになります。
