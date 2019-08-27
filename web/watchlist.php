<?php

set_error_handler(function() { /* ignore errors */ });

$apple = apple();
$tesla = tesla();
$bitcoin = bitcoin();
$bitcoin_cash = bitcoin_cash();
$raiblocks = raiblocks($bitcoin['aktueller_kurs']);

$total = total($apple, $tesla, $bitcoin, $bitcoin_cash, $raiblocks);
$total_2 = total_2($total);

$watchlist = array(
  $apple,
  $tesla,
  $bitcoin,
  $bitcoin_cash,
  $raiblocks,
  $total,
  $total_2
);

print json_encode($watchlist);

function apple() {
  $name = 'Apple Aktie';
  $isin = 'US0378331005';
  $handelsplatz = 'Xetra';
  $einzelpreis = 129;
  $stueckzahl = 3;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 131.0833;

  $parse_result = parse_finanzen_net("http://www.finanzen.net/aktien/Apple-Aktie");

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function tesla() {
  $name = 'Tesla Aktie';
  $isin = 'US88160R1014';
  $handelsplatz = 'Xetra';
  $einzelpreis = 289;
  $stueckzahl = 6;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 290.0417;

  $parse_result = parse_finanzen_net("http://www.finanzen.net/aktien/Tesla-Aktie");

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function bitcoin() {
  $name = 'Bitcoin';
  $isin = 'BTC';
  $handelsplatz = 'Kraken';
  $einzelpreis = 3445.46;
  $stueckzahl = 0.01451185;
  $transaktionsgebuehr = 1.00;
  $einstandskurs = 3445.46;

  $parse_result = parse_kraken("https://www.kraken.com/charts");
  if("".$parse_result['aktueller_kurs'] === "0") {
    $handelsplatz = 'finanzen.net';
    $parse_result = parse_finanzen_net_2("http://www.finanzen.net/devisen/bitcoin-euro-kurs");
  }

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function bitcoin_cash() {
  $name = 'Bitcoin Cash';
  $isin = 'BCC';
  $handelsplatz = 'finanzen.net';
  $einzelpreis = 1141.21;
  $stueckzahl = 0.044444;
  $transaktionsgebuehr = 1.01;
  $einstandskurs = 1141.2114;

  $parse_result = parse_finanzen_net_2("http://www.finanzen.net/devisen/bitcoin-cash-euro-kurs");

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function raiblocks($bitcoin_kurs) {
  $name = 'Nano';
  $isin = 'XRB';
  $handelsplatz = 'cmcap';
  $einzelpreis = 27.17;
  $stueckzahl = 1.840729;
  $transaktionsgebuehr = 1.00;
  $einstandskurs = 27.1631;

  $parse_result = parse_coinmarketcap("https://coinmarketcap.com/currencies/nano/");

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'] * $bitcoin_kurs, $parse_result['zeit']);
}

function parse_finanzen_net($url) {
  $html = file_get_contents($url);
  if($html !== false) {
    $pos = strpos($html, "col-xs-5 col-sm-4 text-sm-right text-nowrap") + 45;
    $pos_2 = strpos($html, "span", $pos) - 1;
    $val = substr($html, $pos, $pos_2 - $pos);
    $aktueller_kurs = floatval(str_replace(',', '.', $val));

    $pos = strpos($html, "quotebox-time") + 15;
    $pos_2 = strpos($html, "div", $pos) - 2;
    $zeit = substr($html, $pos, $pos_2 - $pos);
  } else {
    $aktueller_kurs = '';
    $zeit = '';
  }
  return array(
    'aktueller_kurs' => $aktueller_kurs,
    'zeit' => $zeit);
}

function parse_finanzen_net_2($url) {
  $html = file_get_contents($url);
  if($html !== false) {
    $pos = strpos($html, "col-xs-5 col-sm-4 text-sm-right text-nowrap") + 56;
    $pos_2 = strpos($html, "span", $pos) - 1;
    $val = substr($html, $pos, $pos_2 - $pos);
    $val = str_replace('.', '', $val);
    $val = str_replace(',', '.', $val);
    $aktueller_kurs = (float)$val;

    $pos = strpos($html, "quotebox-time") + 23;
    $zeit = substr($html, $pos, 5).":00";
  } else {
    $aktueller_kurs = '';
    $zeit = '';
  }
  return array(
    'aktueller_kurs' => $aktueller_kurs,
    'zeit' => $zeit);
}

function parse_kraken($url) {
  $html = file_get_contents($url);
  if($html === false) {
    return null;
  }
  $pos = strpos($html, "data-val") + 10;
  $pos_2 = strpos($html, " ", $pos) - 1;
  $val = substr($html, $pos, $pos_2 - $pos);
  $aktueller_kurs = (float)$val;
  date_default_timezone_set("Europe/Berlin");
  $zeit = date("H:i:s", time());

  return array(
    'aktueller_kurs' => $aktueller_kurs,
    'zeit' => $zeit);
}

function parse_coinmarketcap($url) {
  $html = file_get_contents($url);
  if($html !== false) {
    $pos_0 = strpos($html, "data-format-price-crypto data-format-value");
    $pos = strpos($html, ">", $pos_0) + 1;
    $pos_2 = strpos($html, "<", $pos) - 1;
    $val = substr($html, $pos, $pos_2 - $pos);

    $aktueller_kurs = floatval(str_replace(',', '.', $val));

    date_default_timezone_set("Europe/Berlin");
    $zeit = date("H:i:s", time());
  } else {
    $aktueller_kurs = '';
    $zeit = '';
  }
  return array(
    'aktueller_kurs' => $aktueller_kurs,
    'zeit' => $zeit);
}

function line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $aktueller_kurs, $zeit) {
  $preis = $einzelpreis * $stueckzahl;
  $gewinn_verlust = ($aktueller_kurs * $stueckzahl) - $preis;
  $einstandswert = $preis + $transaktionsgebuehr;
  $guv_nach_gebuehr = ($aktueller_kurs * $stueckzahl) - $einstandswert;
  $guv_prozent = 100 / ($preis + $transaktionsgebuehr) * $guv_nach_gebuehr / 100;

  if($aktueller_kurs === '') {
    $gewinn_verlust = '';
    $guv_nach_gebuehr = '';
    $guv_prozent = '';
  }

  return array(
    'name' => $name,
    'isin' => $isin,
    'handelsplatz' => $handelsplatz,
    'einzelpreis' => $einzelpreis,
    'stueckzahl' => $stueckzahl,
    'preis' => $preis,
    'transaktionsgebuehr' => $transaktionsgebuehr,
    'aktueller_kurs' => $aktueller_kurs,
    'zeit' => $zeit,
    'gewinn_verlust' => $gewinn_verlust,
    'einstandskurs' => $einstandskurs,
    'einstandswert' => $einstandswert,
    'guv_nach_gebuehr' => $guv_nach_gebuehr,
    'guv_prozent' => $guv_prozent
  );
}

function total($apple, $tesla, $bitcoin, $bitcoin_cash, $raiblocks) {
  $preis = $apple['preis'] + $tesla['preis'] + $bitcoin['preis'] + $bitcoin_cash['preis'] + $raiblocks['preis'];
  $transaktionsgebuehr = $apple['transaktionsgebuehr'] + $tesla['transaktionsgebuehr'] + $bitcoin['transaktionsgebuehr'] + $bitcoin_cash['transaktionsgebuehr'] + $raiblocks['transaktionsgebuehr'];
  $gewinn_verlust = $apple['gewinn_verlust'] + $tesla['gewinn_verlust'] + $bitcoin['gewinn_verlust'] + $bitcoin_cash['gewinn_verlust'] + $raiblocks['gewinn_verlust'];
  $einstandswert = $apple['einstandswert'] + $tesla['einstandswert'] + $bitcoin['einstandswert'] + $bitcoin_cash['einstandswert'] + + $raiblocks['einstandswert'];
  $guv_nach_gebuehr = $apple['guv_nach_gebuehr'] + $tesla['guv_nach_gebuehr'] + $bitcoin['guv_nach_gebuehr'] + $bitcoin_cash['guv_nach_gebuehr'] + $raiblocks['guv_nach_gebuehr'];
  $guv_prozent = 100 / ($preis + $transaktionsgebuehr) * $guv_nach_gebuehr / 100;

  return array(
    'name' => '',
    'preis' => $preis,
    'transaktionsgebuehr' => $transaktionsgebuehr,
    'gewinn_verlust' => $gewinn_verlust,
    'einstandswert' => $einstandswert,
    'guv_nach_gebuehr' => $guv_nach_gebuehr,
    'guv_prozent' => $guv_prozent
  );
}

function total_2($total) {
  $gewinn_verlust = $total['gewinn_verlust']
    + 21.73 // Verkauf   Tesla 21.08.2017
    +  1.20 // Dividende Apple 17.08.2017
    +  1.20 // Dividende Apple 17.11.2017
    +  1.12 // Dividende Apple 16.02.2018
    +  1.38 // Dividende Apple 18.05.2018
    +  1.43 // Dividende Apple 16.08.2018
    +  1.43 // Dividende Apple 16.11.2018
    +  1.44 // Dividende Apple 14.02.2019
    +  1.53 // Dividende Apple 16.05.2019
    +  1.54 // Dividende Apple 20.08.2019
    ;

  return array(
    'name' => 'Gesamt',
    'gewinn_verlust' => $gewinn_verlust
  );
}
