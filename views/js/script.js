let regular = new Map([
  ['name', /[A-zА-я-Ё-ё]{2,30}$/],
  ['telephone', /^(\+)([- _():=+]?\d[- _():=+]?){11,14}(\s*)?$/],
  ['email', /^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/],
  ['login',/^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$/],
  ['pass',/^[^А-Яа-яЁё]{8,20}$/]
  ])
let regularError = new Map([
    ['name', 'Только русские или латинские символы от 2 до 30'],
    ['telephone', 'Введите номер в формате +ХХХХХХХХХХХ'],
    ['email', 'Неверный email'],
    ['login','Логин должен состоять из латинских цифр и букв'],
    ['pass','От 8 до 20 символов A-z, 0-9 и спец.символы']
    ])
let arrChecks

window.addEventListener('load',()=>{
	let switchButtons=document.getElementsByClassName('switch-menu-item');
	let popupButtons = document.getElementsByClassName('popup-button')
	for(let button of popupButtons){
		button.addEventListener('click',togglePopup)
		button.addEventListener('afteractive',()=>{
			let bg = document.getElementsByClassName('popup-back')[0]
			if(bg!=undefined&&bg.classList.contains("popup-back-active")){
				let switchMenus=bg.getElementsByClassName('switch-menu')
				for(let menu of switchMenus){
					let hr = menu.getElementsByClassName('switch-h-hr')[0]
					let rect=menu.children[0].getBoundingClientRect()
					hr.style.width=rect.width+"px"
				}
			}
		})
	}
	for(let button of switchButtons){
		button.addEventListener('click',switchMenu)
		button.addEventListener('afterswitch',(event)=>{
			let listSwitchHMenu=document.getElementsByClassName('switch-menu')
			for(let menu of listSwitchHMenu){
				let hr = menu.getElementsByClassName('switch-h-hr')[0]
				if(hr!=null && hr!=undefined){
					let rect = menu.getBoundingClientRect()
					let rectItem=event.target.getBoundingClientRect()
					let rectHr=hr.getBoundingClientRect()
					hr.style.left=(rectItem.left-rect.left)+"px"
					hr.style.width=rectItem.width+"px"
				}
			}
		})					
	}
	arrChecks = new Map()
	let regPanel = document.getElementById('reg-panel')
	if(regPanel){
		let inputsReg = regPanel.getElementsByTagName('input')
		for(let input of inputsReg){
			input.addEventListener('input',(ev)=>isCheckedInput(ev,'reg-panel','reg-button'))
			arrChecks.set(input.name,false)
    	} 
	}
	
    let deploys = document.getElementsByClassName('deploy')
    for(let item of deploys){
    	let button = item.querySelector('.deploy-btn')
    	if(button)
    		button.addEventListener('click',(ev)=>deployment(ev,item))
    }
    let objs = document.getElementsByClassName('obj-content')
    for(let obj of objs){
		if(obj.querySelector('.select-obj')!=null)
    		obj.addEventListener('click',selectObj)
		else if(obj.classList.contains('obj-content-user'))
			obj.addEventListener('click',selectObjUser)
    }
    let createRootBtn = document.getElementById('create-root')
	if(createRootBtn) createRootBtn.addEventListener('click',(ev)=>createObj(ev,true))
 
    let createButtons = document.getElementsByClassName('create')
    for(let button of createButtons){
    	button.addEventListener('click',createObj)
    }
    let editButtons = document.getElementsByClassName('edit')
    for(let button of editButtons){
    	button.addEventListener('click',editObj)
    }
    let deleteButtons = document.getElementsByClassName('delete')
    for(let button of deleteButtons){
    	button.addEventListener('click',deleteObj)
    }
})
window.addEventListener('resize',()=>{
	let listSwitchHMenu=document.getElementsByClassName('switch-menu')
	for(let menu of listSwitchHMenu){
		let hr = menu.getElementsByClassName('switch-h-hr')[0]
		if(hr!=null && hr!=undefined){
			let activeBtn = menu.querySelector('.active-switch-menu-item')
			let rect = menu.getBoundingClientRect()
			let rectItem=activeBtn.getBoundingClientRect()
			let rectHr=hr.getBoundingClientRect()
			hr.style.left=(rectItem.left-rect.left)+"px"
			hr.style.width=rectItem.width+"px"
		}
	}
})

