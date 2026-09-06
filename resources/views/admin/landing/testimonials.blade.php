@extends('layouts.admin')

@section('page-title', 'Testimonials')
@section('page-subtitle', 'Traveler quotes shown in the homepage social proof section')

@section('content')

<div x-data="testimonialsManager()" x-init="load()" class="adm-grid-3-2">

    {{-- Add form --}}
    <div>
        <div class="adm-card">
            <div class="adm-card-header"><span class="adm-card-title">Add Testimonial</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div x-show="toast" x-transition
                    :class="toastOk ? 'adm-flash adm-flash--success' : 'adm-flash adm-flash--error'"
                    style="margin:0;" x-text="toast"></div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Author Name (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                        <input type="text" x-model.trim="form.author_name_en" class="adm-input" placeholder="Sarah M." required>
                    </div>
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Country</label>
                        <input type="text" x-model="form.author_country" class="adm-input" placeholder="United States">
                    </div>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Author Name (FR)</label>
                    <input type="text" x-model="form.author_name_fr" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Author Name (NL)</label>
                    <input type="text" x-model="form.author_name_nl" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Quote (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <textarea x-model.trim="form.body_en" class="adm-textarea" rows="3" placeholder="Absolutely unforgettable experience…" required></textarea>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Quote (FR)</label>
                    <textarea x-model="form.body_fr" class="adm-textarea" rows="2"></textarea>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Quote (NL)</label>
                    <textarea x-model="form.body_nl" class="adm-textarea" rows="2"></textarea>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Experience Title</label>
                    <input type="text" x-model="form.experience_title" class="adm-input" placeholder="3-Day Sahara Desert Camp">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Avatar URL</label>
                    <input type="url" x-model="form.avatar_url" class="adm-input" placeholder="https://…">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Rating</label>
                        <input type="number" x-model.number="form.rating" class="adm-input" min="1" max="5" step="0.1" value="5">
                    </div>
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Sort</label>
                        <input type="number" x-model.number="form.sort_order" class="adm-input" min="0" value="0">
                    </div>
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Active</label>
                        <select x-model="form.is_active" class="adm-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div x-show="formErrors.length">
                    <div class="adm-flash adm-flash--error" style="margin:0;">
                        <template x-for="e in formErrors"><div x-text="e"></div></template>
                    </div>
                </div>

                <button @click="save()" :disabled="saving" class="adm-btn adm-btn--primary">
                    <span x-show="!saving">Add Testimonial</span>
                    <span x-show="saving">Saving…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">All Testimonials</span>
            <span style="font-size:.8rem;color:var(--adm-text-muted);" x-text="items.length + ' total'"></span>
        </div>

        <div x-show="loading" style="padding:40px;text-align:center;color:var(--adm-text-muted);">Loading…</div>
        <div x-show="!loading && items.length === 0" class="adm-empty"><p>No testimonials yet.</p></div>

        <div x-show="!loading && items.length > 0">
            <template x-for="item in items" :key="item.id">
                <div style="padding:16px 20px;border-bottom:1px solid var(--adm-border);">

                    <div x-show="editingId !== item.id">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--adm-surface);flex-shrink:0;overflow:hidden;">
                                <img x-show="item.avatar_url" :src="item.avatar_url" alt="" style="width:100%;height:100%;object-fit:cover;">
                                <div x-show="!item.avatar_url" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:var(--adm-navy);"
                                     x-text="(item.author_name?.en || '?').charAt(0).toUpperCase()"></div>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                    <span style="font-weight:700;font-size:.875rem;color:var(--adm-text);" x-text="item.author_name?.en || '—'"></span>
                                    <span style="font-size:.7rem;color:var(--adm-text-muted);" x-text="item.author_country ? '· ' + item.author_country : ''"></span>
                                    <span style="font-size:.75rem;color:var(--adm-gold);font-weight:600;" x-text="'★ ' + (item.rating ?? 5)"></span>
                                </div>
                                <div style="font-size:.8rem;color:var(--adm-text-muted);font-style:italic;line-height:1.5;" x-text="'&quot;' + (item.body?.en || '').substring(0,100) + (item.body?.en?.length > 100 ? '…' : '') + '&quot;'"></div>
                                <div x-show="item.experience_title" style="font-size:.7rem;color:var(--adm-text-muted);margin-top:4px;" x-text="'Re: ' + item.experience_title"></div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;flex-shrink:0;">
                                <span :class="item.is_active ? 'adm-badge adm-badge--active' : 'adm-badge adm-badge--cancelled'"
                                      x-text="item.is_active ? 'Active' : 'Hidden'" style="font-size:.65rem;"></span>
                                <div style="display:flex;gap:4px;">
                                    <button @click="startEdit(item)" class="adm-btn adm-btn--ghost adm-btn--sm">Edit</button>
                                    <button @click="remove(item)" class="adm-btn adm-btn--danger adm-btn--sm">Del</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="editingId === item.id" style="display:flex;flex-direction:column;gap:7px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <input type="text" x-model.trim="editForm.author_name_en" class="adm-input" placeholder="Author EN" required style="font-size:.8rem;padding:5px 8px;">
                            <input type="text" x-model="editForm.author_country" class="adm-input" placeholder="Country" style="font-size:.8rem;padding:5px 8px;">
                        </div>
                        <input type="text" x-model="editForm.author_name_fr" class="adm-input" placeholder="Author FR" style="font-size:.8rem;padding:5px 8px;">
                        <input type="text" x-model="editForm.author_name_nl" class="adm-input" placeholder="Author NL" style="font-size:.8rem;padding:5px 8px;">
                        <textarea x-model.trim="editForm.body_en" class="adm-textarea" rows="2" placeholder="Quote EN" required style="font-size:.8rem;padding:5px 8px;"></textarea>
                        <textarea x-model="editForm.body_fr" class="adm-textarea" rows="2" placeholder="Quote FR" style="font-size:.8rem;padding:5px 8px;"></textarea>
                        <textarea x-model="editForm.body_nl" class="adm-textarea" rows="2" placeholder="Quote NL" style="font-size:.8rem;padding:5px 8px;"></textarea>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <input type="text" x-model="editForm.experience_title" class="adm-input" placeholder="Experience title" style="font-size:.8rem;padding:5px 8px;">
                            <input type="url" x-model="editForm.avatar_url" class="adm-input" placeholder="Avatar URL" style="font-size:.8rem;padding:5px 8px;">
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="number" x-model.number="editForm.rating" class="adm-input" min="1" max="5" step="0.1" placeholder="Rating" style="font-size:.8rem;padding:5px 8px;width:80px;">
                            <input type="number" x-model.number="editForm.sort_order" class="adm-input" min="0" placeholder="Sort" style="font-size:.8rem;padding:5px 8px;width:70px;">
                            <div style="margin-left:auto;display:flex;gap:6px;">
                                <button @click="cancelEdit()" class="adm-btn adm-btn--ghost adm-btn--sm">Cancel</button>
                                <button @click="update(item)" :disabled="saving" class="adm-btn adm-btn--success adm-btn--sm">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function testimonialsManager() {
    return {
        items: [], loading: true, saving: false, editingId: null,
        toast: '', toastOk: true, formErrors: [], editForm: {},
        form: { author_name_en:'', author_name_fr:'', author_name_nl:'', author_country:'', body_en:'', body_fr:'', body_nl:'', experience_title:'', avatar_url:'', rating:5, sort_order:0, is_active:'1' },

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.landing.testimonials.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                });
                const json = await res.json();
                this.items = json.data?.data ?? json.data ?? [];
            } catch(e) { this.showToast('Failed to load.', false); }
            this.loading = false;
        },

        payload(f) {
            return {
                author_name: { en: f.author_name_en, fr: f.author_name_fr || f.author_name_en, nl: f.author_name_nl || f.author_name_en },
                author_country: f.author_country || null,
                body: { en: f.body_en, fr: f.body_fr || f.body_en, nl: f.body_nl || f.body_en },
                experience_title: f.experience_title || null,
                avatar_url: f.avatar_url || null,
                rating: f.rating ?? 5,
                sort_order: f.sort_order ?? 0,
                is_active: f.is_active === '1' || f.is_active === true,
            };
        },

        async save() {
            this.formErrors = [];
            if (!this.form.author_name_en || !this.form.body_en) {
                this.formErrors = ['Author name and quote in English are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.landing.testimonials.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.form)),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                } else {
                    this.items.push((await res.json()).data);
                    this.form = { author_name_en:'', author_name_fr:'', author_name_nl:'', author_country:'', body_en:'', body_fr:'', body_nl:'', experience_title:'', avatar_url:'', rating:5, sort_order:0, is_active:'1' };
                    this.showToast('Testimonial added.');
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        startEdit(item) {
            this.editingId = item.id;
            this.editForm = { author_name_en:item.author_name?.en??'', author_name_fr:item.author_name?.fr??'', author_name_nl:item.author_name?.nl??'', author_country:item.author_country??'', body_en:item.body?.en??'', body_fr:item.body?.fr??'', body_nl:item.body?.nl??'', experience_title:item.experience_title??'', avatar_url:item.avatar_url??'', rating:item.rating??5, sort_order:item.sort_order??0, is_active:item.is_active?'1':'0' };
        },
        cancelEdit() { this.editingId = null; this.editForm = {}; },

        async update(item) {
            this.formErrors = [];
            if (!this.editForm.author_name_en || !this.editForm.body_en) {
                this.formErrors = ['Author name and quote in English are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch(`{{ url('admin/landing/testimonials') }}/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.editForm)),
                });
                if (res.ok) {
                    const idx = this.items.findIndex(i => i.id === item.id);
                    if (idx > -1) this.items[idx] = (await res.json()).data;
                    this.cancelEdit(); this.showToast('Testimonial updated.');
                } else {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        async remove(item) {
            if (!confirm(`Delete testimonial from "${item.author_name?.en ?? '?'}"?`)) return;
            try {
                const res = await fetch(`{{ url('admin/landing/testimonials') }}/${item.id}`, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':this.csrf()} });
                if (!res.ok) { this.showToast('Could not delete testimonial.', false); return; }
                this.items = this.items.filter(i => i.id !== item.id);
                this.showToast('Testimonial deleted.');
            } catch(e) { this.showToast('Network error.', false); }
        },

        showToast(msg, ok=true) { this.toast=msg; this.toastOk=ok; setTimeout(()=>this.toast='',3500); },
    };
}
</script>

@endsection
