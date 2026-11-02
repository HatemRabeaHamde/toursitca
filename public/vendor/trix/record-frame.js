(() => {
 const notify=(dirty,saved=false)=>parent.postMessage({type:'site-content-record-status',dirty,saved},location.origin);
 document.addEventListener('input',event=>{if(event.target.closest('main'))notify(true);});
 document.addEventListener('change',event=>{if(event.target.closest('main'))notify(true);});
 window.addEventListener('site-record-saved',()=>notify(false,true));
 document.addEventListener('submit',event=>{
  const form=event.target;const url=new URL(form.action,location.href);
  if(url.origin===location.origin && url.pathname.startsWith('/admin/')) {
   url.searchParams.set('content_frame','1');form.action=url.href;
   if(!form.querySelector('[name=content_frame]')) {const flag=document.createElement('input');flag.type='hidden';flag.name='content_frame';flag.value='1';form.append(flag);}
  }
 },true);
 document.addEventListener('click',event=>{
  const link=event.target.closest('a[href]');if(!link || link.target==='_blank')return;
  const url=new URL(link.href,location.href);
  if(url.origin===location.origin && url.pathname.startsWith('/admin/')) {url.searchParams.set('content_frame','1');link.href=url.href;}
 },true);
 notify(false,Boolean(window.siteContentRecordSaved));
})();
