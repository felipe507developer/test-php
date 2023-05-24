(function($) {
	
	var direction =  getUrlParams('dir');
	if(direction != 'rtl')
	{direction = 'ltr'; }

	var dezSettingsOptions = {
		typography: "poppins", //Todas las opciones => ["poppins" , "roboto" , "Open Sans" , "Helventivca" ]
		version: "light", //Todas las opciones => ["light" , "dark"]
		layout: "vertical", //Todas las opciones => ["horizontal" , "vertical"]
		headerBg: "color_1", //Todas las opciones => ["color_1," , "color_2," ..... "color_15"]
		navheaderBg: "color_1", //Todas las opciones => ["color_1," , "color_2," ..... "color_15"]
		sidebarBg: "color_1", //Todas las opciones => ["color_1," , "color_2," ..... "color_15"]
		sidebarStyle: "full", //Todas las opciones => ["full" , "mini" , "compact" , "modern" , "overlay" , "icon-hover"]
		sidebarPosition: "fixed", //Todas las opciones => ["static" , "fixed"]
		headerPosition: "fixed", //Todas las opciones => ["static" , "fixed"]
		containerLayout: "full", //Todas las opciones => ["full" , "wide" , "wide-box"]
		direction: direction //Todas las opciones => ["ltr" , "rtl"]
	};
		
	new dezSettings(dezSettingsOptions); 

	jQuery(window).on('resize',function(){
		new dezSettings(dezSettingsOptions); 
	});

})(jQuery);

// (function($) {
	
// 	var direction =  getUrlParams('dir');
// 	if(direction != 'rtl')
// 	{direction = 'ltr'; }

// 	var dezSettingsOptions = {
// 		typography: "poppins",
// 		version: "light",
// 		layout: "Vertical",
// 		headerBg: "color_1",
// 		navheaderBg: "color_1",
// 		sidebarBg: "color_1",
// 		sidebarStyle: "full",
// 		sidebarPosition: "fixed",
// 		headerPosition: "fixed",
// 		containerLayout: "full",
// 		direction: direction
// 	};
		
// 	new dezSettings(dezSettingsOptions); 

// 	jQuery(window).on('resize',function(){
// 		new dezSettings(dezSettingsOptions); 
// 	});

// })(jQuery);