const locale = {
    "separator": " al ",
    "applyLabel": "Seleccionar",
    "cancelLabel": "Cancelar",
    "fromLabel": "Desde", 
    "toLabel": "Hasta",
    "customRangeLabel": "Rango",
    "daysOfWeek": [
        "Dom",
        "Lun",
        "Mar",
        "Mie",
        "Jue",
        "Vie",
        "Sab"
    ],
    "monthNames": [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ],
    "firstDay": 1
};

   
function evaluarancho(idtabla){
  var	height =$('#'+idtabla).height();
  var tabla=  $('#'+idtabla).DataTable();  
  $('#'+idtabla).height('175px');

      if (tabla.data().count()==0) {
          $('#'+idtabla).height('36px');
      }else if ( height <70 && tabla.data().count()>0) {
          $('#'+idtabla).height('175px');
      }
}
const deleteAllCookies = () => {
  localStorage.clear();
  const cookies = document.cookie.split(";");
  for (const cookie of cookies) {
    const eqPos = cookie.indexOf("=");
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  }
}

function getFechaActual(){	
	let f=new Date();
	let fecha = f.getFullYear()+'-'+(f.getMonth()+1)+'-'+f.getDate()+' '+f.getHours()+":"+(f.getMinutes()<10?'0':'') + f.getMinutes() +":"+f.getSeconds();		
	return fecha;
} 

function validar_correo(correo){
    var patron=/^\w+([\.-]?\w+)@\w+([\.-]?\w+)(\.\w{2,6})+$/;
    if(correo.search(patron)==0){
    //Mail correcto
        return true;
    }else{
        //Mail incorrecto
        $.when(swal('Error!','Dirección de correo inválida','error')).done(function(){return false;});
    }
}
function getParamsUrl(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}


function mandatorio(div){
  var res = 1;
  $("#"+div).find(".mandatorio").each(function(){
      if(res == 1){
            if( $(this).attr('type') =='text' || $(this).attr('type') =='email' || $(this).attr('type') =='number' || $(this).attr('type') =='textarea'){					
                if($(this).val() == '' ){						
                    var campo = $(this).prev().text().substr(2);
                    var name = $(this).attr('name');
                    if(campo != ''){
                        swal('Notificación','El campo '+campo+' es obligatorio','warning');
                        res = 0;
                    }else if(name != ''){
                        swal('Notificación','El campo '+name+' es obligatorio','warning');
                        res = 0;
                    }else{
                        swal('Notificación','El campo '+name+' es obligatorio','warning');
                        res = 0;
                    }
                }else if($(this).attr('type') =='email'){
                    var valor = $(this).val();
                    if(!validar_correo(valor)){
                        res = 0;
                    }
                }
            }else if( $(this).attr('type') =="radio" ){				                
                var campo = $(this).parent().parent().parent().parent().prev().text().substr(2);
                if(campo !=''){
                    var nombreelemento = $(this).attr('name');
                    var chk_elemento = $('input:radio[name='+nombreelemento+']:checked').val();
                    if (chk_elemento === undefined){
                        swal('Notificación','El campo '+campo+' es obligatorio','warning');
                            res = 0;  				
                    }					
                }
            }else if ($(this).prop('nodeName') == 'SELECT'){
                var campo = $(this).parent().parent().find('label').text().substr(2);
                if($(this).val() == '' || $(this).val() == '0' || $(this).val() === undefined || $(this).val() == null){
                    swal('Notificación','El campo '+campo+' es obligatorio','warning');
                    res = 0;
                } 
            }
            else if($(this).prop('nodeName') == 'TEXTAREA'){
                if($(this).val() == '' ){						
                    var campo = $(this).prev().text().substr(2);
                    var name = $(this).attr('name');
                    if(campo != ''){
                        swal('Notificación','El campo '+campo+' es obligatorio','warning');
                        res = 0;
                    }else if(name != ''){
                        swal('Notificación','El campo '+name+' es obligatorio','warning');
                        res = 0;
                    }else{
                        swal('Notificación','El campo '+name+' es obligatorio','warning');
                        res = 0;
                    }
                }
            }			
         }
    });
    return	res;
}
//TOMAR PARAMETROS DE LA URL
function getParamsUrl(k){
    var p={};
    location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
    return k?p[k]:p;
}
    
function limpiar(div){
    $('#'+div).find('input[type="text"],input[type="number"],input[type="email"],input[type="textarea"]').each(function() {
        $(this).val('');
    });
    $('#'+div).find('select').each(function() {
        $(this).val('0').trigger('change');
    });
    $('#'+div).find('textarea').each(function() {
        $(this).val('').trigger('change');
    });
    $('#'+div).find('textarea').each(function() {
        $(this).val('').trigger('change');
    });
    $('#'+div).find('radio,checkbox').each(function() {
        $(this).attr('checked', false).trigger('change');
    });
}
function getKey(array, key) {
    let newarray = [];
    for (let index = 0; index < array.length; index++) {
        const element = array[index];
        newarray.push(element[key]);
    }
    return newarray;
}

