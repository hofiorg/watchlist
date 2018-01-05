
read_watchlist();

function read_watchlist() {
  $.getJSON("watchlist.php", function(json) {
    jQuery.each(json, function(index, val) {
      jQuery.each(val, function(name, v) {
        let orig_v = v;
        let field = $("#" + name + "_" + index);
        if(name === 'gewinn_verlust' || name === 'guv_nach_gebuehr') {
          if(v > 0)
            field.addClass("green");
          if(v < 0)
            field.addClass("red");
        }

        if(name === 'guv_prozent') {
          if(v > 0)
            field.addClass("green_bg");
          if(v < 0)
            field.addClass("red_bg");
        }

        if(name === 'einzelpreis' ||
           name === 'preis' ||
           name === 'transaktionsgebuehr' ||
           name === 'aktueller_kurs'  ||
           name === 'gewinn_verlust' ||
           name === 'einstandswert' ||
           name === 'guv_nach_gebuehr'
         )
          v = v.toLocaleString('de-DE', { style: 'currency', currency: 'EUR' });
        else if(name === 'einstandskurs')
          v = v.toLocaleString('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 4 });
        else if(name === 'stueckzahl')
          v = v.toLocaleString('de-DE', { minimumFractionDigits: 8 });
        else if(name === 'guv_prozent')
          v = v.toLocaleString('de-DE', { style: 'percent', minimumFractionDigits: 2 });
        else
          v = v.toLocaleString('de-DE', { minimumFractionDigits: 2 });

        if(name === 'aktueller_kurs') {
          let letzter_kurs = field.html();
          letzter_kurs = letzter_kurs.substr(0, letzter_kurs.indexOf('&'));
          let neuer_kurs = orig_v.toLocaleString('de-DE', { minimumFractionDigits: 2 });
          if(letzter_kurs !== neuer_kurs) {
            let letzter_kurs_float = parseFloat(letzter_kurs.replace(/[^0-9-]/g, ''));
            let neuer_kurs_float = parseFloat(neuer_kurs.replace(/[^0-9-]/g, ''));
            if(!isNaN(letzter_kurs_float)) {
              if(neuer_kurs_float >= letzter_kurs_float)
                field.addClass("blink_green");
              else
                field.addClass("blink_red");
              setTimeout(remove_blink, 500);
            }
          }
        }

        field.html(v);
      });
    });

    $(".btcwdgt-footer").remove();

    setTimeout(read_watchlist, 30000);
  });

  function remove_blink() {
    remove_class($("#aktueller_kurs_0"));
    remove_class($("#aktueller_kurs_1"));
    remove_class($("#aktueller_kurs_2"));
    remove_class($("#aktueller_kurs_3"));
    remove_class($("#aktueller_kurs_4"));
  }

  function remove_class(selector) {
    selector.removeClass("blink_green");
    selector.removeClass("blink_red");
  }
}