function togglePopup(event,id=null){
	let popup=null
	let bg = document.getElementsByClassName('popup-back')
	if(id!=null){
		popup = document.getElementById(id)
	}else if(event!=null){
		if(event.target.hasAttribute('data-target'))
			popup = document.getElementById(event.target.getAttribute('data-target'))
	}
	if(popup!=null){
		if(bg.length>0) bg=bg[0]
		else return
		if(bg.classList.contains("popup-back-active")){
			bg.classList.remove("popup-back-opacity")
			setTimeout(()=>{
				bg.classList.remove("popup-back-active")
				popup.classList.add('d-none')
			},1000);  
		}else{
			popup.classList.remove('d-none')
			bg.classList.add("popup-back-active")
			bg.classList.add("popup-back-opacity")
			let newEvent = new Event('afteractive')
			event.currentTarget.dispatchEvent(newEvent)
		} 
	}else{
		return
	}	
}

function switchMenu(event){
	let button = event.currentTarget;
	if(!button.parentElement.hasAttribute('data-menu-area'))
		return
	let menuItems = button.parentElement.children
	for(let item of menuItems){
		if(item!=button&&item.classList.contains('active-switch-menu-item')){
			let newEvent = new Event('benonactive')
			item.dispatchEvent(newEvent)
		}
		if(item == button)
			item.classList.add('active-switch-menu-item')
		else
			item.classList.remove('active-switch-menu-item')
	}
	let areas = button.parentElement.getAttribute('data-menu-area').split(' ')
	for(let id of areas){
		let container = document.getElementById(id)
		let listMenuBlock = [];
		for(let item of container.children){
			if(item.classList.contains('menu-block')){
				listMenuBlock.push(item)
			}
		}
		if(!button.hasAttribute('data-panel'))
			return
		let listIds=button.getAttribute('data-panel').split(' ')
		for(i=0;i<listMenuBlock.length;i++){
			let check=false;
			for(let itemId of listIds){
				if(itemId==listMenuBlock[i].id){
					listMenuBlock[i].classList.remove("d-none")
					check=true
					break
				}
			}
			if(!check) listMenuBlock[i].classList.add("d-none")
		}
		let newEvent = new Event('afterswitch')
		button.dispatchEvent(newEvent)
	}
}

function isCheckedInput(target,formId,submitButtonId){
    let form = document.getElementById(formId);
    let button = document.getElementById(submitButtonId);
    let input=target instanceof HTMLElement?target:target.currentTarget
    if(!input) throw new Error('Input element not found')

    let listWarning = Array.from(form.getElementsByClassName('warning'))
    switch(input.tagName.toLowerCase()){
        case 'input':{
        	if(input.type=='checkbox'||input.type=='radio'){
        		if(Array.from(arrChecks.keys()).indexOf(input.name)!=-1){
        			if(input.hasAttribute('data-required'))
        				arrChecks.set(input.name,input.checked)
        			else
        				arrChecks.delete(input.name)
        		}
        	}else{
        		let regName = Array.from(regular.keys()).find(el=>new RegExp(el).test(input.name))
        		let isContains = Array.from(arrChecks.keys()).indexOf(input.name)!=-1
        		if(regName&&isContains){
        			let reg = regular.get(regName);
        			let warning = listWarning.find((el)=>el.hasAttribute('data-target')&&el.getAttribute('data-target')==input.name)
        			if(warning){
        				if(!(reg.test(input.value))){
        					warning.textContent = regularError.get(regName)
        					arrChecks.set(input.name,false)
        				}else{
        					warning.textContent=''
        					arrChecks.set(input.name,true);
        				}
        				if(input.hasAttribute('data-relative')){
        					let relative = document.getElementById(input.getAttribute('data-relative'))
        					let relWarning = listWarning.find((el)=>el.hasAttribute('data-target')&&el.getAttribute('data-target')==relative.name)
        					if(relWarning){
        						if(relative.value!=input.value){
        							relWarning.textContent = `Поля ${input.placeholder} и ${relative.placeholder} не совпадают!`
        							arrChecks.set(relative.name,false)
        						}else{
        							relWarning.textContent = ''
        							arrChecks.set(relative.name,true)
        						}
        					}
        				}
        			}
        		}
        	}
            break
        }
        case 'select':{
            if(input.options[input.options.selectedIndex].value!="") arrChecks.set(input.name,true);
            break
        }
    }

    let count=0;
    for(let item of arrChecks.values()){
        if(item==true) count++;
    }
    if(count==arrChecks.size) button.disabled=false;
    else button.disabled=true; 
}

