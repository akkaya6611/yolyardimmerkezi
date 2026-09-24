document.addEventListener('DOMContentLoaded',()=>{
  const city=document.querySelector('[data-edit-city]'),district=document.querySelector('[data-edit-district]');
  if(city&&district){const locations=JSON.parse(city.dataset.locations||'{}');city.addEventListener('change',()=>{district.replaceChildren(new Option('İlçe seçin',''));(locations[city.value]||[]).forEach(name=>district.add(new Option(name,name)));});}
  document.querySelectorAll('.mis360-withdraw-form').forEach(form=>form.addEventListener('submit',event=>{if(!window.confirm('İlan yayından kaldırılacak ve hesabınızda saklanacak. Devam edilsin mi?'))event.preventDefault();}));
});
