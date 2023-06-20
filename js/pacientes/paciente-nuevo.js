$('#fecha-nacimiento').daterangepicker({
    "singleDatePicker": true,
    "showDropdowns": true,
    "maxDate": new Date(),
    "minDate": '1900-01-01',
    "locale": {
        "maxDate": new Date(),
        "format": "YYYY-MM-DD",
        "separator": " al ",
        "applyLabel": "Seleccionar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde", 
        "toLabel": "Hasta",
        "customRangeLabel": "Custom",
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
    }
});
$('#fecha-nacimiento').val("").trigger('change');

// validación de cedula
$('#cedula').blur(function(){
  
    let cedula = $(this).val();  
    if(cedula != ''){  
        $.get("controller/pacientes/pacientes-back.php",{ 'oper' : 'buscarCedula','cedula' : cedula},
        function(response) {    
                if(response.error){
                    swal("Error!",response.mensaje,"error");
                    $('#cedula').val('');
                    $('#cedula').focus();
                }else{
                    toastrSuccess(response.mensaje,'');
                }
        },"json");
    }
});
// GUARDAR

$("#boton-guardar").on('click',function(){
    
    $("#overlay").show();
    if(mandatorio("div-paciente") == 1){
        let cedula = $("#cedula").val();
        let nombre = $("#nombre").val();
        let fecha_nacimiento = $("#fecha-nacimiento").val();
        let sexo = $('input:radio[name=sexo]:checked').val();     
        let telefono = $("#telefono").val();
        let correo = $("#correo").val();
        let tipo_sangre = $("#tipo_sangre").val();

        $.get('controller/pacientes/pacientes-back.php?oper=check_cedula',{
            cedula: cedula,
        }, function(respuesta){
            if(respuesta == 1){
                $.post("controller/pacientes/pacientes-back.php",{
                    "oper" : "nuevo_paciente",
                    "cedula": cedula,
                    "nombre": nombre,
                    "fecha_nacimiento": fecha_nacimiento,
                    "telefono": telefono,
                    "correo": correo,
                    "sexo": sexo,
                    "tipo_sanguineo": tipo_sangre
                },function(response){
                    $("#overlay").hide();
                    if(!response.error){
                        $.when(swal("Buen trabajo!",response.mensaje,"success")).done(function(){
                            location.href="pacientes.php";
                        })
                    }else{
                        swal("Error!",response.mensaje,"error");    
                    }
                },"json");
            }else{
                $("#overlay").hide();
                swal('Error','Ya existe un paciente con la misma identificacion','error');

            }
        });   
    }else{
        $("#overlay").hide();
    }
});

