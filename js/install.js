//let installButton = document.getElementById('appInstall');
	var deferredPrompt ;
	var btnAdd = document.getElementById('appInstall') ;
	launchPromptPwa();
	function launchPromptPwa(){
		var deferredPrompt;

		btnAdd = document.getElementById('appInstall') ;

		window.addEventListener('beforeinstallprompt',  (e) => {
			console.log('0');
			// Prevent Chrome 67 and earlier from automatically showing the prompt
			e.preventDefault();
			// Stash the event so it can be triggered later.
			deferredPrompt = e;
			btnAdd.style.display = "block";
			showAddToHomeScreen();
		});

		btnAdd.addEventListener('click', (e) => {
			console.log('1');
			//btnAdd.style.display = 'none';
			//Show the prompt
			deferredPrompt.prompt();
			// Wait for the user to respond to the prompt
			deferredPrompt.userChoice
				.then((choiceResult) => {
					if (choiceResult.outcome === 'accepted') {
						console.log('User accepted the A2HS prompt');
					} else {
						console.log('User dismissed the A2HS prompt');
					}
					deferredPrompt = null;
				});
		});


		window.addEventListener('appinstalled', (evt) => {
			console.log('a2hs installed');
			btnAdd.style.display = "none";

		});

		if (window.matchMedia('(display-mode: standalone)').matches) {
			console.log('display-mode is standalone');
		}

	}
	
	if('serviceWorker' in navigator){
		console.log(1);
		window.addEventListener('load',()=>{
			console.log(2);
			navigator.serviceWorker.register('service-worker.js').then((reg)=>{
				console.log("Service Worker registered", reg);
			})
		})
	}