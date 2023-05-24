
function regiones(){
	$("#regiones").click();
}
$(document).ready(function() {
$(".select-region").each(function(){
	$(this).on('click',function(){
		let bd = $(this).attr('data-bd');
		$.get("controller/conexion_bd.php",{"oper":"cambiar_bd","bd":bd},function(r){
			if(r.success==1){
				localStorage.setItem('filtros',r.filtros)
				localStorage.setItem('latitud',r.latitud)
				localStorage.setItem('longitud',r.longitud)
				$.when(swal("Buen trabajo","Región cambiada exitosamente","success")).done(function(){
				location.reload();
				});
			}else{
				swal("Error","Ha ocurrido un error","error");
				location.href = "index.php?opcion=LO";
			}
		},'json')
	});
});
$(document).ready(function(){
	filtros();
	function filtros(){ 
		if(localStorage.getItem('filtros') != ''){
			let dnone = '';
			switch(parseInt(localStorage.getItem('nivel'))){
				case 2: case 5: case 21: case 22:
					dnone = 'd-none';
				break;
			}
			let json = JSON.parse(localStorage.getItem('filtros'));	
			let empresa= getCookie('empresa');			
			let organizacion= getCookie('organizacion');	
			let option_org = '';
			let nombre_organizacion_empresa = '';
			let option_emp ='';
			let color ='';
			$.map(json,function(row){
				if(organizacion == row.id){
					if(empresa == 0){
						nombre_organizacion_empresa = row.nombre;
						color = "text-dark";
					}
					option_org +='<option selected value="'+row.id+'">'+row.nombre+'</option>';						
					$.map(row.empresas,function(emp){	
						if(empresa == emp.id){			
							if(empresa != 0){
								nombre_organizacion_empresa = emp.nombre;
								color = "text-info";
							}
							option_emp +='<option selected value="'+emp.id+'" data-dashboard="'+emp.dashboard_phm+'" data-reporte_diario="'+emp.reporte_diario+'" data-hosp_automatica="'+emp.hospitalizacion_automatica+'">'+emp.nombre+'</option>';
							localStorage.setItem('empresa_txt',emp.nombre);
							$("#empresa_txt").html(emp.nombre);
						}else{
							option_emp +='<option  value="'+emp.id+'" data-dashboard="'+emp.dashboard_phm+'" data-reporte_diario="'+emp.reporte_diario+'" data-hosp_automatica="'+emp.hospitalizacion_automatica+'">'+emp.nombre+'</option>';
						}
					});	
				}else{		
					option_org +='<option value="'+row.id+'">'+row.nombre+'</option>';						
				}
			});
			const html_empresa=`<li class="nav-item dropdown notification_dropdown"  id="li-filtros-globales">
									<a href="#" class="dropdown-item ai-icon config-link">
										<i class="lni lni-apartment"></i>
										<span class="ml-2" id="nombre-empresa-organizacion"></span>
									</a>
								</li>`;
			$("#li-filtros-globales").addClass(dnone);
			$("#nombre-empresa-organizacion").html(nombre_organizacion_empresa);
			$("#nombre-empresa-organizacion").addClass(color);
			
			$("#select-empresa").empty();
			$("#select-empresa").append(option_emp);
			$.when($("#select-organizacion").append(option_org)).done(function(){
				$('#select-organizacion').on('change',function(){		
					let json = JSON.parse(localStorage.getItem('filtros'));	

					option_emp ='';
					let id_org = $(this).val();
					$.map(json,function(row){
						if(row.id == id_org){
							$.map(row.empresas,function(emp){
								option_emp +='<option value="'+emp.id+'" data-dashboard="'+emp.dashboard_phm+'" data-reporte_diario="'+emp.reporte_diario+'" data-hosp_automatica="'+emp.hospitalizacion_automatica+'">'+emp.nombre+'</option>';
							});
						}
					});
					$("#select-empresa").empty();
					$("#select-empresa").append(option_emp);
				});
				// $("#select-empresa").select2();
				$('button[data-id="select-empresa"]').hide();
			});
			let lista_ambientes = getCookie('lista_ambientes');
			// $("#select-region").select2();
			$("#select-region").append(lista_ambientes);
		}
	}	

});
$('#select-organizacion').on('change',function(){		
	let json = JSON.parse(localStorage.getItem('filtros'));	

	option_emp ='';
	let id_org = $(this).val();
	$.map(json,function(row){
		if(row.id == id_org){
			$.map(row.empresas,function(emp){
				option_emp +='<option value="'+emp.id+'" data-dashboard="'+emp.dashboard_phm+'" data-reporte_diario="'+emp.reporte_diario+'" data-hosp_automatica="'+emp.hospitalizacion_automatica+'">'+emp.nombre+'</option>';
			});
		}
	});
	$("#select-empresa").empty();
	$("#select-empresa").append(option_emp);
});
$("#select-empresa").select2();
$("#select-organizacion").select2();
$('button[data-id="select-empresa"]').hide();
$('button[data-id="select-organizacion"]').hide();


$("#boton-aplicar-filtros-globales").on('click',function(){
	console.log('click');
	let id_org = $("#select-organizacion").val();
	let id_emp = $("#select-empresa").val();
	let dashboard_phm = $("#select-empresa").attr('data-dashboard');
	let reporte_diario = $("#select-empresa").attr('data-reporte_diario');
	let hospitalizacion_automatica = $("#select-empresa").attr('data-hosp_automatica');
	setCookie('organizacion',id_org);
	setCookie('empresa',id_emp);
	setCookie('dashboard_phm',dashboard_phm);
	setCookie('reporte_diario',reporte_diario);
	setCookie('hospitalizacion_automatica',hospitalizacion_automatica);
	location.reload();
})

});