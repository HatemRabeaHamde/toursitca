(() => {
 const {values, types} = window.siteContentCanvas;
 const targets = new Map();
 const text = value => new DOMParser().parseFromString(String(value), 'text/html').body.textContent.trim();
 const register = (element, key) => {
  element.dataset.contentKey = key;
  element.title = 'Click to edit';
  if (!targets.has(key)) targets.set(key, []);
  targets.get(key).push(element);
 };
 document.querySelectorAll('[data-content-key]').forEach(element => register(element, element.dataset.contentKey));
 const candidates = new Map();
 Object.entries(values).filter(([key,value]) => typeof value === 'string' && value.trim() && !key.startsWith('seo.') && !key.startsWith('geo.')).forEach(([key,value]) => {
  const label=text(value); candidates.set(label, [...(candidates.get(label) || []), key]);
 });
 const walker = document.createTreeWalker(document.querySelector('[data-lv-motion]'), NodeFilter.SHOW_TEXT);
 const nodes = []; while (walker.nextNode()) nodes.push(walker.currentNode);
 nodes.forEach(node => {
  const keys=candidates.get(node.textContent.trim());
  if (!keys || keys.length !== 1 || !node.parentElement || node.parentElement.closest('[data-content-key],script,style')) return;
  const span=document.createElement('span'); span.textContent=node.textContent;
  node.replaceWith(span); register(span,keys[0]);
 });
 Object.entries(values).filter(([key]) => types[key] === 'image').forEach(([key,value]) => {
  document.querySelectorAll('img').forEach(image => { if (image.src === value.url && !image.dataset.contentKey) register(image,key); });
 });
 const style = document.createElement('style');
 style.textContent = 'html .lv [data-reveal],html .lv [data-reveal] *,html .lv [data-hero-title] span{opacity:1!important;visibility:visible!important;transform:none!important} [data-content-key]{cursor:pointer!important} [data-content-key]:hover{outline:2px dashed #d7a54b;outline-offset:5px} [data-content-key]:focus{outline:2px solid #d7a54b}';
 document.head.append(style);
 document.addEventListener('submit', event => { event.preventDefault(); event.stopImmediatePropagation(); }, true);
 document.addEventListener('click', event => {
  let target = event.target.closest('[data-content-key]') || event.target.closest('a')?.querySelector('[data-content-key]');
  const hero=event.target.closest('[data-hero]');
  if (!target && hero) {
   const index=event.target.closest('[data-hero-city]')?.dataset.slideIndex ?? hero.dataset.activeSlide ?? 0;
   target=hero.querySelector('img[data-slide-index=\"'+index+'\"]');
  }
  if (event.target.closest('a,button[type=submit]') || target) { event.preventDefault(); event.stopImmediatePropagation(); }
  if (!target) return;
  const key = target.dataset.contentKey;
  parent.postMessage({type:'site-content-select',key},location.origin);
  if (types[key] === 'text' || types[key] === 'textarea') {
   target.contentEditable = 'plaintext-only'; target.focus();
   target.addEventListener('input', () => parent.postMessage({type:'site-content-input', key, value:target.textContent},location.origin));
  }
 },true);
 window.addEventListener('message',event => {
  if (event.origin !== location.origin || event.source !== parent) return;
  if (event.data.type === 'site-content-reveal') {
   const match=event.data.key?.match(/^hero\.slide_([2-5])_/);
   if(match || event.data.key === 'hero.image') window.dispatchEvent(new CustomEvent('site-content-slide',{detail:match ? Number(match[1])-1 : 0}));
   const image=(targets.get(event.data.key) || []).find(target=>target.tagName === 'IMG');
   if(image) parent.postMessage({type:'site-content-image-default',key:event.data.key,url:image.src},location.origin);
   return;
  }
  if(event.data.type !== 'site-content-update') return;
  const {key,value} = event.data;
  (targets.get(key) || []).forEach(target => {
   if (types[key] === 'image') { if(target.tagName === 'IMG') target.src = value; else target.style.backgroundImage = 'url(\"'+value+'\")'; }
   else target.textContent = text(value);
  });
 });
})();
