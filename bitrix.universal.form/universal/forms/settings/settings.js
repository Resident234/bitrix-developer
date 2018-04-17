var Base64 = {


    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",


    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },


    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

};


function NextypeGeolocationDeleteRow(el) {
    $(el).parents('tr.row').remove();
}

function NextypeFormsNewItem(obj) {
    obj = (obj) || null;
    var types = '<option value="text">&#1058;&#1077;&#1082;&#1089;&#1090;&#1086;&#1074;&#1086;&#1077; &#1087;&#1086;&#1083;&#1077;</option>';
        types += '<option value="select">&#1042;&#1099;&#1087;&#1072;&#1076;&#1072;&#1102;&#1097;&#1080;&#1081; &#1089;&#1087;&#1080;&#1089;&#1086;&#1082;</option>';
        types += '<option value="textarea">&#1052;&#1085;&#1086;&#1075;&#1086;&#1089;&#1090;&#1088;&#1086;&#1095;&#1085;&#1086;&#1077; &#1087;&#1086;&#1083;&#1077; &#1074;&#1074;&#1086;&#1076;&#1072;</option>';
        types += '<option value="hidden">&#1057;&#1082;&#1088;&#1099;&#1090;&#1086;&#1077; &#1087;&#1086;&#1083;&#1077;</option>';
        types += '<option value="email">Email</option>';
        types += '<option value="file">&#1060;&#1072;&#1081;&#1083;</option>';
    
    
    var html  = '<table class="NextypeFormsField">';
        html += '<tr>';
        html += '<td width="33%"><label>&#1047;&#1072;&#1075;&#1086;&#1083;&#1086;&#1074;&#1086;&#1082; *</label><input data-name="label" type="text" /></td>';
        html += '<td width="33%"><label>&#1058;&#1080;&#1087; *</label><select data-name="type">'+types+'</select></td>';
        html += '<td width="33%"><label><input value="Y" type="checkbox" data-name="required" /> &#1054;&#1073;&#1103;&#1079;&#1072;&#1090;&#1077;&#1083;&#1100;&#1085;&#1086;&#1077;</label>';
        html += '<button type="button" class="remove">&#215;</button></td>';
        html += '</tr><tr>';
        html += '<td><label>&#1052;&#1072;&#1089;&#1082;&#1072; &#1074;&#1074;&#1086;&#1076;&#1072;</label><input placeholder="+7 (###) ###-##-##" data-name="mask" type="text" /></td>';
        html += '<td><label>&#1047;&#1085;&#1072;&#1095;&#1077;&#1085;&#1080;&#1077; &#1087;&#1086; &#1091;&#1084;&#1086;&#1083;&#1095;&#1072;&#1085;&#1080;&#1102;</label><input type="text" data-name="default" /></td>';
        html += '<td><label>&#1057;&#1080;&#1084;&#1074;&#1086;&#1083;&#1100;&#1085;&#1099;&#1081; &#1082;&#1086;&#1076;</label><input pattern="[A-Z0-9_]" data-name="name" type="text" /></td>';
        html += '</tr><tr class="values" style="display:none">';
        html += '<td colspan="3"><label>&#1044;&#1086;&#1089;&#1090;&#1091;&#1087;&#1085;&#1099;&#1077; &#1079;&#1085;&#1072;&#1095;&#1077;&#1085;&#1080;&#1103; &#1074;&#1099;&#1087;&#1072;&#1076;&#1072;&#1102;&#1097;&#1077;&#1075;&#1086; &#1089;&#1087;&#1080;&#1089;&#1082;&#1072; *</label><textarea data-name="values"></textarea></td>';
        html += '</tr><tr>';
        html += '<td><label>Класс обертка</label><input pattern="[A-Z0-9_]" data-name="class-overlay" type="text" /></td>';
        html += '<td><label>Класс</label><input pattern="[A-Z0-9_]" data-name="class" type="text" /></td>';
        html += '<td><label>Плейсхолдер</label><input data-name="placeholder" type="text" /></td>';
        html += '</tr></table>';
    
    html = $(html);
    
    if (obj) {
        $.each(obj, function (index, value) {
            var field = html.find('[data-name="'+index+'"]');
            if (field.attr('type') == "checkbox") {
                if (value == "Y")
                    field.attr('checked', 'checked');
                else
                    field.removeAttr('checked');
            }
            else if (field.get(0).tagName == "select") {
                field.val(value);
                field.find('option').removeAttr('selected');
                field.find('option[value="'+value+'"]').attr('selected', 'selected');
            } else {
                field.val(value);
            }
        });
    }
    
    if (html.find('[data-name="type"]').val() == "select") {
        html.find('tr.values').show();
    } else {
        html.find('tr.values').hide();
    }
    
    html.find('[data-name="type"]').on('change', function () {
        if ($(this).val() == "select") {
            $(this).parents('table').find('tr.values').show();
        } else {
            $(this).parents('table').find('tr.values').hide();
        }
    });

    return html;
}

