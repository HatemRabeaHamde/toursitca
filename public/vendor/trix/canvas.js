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
 const strings = new Map(Object.entries(values).filter(([key,value]) => typeof value === 'string' && value.trim() && !key.startsWith('seo.') && !key.startsWith('geo.')).map(([key,value]) => [text(value),key]));
 const walker = document.createTreeWalker(document.querySelector('[data-lv-motion]'), NodeFilter.SHOW_TEXT);
 const nodes = []; while (walker.nextNode()) nodes.push(walker.currentNode);
 nodes.forEach(node => {
  const key = strings.get(node.textContent.trim());
  if (key && node.parentElement && !node.parentElement.closest('script,style')) register(node.parentElement,key);
 });
 Object.entries(values).filter(([key]) => types[key] === 'image').forEach(([key,value]) => {
  document.querySelectorAll('img').forEach(image => { if (image.src === value.url) register(image,key); });
 });
 const style = document.createElement('style');
 style.textContent = 'html .lv [data-reveal],html .lv [data-reveal] *,html .lv [data-hero-title] span{opacity:1!important;visibility:visible!important;transform:none!important} [data-content-key]{cursor:pointer!important} [data-content-key]:hover{outline:2px dashed #d7a54b;outline-offset:5px} [data-content-key]:focus{outline:2px solid #d7a54b}';
 document.head.append(style);
 document.addEventListener('click', event => {
  let target = event.target.closest('[data-content-key]');
  if (!target && event.target.closest('[data-hero]')) target = event.target.closest('[data-hero]').querySelector('img[data-content-key]');
  if (event.target.closest('a,button') || target) { event.preventDefault(); event.stopImmediatePropagation(); }
  if (!target) return;
  const key = target.dataset.contentKey;
  parent.postMessage({type:'site-content-select',key},location.origin);
  if (types[key] === 'text' || types[key] === 'textarea') {
   target.contentEditable = 'plaintext-only'; target.focus();
   target.addEventListener('input', () => parent.postMessage({type:'site-content-input', key, value:target.textContent},location.origin));
  }
 },true);
 window.addEventListener('message',event => {
  if (event.origin !== location.origin || event.source !== parent || event.data.type !== 'site-content-update') return;
  const {key,value} = event.data;
  (targets.get(key) || []).forEach(target => {
   if (types[key] === 'image') target.src = value;
   else target.textContent = text(value);
  });
 });
})();
