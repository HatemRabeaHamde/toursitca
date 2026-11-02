document.addEventListener('alpine:init',()=>{
 Alpine.data('siteSettings',()=>({
  locale:'en',dirty:false,resets:{},removed:{},urls:window.siteSettingsData.urls,schemas:window.siteSettingsData.schemas,fields:window.siteSettingsData.fields,defaults:window.siteSettingsData.defaults,
  value(key){if(this.resets[key])return this.defaults[this.locale]?.[key] || '';if(this.removed[key])return {url:'',alt:''};return this.fields[key]?.[this.locale] || this.fields[key]?.en || this.defaults[this.locale]?.[key] || '';},
  text(key){const value=this.value(key);return typeof value==='string' ? value : '';},
  safeUrl(key){try { const url=new URL(this.text(key)); return ['http:','https:'].includes(url.protocol) ? url.href : ''; } catch { return ''; }},
  image(key){return this.value(key)?.url || '';}, alt(key){return this.value(key)?.alt || '';},
  update(event){
   const input=event.target; const match=(input.name || '').match(/^fields\[([^\]]+)\]\[(text|alt|upload|reset|remove)\](?:\[(en|fr|nl)\])?/);
   if(!match)return;
   const key=match[1].replaceAll('__','.'); if(!Object.hasOwn(this.fields,key))return;
   this.dirty=true; const language=match[3] || this.locale;
   if(match[2]==='text')this.fields[key][language]=input.value;
   if(match[2]==='alt')this.fields[key][language]={...this.value(key),alt:input.value};
   if(match[2]==='upload' && input.files[0])['en','fr','nl'].forEach(locale=>this.fields[key][locale]={...this.value(key),url:URL.createObjectURL(input.files[0])});
   if(match[2]==='reset')this.resets[key]=input.checked;
   if(match[2]==='remove')this.removed[key]=input.checked;
  }
 }));
});
