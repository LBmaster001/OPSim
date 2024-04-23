<?php

include './zzImageConverter.php';
include './Libraries/Trie.php';

$titleTrie = [];
$subtitleTrie = [];
$costTrie = [];
$powerTrie = [];
$leaderLifeTrie = [];
$counterTrie = [];
$aspectsTrie = [];
$traitsTrie = [];
$arenasTrie = [];
$uuidLookupTrie = [];
$typeTrie = [];
$searchTypeTrie = [];
$colorTrie = [];
$langTrie = [];
$triggerTrie = [];

$dataZip = "./data.zip";
$zipFile = new ZipArchive;
$zipFile->open($dataZip);
echo "Files in Zip: " . $zipFile->numFiles . "<br>";
for ($i = 0; $i < $zipFile->numFiles; ++$i)
{
  $filename = $zipFile->getNameIndex($i);
  if (strpos($filename, ".json") === false) continue;
  $content = $zipFile->getFromName($filename);
  echo "Loading File: " . $filename . "<br>";
  LoadFile($content);
}
$zipFile->close();

function LoadFile(string $content) : void {
  global $titleTrie, $subtitleTrie, $costTrie, $powerTrie, $leaderLifeTrie, $counterTrie, $aspectsTrie, $traitsTrie, $arenasTrie, $uuidLookupTrie, $typeTrie, $searchTypeTrie, $colorTrie, $langTrie, $triggerTrie;
  $data = json_decode($content);
  echo "Data OK: " . ($data != null) . " Content Length: " . strlen($content) . "<br>";

  for ($i = 0; $i < count($data); ++$i)
  {
    $card = $data[$i];

    $cardID = $card->id;
    if ($cardID !== $card->number) {
      echo "Skipping: " . $cardID . "<br>";
      continue;
    
    }
    echo "Processing Card: " . $cardID . "<br>";
    AddToTries($cardID, $card->id);
  }

  if (!is_dir("./GeneratedCode")) mkdir("./GeneratedCode", 777, true);

  $generateFilename = "./GeneratedCode/GeneratedCardDictionaries.php";
  $handler = fopen($generateFilename, "w");

  fwrite($handler, "<?php\r\n");

  GenerateFunction($titleTrie, $handler, "CardTitle", true, "");
  GenerateFunction($subtitleTrie, $handler, "CardSubtitle", true, "");
  GenerateFunction($costTrie, $handler, "CardCost", false, -1);
  GenerateFunction($powerTrie, $handler, "CardPower", false, -1);
  GenerateFunction($leaderLifeTrie, $handler, "CardLeaderLife", false, -1);
  GenerateFunction($counterTrie, $handler, "CardCounter", false, -1);
  GenerateFunction($aspectsTrie, $handler, "CardAspects", true, "");
  GenerateFunction($traitsTrie, $handler, "CardTraits", true, "");
  GenerateFunction($arenasTrie, $handler, "CardArenas", true, "");
  GenerateFunction($typeTrie, $handler, "DefinedCardType", true, "");
  GenerateFunction($searchTypeTrie, $handler, "CardSearchType", true, "");
  GenerateFunction($colorTrie, $handler, "CardColor", true, "");
  GenerateFunction($langTrie, $handler, "CardLang", true, "");
  GenerateFunction($triggerTrie, $handler, "CardTrigger", true, "");

  GenerateFunction($uuidLookupTrie, $handler, "UUIDLookup", true, "");

  fwrite($handler, "?>");

  fclose($handler);
}

function GenerateFunction($cardArray, $handler, $functionName, $isString, $defaultValue, $dataType = 0)
{
  fwrite($handler, "function " . $functionName . "(\$cardID) {\r\n");
  TraverseTrie($cardArray, "", $handler, $isString, $defaultValue, $dataType);
  fwrite($handler, "}\r\n\r\n");
}

function AddToTries($cardID, $uuid)
{
  global $uuidLookupTrie, $titleTrie, $subtitleTrie, $costTrie, $hpTrie, $powerTrie, $typeTrie, $counterTrie, $leaderLifeTrie, $colorTrie, $searchTypeTrie, $langTrie, $triggerTrie, $card;
  global $aspectsTrie, $arenasTrie;
  AddToTrie($uuidLookupTrie, $cardID, 0, $uuid);
  AddToTrie($titleTrie, $uuid, 0, str_replace('"', "'", isset($card->name) ? $card->name : ""));
  AddToTrie($subtitleTrie, $uuid, 0, str_replace('"', "'", isset($card->effect) ? $card->effect : ""));
  AddToTrie($costTrie, $uuid, 0, isset($card->cost) ? $card->cost : -1);
  AddToTrie($powerTrie, $uuid, 0, isset($card->power) ? $card->power : -1);
  AddToTrie($typeTrie, $uuid, 0, isset($card->category) ? $card->category : "");
  AddToTrie($leaderLifeTrie, $uuid, 0, isset($card->life) ? $card->life : -1);
  AddToTrie($counterTrie, $uuid, 0, isset($card->counter) ? $card->counter : -1);
  AddToTrie($langTrie, $uuid, 0, isset($card->lang) ? $card->lang : "EN");
  AddToTrie($triggerTrie, $uuid, 0, isset($card->trigger) ? $card->trigger : "");
  
  $searchType = "";
  if(isset($card->type)) {
    for($j = 0; $j < count($card->type); ++$j)
    {
      if($searchType != "") $searchType .= ",";
      $searchType .= $card->type[$j];
    }
  }
  AddToTrie($searchTypeTrie, $uuid, 0, $searchType);

  $colors = "";
  if (isset($card->color)) {
    for($j = 0; $j < count($card->color); ++$j)
    {
      if($colors != "") $colors .= ",";
      $colors .= $card->color[$j];
    }
  }
  AddToTrie($colorTrie, $uuid, 0, $colors);

}

?>