function deployment(ev,content){
	let rootCont = content
	let deployBtn = ev.currentTarget
	deployBtn.classList.toggle('deploy-btn-active')
	let objCont = rootCont.querySelector('.obj-container')
	objCont.classList.toggle('d-none')
}

function selectObj(event){
	let checks = document.querySelectorAll('.obj-content .select-obj:checked')
	let currentCheck = event.currentTarget.querySelector('.select-obj')
	if(!checks.length){
		adminBtns=document.querySelectorAll('#nav-admin-btns>.admin-button')
		for(let button of adminBtns){
			button.disabled=false
		}
	}else{
		for(let check of checks){
			check.checked=false
		}
	}
	currentCheck.checked=true
}

function getSelectedObj(btnSelector){
	let items = document.getElementsByClassName('obj-item')
	let currentObj = undefined
	for(let item of items){
		if(item.querySelector(btnSelector)==event.currentTarget){
			currentObj=item
			break
		}
	}
	if(!currentObj){
		for(let item of items){
			if(item.querySelector('.obj-content .select-obj:checked')!=null){
				currentObj=item
				break
			}
		}
	}
	return currentObj
}

function findContainer(event,root=false){
	let rootCont=undefined;
	if(root){
		rootCont = document.getElementById('root-container')
		if(!rootCont){
			let contentCont=document.getElementById('content-cont')
			while(contentCont.children.length){
				contentCont.removeChild(contentCont.children[0])
			}
			rootCont = document.createElement('ul')
			rootCont.className = 'obj-container pos-rel'
			rootCont.id = 'root-container'
			contentCont.appendChild(rootCont)
		}
	}else{
		let items = document.getElementsByClassName('obj-item')
		let currentObj=getSelectedObj('.create')
		if(currentObj){
			if(!currentObj.getElementsByClassName('obj-container').length){
				let cont = document.createElement('ul')
				cont.className='obj-container pos-rel d-none'
				cont.setAttribute('data-parent-id',currentObj.getAttribute('data-id'))
				currentObj.appendChild(cont)
				currentObj.classList.add('deploy')
				let managePanel = currentObj.querySelector('.manage-panel')
				let deployBtn = document.createElement('button')
				let deploySpan = document.createElement('span')
				deploySpan.innerHTML='&#10095;'
				deployBtn.appendChild(deploySpan)
				deployBtn.className='deploy-btn ml-20'
				managePanel.appendChild(deployBtn)
				deployBtn.addEventListener('click',(ev)=>deployment(ev,currentObj))
				deployBtn.dispatchEvent(new Event('click'))
				rootCont=cont
			}else{
				rootCont = currentObj.getElementsByClassName('obj-container')[0]
				let deployBtn = currentObj.querySelector('.deploy-btn:not(.deploy-btn-active)')
				if(deployBtn!=null)
					deployBtn.dispatchEvent(new Event('click'))
			}
		}
	}
	return rootCont
}

