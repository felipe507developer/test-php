const id_paciente =  getParamsUrl('id');
var id_hospit_global = '';
if(decodeURIComponent(getCookie('region')) == "VIV Care"){
    $(".titulo_viv").html('Evento');    
}
$("#btn-editar-paciente").on('click',function(){
	location.href ='paciente-editar.php?id='+id_paciente+'&historial=1';
})
$.get("controller/pacientes/pacientes-back.php",{"oper":"get_paciente","id":id_paciente},function(response){
	if(response.error){
		swal("Error!",response.mensaje,"error").then(()=>{
			window.location="pacientes.php";
		})
	}else{
		$("#nombre-paciente").html(response.nombre);
		$("#cedula-paciente").html(response.cedula);
		$("#fecha-nacimiento-paciente").html(response.fechanac);
		$("#edad-paciente").html(response.edad);
		$("#sexo-paciente").html(response.sexo);
		$("#direccion-paciente").html(response.direccion);
		$("#telefono-casa-paciente").html(response.telefonocasa);
		$("#telefono-celular-paciente").html(response.celular);
		$("#correo-paciente").html(response.email);
		id_hospit_global = response.id_hospitalizacion;
		recargar_grafico(id_hospit_global,false,false);
		if(response.imagen_perfil != ''){	

			$('#imagen_perfil').attr('src',response.imagen_perfil);
		}
		let color = "dark";
		let estado = "No asignado";
		switch(response.id_estado){
			case "50": $("#estado-paciente").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-success">${response.estado_paciente}</span>`); break;
			case "51": $("#estado-paciente").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-info">${response.estado_paciente}</span>`); break;
			case "52": $("#estado-paciente").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-warning">${response.estado_paciente}</span>`); break;
			case "53": $("#estado-paciente").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-danger">${response.estado_paciente}</span>`); break;
			default: $("#estado-paciente").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-dark">No asignado</span>`); break;
		}
		switch(response.tiposangre){
			case "A":$("#tipo-sangre-paciente").html("A+");break;
			case "AN":$("#tipo-sangre-paciente").html("A-");break;
			case "B":$("#tipo-sangre-paciente").html("B+");break;
			case "BN":$("#tipo-sangre-paciente").html("B-");break;
			case "O":$("#tipo-sangre-paciente").html("O+");break;
			case "ON":$("#tipo-sangre-paciente").html("O-");break;
			case "AB":$("#tipo-sangre-paciente").html("AB+");break;
			case "ABN":$("#tipo-sangre-paciente").html("AB-");break;
			default:$("#tipo-sangre-paciente").html(response.tiposangre);break;
		}
		if(response.sexo == 'F'){
			$("#icono-sexo-paciente").html('<i style="font-size:1.4em;color:#36c95f;" class="las la-venus la-xg"></i>');
		}else if(response.sexo == 'M'){
			$("#icono-sexo-paciente").html('<i style="font-size:1.4em;color:#36c95f;" class="las la-mars la-xg"></i>');
		}
		let contactos_html = ``
		$.map(response.contactos, function(contacto){
			contactos_html+=`<li>
								<div class="timeline-panel bgl-dark flex-wrap border-0 p-3 rounded">
									<!--div class="media bg-transparent mr-2">
										<img class="rounded-circle" alt="image" width="48" src="images/widget/6.jpg">
									</div-->
									<div class="media-body">
										<h5 class="mb-1 fs-18">${contacto.nombre}</h5>
										<span>Relacion: <strong class="text-primary">${contacto.relacion}</strong></span>
									</div>
									<ul class="mt-3 d-flex flex-wrap" style="margin-top: 0px !important;">
										<li class="mr-2">
											Correo:<span class="text-info">${contacto.correo}</span><br>
											Tel&eacute;fono:<span class="text-info">${contacto.celular}</span><br>
											Pa&iacute;s de residencia:<span class="text-info">${contacto.pais}</span><br>
											Rol:<span class="text-info">${(contacto.rol)? contacto.rol : ''}</span>
										</li>
									</ul>								
								</div>
							</li>`;
		});
		$("#div-contactos").html(contactos_html);
		let seguros_html= '';
		$.map(response.seguros, function(seguro){
			seguros_html+=`<li>
								<div class="timeline-badge primary"></div>
								<span class="timeline-panel text-muted">
									<strong class="text-info">Fecha efectiva: ${seguro.fecha_efectiva}</strong>
									<h6>Seguro: <strong>${seguro.seguro.split(' | ')[1]}</strong></h6>
									<h6>Asegurado: <strong>${seguro.cedula_asegurado} | ${seguro.nombre_asegurado}</strong></h6>
									<h6>Nro Poliza: <strong>${seguro.numero_poliza}</strong></h6>
									<h6>Certificado:<strong> ${seguro.certificado}</strong></h6>
								</span>
							</li>`;
		});
		$("#div-seguros").html(seguros_html);
		cargar_antecedentes();
		$.get("controller/pacientes/pacientes-back.php",{"oper":"get_perfil_hospitalizacion","id_paciente":id_paciente},function(hosp){
			let html_diagnosticos = "";
			let html_medicostratantes = "";
			let diagnosticos = hosp.diagnosticos.split('//');
			$.map(diagnosticos,function(diagnostico){
				html_diagnosticos+=`<li>
										<div class="timeline-badge info"></div>
										<a class="timeline-panel text-muted" href="#">
											<h6>${diagnostico}</h6>
										</a>
									</li>
				`;
			});
            let otros_medicos_tratantes = hosp.otros_medicos_tratantes;
            $.map(otros_medicos_tratantes,function(medico){
                html_medicostratantes+=
                                    `<li>
											<div class="media-body">
												<h4 ><strong class="text-info">${medico.nombre} ${medico.apellido}</strong></h4>
												<span >
													<svg width="22" height="22" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M28.2884 21.7563V25.6138C28.2898 25.9719 28.2165 26.3264 28.073 26.6545C27.9296 26.9826 27.7191 27.2771 27.4553 27.5192C27.1914 27.7613 26.8798 27.9456 26.5406 28.0604C26.2014 28.1751 25.8419 28.2177 25.4853 28.1855C21.5285 27.7555 17.7278 26.4035 14.3885 24.238C11.2817 22.2638 8.64771 19.6297 6.67352 16.523C4.50043 13.1685 3.14808 9.34928 2.72601 5.37477C2.69388 5.0192 2.73614 4.66083 2.8501 4.32248C2.96405 3.98413 3.14721 3.67322 3.38792 3.40953C3.62862 3.14585 3.92159 2.93517 4.24817 2.79092C4.57475 2.64667 4.9278 2.57199 5.28482 2.57166H9.14232C9.76634 2.56552 10.3713 2.78649 10.8445 3.1934C11.3176 3.60031 11.6267 4.16538 11.714 4.78329C11.8768 6.01778 12.1788 7.22988 12.6141 8.39648C12.7871 8.85671 12.8245 9.35689 12.722 9.83775C12.6194 10.3186 12.3812 10.76 12.0354 11.1096L10.4024 12.7426C12.2329 15.9617 14.8983 18.6271 18.1174 20.4576L19.7504 18.8246C20.1001 18.4789 20.5414 18.2406 21.0223 18.1381C21.5031 18.0355 22.0033 18.073 22.4636 18.246C23.6302 18.6813 24.8423 18.9832 26.0767 19.1461C26.7014 19.2342 27.2718 19.5488 27.6796 20.0301C28.0874 20.5113 28.304 21.1257 28.2884 21.7563Z" stroke="#2BC155" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
													</svg>
												</span>
												<span class="" >${medico.telefono}</span>
												<p class="text-primary">${medico.especialidades}</p>
											</div>
									</li>
				`;
            });
            $("#div-diagnosticos").html(html_diagnosticos);
			$("#healthmanager").html(hosp.healthmanager); // "Cristy Diaz"
			$("#otros-medicos-tratantes").html(html_medicostratantes);
			$("#medico-tratante").html(hosp.medico_tratante);
			$("#especialidades-medico-tratante").html(hosp.especialidades);
			$("#rating-medico").html(hosp.estrellas);
			$("#telefono-medico-tratante").html(hosp.telefono_medico);
			hosp.estado_administrativo // "0"
			hosp.estado_clinico // "activa"
			$("#fecha-inicio-hosp").html(hosp.fecha_inicio); // "2022-08-28"
			$("#fecha-fin-hosp").html(hosp.fecha_fin); // "2022-08-28"
			hosp.hospital // "Hospital Nacional"
			hosp.serial_gateway // ""
			hosp.tipo_hospitalizacion // "normal"
			switch(hosp.triage){
				case "1": $("#triage").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-success">Estable</span>`); break;
				case "2": $("#triage").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-warning">Comprometido</span>`); break;
				case "3": $("#triage").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-danger">Inestable</span>`); break;
				default: $("#triage").html(`<span style="text-transform: capitalize; cursor: pointer;" class="badge badge-dark">No asignado</span>`); break;
			}
		},"json")
		cargar_evaluaciones();
	}
},"json")
function quitar_antecedente(id,tipo){
	$.post("controller/pacientes/historial-back.php",{"oper":"quitar_antecedente","tipo":tipo,"id":id},function(response){
		if(response.error){
			swal("Error","ha ocurrido un error al guardar");
		}else{
			cargar_antecedentes()
		}
	},"json")
}
function cargar_antecedentes(){
	$.get("controller/pacientes/historial-back.php",{"oper":"antecedentes","id_paciente":id_paciente},function(antecedentes){
		//Alergias
		let html_alergias ='';
		$.map(antecedentes.alergias,function(alergia){
			html_alergias += 	`<li>
									<div class="timeline-badge danger"></div>
									<span class="timeline-panel text-muted" >
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${alergia.id}" class="fas fa-trash text-danger btn-quitar-alergia" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span> ${alergia.nombre}</h6>
									</span>
								</li>`;
		})
		if(html_alergias!= ''){
			$.when($("#div-antecedentes-alergias").html(html_alergias)).done(function(){
				$(".btn-quitar-alergia").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'alergias');
				})
			});
		}
		// antecedentes personales patologicos 
		let html_patologicos ='';
		$.map(antecedentes.ant_patologicos,function(antecedente){
			html_patologicos += `<li>
									<div class="timeline-badge warning"></div>
									<span class="timeline-panel text-muted" >
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${antecedente.id}" class="fas fa-trash text-danger btn-quitar-ant-patologico" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span>  ${antecedente.nombre}</h6>
									</span>
								</li>`;
		})
		if(html_patologicos!= ''){
			$.when($("#div-antecedentes-patologicos").html(html_patologicos)).done(function(){
				$(".btn-quitar-ant-patologico").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'patologicos');
				})
			});
		}
		// habitos 
		let html_habitos ='';
		$.map(antecedentes.habitos,function(antecedente){
			html_habitos += `<li>
									<div class="timeline-badge warning"></div>
									<span class="timeline-panel text-muted" >
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${antecedente.id}" class="fas fa-trash text-danger btn-quitar-ant-habito" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span>  ${antecedente.nombre}</h6>
									</span>
								</li>`;
		})
		if(html_habitos!= ''){
			$.when($("#div-antecedentes-habitos").html(html_habitos)).done(function(){
				$(".btn-quitar-ant-habito").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'habitos');
				})
			});
		}
		// antecedentes quirurgicos  
		let html_quirurgicos ='';
		$.map(antecedentes.ant_quirurgicos,function(antecedente){
			html_quirurgicos += `<li>
									<div class="timeline-badge warning"></div>
									<span class="timeline-panel text-muted" >
										<span>${(antecedente.fecha=='0000-00-00')?'No hay fecha':antecedente.fecha}</span>
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${antecedente.id}" class="fas fa-trash text-danger btn-quitar-ant-quirurgicos" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span>  ${antecedente.nombre}</h6>
									</span>
								</li>`;
		})
		if(html_quirurgicos!= ''){
			$.when($("#div-antecedentes-quirurgicos").html(html_quirurgicos)).done(function(){
				$(".btn-quitar-ant-quirurgicos").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'quirurgicos');
				})
			});
		}
		// antecedentes familiares  
		let html_familiares ='';
		$.map(antecedentes.ant_familiares,function(antecedente){
			html_familiares += `<li>
									<div class="timeline-badge warning"></div>
									<span class="timeline-panel text-muted" >
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${antecedente.id}" class="fas fa-trash text-danger btn-quitar-ant-familiar" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span>  ${antecedente.nombre} (${antecedente.parentesco})</h6>
									</span>
								</li>`;
		})
		if(html_familiares!= ''){
			$.when($("#div-antecedentes-familiares").html(html_familiares)).done(function(){
				$(".btn-quitar-ant-familiar").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'familiares');
				})
			});
		}
		// antecedentes vacunas  
		let html_vacunas ='';
		$.map(antecedentes.vacunas,function(antecedente){
			html_vacunas += `<li>
									<div class="timeline-badge warning"></div>
									<span class="timeline-panel text-muted" >
										<span>${(antecedente.fecha=='0000-00-00')?'No hay fecha':antecedente.fecha}</span>
										<h6><span style="display:inline; font-size:1.2em; cursor: pointer;" data-id="${antecedente.id}" class="fas fa-trash text-danger btn-quitar-ant-vacunas" data-toggle="tooltip" data-original-title="Quitar" data-placement="right" aria-hidden="true"></span>  ${antecedente.nombre} ${(antecedente.dosis!= '')?"("+antecedente.dosis+")":""}</h6>
									</span>
								</li>`;
		})
		if(html_vacunas!= ''){
			$.when($("#div-antecedentes-vacunas").html(html_vacunas)).done(function(){
				$(".btn-quitar-ant-vacunas").on('click',function(){
					quitar_antecedente($(this).attr('data-id'),'vacunas');
				})
			});
		}
	},'json')
}
$("#boton-agregar-alergia-paciente").on('click',function(){
	let alergia = $("#select-alergias").val();
	if(alergia == 0 || alergia == null || alergia == undefined || alergia == ''){
		swal("Error","Debe seleccionar una alergia","error")
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"alergias","id":alergia,"id_paciente":id_paciente},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
$("#btn-guardar-alergia").on('click',function(){
	let nombre = $("#nuevo-alergia-nombre").val();
	if(nombre == 0 || nombre == null || nombre == undefined || nombre == ''){
		swal("Error","Debe asignar un nombre","error")
	}else{
		$.post("controller/maestros/alergias-back.php",{"oper":"nuevo","nombre":nombre},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				$("#modal-alergia").modal('hide');
				cargar_alergias()
			}
		},"json")
	}
})
cargar_alergias();
function cargar_alergias(){
	$.get("controller/maestros/alergias-back.php?oper=alergias",function(alergias){
		let options = `<option value="0">Seleccione</option>`;
		$.map(alergias,function(alergia){        
			options += `<option value="${alergia.id}" >${alergia.nombre}</option>`;
		})
		$("#select-alergias").empty();
		$("#select-alergias").select2();
		$("#select-alergias").append(options);
		$('button[data-id="select-alergias"]').hide();
	},'json')
}
$("#select-patologicos").select2({
    placeholder: 'Antecedentes patológicos',
    ajax: {
      url: "controller/maestros/enfermedades-back.php?oper=enfermedades_pag",
      dataType: 'json',
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1
        };
      },
      processResults: function (data, params) {
          params.page = params.page || 1;
          return {
              results: data.results,
              pagination: {
                  more: (params.page * 5) < data.count
              }
          };
      },
      minimumInputLength: 1,
    }
});
$("#boton-agregar-patologico-paciente").on('click',function(){
	let id = $("#select-patologicos").val();
	if(id == 0 || id == null || id == undefined || id == ''){
		swal("Error","Debe seleccionar un antecedente","error")
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"patologicos","id":id,"id_paciente":id_paciente},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
cargar_habitos();
function cargar_habitos(){
	$.get("controller/maestros/habitos-back.php?oper=habitos",function(habitos){
		let options = `<option value="0">Seleccione</option>`;
		$.map(habitos,function(habito){        
			options += `<option value="${habito.id}" >${habito.nombre}</option>`;
		})
		$("#select-habitos").empty();
		$("#select-habitos").select2();
		$("#select-habitos").append(options);
		$('button[data-id="select-habitos"]').hide();
	},'json')
}
$("#btn-guardar-habito").on('click',function(){
	let nombre = $("#nuevo-habito-nombre").val();
	if(nombre == 0 || nombre == null || nombre == undefined || nombre == ''){
		swal("Error","Debe asignar un nombre","error")
	}else{
		$.post("controller/maestros/habitos-back.php",{"oper":"nuevo","nombre":nombre},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				$("#modal-habito").modal('hide');
				cargar_habitos()
			}
		},"json")
	}
})
$("#boton-agregar-habito-paciente").on('click',function(){
	let id = $("#select-habitos").val();
	if(id == 0 || id == null || id == undefined || id == ''){
		swal("Error","Debe seleccionar un antecedente","error")
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"habitos","id":id,"id_paciente":id_paciente},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
cargar_ant_quirurgicos();
function cargar_ant_quirurgicos(){
	$.get("controller/maestros/antecedentes-quirurgicos-back.php?oper=antecedentes_quirurgicos",function(antecedentes){
		let options = `<option value="0">Seleccione</option>`;
		$.map(antecedentes,function(antecedente){        
			options += `<option value="${antecedente.id}" >${antecedente.nombre}</option>`;
		})
		$("#select-ant-quirurgicos").empty();
		$("#select-ant-quirurgicos").select2();
		$("#select-ant-quirurgicos").append(options);
		$('button[data-id="select-ant-quirurgicos"]').hide();
	},'json')
}
$("#btn-guardar-ant-quirurgico").on('click',function(){
	let nombre = $("#nuevo-ant-quirurgico-nombre").val();
	if(nombre == 0 || nombre == null || nombre == undefined || nombre == ''){
		swal("Error","Debe asignar un nombre","error")
	}else{
		$.post("controller/maestros/antecedentes-quirurgicos-back.php",{"oper":"nuevo","nombre":nombre},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				$("#modal-ant-quirurgico").modal('hide');
				cargar_ant_quirurgicos()
			}
		},"json")
	}
})
$("#boton-agregar-ant-quirurgico-paciente").on('click',function(){
	let id = $("#select-ant-quirurgicos").val();
	let fecha = $("#ant-quirurgicos-fecha").val();
	if(id == 0 || id == null || id == undefined || id == ''){
		swal("Error","Debe seleccionar un antecedente","error")
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"quirurgicos","id":id,"id_paciente":id_paciente,"fecha":fecha},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
$('#ant-quirurgicos-fecha,#ant-vacuna-fecha,#fecha_documento').daterangepicker({
    "singleDatePicker": true,
    "showDropdowns": true,
    "maxDate": new Date(),
    "minDate": '1900-01-01',
	"autoUpdateInput": false,
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
$('#ant-quirurgicos-fecha,#ant-vacuna-fecha,#fecha_documento').on('apply.daterangepicker', function(ev, picker) {
	$(this).val(picker.startDate.format("YYYY-MM-DD"));
});
$('#ant-quirurgicos-fecha,#ant-vacuna-fecha,#fecha_documento').on('cancel.daterangepicker', function(ev, picker) {
	$(this).val('');
});
cargar_ant_familiares();
function cargar_ant_familiares(){
	$.get("controller/maestros/antecedentes-familiares-back.php?oper=antecedentes_familiares",function(antecedentes){
		let options = `<option value="0">Seleccione</option>`;
		$.map(antecedentes,function(antecedente){        
			options += `<option value="${antecedente.id}" >${antecedente.nombre}</option>`;
		})
		$("#select-ant-familiares").empty();
		$("#select-ant-familiares").select2();
		$("#select-ant-familiares").append(options);
		$('button[data-id="select-ant-familiares"]').hide();
	},'json')
}
$("#btn-guardar-ant-familiar").on('click',function(){
	let nombre = $("#nuevo-ant-familiar-nombre").val();
	if(nombre == 0 || nombre == null || nombre == undefined || nombre == ''){
		swal("Error","Debe asignar un nombre","error")
	}else{
		$.post("controller/maestros/antecedentes-familiares-back.php",{"oper":"nuevo","nombre":nombre},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				$("#modal-ant-familiar").modal('hide');
				cargar_ant_familiares()
			}
		},"json")
	}
})
$("#boton-agregar-ant-familiar-paciente").on('click',function(){
	let id = $("#select-ant-familiares").val();
	let parentesco = $("#select-parentesco-ant-familiares").val();
	if(id == 0 || id == null || id == undefined || id == ''){
		swal("Error","Debe seleccionar un antecedente","error")
		return;
	}else if(parentesco == 0 || parentesco == null || parentesco == undefined || parentesco == ''){
		swal("Error","Debe seleccionar un parentesco","error")
		return;
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"familiares","id":id,"id_paciente":id_paciente,"parentesco":parentesco},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
$.get("controller/maestros/parentescos-back.php?oper=parentescos",function(parentescos){
	let options = `<option value="0">Seleccione</option>`;
	$.map(parentescos,function(parentesco){        
		options += `<option value="${parentesco.nombre}" >${parentesco.nombre}</option>`;
	})
	$("#select-parentesco-ant-familiares").empty();
	$("#select-parentesco-ant-familiares").select2();
	$("#select-parentesco-ant-familiares").append(options);
	$('button[data-id="select-parentesco-ant-familiares"]').hide();
},'json')
cargar_ant_vacuna();
function cargar_ant_vacuna(){
	$.get("controller/maestros/vacunas-back.php?oper=vacunas",function(vacunas){
		let options = `<option value="0">Seleccione</option>`;
		$.map(vacunas,function(vacuna){        
			options += `<option value="${vacuna.id}" >${vacuna.nombre}</option>`;
		})
		$("#select-ant-vacunas").empty();
		$("#select-ant-vacunas").select2();
		$("#select-ant-vacunas").append(options);
		$('button[data-id="select-ant-vacunas"]').hide();
	},'json')
}
$("#btn-guardar-ant-vacuna").on('click',function(){
	let nombre = $("#nuevo-ant-vacuna-nombre").val();
	if(nombre == 0 || nombre == null || nombre == undefined || nombre == ''){
		swal("Error","Debe asignar un nombre","error")
	}else{
		$.post("controller/maestros/vacunas-back.php",{"oper":"nuevo","nombre":nombre},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				$("#modal-ant-vacunas").modal('hide');
				cargar_ant_familiares()
			}
		},"json")
	}
})
$("#boton-agregar-ant-vacuna-paciente").on('click',function(){
	let id = $("#select-ant-vacunas").val();
	let fecha = $("#ant-vacuna-fecha").val();
	let dosis = $("#ant-vacuna-dosis").val();
	if(id == 0 || id == null || id == undefined || id == ''){
		swal("Error","Debe seleccionar una vauna","error")
		return;
	}else{
		$.post("controller/pacientes/historial-back.php",{"oper":"agregar_antecedente","tipo":"vacunas","id":id,"id_paciente":id_paciente,"fecha":fecha,"dosis":dosis},function(response){
			if(response.error){
				swal("Error","ha ocurrido un error al guardar");
			}else{
				cargar_antecedentes()
			}
		},"json")
	}
})
function cargar_evaluaciones(){
	$.get('controller/pacientes/historial-back.php?oper=cargar_evaluaciones_medicas&id_paciente='+id_paciente,'',function(evaluaciones){
		let html = '';
		let i = 0;
		$.map(evaluaciones,function(evaluacion){
			i = 1;
			html +=`<div class="accordion__item">
						<div class="accordion__header collapsed" data-toggle="collapse" data-target="#evaluacion_${evaluacion.id}">
							<span class="accordion__header--text">${evaluacion.evaluacion} (${evaluacion.fecha}) :<strong style="color:${evaluacion.color}">${evaluacion.resultado}</strong></span>
							<span class="accordion__header--indicator indicator_bordered"></span>
						</div>
						<div id="evaluacion_${evaluacion.id}" class="collapse accordion__body" data-parent="#accordion-evaluaciones">
							<div class="accordion__body--text" >
								<div class="card"  id="card_${evaluacion.id}">
									<div class="card-header">
										<h5 class="card-title">Preguntas de evaluaci&oacute;n</h5>
									</div>
									<div class="card-body" style="overflow-y: auto;">
										<div class="row">`;
			$.map(evaluacion.preguntas,function(pregunta){
				html+=`						<div class="col-md-12 col-xs-12 col-sm-12">		
												<p>${i}) ${pregunta.texto}: <strong>${pregunta.respuesta}</strong></p>
											</div>`;
				i++;
			})
			html+=`						</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			`;
		})
		$("#accordion-evaluaciones").html(html)
	},"json");
	
}

	//chart
	
var tablaAlertasSV = $("#tabla-alertas-signos-vitales").DataTable({
	responsive: true,
	searching: false,
	paging: false,
	lengthChange: false,
	ordering: false,
	"ajax"		:"controller/pacientes/historial-back.php?oper=cargar_alertas_sv&id_paciente="+id_paciente,
	"columns"	: [			
		{ 	"data": "signo_vital",
			
		},			
		{ 	"data": "id",
			render:function(data,type,row){
				if(data !=""){
					return `${row.valor_minimo} - ${row.valor_maximo}`;
				}
				return "";
			}
		},
		{ 	"data": "usuario" },
		{ 	"data": "fecha_actualizacion",
			render: function(data,typw,row){
				if(data!=''){
					return moment(data).format("DD-MMM-YYYY h:m A");
				}
				return ``;
			} 
		}
		],
	rowId: 'id', 
	"columnDefs": [
		{
			"targets"	: [ 0,3 ],
			"width"		:  "20%"
		},
		{
			"targets"	: [ 1,2 ],
			"width"		:  "30%"
		}
	],	
	"language": {
		"url": "js/Spanish.json",
	},
});
		
	$.get('controller/pacientes/historial-back.php?oper=signos_vitales&id_paciente='+id_paciente,{
		},function(respuesta){
		grafica_sv(respuesta.grafica);

	},'json');

	function grafica_sv(grafica){		
		if(grafica !== undefined){	
			for(var i = 0; i <grafica.series.arrFrecuenciaCardiaca.data.length; i++){                
				grafica.series.arrFrecuenciaCardiaca.data[i].y = Number(grafica.series.arrFrecuenciaCardiaca.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrFrecuenciaRespiratoria.data.length; i++){                
				grafica.series.arrFrecuenciaRespiratoria.data[i].y = Number(grafica.series.arrFrecuenciaRespiratoria.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrOximetria.data.length; i++){                
				grafica.series.arrOximetria.data[i].y = Number(grafica.series.arrOximetria.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrSistolica.data.length; i++){                
				grafica.series.arrSistolica.data[i].y = Number(grafica.series.arrSistolica.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrDiastolica.data.length; i++){                
				grafica.series.arrDiastolica.data[i].y = Number(grafica.series.arrDiastolica.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrTemperatura.data.length; i++){                
				grafica.series.arrTemperatura.data[i].y = Number(grafica.series.arrTemperatura.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrDolor.data.length; i++){                
				grafica.series.arrDolor.data[i].y = Number(grafica.series.arrDolor.data[i].y);
			}
			for(var i = 0; i <grafica.series.arrGlicemia.data.length; i++){                
				grafica.series.arrGlicemia.data[i].y = Number(grafica.series.arrGlicemia.data[i].y);
			}
			let longitud=grafica.arrCategorias.length-1;
			var signosvitales = {
				chart: {
					renderTo: "chart", 
					defaultSeriesType: "spline",
					backgroundColor:"rgba(255, 255, 255, 0.0)",
					
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function() {
									abrir_visita(this.id);
								}
							}
						}
					}
				},
				credits: {
					enabled: false
				},
				title: {
					text: null
				},
				subtitle: {
					text: null
				},
				xAxis: {
					labels: {
						style: { color: "#000000" , fontSize: "14px" , overflow: 'justify', marginLeft:"30px" },
					},
					categories: grafica.arrCategorias,
					range: longitud,
					min : 0,
					max: longitud,  
					tickInterva: 0,
					step: 0,
					pointInterval: 0
				},
				yAxis: {
					title: {
						text: null
					},
					plotLines: [{
						width: 2,
						color: "#808080"
					}],
					max: null
				},
				tooltip: {
					valueDecimals: 1,
					valuePrefix: null,
					valueSuffix: null
				},
				scrollbar: {
					enabled: true,
				},
				legend: {
					enable: true,
					verticalAlign: 'top', 
					// y: 100, 
					align: 'right' 
					
				},
				series: [
					{ 
						data: grafica.series.arrFrecuenciaCardiaca.data,
						name: "Frec. Cardíaca",
						color: "#D50000"
					},{ 
						data: grafica.series.arrFrecuenciaRespiratoria.data,
						name: "Frec. Respiratoria",
						color: "#AA00FF"
					},{ 
						data: grafica.series.arrOximetria.data,
						name: "Sat. Oxigeno",
						color: "#304FFE",
					},{ 
						data: grafica.series.arrSistolica.data,
						name: "Sistólica",
						color: "#2962FF",
					},{ 
						data: grafica.series.arrDiastolica.data,
						name: "Diastólica",
						color: "#00BFA5",
					},{  
						data: grafica.series.arrTemperatura.data,
						name: "Temperatura",
						color: "#00C853",
					},{ 
						data: grafica.series.arrDolor.data,
						name: "Nivel de Dolor",
						color: "#AEEA00",
					},{ 
						data: grafica.series.arrGlicemia.data,
						name: "Glicemia Capilar",
						color: "#FFAB00",
					}
				]
			};
			var chart = new Highcharts.Chart(signosvitales);
			$(".highcharts-background").attr('stroke','');
		}else{
			$("#chart").html('');
		}
	}
	

	//notas medicas 
	$.get("controller/pacientes/historial-back.php",{"oper":"notas_medicos","id_paciente":id_paciente},function(notas){
		let html =``;
		if(!notas.vacio){
			$.map(notas, function(nota){
				html +=`<div class="accordion__item">
					<div class="accordion__header collapsed" data-toggle="collapse" data-target="#nota-${nota.id}">
						<span class="accordion__header--icon"></span>
						<span class="accordion__header--text">${nota.medico} - ${nota.fecha}</span>
						<span class="accordion__header--indicator indicator_bordered"></span>
					</div>
					<div id="nota-${nota.id}" class="collapse accordion__body" data-parent="#accordion-notas-medicas">
						<div class="accordion__body--text">
							<b>Subjetiva:</b> ${nota.subjetiva}<br>
							<b>Objetiva:</b> ${nota.objetiva}<br>
							<b>Analisis:</b> ${nota.analisis}<br>
							<b>Plan:</b> ${nota.plan}<br>
						</div>
					</div>
				</div>`;
			})
			$("#accordion-notas-medicas").html(html);
		}
	},"json")
	//notas enfermerìa 
	$.get("controller/pacientes/historial-back.php",{"oper":"notas_enfermeria","id_paciente":id_paciente},function(notas){
		let html =``;
		if(!notas.vacio){
			$.map(notas, function(nota){
				html +=`<div class="accordion__item">
					<div class="accordion__header collapsed" data-toggle="collapse" data-target="#nota-${nota.id}">
						<span class="accordion__header--icon"></span>
						<span class="accordion__header--text">${nota.recurso} - ${nota.fecha}</span>
						<span class="accordion__header--indicator indicator_bordered"></span>
					</div>
					<div id="nota-${nota.id}" class="collapse accordion__body" data-parent="#accordion-notas-enfermeria">
						<div class="accordion__body--text">
							${nota.comentario}
							</b> ${nota.fechan}
						</div>
					</div>
				</div>`;
			})
			$("#accordion-notas-enfermeria").html(html);
		}
	},"json")


	
    var tablaTarjetasMedicamentos = $("#tabla-tarjetas-medicamentos").DataTable({
        responsive: true,
        order: [[0,'desc']],

        "ajax"		: "controller/tarjetas-medicamentos/tarjetas-medicamentos-back.php?oper=getTarjetas&id_paciente="+id_paciente,
        "columns"	: [
            {"data":"id",
                render: function(data,type,row){
                    if(data!= ''){
                        let imprimir_completa = `<a target="_blank" href="controller/tarjetas-medicamentos/imprimir-tarjeta-completa.php?id=${data}" style="cursor: pointer" class="dropdown-item "  data-id="${row.id}">Imprimir tarjeta completa</a>`;
                        let imprimir_simple = `<a target="_blank" href="controller/tarjetas-medicamentos/imprimir-tarjeta-simple.php?id=${data}" style="cursor: pointer" class="dropdown-item "  data-id="${row.id}">Imprimir tarjeta simple</a>`;
                        let imprimir_conteo = `<a target="_blank" href="controller/tarjetas-medicamentos/imprimir-conteo-presencial.php?id=${data}" style="cursor: pointer" class="dropdown-item "  data-id="${row.id}">Imprimir conteo presencial</a>`;
                        let editar = ``
                        let eliminar = ``;
                        let color= `info`;
                        let activar= ``;
                        if(row.estado == 0){                            
                            editar  = `<a href="tarjeta-medicamentos-editar.php?id=${data}" style="cursor: pointer" class="dropdown-item "  data-id="${row.id}">Editar</a>`;
                            eliminar = `<a style="cursor: pointer" class="dropdown-item text-danger eliminar-tarjeta-medicamentos"  data-id="${row.id}" onclick="eliminarTarjetaMedicamentos(${row.id})">Eliminar</a>`;
                            switch(row.estatus_seguimiento){
                                case 0: color = 'info'; break;
                                case 2: color = 'warning'; break;
                                default: color = ''; break;
                            }
                        }
                        marcador = `
                                <span class="btn-icon btn-xs boton-coment-32841"  style="padding: 0;">
                                    <i class="fas fa-circle text-${color} i-header" aria-hidden="true" style="cursor: initial;"></i>
                                </span>
                            `; 
                        return `<div class="dropdown">
                                    <button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
                                        <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                                    </button>
                                    <div class="dropdown-menu">                         
                                        ${imprimir_completa}                                                                                                        
                                        ${imprimir_simple}                                                                                                        
                                        ${imprimir_conteo}                                                                                                        
                                    </div>
                                </div>
                        `;
                    }
                    return ``;
                }
            },
            {"data":"estado",
                render: function(data,type,row){
                    if(data != ''){
                        switch(data){
                            case "0": return `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-warning " data-id="${row.id}">Pendiente</span>`; break;
                            case "1": return `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-success" data-id="${row.id}">Activa</span>`; break;
                            case "2": return `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-dark">Finalizada</span>`; break;
                        }
                    }
                    return ``;
                }
            },
            {"data":"usuario"},
            {"data":"fecha"},
        ],
        rowId: 'id',
        "columnDefs": [
            {
                "targets"	: [ 0,1 ],
                "width"		:  "5%"
            }
        ],
        "language": {
            "url": "js/Spanish.json",
            "info": "MOSTRANDO PAGINA _PAGE_ DE _PAGES_"
        }
    });

    var tablaOrdenesDetalle = $("#tablaOrdenesDetalle").DataTable({
        responsive: true,
        //order: [[5,'desc']],

        dataType: "json",
        "ajax"		: {
            "url": "controller/orden-medica/orden-medica-back.php?oper=get_orden_medicas&pacienteid="+id_paciente,
            "dataSrc": "data.detalle",
        },
        "columns"	: [
            { 	"data": "id",
                render: function(data,type,row){
                    if(row.estatus != '-'){
                            return `
                            <div class="dropdown">
                                <button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
                                    <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
                                </button>
                                <div class="dropdown-menu">
									<a href="controller/orden-medica/reporteordenmedica.php?idreporte=${data}" target="_blank" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Imprimir</a>
                                </div>
                            </div>`;
                    }else{
                        return `-`;
                    }
                }
            },
            { 	"data": "estatus",
                render: function(data,type,row){
                    if(data != '-'){
                        if(data == 0){
                            return`<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-warning">Pendiente</span>`;
                        }else
                        if(data == 1){
                            return`<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-success">Activo</span>`;
                        }else
                        if(data == 2){
                            return`<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-dark">Finalizado</span>`;
                        }
                    }else{
                        return `-`;
                    }
                }
            },
            { 	"data": "creado"
            },
            { 	"data": "created_at"
            },
        ],
        rowId: 'id',
        "columnDefs": [
            {
                "targets"	: [ 0,1 ],
                "width"		:  "5%"
            }
        ],
        "language": {
            "url": "js/Spanish.json",
            "info": "MOSTRANDO PAGINA _PAGE_ DE _PAGES_"
        }
    });


	var tabla_documentos_resultados = $("#tabla-documentos-resultados").DataTable({
		responsive: true,
		scrollCollapse: true,
		"scrollY": "100%",
		"ajax"		:"controller/pacientes/historial-back.php?oper=listar_resultados_laboratorios&id_paciente="+id_paciente,
		"columns"	: [			
			{ 	"data": "id",
				render:function(data,type,row){
					if(data != ''){
						return `
						<div class="dropdown acciones-doc-lab-${data}" >
							<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
								<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
							</button>
							<div class="dropdown-menu">
								<a href="${row.file}" target="_blank" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Abrir</a>
								<a style="cursor: pointer" data-id="${data}" class="dropdown-item editar-documento-lab"  data-id="${row.id}" >Editar</a>
								<a style="cursor: pointer" class="dropdown-item text-danger eliminar-documento"  data-id="${row.id}" onclick="eliminarDocumento(${row.id})">Eliminar</a>
							</div>
							</div>
							<button type="button" class="btn btn-success light sharp guardar-doc-lab-${data}"  data-id="${data}" data-toggle="dropdown" aria-expanded="false" style="display:none">
								<i class="lni lni-save"></i>
							</button>     `;
					}
					return ``;
				}
			},			
			{ 	"data": "fecha",
				render:function(data,type,row){
					if(data !=""){
						return moment(data).format("DD-MMM-YYYY");
					}
					return "";
				}
			},
			{ 	"data": "hospitalizacion" },
			{ 	"data": "motivo" },			
			{ 	"data": "tipo" }
			],
		rowId: 'id', 
		"columnDefs": [
			{
				"targets"	: [ 0,1,2 ],
				"width"		:  "9%"
			},
			{
				"targets"	: [ 4 ],
				"width"		:  "22%"
			}
		],	
		"language": {
			"url": "js/Spanish.json",
		},
		drawCallback: function( settings ) {			
			// $("#tabla-documentos-resultados").DataTable().columns.adjust();
		}
		
	});
	var tablaDocumentos = $("#tablaDocumentos").DataTable({
		responsive: true,
		scrollCollapse: true,
		"scrollY": "100%",
		"ajax"		:"controller/pacientes/historial-back.php?oper=listar_documentos&id_paciente="+id_paciente,
		"columns"	: [			
			{ 	"data": "id",
				render:function(data,type,row){
					if(data != ''){
						return `
						<div class="dropdown acciones-doc-${data}">
							<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
								<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
							</button>
							<div class="dropdown-menu">
								<a href="${row.file}" target="_blank" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Abrir</a>
								<a style="cursor: pointer" data-id="${data}"class="dropdown-item editar-documento"  data-id="${row.id}" >Editar</a>
								<a style="cursor: pointer" class="dropdown-item text-danger eliminar-documento"  data-id="${row.id}" onclick="eliminarDocumento(${row.id})">Eliminar</a>
							</div>
						</div>
						<button type="button" class="btn btn-success light sharp guardar-doc-${data}"  data-id="${data}" data-toggle="dropdown" aria-expanded="false" style="display:none">
							<i class="lni lni-save"></i>
						</button>    `;
					}
					return ``;
				}
			},			
			{ 	"data": "fecha",
				render:function(data,type,row){
					if(data !=""){
						return moment(data).format("DD-MMM-YYYY");
					}
					return "";
				}
			},
			{ 	"data": "hospitalizacion" },
			{ 	"data": "motivo" },			
			{ 	"data": "tipo" }
			],
		rowId: 'id', 
		"columnDefs": [
			{
				"targets"	: [ 0,1,2 ],
				"width"		:  "9%"
			},
			{
				"targets"	: [ 4 ],
				"width"		:  "22%"
			}
		],	
		"language": {
			"url": "js/Spanish.json",
		},
		drawCallback: function( settings ) {			
			// $("#tablaDocumentos").DataTable().columns.adjust();
		}
		
	});


	$("#tabla-documentos-resultados").on("draw.dt",function(){
		$(".editar-documento-lab").off();
		$(".editar-documento-lab").on('click',function(){
			let id = $(this).attr('data-id');
			let reg = $(this).closest('tr');
			let row =tabla_documentos_resultados.row(reg).data();
			let datosJson = row;            
			let campo_motivo =`<input type="text"  id="editar-motivo-p-${id}" class="form-control" value="${row.motivo}">`;        
			let campo_fecha =`<input type="text"  id="editar-fecha-p-${id}" class="form-control" value="${row.fecha}">`;        
			$(this).parent().parent().parent().next().next().next().html(campo_motivo);
			$.when($(this).parent().parent().parent().next().html(campo_fecha)).done( function(){
				$(`#editar-fecha-p-${id}`).daterangepicker({
					"singleDatePicker": true,
					"showDropdowns": true,
					"maxDate": new Date(),
					"minDate": '1900-01-01',
					"autoUpdateInput": false,
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
				$(`#editar-fecha-p-${id}`).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format("YYYY-MM-DD"));
				});
				$(`#editar-fecha-p-${id}`).on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
				});
			})
			$(".acciones-doc-lab-"+id).hide();
			$(".guardar-doc-lab-"+id).show();
			$(".guardar-doc-lab-"+id).off();
			$(".guardar-doc-lab-"+id).on('click',function(){
				$("#overlay").show();
				let fecha= $(`#editar-fecha-p-${id}`).val();
				let motivo= $(`#editar-motivo-p-${id}`).val();
				$.post("controller/pacientes/historial-back.php",{"oper":"editar_documentos","id":id,"fecha":fecha,"motivo":motivo},
					function(response){
						$("#overlay").hide();
						if (!response.error){
							swal("Buen trabajo!",response.mensaje,"success");
							tabla_documentos_resultados.ajax.reload(null,false);
						}else{
							swal("Error!",response.mensaje,"error");
						}
				},"json")
			})
		});
	});
	$("#tablaDocumentos").on("draw.dt",function(){
		$(".editar-documento").off();
		$(".editar-documento").on('click',function(){
			let id = $(this).attr('data-id');
			let reg = $(this).closest('tr');
			let row =tablaDocumentos.row(reg).data();
			let datosJson = row;            
			let campo_motivo =`<input type="text"  id="editar-motivo-d-${id}" class="form-control" value="${row.motivo}">`;        
			let campo_fecha =`<input type="text"  id="editar-fecha-d-${id}" class="form-control" value="${row.fecha}">`;        
			$(this).parent().parent().parent().next().next().next().html(campo_motivo);
			$.when($(this).parent().parent().parent().next().html(campo_fecha)).done( function(){
				$(`#editar-fecha-d-${id}`).daterangepicker({
					"singleDatePicker": true,
					"showDropdowns": true,
					"maxDate": new Date(),
					"minDate": '1900-01-01',
					"autoUpdateInput": false,
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
				$(`#editar-fecha-d-${id}`).on('apply.daterangepicker', function(ev, picker) {
					$(this).val(picker.startDate.format("YYYY-MM-DD"));
				});
				$(`#editar-fecha-d-${id}`).on('cancel.daterangepicker', function(ev, picker) {
					$(this).val('');
				});
			})
			$(".acciones-doc-"+id).hide();
			$(".guardar-doc-"+id).show();
			$(".guardar-doc-"+id).off();
			$(".guardar-doc-"+id).on('click',function(){
				$("#overlay").show();
				let fecha= $(`#editar-fecha-d-${id}`).val();
				let motivo= $(`#editar-motivo-d-${id}`).val();
				$.post("controller/pacientes/historial-back.php",{"oper":"editar_documentos","id":id,"fecha":fecha,"motivo":motivo},
					function(response){
						$("#overlay").hide();
						if (!response.error){
							swal("Buen trabajo!",response.mensaje,"success");
							tablaDocumentos.ajax.reload(null,false);
						}else{
							swal("Error!",response.mensaje,"error");
						}
				},"json")
			})
		});
	})


	$("#Documento_file").on('change',function () {
		let file = $(this).prop('files');		
		let nombre_archivo = document.getElementById('Documento_file').files[0].name;
		$("#label_documento").html(nombre_archivo);
		$("#documento_val").val(file);
	});

	$('#guardar_documento').on('click', function () {
		let motivo = $('#motivodocumento').val();
		let fecha = $('#fecha_documento').val();
		let tipodocumento = $('#tipo_documento').val();
		let file = $('#Documento_file').prop('files');
		var formData = new FormData();
		formData.append("oper", "subir_documento");						
		formData.append("fecha", fecha);						
		formData.append("tipodocumento", tipodocumento);						
		formData.append("motivo", motivo);	
		formData.append("id_paciente", id_paciente);					
		formData.append("file", file[0]);	
		
		$.ajax({
			type: 'post',
			url: "controller/pacientes/historial-back.php",
			data: formData,
			dataType: "json",
			success: function (response) {
				if(!response.error){
					swal("Buen trabajo!", response.mensaje, "success");
					tablaDocumentos.ajax.reload();
					tabla_documentos_resultados.ajax.reload();
				}else{
					swal("Error!", response.mensaje, "error");
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				demo.showSwal('error-message','Error!','Error al guardar imagen');
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function eliminarDocumento(id){
		$.post("controller/pacientes/historial-back.php",{"oper":"eliminar_documento","id":id},function(response){
			if(!response.error){
				swal("Buen trabajo!", response.mensaje, "success");
				tablaDocumentos.ajax.reload();
				tabla_documentos_resultados.ajax.reload();
			}else{
				swal("Error!", response.mensaje, "error");
			}
		},"json")
	}

	$("#tipo_documento").select2();
	$('button[data-id="tipo_documento"]').hide();


	
$('#fecha_documento').daterangepicker({
    "singleDatePicker": true,
    "showDropdowns": true,
   // "maxDate": new Date(),
    "minDate": '1900-01-01',
	"autoUpdateInput": false,
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
$('#fecha_documento').on('apply.daterangepicker', function(ev, picker) {
	$(this).val(picker.startDate.format("YYYY-MM-DD"));
});
$('#fecha_documento').on('cancel.daterangepicker', function(ev, picker) {
	$(this).val('');
});


var tabla_recetas_medicas = $("#tabla-recetas-medicas").DataTable({
	responsive: true,
	scrollCollapse: true,
	"scrollY": "100%",
	"ajax"		:"controller/pacientes/historial-back.php?oper=receta_medica&id_paciente="+id_paciente,
	"columns"	: [			
		{ 	"data": "id",
			render:function(data,type,row){
				if(data != ''){
					return `
					<div class="dropdown">
						<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
							<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
						</button>
						<div class="dropdown-menu">
							<a href="controller/pacientes/imprimir-receta-medica.php?id=${data}" target="_blank" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Imprimir</a>							
						</div>
					</div>`;
				}
				return ``;
			}
		},			
		{ 	"data": "fecha" },
		{ 	"data": "medico" },
		{ 	"data": "receta" }
		],
	rowId: 'id', 
	"columnDefs": [
		{
			"targets"	: [ 0,1 ],
			"width"		:  "9%"
		},
		{
			"targets"	: [ 2 ],
			"width"		:  "22%"
		}
	],	
	"language": {
		"url": "js/Spanish.json",
	},
	drawCallback: function( settings ) {			
		// $("#tabla-recetas-medicas").DataTable().columns.adjust();
	}
	
});

var tabla_notas_evolucion = $("#tabla-notas-evolucion").DataTable({
	responsive: true,
	scrollCollapse: true,
	"scrollY": "100%",
	"ajax"		:"controller/pacientes/historial-back.php?oper=notas_evolucion&id_paciente="+id_paciente,
	"columns"	: [			
		{ 	"data": "id",
			render:function(data,type,row){
				if(data != ''){
					return `Nota-${data}`;
				}
				return ``;
			}
		},			
		{ 	"data": "fecha" },
		{ 	"data": "medico" },
		{ 	"data": "nota" }
		],
	rowId: 'id', 
	"columnDefs": [
		{
			"targets"	: [ 0,1 ],
			"width"		:  "9%"
		},
		{
			"targets"	: [ 2 ],
			"width"		:  "22%"
		}
	],	
	"language": {
		"url": "js/Spanish.json",
	},
	drawCallback: function( settings ) {			
		// $("#tabla-notas-evolucion").DataTable().columns.adjust();
	}
	
});

$("#nota_evolucion").on('click',function(){
    tabla_notas_evolucion.ajax.reload();
    tabla_recetas_medicas.ajax.reload();
})

$("#tabla_documentos").on('click',function(){
    tablaDocumentos.ajax.reload();
    tabla_documentos_resultados.ajax.reload();
})

$("#tarjeta").on('click',function(){
    tablaOrdenesDetalle.ajax.reload();
    tablaTarjetasMedicamentos.ajax.reload();
    tablaOrdenesLaboratorio.ajax.reload();
})

$("#imagen_perfil").on('click',function(){
    $("#archivo-imagen-perfil").click();
});
$("#archivo-imagen-perfil").on('change',function(e){
    //var formData = new FormData($("#nuevo-paciente-dir")[0]);	
    //$("#nombre_imagen").html($('#archivo_direccion')[0].files[0].name);    
    let input = e.target
    if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#imagen_perfil').attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[0]);
		let datos_perfil = new FormData($("#perfil")[0]);
		datos_perfil.append("oper", "subir_foto_perfil");
		datos_perfil.append("id_paciente", id_paciente);
		$.ajax({
			type: 'post',
			url: "controller/pacientes/pacientes-back.php",
			data: datos_perfil,
			dataType: "json",
			success: function (resp) {
				if(!resp.error){                    
					swal("Buen trabajo!",resp.mensaje,"success")
				}else{
					swal("Error",resp.mensaje,"error")
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				swal("Error","Ha ocurrido un error inesperado","error");
			},
			cache: false,
			contentType: false,
			processData: false
		});
    }
});

function actualizar_doc(id,fecha,motivo){
	$.post("controller/pacientes/historial-back.php",{"oper":"editar_documentos","id":id,"fecha":fecha,"motivo":motivo},
	function(response){
		return response;
	},"json")
}

function cargar_graficos(data){
	if (!data.length) return false; 
    grafica_sv('vp_grafica_sv_0',data[0]);
    grafica_sv('vp_grafica_sv_1',data[1]);
    grafica_sv('vp_grafica_sv_2',data[2]);
    grafica_sv('vp_grafica_sv_3',data[3]);
    grafica_sv('vp_grafica_sv_4',data[4]);
    grafica_sv('vp_grafica_sv_5',data[5]);
}
function grafica_sv(div,data){
	if (!data) return false;
    Highcharts.chart(div, {
        chart: {
            type: 'spline'
        },  
        xAxis: {
            categories: data.categorias
        },
        title: {
            text: 'Valores'
        },
        labels: {
            formatter: function () {
                return this.value;
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: data.series
        
        
    });
}
var data_sv = {
    "data":{
        "id" :"",
        "fecha" :"",
        "tipo" :"",
        "recurso" :"",
        "signo_vital" :"",
        "valor" :""    
    }
};
var tabla_signos_vitales = $("#tabla-signos-vitales").DataTable({
    responsive: true,
    lengthChange: true,
    pageLength:10,
    searching: true,
    order: [[1,'desc']],
    data : data_sv,
    "columns"	: [
        {   "data": 'id',
        },
        {   "data": 'fecha',
            render:function(data,type,row){
                if(data != ''){
                    return `${moment(data).format("DD-MMM-YYYY h:mm A")}`;
                }
                return ``;
            }
        },
        {"data": 'tipo'},
        {"data": 'recurso' },
        {"data": 'signo_vital'},
        {"data": 'valor'}
    ],
    "columnDefs": [
        {
            targets	: [ 0 ],
            width		:  "1%",
			visible: false,
			searchable: false,
        }
    ],
    "language": {
        "url": "js/Spanish.json",
        "info": "MOSTRANDO PAGINA _PAGE_ DE _PAGES_"
    }
});
//tabla-signos-vitales-paciente
var data_svp = {
    "data":{
        "id" :"",
        "fecha" :"",        
        "signo_vital" :"",
        "valor" :"" ,   
        "condicion" :""
    }
};
var tabla_signos_vitales_paciente = $("#tabla-signos-vitales-paciente").DataTable({
    responsive: true,
    lengthChange: true,
    pageLength:10,
    searching: true,
    order: [[1,'desc']],
    data : data_svp,
    "columns"	: [
        {   "data": 'id',
        },
        {   "data": 'fecha',
            render:function(data,type,row){
                if(data != ''){
                    return `${moment(data).format("DD-MMM-YYYY h:mm A")}`;
                }
                return ``;
            }
        },
        
        {"data": 'signo_vital'},
        {"data": 'valor'},
        {"data": 'condicion',
            render:function(data,type,row){
                if(data != ''){
                    return `<span style="color:${row.color}">${data}</span>`;
                }
                return '';
            } 
        },
    ],
    "columnDefs": [
        {
            targets	: [ 0 ],
            width		:  "1%",
			visible: false,
			searchable: false,
        }
    ],
    "language": {
        "url": "js/Spanish.json",
        "info": "MOSTRANDO PAGINA _PAGE_ DE _PAGES_"
    }
});
function recargar_grafico(id_hospitalizacion,_inicio,_fin){
	$("#overlay").show();
	const inicio = _inicio != false ? _inicio : moment().subtract(7, 'days').format('YYYY-MM-DD');
	const fin = _fin != false ? _fin : moment().format('YYYY-MM-DD');
	$("#rango-sv-paciente").val(moment(inicio).format('DD-MMM-YYYY')+' al '+moment(fin).format('DD-MMM-YYYY'));
    $.get("controller/gestion-visitas/reporte-salud-back.php",
    {
        "oper":"data_reporte",
        "id_hospitalizacion": id_hospitalizacion,
        "inicio": inicio,
        "fin": fin
    },function(data){
        cargar_graficos(data.data_grafica);

		// limpia y dibuja la tabla
		tabla_signos_vitales.clear().draw();
        tabla_signos_vitales.rows.add(data.data_sv).draw();
		tabla_signos_vitales.columns.adjust().draw(); 

        tabla_signos_vitales_paciente.clear().draw();
        tabla_signos_vitales_paciente.rows.add(data.data_svp).draw();
		tabla_signos_vitales_paciente.columns.adjust().draw();
    },"json").then(()=> $("#overlay").hide());
}

$("#div-tabla-sv,#div-tabla-svp").on('click',function(){  
    setTimeout(() => {
        tabla_signos_vitales.columns.adjust().draw();
		tabla_signos_vitales_paciente.columns.adjust().draw();
    }, 100);
});

document.addEventListener("DOMContentLoaded",()=>{
	$('#rango-sv-paciente').daterangepicker({
        "showDropdowns": true,
        "locale": {
            "maxDate": new Date(),
            "format": "DD-MMM-YYYY",
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
	$("#rango-sv-paciente").on('change',function (evento) {
		event.preventDefault();
		if(!evento.target.value || evento.target.value == 'Invalid date') return false;
		let fecha_separada = evento.target.value.split(" ");
		const inicio = moment( fecha_separada[0],'DD-MMM-YYYY').format('YYYY-MM-DD');
		const fin = moment( fecha_separada[2],'DD-MMM-YYYY').format('YYYY-MM-DD');
		recargar_grafico(id_hospit_global,inicio,fin);
	});

});

var tablaOrdenesLaboratorio = $("#tabla-ordenes-laboratorios").DataTable({
	responsive: true,
	"ajax"		: "controller/ordenes-laboratorios/ordenes-laboratorios-back.php?oper=getOrdenes&id_paciente="+id_paciente,
	"columns"	: [
		{"data":"id",
			render: function(data,type,row){
				if(data!= ''){
					let imprimir = `<a  target="_blank" href="controller/ordenes-laboratorios/imprimir-orden-lab.php?idOrdenLab=${data}" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Imprimir</a>`;
					let cargar_resultados = ``;
					let editar = ``
					let eliminar = ``;
					let ver_resultados = ``;
					if(row.documentos >= 1){
						ver_resultados = `<a style="cursor: pointer" class="dropdown-item ver-resultados"  data-id="${row.id}" >Ver resultados</a>`
					}
					if(parseInt(row.documentos) < parseInt(row.detalles)){
						cargar_resultados = `<a style="cursor: pointer" class="dropdown-item cargar-resultados"  data-id="${row.id}" >Cargar resultados</a>`
					}
					if(row.estado == 64){
						editar  = `<a href="orden-laboratorio-editar.php?idOrdenLab=${row.id}" style="cursor: pointer" class="dropdown-item"  data-id="${row.id}">Editar</a>`;
						eliminar = `<a style="cursor: pointer" onclick="eliminarOrdenLaboratorio(${row.id})" class="dropdown-item text-danger"  data-id="${row.id}" id="eliminar-orden-lab">Eliminar</a>`;
					}
					if(row.estado == 65) cargar_resultados = ``;
					return `<div class="dropdown">
								<button type="button" class="btn btn-success light sharp" data-toggle="dropdown" aria-expanded="false">
									<svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg>
								</button>                                        
								<div class="dropdown-menu">
									${imprimir}
								</div>
							</div>
					`;
				}
				return ``;
			}
		},
		{"data":"id"},
		{"data":"estado",
			render: function(data,type,row){
				if(data != ''){
					let rechazar =``;
					let completar= ``;
					let estado = ``;
					switch(data){
						case "62":
							estado = `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-success " data-id="${row.id}">Parcial</span>`;
							rechazar =`<a class="dropdown-item rechazar-orden-lab" href="#"  data-id="${row.id}"><b class="text-danger">Rechazar</b></a>`
							completar = `<a class="dropdown-item finalizar-orden-lab" href="#"  data-id="${row.id}"><b class="text-dark">Completar</b></a>`;
						break;
						case "63":
							estado = `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-danger " data-id="${row.id}">Rechazada</span>`;
						break;
						case "64":
							estado = `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-warning " data-id="${row.id}">Pendiente</span>`;
							rechazar =`<a class="dropdown-item rechazar-orden-lab" href="#"  data-id="${row.id}"><b class="text-danger">Rechazar</b></a>`
						break;
						case "65":
							estado = `<span style="text-transform: capitalize; cursor: pointer; width:100%" class="badge badge-md badge-dark " data-id="${row.id}">Completa</span>`;
						break;
					}
					return `${estado}`; 
				}
				return ``;
			}
		},
		{"data":"fechaMuestra"},
		{"data":"laboratorio"},
		{"data":"creador"},
		{"data":"fecha"}
	],
	rowId: 'id',
	"autoWidth": false, // para que
	"columnDefs": [
		{
			"targets"	: [ 0 ],
			"width"		:  "2%"
		},
	],
	"language": {
		"url": "js/Spanish.json",
		"info": "MOSTRANDO PAGINA _PAGE_ DE _PAGES_"
	}
});


$("#rango-fechas").daterangepicker({
    "showDropdowns": true,
    "minDate": '2018-01-01',
    "locale": {
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
var inicio, fin;
$("#btn-seleccionar-fechas").on('click',function(){
    let rango = $("#rango-fechas").val().split(' al ')
    inicio = moment(rango[0]).format("YYYY-MM-DD");    
    fin= moment(rango[1]).format("YYYY-MM-DD");
	window.open(`controller/pacientes/imprimir-historial-paciente.php?id_paciente=${id_paciente}&inicio=${inicio}&fin=${fin}`)
	$("#modal-fechas").modal('hide');
})