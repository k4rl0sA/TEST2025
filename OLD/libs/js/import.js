document.addEventListener('DOMContentLoaded', function() {
/******************START IMPORT***********************/
const modal = document.getElementById('modal'),
	openModalBtn = document.getElementById('openModal'),
	cancelLoadingBtn = document.getElementById('cancelLoading'),
	closeModalBtn = document.getElementById('closeModal'),
	progressBar = document.getElementById('progressBar'),
	progressText = document.getElementById('progressText'),
	statusMessage = document.getElementById('statusMessage'),
	fileName = document.getElementById('file-name'),
	fileInput = document.getElementById('fileInput');

	let loading = false,progress = 0;
	

fileInput.addEventListener('change', (event) => {
    const files = event.target.files;
    showFileName(files);
});

const showFileName = (files) => {
    if (files.length) {
        fileName.textContent = `Archivo : ${files[0].name}`;
    } else {
        fileName.textContent = 'Selecciona un archivo aquí';
    }
};

function cancelLoading() {
	loading = false;
	resetProgress();
	statusMessage.textContent = 'Carga cancelada por el usuario.';
}
function resetProgress() {
	progress = 0;
	updateProgress(progress);
	fileInput.value = '';
	progressBar.style.width=0;
	progressText.textContent='0% Completado'
	// startLoadingBtn.style.display = 'inline-block';
	cancelLoadingBtn.style.display = 'none';
	closeModalBtn.style.display = 'none';
}



openModalBtn.onclick = () => {
	modal.style.display = "block";
    closeModalBtn.style.display = "block";
};

closeModalBtn.onclick = () => {
	
	fileName.textContent='Selecciona un archivo aquí';
	modal.style.display = "none";
    statusMessage.textContent ='';
    resetProgress();
};

cancelLoadingBtn.onclick = cancelLoading;

/* const observer = new MutationObserver(() => {
	if (statusMessage.textContent.trim() !== "") {
		statusMessage.classList.add('has-cont');
	} else {
		statusMessage.classList.remove('has-cont');
	}
});

observer.observe(statusMessage, { childList: true, subtree: true }); */
});

function startImport(file,ncol,tab,imp) {
	const formData = new FormData();
	formData.append('archivo', file);
	formData.append('ncol', ncol);
	formData.append('tab', tab);

	fetch(imp, {
		method: 'POST',
		body: formData
	})
	.then(response => response.body)
	.then(body => {
		const reader = body.getReader();
		const decoder = new TextDecoder("utf-8");
		let buffer = '';

		function processText({ done, value }) {
			if (done) {
				console.log("Carga completada.");
				return;
			}

			buffer += decoder.decode(value, { stream: true });
			let parts = buffer.split('\n');
			buffer = parts.pop();
			const errorsAll = []; 
			let endInd = parts.length - 1;
			parts.forEach((part, index) => {
				if (part.trim()) {
					try {
						const json = JSON.parse(part);
						console.log('JSON recibido:', json);
						handleServerResponse(json, errorsAll, index, endInd);
					} catch (error) {
						console.error("Error al procesar JSON:", error, part);
					}
				}
			});
			reader.read().then(processText);
		}
		reader.read().then(processText);
	})
	.catch(error => {
		console.error('Error:', error);
		statusMessage.textContent = `Ocurrió un error: ${error.message}`;
	});
}

function handleServerResponse(json, errorsAll, index, endInd) {
	if (json.status === 'progress') {
		updateProgress(json.progress);
		statusMessage.textContent = json.errors;
		if (json.errors) errorsAll.push(json.errors);

	} else if (json.status === 'success') {
		updateProgress(json.progress);
		statusMessage.textContent = json.message;
		if (json.errors) errorsAll.push(json.errors);
		// closeModalBtn.style.display = 'inline-block';

	} else if (json.status === 'error') {
		if (json.errors) errorsAll.push(json.errors);
	}

	if (index === endInd) {
		statusMessage.innerHTML += "<br><br>Errores:<br>" + errorsAll.join("<br>");
	}
}

function updateProgress(newProgress) {
    let currentProgress = parseInt(progressBar.style.width) || 0;
    function updateGradually() {
        if (currentProgress < newProgress) {
            currentProgress += 1;
            progressBar.style.width = `${currentProgress}%`;
            progressText.textContent = `${currentProgress}% completado`;
            setTimeout(updateGradually, 50);
        }
    }
    updateGradually();
}


/******************END IMPORT************************/