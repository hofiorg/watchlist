<?php

set_error_handler(function() { /* ignore errors */ });

$apple = apple();
$tesla = tesla();
$bitcoin = bitcoin();

$total = total($apple, $tesla, $bitcoin);
$total_2 = total_2($total);

$watchlist = array(
  $apple,
  $tesla,
  $bitcoin,
  $total,
  $total_2
);

print json_encode($watchlist);

function apple() {
  $name = 'Apple Aktie';
  $isin = 'US0378331005';
  $handelsplatz = 'Xetra';
  $einzelpreis = 32.25;
  $stueckzahl = 12;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 32.770833;

  $parse_result = parse_finanzen_net("http://www.finanzen.net/aktien/Apple-Aktie");

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function tesla() {
  $name = 'Tesla Aktie';
  $isin = 'US88160R1014';
  $handelsplatz = 'Xetra';
  $einzelpreis = 57.8;
  $stueckzahl = 30;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 58.008333;

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

  $parse_result = null; // parse_kraken("https://www.kraken.com");
  if($parse_result === null || "".$parse_result['aktueller_kurs'] === "0") {
    $handelsplatz = 'finanzen.net';
    $parse_result = parse_finanzen_net_2("http://www.finanzen.net/devisen/bitcoin-euro-kurs");
  }

  return line($name, $isin, $handelsplatz, $einzelpreis, $stueckzahl, $transaktionsgebuehr, $einstandskurs, $parse_result['aktueller_kurs'], $parse_result['zeit']);
}

function parse_finanzen_net($url) {
  $html = file_get_contents($url);
  if($html !== false) {
    $pos = strpos($html, "col-xs-5 col-sm-4 text-sm-right text-nowrap") + 45;
    $pos_2 = strpos($html, "span", $pos) - 1;
    $val = substr($html, $pos, $pos_2 - $pos);
    $aktueller_kurs = floatval(str_replace(',', '.', str_replace('.', '', $val)));

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
  $pos = strpos($html, "asset-price black-color") + 3;
  $pos_2 = strpos($html, "<", $pos) - 1;
  $val = substr($html, $pos, $pos_2 - $pos);
  $aktueller_kurs = (float)$val;
  date_default_timezone_set("Europe/Berlin");
  $zeit = date("H:i:s", time());

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

function total($apple, $tesla, $bitcoin) {
  $preis = $apple['preis'] + $tesla['preis'] + $bitcoin['preis'];
  $transaktionsgebuehr = $apple['transaktionsgebuehr'] + $tesla['transaktionsgebuehr'] + $bitcoin['transaktionsgebuehr'];
  $gewinn_verlust = $apple['gewinn_verlust'] + $tesla['gewinn_verlust'] + $bitcoin['gewinn_verlust'];
  $einstandswert = $apple['einstandswert'] + $tesla['einstandswert'] + $bitcoin['einstandswert'];
  $guv_nach_gebuehr = $apple['guv_nach_gebuehr'] + $tesla['guv_nach_gebuehr'] + $bitcoin['guv_nach_gebuehr'];
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
    +  1.56 // Dividende Apple 14.11.2019
    +  1.58 // Dividende Apple 20.02.2020
    +  1.68 // Dividende Apple 23.05.2020
    +  1.54 // Dividende Apple 20.08.2020
    +  1.54 // Dividende Apple 18.11.2020
    ;

  return array(
    'name' => 'Gesamt',
    'gewinn_verlust' => $gewinn_verlust
  );
}
