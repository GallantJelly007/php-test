window.addEventListener('load',()=>{
    let authButton = document.getElementById('auth-button');
    if(authButton) authButton.addEventListener('click',authorization);
    let regButton = document.getElementById('reg-button');
    if(regButton) regButton.addEventListener('click',register);
})

let timeout

function authorization(){
	clearTimeout(timeout)
    let load = document.getElementById('auth-panel-load')
	let resultMessage = document.getElementById('auth-result')
	resultMessage.classList.add('d-none')
    load.classList.remove('d-none')
    jax.post(window.location+'auth',{
        data:'auth-panel'
    }).then(result=>{
		load.classList.add('d-none')
        if(result.data.success==0){
           resultMessage.classList.add('result-error')
           resultMessage.classList.remove('d-none')
           resultMessage.textContent = result.data.message
		   timeout = setTimeout(()=>resultMessage.classList.add('d-none'),5000)
        }else{
			window.location.reload();
		}
    }).catch(err=>{
        load.classList.add('d-none')
        console.error(err)
    })
}


function register(){
	clearTimeout(timeout)
	let load = document.getElementById('auth-panel-load')
	let resultMessage = document.getElementById('reg-result')
	resultMessage.classList.add('d-none')
    load.classList.remove('d-none')
    jax.post(window.location+'registration',{
        data:'reg-panel'
    }).then(result=>{
		load.classList.add('d-none')
		if(result.data.success==1)
			resultMessage.classList.add('result-success')
		else
			resultMessage.classList.add('result-error')
		resultMessage.classList.remove('d-none')
		resultMessage.textContent = result.data.message
		let panel = document.getElementById('reg-panel')
		let inputs = panel.getElementsByTagName('input')
		for(let input of inputs){
			if(input.type!='checkbox'&&input.type!='radio')
				input.value=''
			else
				input.checked=false
		}
		for(let key of arrChecks.keys()){
			arrChecks.set(key,false)
		}
		let regButton = document.getElementById('reg-button')
		regButton.disabled=true
		timeout = setTimeout(()=>resultMessage.classList.add('d-none'),5000)
    }).catch(err=>{
		load.classList.add('d-none')
		console.error(err)
	})
}

function saveRequest(obj,successCallback,errorCallback){
	let nameInput = obj.querySelector('.obj-name')
	let descInput = obj.querySelector('.obj-desc')
	let params= {
		title: nameInput.textContent,
		description: descInput.textContent
	}
	if(obj.hasAttribute('data-id')){
		params.id = obj.getAttribute('data-id')
	}
	if(obj.parentElement.hasAttribute('data-parent-id')){
		params.parentId = obj.parentElement.getAttribute('data-parent-id')
	}
	let load = obj.querySelector('.load-cont')
	if(load!=null) load.classList.remove('d-none')
	jax.post(window.location+'save-object',{
        data:params
    }).then(result=>{
		let status = load.querySelector('.status')
		let loadAnim = load.querySelector('.load')
		if(status!=null&&loadAnim!=null){
			loadAnim.classList.add('d-none')
			if(result.data.success==1){
				status.classList.add('success')
				status.textContent = "Успешно"
				if(successCallback)
					successCallback(result.data)
			}else{
				status.classList.add('error')
				status.textContent = "Ошибка"
				if(errorCallback)
					errorCallback(result.data)
			}
			setTimeout(()=>{
				load.classList.add('d-none')
				status.classList.remove('error')
				status.classList.remove('success')
				status.textContent = "Сохранение"
				loadAnim.classList.remove('d-none')
			},5000)
		}
		
    }).catch(err=>{
		if(load!=null) load.classList.add('d-none')
		console.error(err)
	})
}

function deleteRequest(obj,successCallback,errorCallback){
	if(obj.hasAttribute('data-id')){
		params = {
			id:obj.getAttribute('data-id')
		}
		jax.post(window.location+'delete-object',{
			data:params
		}).then(result=>{
			if(result.data.success==1){
				if(successCallback)
					successCallback(result.data)
			}else{
				if(errorCallback)
					errorCallback(result.data)	
			}
		}).catch(err=>{
			if(load!=null) load.classList.add('d-none')
			console.error(err)
		})
	}
}