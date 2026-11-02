document.addEventListener('alpine:init', () => {
 Alpine.data('siteContentEditor', () => ({
  recordUrl:'',recordLabel:'',recordDirty:false,recordChanged:false,imagePreviews:{},dirty:false, selected:'', locale:'en', previewLocale:'en',
  init() {
   this.selected = new URLSearchParams(location.search).get('field') || '';
   this.$watch('selected', key => this.$refs.canvas.contentWindow.postMessage({type:'site-content-reveal', key}, location.origin));
   window.addEventListener('message',event => {
    if(event.origin!==location.origin)return;
    if(event.source===this.$refs.recordFrame?.contentWindow && event.data.type==='site-content-record-status') {this.recordDirty=Boolean(event.data.dirty);if(event.data.saved)this.recordChanged=true;return;}
    if(event.source!==this.$refs.canvas.contentWindow)return;
    if(event.data.type==='site-content-record') {this.openRecord(event.data.url,event.data.label);return;}
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
  openRecord(value,label) {
   const url=new URL(value,location.origin);
   const allowed=/^\/admin\/(?:experiences|hosts)(?:\/\d+\/edit)?$/.test(url.pathname) || /^\/admin\/landing\/(?:categories|destinations|faqs|testimonials)$/.test(url.pathname) || /^\/admin\/site-content\/records\/(?:categories|destinations|faqs|testimonials)\/\d+\/edit$/.test(url.pathname);
   if(url.origin!==location.origin || !allowed)return;
   url.searchParams.set('content_frame','1');this.recordLabel=label;this.recordDirty=false;this.recordChanged=false;this.recordUrl=url.href;
  },
  closeRecord(refresh=false) {
   if(this.recordDirty && !confirm(window.siteRecordCopy.discard))return;
   const changed=this.recordChanged;this.recordUrl='';this.recordDirty=false;
   if(refresh || changed)this.refreshCanvas();
  },
  refreshCanvas() {
   const url=new URL(this.$refs.canvas.src);url.searchParams.set('record_refresh',String(Date.now()));this.$refs.canvas.src=url.href;
  },
  canvasLoaded() {
   this.$refs.canvas.contentWindow.postMessage({type:'site-content-reveal',key:this.selected},location.origin);
   if(!this.dirty)return;
   this.$el.querySelectorAll('[name^="fields["]').forEach(input=>{
    const match=input.name.match(/^fields\[([^\]]+)\]\[text\]\[(en|fr|nl)\]/);
    if(match && match[2]===this.locale && input.value)this.$refs.canvas.contentWindow.postMessage({type:'site-content-update',key:match[1].replaceAll('__','.'),value:input.value},location.origin);
   });
   Object.entries(this.imagePreviews).forEach(([key,value])=>this.$refs.canvas.contentWindow.postMessage({type:'site-content-update',key,value},location.origin));
  },
  update(event) {
   let input=event.target;
   if (input.tagName === 'TRIX-EDITOR' && document.activeElement !== input) return;
   if (input.type === 'hidden' && document.activeElement?.tagName !== 'TRIX-EDITOR') return;
   if (input.tagName === 'TRIX-EDITOR') input=document.getElementById(input.getAttribute('input'));
   if (!(input.name || '').startsWith('fields[')) return;
   this.dirty=true;
   const upload=(input.name || '').match(/^fields\[([^\]]+)\]\[upload\]/);
   if (upload && input.files[0]) {
    this.imagePreviews[upload[1].replaceAll('__','.')]=URL.createObjectURL(input.files[0]);
    this.$refs.canvas.contentWindow.postMessage({type:'site-content-update',key:upload[1].replaceAll('__','.'),value:this.imagePreviews[upload[1].replaceAll('__','.')]},location.origin);
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