function createObj(event,root=false){
	let rootCont=findContainer(event,root)
	if(rootCont){
		let item = document.createElement('li')
		item.className='obj-item pos-rel'
		let content = document.createElement('div')
		content.className = 'obj-content p-10 maxw-70 maxw-m-100 d-flex fd-col'
		let name = document.createElement('input')
		name.className='obj-name mb-05 w-100'
		name.type="text"
		name.placeholder="Имя объекта"
		let desc = document.createElement('textarea')
		desc.className='obj-desc w-100'
		desc.placeholder="Описание объекта"
		content.appendChild(name)
		content.appendChild(desc)
		let buttons = createEditButtons()
		content.appendChild(buttons.container)
		item.appendChild(content)
		rootCont.appendChild(item)
		let rect=item.getBoundingClientRect()
		window.scrollTo(0,rect.top+window.scrollY-window.screen.height/2)
		buttons.cancel.addEventListener('click',()=>{
			rootCont.removeChild(item)
			checkChildren(rootCont)
		})
		buttons.save.addEventListener('click',(ev)=>saveObj(ev,item,true))
	}
}

function createEditButtons(){
	let buttonCont = document.createElement('div')
	buttonCont.className='mt-10 d-flex jc-bspace ai-center edit-buttons'
	let buttonSave = document.createElement('button')
	let buttonCancel = document.createElement('button')
	buttonSave.textContent='Сохранить'
	buttonSave.className='button fill-button'
	buttonCancel.textContent='Отмена'
	buttonCancel.className='button fill-button cancel-button'
	buttonCont.appendChild(buttonSave)
	buttonCont.appendChild(buttonCancel)
	return {container: buttonCont, save:buttonSave, cancel:buttonCancel}
}

function editObj(event){
	let currentObj=getSelectedObj('.edit')
	let prevName='',prevDesc='',prevParentId = null
	if(currentObj){
		let objName = currentObj.querySelector('.obj-name')
		let objDesc = currentObj.querySelector('.obj-desc')
		let managePanel = currentObj.querySelector('.manage-panel')
		let selectObj = currentObj.querySelector('.select-obj')
		if(selectObj!=null) selectObj.classList.add('d-none')
		if(managePanel!=null) managePanel.classList.add('d-none')
		if(objName!=null&&objDesc!=null){
			let nameInput = document.createElement('input')
			nameInput.type='text'
			let descInput = document.createElement('textarea')
			nameInput.className='obj-name mb-05 w-100'
			descInput.className='obj-desc w-100'
			nameInput.placeholder = "Имя объекта"
			descInput.placeholder = "Описание объекта"
			prevName = objName.textContent
			prevDesc = objDesc.textContent
			prevParentId = currentObj.parentElement.getAttribute('data-parent-id')
			nameInput.value = objName.textContent
			descInput.value = objDesc.textContent
			objName.parentElement.insertBefore(nameInput,objName)
			objName.parentElement.removeChild(objName)
			objDesc.parentElement.insertBefore(descInput,objDesc)
			objDesc.parentElement.removeChild(objDesc)
			if(currentObj.hasAttribute('data-id')){
				let currentId = currentObj.getAttribute('data-id')
				let cObj = listObjs.find((item)=>item.id==currentId)
				if(cObj){
					let divSelect = document.createElement('div')
					divSelect.className='d-flex ai-center mt-10 parent-change-cont'
					let selectParent = document.createElement('select')
					selectParent.name="parent_id"
					selectParent.className="flex-1 parent-change"
					let option = document.createElement('option')
					option.value=0
					option.textContent = `Нет родителя`
					option.selected=true
					selectParent.appendChild(option)
					let objChildIds=getChildsID(currentId,listObjs)
					let allowedParents = listObjs.filter(el=>!objChildIds.includes(el.id)&&el.id!=currentId)
					for(let item of allowedParents){
						option = document.createElement('option')
						option.value=item.id
						option.textContent = `${item.title} - ${item.id}`
						selectParent.appendChild(option)
						if(item.id==cObj.parentId) {
							option.selected=true
						}
					}
					let selectLabel = document.createElement('p')
					selectLabel.className='mr-05'
					selectLabel.textContent = 'Смена родителя: '
					divSelect.appendChild(selectLabel)
					divSelect.appendChild(selectParent)
					descInput.parentElement.appendChild(divSelect)
				}
			}
			let objCont = currentObj.querySelector('.obj-content')
			if(objCont!=null){
				let buttons = createEditButtons()
				objCont.appendChild(buttons.container)
				buttons.cancel.addEventListener('click',()=>{
					replaceElement(currentObj,prevName,prevDesc,prevParentId)
				})
				buttons.save.addEventListener('click',(ev)=>saveObj(ev,currentObj))
			}
		}
	}
}

