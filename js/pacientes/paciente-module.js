// CAMBIAR ESTADO
$(document).on("click", "#btn-status", function () {
    $("#overlay").show();

  if (mandatorio("div-paciente") == 1) {
    var id = $(this).data("id"); //id user
    var cedula = $(this).data("cedula");
    var estado = $(this).data("status"); //status

    $.get(
      "controller/pacientes/pacientes-back.php?oper=check_cedula_editar",
      {
        cedula: cedula,
        id: id,
      },
      function (respuesta) {
        if (respuesta == 1) {
          $.post(
            "controller/pacientes/pacientes-back.php",
            {
              oper: "cambiar_estado",
              id_paciente: id,
              id_estado: estado,
            },
            function (response) {
              $("#overlay").hide();

              if (!response.error) {
                $.when(swal("Buen trabajo!", response.mensaje, "success")).done(
                  function () {
                    location.href = "pacientes.php";
                  }
                );
              } else {
                swal("Error!", response.mensaje, "error");
              }
            },
            "json"
          );
        } else {
          $("#overlay").hide();
          swal("Error");
        }
      }
    );
  } else {
    $("#overlay").hide();
  }
});

//ELIMINAR INACTIVO
$(document).on("click", "#eliminar-paciente", function () {
    $("#overlay").show();

  if (mandatorio("div-paciente") == 1) {
    var id = $(this).data("id"); //id user
    var cedula = $(this).data("cedula");
    var estado = $(this).data("status"); //status

    $.get(
      "controller/pacientes/pacientes-back.php?oper=check_cedula_editar",
      {
        cedula: cedula,
        id: id,
      },
      function (respuesta) {
        if (respuesta == 1) {
          $.post(
            "controller/pacientes/pacientes-back.php",
            {
              oper: "eliminar_paciente",
              id_paciente: id,
              id_estado: estado,
            },
            function (response) {
              $("#overlay").hide();

              if (!response.error) {
                $.when(swal("Buen trabajo!", response.mensaje, "success")).done(
                  function () {
                    location.href = "pacientes.php";
                  }
                );
              } else {
                swal("Error!", response.mensaje, "error");
              }
            },
            "json"
          );
        } else {
          $("#overlay").hide();
          swal("Error");
        }
      }
    );
  } else {
    $("#overlay").hide();
  }
});
