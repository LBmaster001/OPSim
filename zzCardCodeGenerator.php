<?php

  include './zzImageConverter.php';
  include './Libraries/Trie.php';
  
  $titleTrie = [];
  $subtitleTrie = [];
  $costTrie = [];
  $hpTrie = [];
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
  
  $data = json_decode(file_get_contents("data.json"));

    for ($i = 0; $i < count($data); ++$i)
    {
      $card = $data[$i];

      $cardID = $card->id;
      AddToTries($cardID, $card->id);
    }
  $legalIn = "EN"; // EN or JP

  if (!is_dir("./GeneratedCode")) mkdir("./GeneratedCode", 777, true);

  $generateFilename = "./GeneratedCode/GeneratedCardDictionaries.php";
  $handler = fopen($generateFilename, "w");

  fwrite($handler, "<?php\r\n");

  GenerateFunction($titleTrie, $handler, "CardTitle", true, "");
  GenerateFunction($subtitleTrie, $handler, "CardSubtitle", true, "");
  GenerateFunction($costTrie, $handler, "CardCost", false, -1);
  GenerateFunction($hpTrie, $handler, "CardHP", false, -1);
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

  GenerateFunction($uuidLookupTrie, $handler, "UUIDLookup", true, "");

  fwrite($handler, "?>");

  fclose($handler);

  function GenerateFunction($cardArray, $handler, $functionName, $isString, $defaultValue, $dataType = 0)
  {
    fwrite($handler, "function " . $functionName . "(\$cardID) {\r\n");
    TraverseTrie($cardArray, "", $handler, $isString, $defaultValue, $dataType);
    fwrite($handler, "}\r\n\r\n");
  }

  function AddToTries($cardID, $uuid)
  {
    global $uuidLookupTrie, $titleTrie, $subtitleTrie, $costTrie, $hpTrie, $powerTrie, $typeTrie, $counterTrie, $leaderLifeTrie, $colorTrie, $searchTypeTrie, $langTrie, $card;
    global $aspectsTrie, $arenasTrie;
    AddToTrie($uuidLookupTrie, $cardID, 0, $uuid);
    AddToTrie($titleTrie, $uuid, 0, str_replace('"', "'", $card->name));
    AddToTrie($subtitleTrie, $uuid, 0, str_replace('"', "'", $card->effect));
    AddToTrie($costTrie, $uuid, 0, $card->cost);
    AddToTrie($hpTrie, $uuid, 0, $card->power);
    AddToTrie($powerTrie, $uuid, 0, $card->power);
    AddToTrie($typeTrie, $uuid, 0, $card->category);
    AddToTrie($leaderLifeTrie, $uuid, 0, $card->life);
    AddToTrie($counterTrie, $uuid, 0, $card->counter);
    AddToTrie($langTrie, $uuid, 0, $card->lang);
    
    $searchType = "";
    for($j = 0; $j < count($card->type); ++$j)
    {
      if($searchType != "") $searchType .= ",";
      $searchType .= $card->type[$j];
    }
    AddToTrie($searchTypeTrie, $uuid, 0, $searchType);

    $colors = "";
    for($j = 0; $j < count($card->color); ++$j)
    {
      if($colors != "") $colors .= ",";
      $colors .= $card->color[$j];
    }
    AddToTrie($colorTrie, $uuid, 0, $colors);

  }

?>
