<?php

set_error_handler(function() { /* ignore errors */ });

$apple = apple();
$tesla = tesla();
$bitcoin = bitcoin();
$bitcoin_cash = bitcoin_cash();

$total = total($apple, $tesla, $bitcoin, $bitcoin_cash);
$total_2 = total_2($total);

$watchlist = array(
  $apple,
  $tesla,
  $bitcoin,
  $bitcoin_cash,
  $total,
  $total_2
);

print json_encode($watchlist);

function apple() {
  $einzelpreis = 129;
  $stueckzahl = 3;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 131.0833;

  $html = file_get_contents("http://www.finanzen.net/aktien/Apple-Aktie");
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
    'name' => 'Apple Aktie',
    'isin' => 'US0378331005',
    'handelsplatz' => 'Xetra',
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

function tesla() {
  $einzelpreis = 289;
  $stueckzahl = 6;
  $transaktionsgebuehr = 6.25;
  $einstandskurs = 290.0417;

  $html = file_get_contents("http://www.finanzen.net/aktien/Tesla-Aktie");
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
    'name' => 'Tesla Aktie',
    'isin' => 'US88160R1014',
    'handelsplatz' => 'Xetra',
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

function bitcoin() {
  $einzelpreis = 3445.46;
  $stueckzahl = 0.01451185;
  $transaktionsgebuehr = 1.00;
  $einstandskurs = 3445.46;

  $html = file_get_contents("https://www.kraken.com/charts");
  if($html !== false) {
    $pos = strpos($html, "data-val") + 10;
    $pos_2 = strpos($html, " ", $pos) - 1;
    $val = substr($html, $pos, $pos_2 - $pos);

    $aktueller_kurs = (float)$val;

    date_default_timezone_set("Europe/Berlin");
    $zeit = date("H:i:s", time());
  } else {
    $html = file_get_contents("http://www.finanzen.net/devisen/bitcoin-euro-kurs");
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
  }

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
    'name' => 'Bitcoin',
    'isin' => 'BTC',
    'handelsplatz' => 'Kraken',
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

function bitcoin_cash() {
  $einzelpreis = 1141.21;
  $stueckzahl = 0.044444;
  $transaktionsgebuehr = 1.01;
  $einstandskurs = 1141.2114;

  $html = file_get_contents("http://www.finanzen.net/devisen/bitcoin-cash-euro-kurs");
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
    'name' => 'Bitcoin Cash',
    'isin' => 'BCC',
    'handelsplatz' => 'Kraken',
    'einzelpreis' => $einzelpreis,
    'stueckzahl' => $stueckzahl,
    'preis' => $preis,
    'zeit' => $zeit,
    'transaktionsgebuehr' => $transaktionsgebuehr,
    'aktueller_kurs' => $aktueller_kurs,
    'gewinn_verlust' => $gewinn_verlust,
    'einstandskurs' => $einstandskurs,
    'einstandswert' => $einstandswert,
    'guv_nach_gebuehr' => $guv_nach_gebuehr,
    'guv_prozent' => $guv_prozent
  );
}

function total($apple, $tesla, $bitcoin, $bitcoin_cash) {
  $preis = $apple['preis'] + $tesla['preis'] + $bitcoin['preis'] + $bitcoin_cash['preis'];
  $transaktionsgebuehr = $apple['transaktionsgebuehr'] + $tesla['transaktionsgebuehr'] + $bitcoin['transaktionsgebuehr'] + $bitcoin_cash['transaktionsgebuehr'];
  $gewinn_verlust = $apple['gewinn_verlust'] + $tesla['gewinn_verlust'] + $bitcoin['gewinn_verlust'] + $bitcoin_cash['gewinn_verlust'];
  $einstandswert = $apple['einstandswert'] + $tesla['einstandswert'] + $bitcoin['einstandswert'] + $bitcoin_cash['einstandswert'];
  $guv_nach_gebuehr = $apple['guv_nach_gebuehr'] + $tesla['guv_nach_gebuehr'] + $bitcoin['guv_nach_gebuehr'] + $bitcoin_cash['guv_nach_gebuehr'];
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

  $preis = $apple['preis'] + $tesla['preis'] + $bitcoin['preis'] + $bitcoin_cash['preis'];
  $transaktionsgebuehr = $apple['transaktionsgebuehr'] + $tesla['transaktionsgebuehr'] + $bitcoin['transaktionsgebuehr'] + $bitcoin_cash['transaktionsgebuehr'];
  $einstandswert = $apple['einstandswert'] + $tesla['einstandswert'] + $bitcoin['einstandswert'] + $bitcoin_cash['einstandswert'];
  $guv_nach_gebuehr = $apple['guv_nach_gebuehr'] + $tesla['guv_nach_gebuehr'] + $bitcoin['guv_nach_gebuehr'] + $bitcoin_cash['guv_nach_gebuehr'];
  $guv_prozent = 100 / ($preis + $transaktionsgebuehr) * $guv_nach_gebuehr / 100;

  $gewinn_verlust = $total['gewinn_verlust'] + 24.13;

  return array(
    'name' => 'Gesamt',
    'gewinn_verlust' => $gewinn_verlust
  );
}

?>
