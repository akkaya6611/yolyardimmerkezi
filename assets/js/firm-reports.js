document.querySelectorAll('.mis360-report-form').forEach(form=>{
 form.addEventListener('submit',async event=>{
  event.preventDefault();const button=form.querySelector('button[type="submit"]'),status=form.querySelector('.mis360-report-status');
  if(button.disabled)return;button.disabled=true;status.textContent='Gönderiliyor…';status.classList.remove('is-error');
  try{const response=await fetch(form.action,{method:'POST',body:new FormData(form),credentials:'same-origin'});const data=await response.json();
   if(!data.success)throw new Error(data.data?.message||'Bildirim gönderilemedi. Tekrar deneyin.');
   form.reset();status.textContent=data.data.message;
  }catch(error){status.classList.add('is-error');status.textContent=error instanceof SyntaxError?'Sunucu yanıtı alınamadı. Lütfen tekrar deneyin.':(error.message==='Failed to fetch'?'Bağlantı kurulamadı. Lütfen tekrar deneyin.':error.message);}
  finally{button.disabled=false;}
 });
});
