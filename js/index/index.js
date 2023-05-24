$(function(){
    window.localStorage.clear();
    $("#txtUsuario").on('change',function(){
        if($(this).val()){
            $("#inicio_sesion").removeAttr("disabled");
        }else{
            $("#inicio_sesion").attr("disabled", true);
        }
    });
    $('#frmAcceso').submit(function(){
        //console.log('paso');
        let usuario = $("#txtUsuario").val();
        let clave = $("#txtClave").val();
        fetch("controller/login/login-back.php?oper=login",{
            method: "POST",
            body: JSON.stringify({usuario,clave})
        }).then(response=>response.json())
        .then(json =>{
            if(!json.error){
                localStorage.setItem('usuario',usuario);
                location.href="pacientes.php";
            }else{
                swal("Error",json.mensaje,"error");
                return false;
            }
        })
        .catch(error => console.error(error))
        return false;
    });
    $('#showPassword').click(function(){
        let tipo_input  = document.getElementById('txtClave').type
        let cambio = "password";
        let tool = "Mostrar";
        if(tipo_input== cambio){
            cambio = "text"
            $('#icono').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
            tool = "Ocultar";

        }else{
            $('#icono').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
        }
        document.getElementById('txtClave').type = cambio
        document.getElementById('showPassword').title = tool+' contraseña'
    });
});
