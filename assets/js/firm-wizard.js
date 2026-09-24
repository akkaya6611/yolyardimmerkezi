document.addEventListener('DOMContentLoaded',function(){
const form=document.querySelector('[data-firm-wizard]');if(!form)return;
const plan=form.querySelector('[data-plan-step]'),details=form.querySelector('[data-details-step]');
let step=1;
function show(next,focus){step=next;plan.hidden=next!==1;details.hidden=next!==2;details.disabled=next!==2;
form.querySelectorAll('[data-step-label]').forEach(function(el){if(Number(el.dataset.stepLabel)===next)el.setAttribute('aria-current','step');else el.removeAttribute('aria-current');});
if(focus){const el=next===2?details.querySelector('legend'):plan.querySelector('input:checked');if(el)el.focus();}
}
function advance(){const selected=form.querySelector('input[name="firm_plan"]:checked:not(:disabled)');if(!selected||selected.value!=='free'){plan.querySelector('input[value="free"]').reportValidity();return;}show(2,true);}
form.querySelector('[data-plan-next]').addEventListener('click',advance);
form.querySelector('[data-plan-back]').addEventListener('click',function(){show(1,true);});
form.addEventListener('submit',function(event){if(step===1){event.preventDefault();advance();}});
show(form.dataset.startStep==='2'?2:1,false);
});

document.addEventListener('DOMContentLoaded',function(){
const configs=[['firm_profile','.mis360-profile-preview',1],['firm_slider','.mis360-slider-preview',0]];
configs.forEach(function(config){const input=document.getElementById(config[0]);if(!input)return;const preview=document.querySelector(config[1]);let urls=[];
input.addEventListener('change',function(){urls.forEach(URL.revokeObjectURL);urls=[];preview.replaceChildren();input.setCustomValidity('');const limit=input.id==='firm_slider'?Number(input.dataset.sliderLimit):1;
if(input.id==='firm_slider')document.getElementById('slider_file_count').value=input.files.length;
if(input.files.length>limit)input.setCustomValidity(limit?'En fazla '+limit+' fotoğraf seçebilirsiniz.':'Paketinizde slider özelliği bulunmuyor.');
let total=0;for(const file of input.files){total+=file.size;if(file.size>5*1024*1024||!['image/jpeg','image/png','image/webp'].includes(file.type))input.setCustomValidity('En fazla 5 MB JPG, PNG veya WebP seçin.');}
if(total>20*1024*1024)input.setCustomValidity('Toplam yükleme en fazla 20 MB olabilir.');if(!input.checkValidity()){input.reportValidity();return;}
Array.from(input.files).forEach(function(file){const box=document.createElement('figure'),img=document.createElement('img'),caption=document.createElement('figcaption');const url=URL.createObjectURL(file);urls.push(url);img.src=url;img.alt=file.name;caption.textContent=file.name;box.append(img,caption);preview.append(box);});
});});});

document.addEventListener('DOMContentLoaded',function(){const name=document.getElementById('firm_name'),city=document.getElementById('city'),district=document.getElementById('district');if(!name)return;function update(){const n=document.querySelector('[data-preview-name]'),l=document.querySelector('[data-preview-location]');if(n)n.textContent=name.value.trim()||'İşletmenizin adı';if(l)l.textContent=[city.value.trim(),district.value.trim()].filter(Boolean).join(' / ')||'İl / İlçe';} [name,city,district].forEach(function(el){el.addEventListener('input',update);});update();});

document.addEventListener('DOMContentLoaded',function(){
const city=document.querySelector('[data-registration-locations]'),district=document.getElementById('district');if(!city||!district)return;
const locations=JSON.parse(city.dataset.registrationLocations);
city.addEventListener('change',function(){const items=locations[city.value]||[];district.replaceChildren(new Option(items.length?'İlçe seçin':'Önce il seçin',''));items.forEach(function(name){district.add(new Option(name,name));});district.disabled=!items.length;district.dispatchEvent(new Event('input',{bubbles:true}));city.dispatchEvent(new Event('input',{bubbles:true}));});
district.addEventListener('change',function(){district.dispatchEvent(new Event('input',{bubbles:true}));});
});
