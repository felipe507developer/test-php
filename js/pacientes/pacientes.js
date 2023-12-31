const usuario = localStorage.getItem("usuario");
const inicial_usuario = usuario.substr(0, 1).toUpperCase();
$("#span-nombre-usuario").html(usuario);
$("#div-inicial-usuario").html(inicial_usuario);

// Obtener el valor del elemento almacenado en localStorage
var tipo_usuario = localStorage.getItem('usuario');

var tipo_usuario = localStorage.getItem("usuario");


var tablapacientes = $("#tablapacientes").DataTable({
  responsive: true,
  sDom: '<"top"f><"float-left"pl>rt<"bottom"ip><"clear">',
  ajax: {
    url: "controller/pacientes/pacientes-back.php?oper=pacientes",
    type: "GET",
    data: { nivel: localStorage.getItem("nivel") },
  },
  columns: [
    {
      data: "id",
      render: function (data, type, row) {
        if (data != "") {

			if(tipo_usuario === "administrador"){
				return `

				<div class="dropdown">
					<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
						<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item editar-paciente"  href="paciente-editar.php?id=${row.id}">Editar</a>
						<a id="eliminar-paciente" class="dropdown-item eliminar-paciente text-danger"  data-id="${data}" data-status="${row.estado}" data-cedula="${row.cedula}" data-toggle="modal" data-target="#reporte-acuses">Eliminar</a>
			
					</div>
				</div>
			`;
			}
			else if(tipo_usuario === "supervisor"){
				return `

				<div class="dropdown">
					<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
						<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item editar-paciente"  href="paciente-editar.php?id=${row.id}">Editar</a>
					</div>
				</div>
			`;
			}else if(tipo_usuario==="analista"){
				return row.id;
				
			}
          
        } else {
          return "";
        }
      },
    },
    { data: "cedula" },
    { data: "nombre" },
    {
      data: "fecha_nacimiento",
      render: function (data, type, row) {
        if (data != null) {
          return moment().diff(data, "years");
        }
        return data;
      },
    },
    {
      data: "sexo",
      render: function (data, type, row) {
        if (data == "M") {
          return "Masculino";
        } else if (data == "F") {
          return "Femenino";
        } else {
          return "Sin definir";
        }
      },
    },
    {
      data: "estado",
      render: function (data, type, row) {
        if (data != "") {
          let color = "";
          let estatus = "";
          let opciones = "";
		  let btn_status;
		  let desabilitar = "disabled";

          if (data == "1") {
            color = "success";
            estatus = "Activo";
			btn_status = 'Deshabilitar';
          } else {
            color = "dark";
            estatus = "Inactivo";
			btn_status = 'Activar';
			

          }
         
          if(tipo_usuario === "analista"){
			return `<div class="dropdown ml-auto text-center">
											
				<span style="pointer-events: none;" disabled="true" data-toggle="dropdown" aria-expanded="false" style="text-transform: capitalize; cursor: pointer;" class="badge badge-${color}">${estatus}</span>
				
				<div class="dropdown-menu">
					<a class="dropdown-item editar-paciente text-warning" data-status="${data}" data-id=${row.id} data-cedula="${row.cedula}" id="btn-status"  href="#">${btn_status}</a>
				</div>
			</div>`;
		  }else if(tipo_usuario === "supervisor" || tipo_usuario === "administrador"){
			return `<div class="dropdown ml-auto text-center">
											
				<span data-toggle="dropdown" aria-expanded="false" style="text-transform: capitalize; cursor: pointer;" class="badge badge-${color}">${estatus}</span>
				
				<div class="dropdown-menu">
					<a class="dropdown-item editar-paciente text-warning" data-status="${data}" data-id=${row.id} data-cedula="${row.cedula}" id="btn-status"  href="#">${btn_status}</a>
				</div>
			</div>`;
		  }
        } else {
          return ``;
        }
      },
    },
  ],
  rowId: "id", // CAMPO DE LA DATA QUE RETORNARÁ EL MÉTODO id()
  columnDefs: [
    //OCULTAR LA COLUMNA ID
    {
      targets: [0],
      searchable: false,
    },
  ],
  language: {
    url: "js/Spanish.json",
    info: "Mostrando página _PAGE_ de _PAGES_",
  },
  drawCallback: function (settings) {
    console.log("DataTables has redrawn the table");
    $("#tablapacientes").DataTable().columns.adjust();
  },
});

$("#tablapacientes").on("draw.dt", function () {
  $('[data-toggle="tooltip"]').tooltip();
});