if(screen.width >= 1200){
	$('#main-wrapper').addClass('menu-toggle');
	//$('.hamburger').addClass('is-active');	
}

$("#filtros_organizacion, #filtros_empresa").select2({});
// $('[data-toggle="tooltip"]').tooltip();


if(screen.width >= 1200){
	$('#main-wrapper').addClass('menu-toggle');
	//$('.hamburger').addClass('is-active');	
}

function setCookie(cName, cValue, expDays = 1) {
    let date = new Date();
    date.setTime(date.getTime() + (expDays * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
}
function cerrar_config(){
    $(".config-close").click();
}

//TOASTR
function  toastrError(message,tittle) {
    toastr.error(message, tittle, {
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    });
}
  
function  toastrWarning(message,tittle) {
    toastr.warning(message, tittle,{
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "500",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
}
  
function  toastrInfo(message,tittle) {
    toastr.info(message, tittle,{
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
}

function  toastrInfoCenter(message,tittle) {
    toastr.info(message, tittle,{
        positionClass: "toast-bottom-full-width",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !1,
        closeButton: true,
        extendedTimeOut: 0,
        timeOut: 0,
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    }).css({"min-width":"50vw"})
}

function  toastrSuccess(message,tittle) {
    toastr.success(message, tittle,{
        positionClass: "toast-top-right",
        timeOut: 5e3,
        closeButton: !0,
        debug: !1,
        newestOnTop: !0,
        progressBar: !0,
        preventDuplicates: !0,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        tapToDismiss: !1
    })
}

function dataRangePickerOptions(single= true, maxDate = new Date(), minDate = '1900-01-01',format = "YYYY-MM-DD",autoUpdateInput = false, timePicker = false,ranges = {}){
    return {
        "singleDatePicker": single,
        "showDropdowns": true,
        "maxDate": maxDate,
        "minDate": minDate,
        "autoUpdateInput": autoUpdateInput,
        "timePicker": timePicker,
        "ranges": $.isEmptyObject(ranges) ? false : ranges,
        "locale": {
            "maxDate": maxDate,
            "format": format,
            ...locale
        }
    };
}
$(document).ready(function() {
    moment.updateLocale(
        moment.locale('es',{
            months: 'Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre'.split('_'),
            monthsShort: 'Ene_Feb_Mar_Abr_May_Jun_Jul_Ago_Sep_Oct_Nov_Dic'.split('_'),
            weekdays: 'Domingo_Lunes_Martes_Miercoles_Jueves_Viernes_Sabado'.split('_'),
            weekdaysShort: 'Dom_Lun_Mar_Mier_Jue_Vier_Sab'.split('_'),
            weekdaysMin: 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_')
        }),
        { invalidDate: "Fecha inválida" }
    );
    moneda();
});

function moneda(){
    let signo_moneda = getCookie('moneda');
    $(".moneda").each(function(){
        $(this).html(decodeURIComponent(signo_moneda));        
    })
}

/*VALIDACIÓN DE CAMPOS DE TEXTO*/
$(".solotexto").each(function(){
    $(this).bind('keypress', solotexto);
});
$(".noquotes").each(function(){
    $(this).bind('keypress', (e) => noquotes(e.key));
});	
$(".solonumero").each(function(){
    $(this).bind('keypress', soloNumero);
});	
$(".solotextoynumero").each(function(){
    $(this).bind('keypress', solotextoynumero);
});
$(".campo_cedula").each(function(){
    $(this).bind('keypress', campo_cedula);
});
$(".email").each(function(){
    $(this).on('change', function(){
        if( $(this).val() != '' ){
            if( !validar_correo($(this).val()) ){
                $.when(swal('Error!','Dirección de correo inválida','error')).done(function(){$(this).focus();});
                $(this).focus();
            }			
        }		
    });		
});

$(".precio").each(function(){
    $(this).on('blur',function(){
        let valor = $(this).val();
        // $(this).val(valor.numberFormat(2, '.', ','))
    })
})
$(".cedula").each(function(){
    $(this).on('blur', function(){
        if( $(this).val() != '' ){
            if( !validar_cedula($(this).val()) ){
                $.when(swal('Error!','Formato de cédula inválido','error')).done(function(){$(this).focus();});
            }			
        }		
    });		
});
$(".celular").each(function(){
    $(this).on('blur', function(){
        var valor = $(this).val();
        if( valor != '' ){
            var valor1 = valor.substring(0,4);
            var valor2 = valor.substring(4,8);
            var valor_final = valor1+'-'+valor2;
            $(this).val(valor_final);
        }		
    });	
    $(this).on('focus', function(){
        var valor =$(this).val() ;
        var valor_nuevo = valor.replace(/-/g,'');
        $(this).val(valor_nuevo);
    });
});
$(".telefono").each(function(){
    $(this).on('blur', function(){
        var valor = $(this).val();
        if( valor != '' ){
            var valor1 = valor.substring(0,3);
            var valor2 = valor.substring(3,8);
            var valor_final = valor1+'-'+valor2;
            $(this).val(valor_final);
        }		
    });	
    $(this).on('focus', function(){
        var valor =$(this).val() ;
        var valor_nuevo = valor.replace(/-/g,'');
        $(this).val(valor_nuevo);
    });
});

function solotexto(event) {
   var value = String.fromCharCode(event.which);
   var pattern = new RegExp(/[A-Za-záéíóúÁÉÍÓÚñÑ ']/g);
   return pattern.test(value);
}
function solotextoynumero(event) {
   var value = String.fromCharCode(event.which);
   var pattern = new RegExp(/[A-Za-z0-9-']/g);
   return pattern.test(value);
}
function campo_cedula(event) {
   var value = String.fromCharCode(event.which);
   var pattern = new RegExp(/[0-9-aeinpvAEINPV']/g);
   return pattern.test(value);
}
function soloNumero(event) {
   var value = String.fromCharCode(event.which);
   var pattern = new RegExp(/[0-9 ]/g);
   return pattern.test(value);
}

function noquotes(text) {
    return (/^[^"'`´\t]*$/g).test(text);
}

$(".cedula").each(function(){
    $(this).on('blur', function(){
        if( $(this).val() != '' && $(this).hasClass("cedula")){
            if( !validar_cedula($(this).val()) ){
                $.when(swal('Error!','Formato de cédula inválido','error')).done(function(){$(this).focus();});
            }			
        }		
    });		
});

function validar_cedula(cedula){
    Array.prototype.insert = function(index, item) {
        this.splice(index, 0, item);
    };
    var re = /^P$|^(?:PE|E|N|[23456789]|[23456789](?:A|P)?|1[0123]?|1[0123]?(?:A|P)?)$|^(?:PE|E|N|[23456789]|[23456789](?:AV|PI)?|1[0123]?|1[0123]?(?:AV|PI)?)-?$|^(?:PE|E|N|[23456789](?:AV|PI)?|1[0123]?(?:AV|PI)?)-(?:\d{1,4})-?$|^(PE|E|N|[23456789](?:AV|PI)?|1[0123]?(?:AV|PI)?)-(\d{1,4})-(\d{1,6})$/i;
    var matched = cedula.match(re);
    // matched contains:
    // 1) if the cedula is complete (cedula = 8-AV-123-123)
    //    matched = [cedula, first part, second part, third part]
    //    [8AV-123-123]
    // 2) if the cedula is not complete (cedula = "1-1234")
    //    matched = ['1-1234', undefined, undefined, undefined]
    var isComplete = false;
    if (matched !== null) {
        matched.splice(0, 1); // remove the first match, it contains the input string.
        if (matched[0] !== undefined) {
            // if matched[0] is set => cedula complete
            isComplete = true;
            if (matched[0].match(/^PE|E|N$/)) {
                matched.insert(0, "0");
            }
            if (matched[0].match(/^(1[0123]?|[23456789])?$/)) {
                matched.insert(1, "");
            }
            if (matched[0].match(/^(1[0123]?|[23456789])(AV|PI)$/)) {
                var tmp = matched[0].match(/(\d+)(\w+)/);
                matched.splice(0, 1);
                matched.insert(0, tmp[1]);
                matched.insert(1, tmp[2]);
            }
        } // matched[0]
    }
    var result = {
    isValid: cedula.length === 0 ? true : re.test(cedula),
    inputString: cedula,
    isComplete: isComplete,
    cedula: isComplete ? matched.splice(0, 4) : null
    };
    return result.isValid;
}

$(".email").each(function(){
    $(this).on('change', function(){
        if( $(this).val() != '' ){
            if( !validar_correo($(this).val()) ){
                $.when(swal('Error!','Dirección de correo inválida','error')).done(function(){$(this).focus();});
                $(this).focus();
            }			
        }		
    });		
});

function validar_correo(correo){
    var patron=/^\w+([\.-]?\w+)@\w+([\.-]?\w+)(\.\w{2,6})+$/;
    if(correo.search(patron)==0){
    //Mail correcto
        return true;
    }else{
        //Mail incorrecto
        $.when(swal('Error!','Dirección de correo inválida','error')).done(function(){return false;});
    }
}