function replaceElement(obj,name,desc,parentId){
	let inputName = obj.querySelector('.obj-name')
	let inputDesc = obj.querySelector('.obj-desc')
	let managePanel = obj.querySelector('.manage-panel')
	let selectObj = obj.querySelector('.select-obj')
	let parentChange = obj.querySelector('.parent-change-cont')

	let buttons = obj.querySelector('.edit-buttons')
	objName=document.createElement('p')
	objDesc = document.createElement('p')
	objName.className='fw-bold obj-name c-main'
	objDesc.className='bt-1 pt-05 fsz-07 obj-desc c-dark'
	objName.textContent=name
	objDesc.textContent=desc
	if(inputName!=null){
		inputName.parentElement.insertBefore(objName, inputName)
		inputName.parentElement.removeChild(inputName)
	}
	if(inputDesc!=null){
		inputDesc.parentElement.insertBefore(objDesc, inputDesc)
		inputDesc.parentElement.removeChild(inputDesc)
	}
	let container=undefined
	if(parentId==0||parentId==null){
		container = document.getElementById('root-container');
	}else{
		container = document.querySelector(`.obj-container[data-parent-id="${parentId}"]`);
	}
	let prevContainer = obj.parentElement
	let currentParentId = prevContainer.getAttribute('data-parent-id')
	console.log(parentId+' | '+currentParentId)
	if(parentId!=currentParentId){
		if(container==null||!container){
			let parentObj = document.querySelector(`.obj-item[data-id="${parentId}"]`)
			if(parentObj!=null){
				container = document.createElement('ul')
				container.className='obj-container pos-rel d-none'
				container.setAttribute('data-parent-id',parentId)
				parentObj.classList.add('deploy')
				let managePanel = parentObj.querySelector('.manage-panel')
				let deployBtn = document.createElement('button')
				let deploySpan = document.createElement('span')
				deploySpan.innerHTML='&#10095;'
				deployBtn.appendChild(deploySpan)
				deployBtn.className='deploy-btn ml-20'
				managePanel.appendChild(deployBtn)
				parentObj.appendChild(container)
				deployBtn.addEventListener('click',(ev)=>deployment(ev,parentObj))
				deployBtn.dispatchEvent(new Event('click'))
			}
		}
		if(container){
			
			container.appendChild(obj)
			if(!prevContainer.children.length){
				let prevParent = prevContainer.parentElement
				prevParent.classList.remove('deploy')
				let deployBtn = prevParent.querySelector('.deploy-btn')
				if(deployBtn!=null) deployBtn.parentElement.removeChild(deployBtn)
				prevParent.removeChild(prevContainer)
			}
		}
	}

	if(parentChange!=null) parentChange.parentElement.removeChild(parentChange)
	if(selectObj!=null) selectObj.classList.remove('d-none')
	if(managePanel!=null) managePanel.classList.remove('d-none')
	if(buttons!=null) buttons.parentElement.removeChild(buttons)
	
}

function deleteObj(event){
	let currentObj=getSelectedObj('.delete')
	if(currentObj){
		deleteRequest(currentObj,(data)=>{
			let cont = currentObj.parentElement
			cont.removeChild(currentObj)
			checkChildren(cont)
			adminBtns=document.querySelectorAll('#nav-admin-btns>.admin-button:not(#create-root)')
			for(let button of adminBtns){
				button.disabled=true
			}
			if(data?.content){
				let container = document.getElementById('content-cont')
				container.innerHTML+=data.content
			}
		})
		event.stopPropagation()
	}
}