function NextypeFormsChangeField() {
    var arData = [];
    var index = 0;
    $("table.NextypeFormsField").each(function () {
        arData[index] = {};
        $(this).find('input, textarea, select').each(function () {
            if ($(this).data('name') != "") {
                var value = $(this).val();
                
                if ($(this).attr('type') == "checkbox" && !$(this).is(":checked"))
                    value = "";
                
                arData[index][$(this).data('name')] = value;
            }
        });
        
        index++;
    });
    
    $("#NextypeFormsFields input[name='FIELDS']").val(Base64.encode(JSON.stringify(arData)));
    
}

function OnNextypeFormsEditStart(arParams) {

  this.arParams = arParams;
   var wrap = $(this.arParams.oCont).parents('.bxcompprop-prop-tr'); 
   var bxwrap = $(this.arParams.oCont).parents('.bxcompprop-wrap');
   
   // Hide aside menu
   bxwrap.find('.bxcompprop-left').hide();
   bxwrap.find('.bxcompprop-right').css({
       width: '95%',
       float: 'none',
       left: '0px'
   });
   
   wrap.find(">td:eq(0)").remove();
   wrap.find(">td:eq(0)").attr('colspan', '2').removeClass('bxcompprop-cont-table-r').attr('id', 'NextypeFormsFields');
   var tdWrap = wrap.find(">td:eq(0)");
   var inputVal = $("#NextypeFormsFields input[name='FIELDS']").val();


   
   if (inputVal !== undefined && inputVal != "")
      var json = JSON.parse(Base64.decode(inputVal));



   tdWrap.append('<div></div>');
   tdWrap.append('<input type="button" id="NextypeFormsFieldsAdd" value="+ &#1044;&#1086;&#1073;&#1072;&#1074;&#1080;&#1090;&#1100; &#1085;&#1086;&#1074;&#1086;&#1077; &#1087;&#1086;&#1083;&#1077;" />');
   
   var container = tdWrap.find(">div");
   
   if (typeof json != undefined) {
       $.each(json, function (index, value) {
           container.append(NextypeFormsNewItem(value));
       });
   } else {
       container.append(NextypeFormsNewItem());
   }
   
   //console.log(json);
   
   $("body").on('click', '#NextypeFormsFieldsAdd', function () {
       container.append(NextypeFormsNewItem());
       //console.log(JSON.parse(Base64.decode($("#NextypeFormsFields input[name='FIELDS']").val())));
      
   });
   
   $("body").on('keyup' ,'table.NextypeFormsField input, table.NextypeFormsField textarea', function () {
       NextypeFormsChangeField();
   });
   
   $("body").on('change' ,'table.NextypeFormsField input[type="checkbox"], table.NextypeFormsField select', function () {
       NextypeFormsChangeField();
   });
   
   $("body").on('click' ,'table.NextypeFormsField button.remove ', function () {
       $(this).parents('table.NextypeFormsField').remove();
       NextypeFormsChangeField();
   });
}

function OnNextypeFormsEdit(arParams) {
    
    if(!window.jQuery){
      var script = document.createElement('script');
      script.src = "//yastatic.net/jquery/2.2.3/jquery.min.js";
      document.body.appendChild(script);
      
      script.onload = function() {
        OnNextypeFormsEditStart.call(this, arParams);
      };

    }
    else
    {
      OnNextypeFormsEditStart.call(this, arParams);
    }
}
