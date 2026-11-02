document.addEventListener('alpine:init', () => {
 Alpine.data('siteContentEditor', () => ({
  dirty:false, selected:'', locale:'en', previewLocale:'en',
  init() {
   this.selected = new URLSearchParams(location.search).get('field') || '';
   this.$watch('selected', key => this.$refs.canvas.contentWindow.postMessage({type:'site-content-reveal', key}, location.origin));
   window.addEventListener('message',event => {
    if (event.origin !== location.origin || event.source !== this.$refs.canvas.contentWindow) return;
    const key = event.data.key;
    if (!Object.hasOwn(this.fields,key)) return;
    if (event.data.type === 'site-content-select') { this.selected=key; this.$refs.canvas.contentWindow.postMessage({type:'site-content-reveal',key},location.origin); }
    if (event.data.type === 'site-content-image-default') window.dispatchEvent(new CustomEvent('site-content-image-default',{detail:{key,url:event.data.url}}));
    if (event.data.type === 'site-content-input') {
     const input=document.getElementById(key.replaceAll('.','__')+'-'+this.locale);
     if(input) { input.value=event.data.value; this.dirty=true; }
    }
   });
  },
  fields: window.siteContentFields,
  update(event) {
   let input=event.target;
   if (input.tagName === 'TRIX-EDITOR' && document.activeElement !== input) return;
   if (input.type === 'hidden' && document.activeElement?.tagName !== 'TRIX-EDITOR') return;
   if (input.tagName === 'TRIX-EDITOR') input=document.getElementById(input.getAttribute('input'));
   if (!(input.name || '').startsWith('fields[')) return;
   this.dirty=true;
   const upload=(input.name || '').match(/^fields\[([^\]]+)\]\[upload\]/);
   if (upload && input.files[0]) {
    this.$refs.canvas.contentWindow.postMessage({type:'site-content-update',key:upload[1].replaceAll('__','.'),value:URL.createObjectURL(input.files[0])},location.origin);
    return;
   }
   const match=(input.name || '').match(/^fields\[([^\]]+)\]\[text\]\[(en|fr|nl)\]/);
   if (!match || match[2]!==this.locale) return;
   this.$refs.canvas.contentWindow.postMessage({type:'site-content-update',key:match[1].replaceAll('__','.'),value:input.value},location.origin);
  },
  changeLocale() {
   if(this.dirty && !confirm('The preview will reload the saved draft in this language. Your unsaved edits remain in the form. Continue?')) { this.locale=this.previewLocale; return; }
   this.previewLocale=this.locale;
   this.$refs.canvas.src=this.previewBase.replace('/en?', '/'+this.locale+'?');
  },
  previewBase:window.siteContentPreview
 }));
});