function saveObj(event,obj,isNew=false){
	let inputName = obj.querySelector('.obj-name')
	let inputDesc = obj.querySelector('.obj-desc')
	let objContent = inputName.parentElement
	if(isNew){
		let topBlock = document.createElement('div')
		topBlock.className = "d-flex jc-bspace ai-center mb-05 fd-m-col-rs"
		let topName = document.createElement('div')
		topName.className = 'd-flex w-100 obj-title'
		let checkboxObj = document.createElement('input')
		checkboxObj.type='checkbox'
		checkboxObj.className = 'select-obj mr-05'
		let objName = document.createElement('p')
		objName.className = 'fw-bold obj-name c-main'
		objName.textContent = inputName.value
		topName.appendChild(checkboxObj)
		topName.appendChild(objName)
		topBlock.appendChild(topName)
		let objDesc = document.createElement('p')
		objDesc.className = 'bt-1 pt-05 fsz-07 obj-desc c-dark'
		objDesc.textContent = inputDesc.value
		let loadCont = document.createElement('div')
		loadCont.className = 'load-cont d-flex mt-05 jc-end ai-center d-none'
		let loadText = document.createElement('p')
		loadText.className='status d-flex ai-center c-main fw-bold fsz-07 mr-05'
		loadText.textContent='Сохранение'
		let load = document.createElement('div')
		load.className = 'load'
		let loadCenter = document.createElement('div')
		loadCenter.className='load-center'
		load.appendChild(loadCenter)
		loadCont.appendChild(loadText)
		loadCont.appendChild(load)
		while(objContent.children.length){
			objContent.removeChild(objContent.children[0])
		}
		objContent.appendChild(topBlock)
		objContent.appendChild(objDesc)
		objContent.appendChild(loadCont)
		saveRequest(obj,(data)=>{
			if(data.newId){
				obj.setAttribute('data-id',data.newId)
				let manage = createManagePanel()
				let title = obj.querySelector('.obj-title')
				title.parentElement.appendChild(manage)
				let content = obj.querySelector('.obj-content')
				if(content.querySelector('.select-obj')!=null){
					content.addEventListener('click',selectObj)
				}
			}
		})
	}else{
		let selectParent = obj.querySelector('.parent-change')
		let parentId = selectParent.options[selectParent.options.selectedIndex].value
		replaceElement(obj,inputName.value,inputDesc.value,parentId)
		saveRequest(obj)
	}
}

function createManagePanel(){
	let cont = document.createElement('div')
	cont.className = 'd-flex ml-10 manage-panel w-m-100 jc-m-bspace ml-m-0 mb-m-10'
	let list = document.createElement('ul')
	list.className = 'admin-btns d-flex ai-center'
	let create = document.createElement('li')
	create.className='admin-button'
	let edit = create.cloneNode()
	let del = create.cloneNode()
	create.classList.add('create')
	edit.classList.add('edit')
	del.classList.add('delete')
	list.appendChild(create)
	list.appendChild(edit)
	list.appendChild(del)
	cont.appendChild(list)
	create.addEventListener('click',createObj)
	edit.addEventListener('click',editObj)
	del.addEventListener('click',deleteObj)
	return cont
}

function checkChildren(root){
	if(!root.children.length){
		let cont = root.parentElement
		cont.removeChild(root)
		cont.classList.remove('deploy')
		let btn = cont.querySelector('.deploy-btn')
		if(btn!=null){
			btn.parentElement.removeChild(btn)
		}
	}
}

function selectObjUser(event){
	let selectedObj = document.querySelector('.obj-content-selected')
	
	if(selectedObj!=null&&selectObj!=event.currentTarget){
		selectedObj.classList.remove('obj-content-selected')
		event.currentTarget.classList.add('obj-content-selected')
	}else{
		event.currentTarget.classList.add('obj-content-selected')
	}
	let objItem = event.currentTarget.parentElement
	let aside = document.getElementById('aside-desc')
	while(aside.children.length){
		aside.removeChild(aside.children[0])
	}
	let desc = document.createElement('p')
	desc.className="p-10"
	desc.textContent = objItem.getAttribute('data-desc')
	aside.appendChild(desc)
}

function getChildsID(parentId,objs){
	let arr=[]
	let childs = objs.filter(el=>el.parentId==parentId)
	let childIDs = childs.map(el=>el.id)
	if(childs.length){
		arr = arr.concat(childIDs)
		for(let id of childIDs){
			arr = arr.concat(getChildsID(id,objs))
		}
	}
	return arr
}

