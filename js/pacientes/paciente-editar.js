const id = getParamsUrl('id');
const histoial = getParamsUrl('histoial');
var cedula_actual ='';
var option_paises = '';
$.get("controller/pacientes/pacientes-back.php",{"oper":"get_paciente","id":id},function(response){
    $("#cedula").val(response.cedula);
    cedula_actual =response.cedula;
    $("#nombre").val(response.nombre);
    $("#fecha-nacimiento").val(response.fecha_nacimiento);
    //$("#sexo").val(response.sexo);
    $('input[type="radio"][name="sexo"][value="'+response.sexo+'"]').click();
    
    $("#telefono").val(response.telefono);
    $("#correo").val(response.correo);
},"json")

$('#fecha-nacimiento').daterangepicker({
    "singleDatePicker": true,
    "showDropdowns": true,
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
// validación de cedula
$('#cedula').blur(function(){
    let cedula = $(this).val();  
    if(cedula != '' && cedula != cedula_actual){  
        $.get("controller/pacientes/pacientes-back.php",{ 'oper' : 'buscarCedula','cedula' : cedula},
        function(response) {    
                if(response.error){
                    swal("Error!",response.mensaje,"error");
                    $('#cedula').val(cedula_actual);
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
        
        $.get('controller/pacientes/pacientes-back.php?oper=check_cedula_editar',{
            cedula: cedula,
            id: id,
        }, function(respuesta){
            if(respuesta == 1){
                $.post("controller/pacientes/pacientes-back.php",{
                    "oper" : "editar_paciente",
                    "id_paciente": id,
                    "cedula": cedula,
                    "nombre": nombre,
                    "fecha_nacimiento": fecha_nacimiento,
                    "telefono": telefono,
                    "correo": correo,
                    "sexo": sexo,
                },function(response){
                    $("#overlay").hide();

                    if(!response.error){
                        $.when(swal("Buen trabajo!",response.mensaje,"success")).done(function(){
                            location.href="pacientes.php";
                        });
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

