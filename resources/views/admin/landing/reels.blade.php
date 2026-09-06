@extends('layouts.admin')

@section('page-title', 'Travel Reels')
@section('page-subtitle', 'Short video reels showcasing Morocco experiences')

@section('content')

<div x-data="reelsManager()" x-init="load()" class="adm-grid-3-2">

    {{-- Add form --}}
    <div>
        <div class="adm-card">
            <div class="adm-card-header"><span class="adm-card-title">Add Reel</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div x-show="toast" x-transition
                    :class="toastOk ? 'adm-flash adm-flash--success' : 'adm-flash adm-flash--error'"
                    style="margin:0;" x-text="toast"></div>

                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Title (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.title_en" class="adm-input" placeholder="Sunset over the Sahara" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Title (FR)</label>
                    <input type="text" x-model="form.title_fr" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Title (NL)</label>
                    <input type="text" x-model="form.title_nl" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">City</label>
                    <input type="text" x-model="form.city" class="adm-input" placeholder="Merzouga">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Thumbnail URL</label>
                    <input type="url" x-model="form.thumbnail_url" class="adm-input" placeholder="https://…">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Video URL</label>
                    <input type="url" x-model="form.video_url" class="adm-input" placeholder="https://…">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
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
                    <span x-show="!saving">Add Reel</span>
                    <span x-show="saving">Saving…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">All Reels</span>
            <span style="font-size:.8rem;color:var(--adm-text-muted);" x-text="items.length + ' total'"></span>
        </div>

        <div x-show="loading" style="padding:40px;text-align:center;color:var(--adm-text-muted);">Loading…</div>
        <div x-show="!loading && items.length === 0" class="adm-empty"><p>No reels yet.</p></div>

        <div x-show="!loading && items.length > 0">
            <template x-for="item in items" :key="item.id">
                <div style="display:flex;gap:12px;padding:14px 20px;border-bottom:1px solid var(--adm-border);align-items:flex-start;">

                    {{-- Thumbnail preview --}}
                    <div style="width:56px;height:40px;border-radius:6px;background:var(--adm-surface);flex-shrink:0;overflow:hidden;">
                        <img x-show="item.thumbnail_url" :src="item.thumbnail_url" alt="" style="width:100%;height:100%;object-fit:cover;">
                        <div x-show="!item.thumbnail_url" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:var(--adm-text-muted);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16"/></svg>
                        </div>
                    </div>

                    <div style="flex:1;min-width:0;">
                        <div x-show="editingId !== item.id">
                            <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);" x-text="item.title?.en || '—'"></div>
                            <div style="font-size:.7rem;color:var(--adm-text-muted);margin-top:2px;" x-text="item.city || ''"></div>
                        </div>
                        <div x-show="editingId === item.id" style="display:flex;flex-direction:column;gap:5px;">
                            <input type="text" x-model.trim="editForm.title_en" class="adm-input" placeholder="Title EN" required style="font-size:.8rem;padding:5px 8px;">
                            <input type="text" x-model="editForm.title_fr" class="adm-input" placeholder="Title FR" style="font-size:.8rem;padding:5px 8px;">
                            <input type="text" x-model="editForm.title_nl" class="adm-input" placeholder="Title NL" style="font-size:.8rem;padding:5px 8px;">
                            <input type="text" x-model="editForm.city" class="adm-input" placeholder="City" style="font-size:.8rem;padding:5px 8px;">
                            <input type="url" x-model="editForm.thumbnail_url" class="adm-input" placeholder="Thumbnail URL" style="font-size:.8rem;padding:5px 8px;">
                            <input type="url" x-model="editForm.video_url" class="adm-input" placeholder="Video URL" style="font-size:.8rem;padding:5px 8px;">
                            <input type="number" x-model.number="editForm.sort_order" class="adm-input" placeholder="Sort" style="font-size:.8rem;padding:5px 8px;width:80px;">
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;flex-shrink:0;">
                        <span :class="item.is_active ? 'adm-badge adm-badge--active' : 'adm-badge adm-badge--cancelled'"
                              x-text="item.is_active ? 'Active' : 'Hidden'" style="font-size:.65rem;"></span>
                        <div x-show="editingId !== item.id" style="display:flex;gap:4px;">
                            <button @click="startEdit(item)" class="adm-btn adm-btn--ghost adm-btn--sm">Edit</button>
                            <button @click="remove(item)" class="adm-btn adm-btn--danger adm-btn--sm">Del</button>
                        </div>
                        <div x-show="editingId === item.id" style="display:flex;gap:4px;">
                            <button @click="update(item)" :disabled="saving" class="adm-btn adm-btn--success adm-btn--sm">Save</button>
                            <button @click="cancelEdit()" class="adm-btn adm-btn--ghost adm-btn--sm">✕</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function reelsManager() {
    return {
        items: [], loading: true, saving: false, editingId: null,
        toast: '', toastOk: true, formErrors: [], editForm: {},
        form: { title_en:'', title_fr:'', title_nl:'', city:'', thumbnail_url:'', video_url:'', sort_order:0, is_active:'1' },

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.landing.reels.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                });
                const json = await res.json();
                this.items = json.data?.data ?? json.data ?? [];
            } catch(e) { this.showToast('Failed to load.', false); }
            this.loading = false;
        },

        payload(f) {
            return {
                title: { en: f.title_en, fr: f.title_fr || f.title_en, nl: f.title_nl || f.title_en },
                city: f.city || null,
                thumbnail_url: f.thumbnail_url || null,
                video_url: f.video_url || null,
                sort_order: f.sort_order ?? 0,
                is_active: f.is_active === '1' || f.is_active === true,
            };
        },

        async save() {
            this.formErrors = [];
            if (!this.form.title_en) { this.formErrors = ['English title is required.']; return; }
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.landing.reels.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.form)),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                } else {
                    this.items.push((await res.json()).data);
                    this.form = { title_en:'', title_fr:'', title_nl:'', city:'', thumbnail_url:'', video_url:'', sort_order:0, is_active:'1' };
                    this.showToast('Reel added.');
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        startEdit(item) {
            this.editingId = item.id;
            this.editForm = { title_en:item.title?.en??'', title_fr:item.title?.fr??'', title_nl:item.title?.nl??'', city:item.city??'', thumbnail_url:item.thumbnail_url??'', video_url:item.video_url??'', sort_order:item.sort_order??0, is_active:item.is_active?'1':'0' };
        },
        cancelEdit() { this.editingId = null; this.editForm = {}; },

        async update(item) {
            this.formErrors = [];
            if (!this.editForm.title_en) { this.formErrors = ['English title is required.']; return; }
            this.saving = true;
            try {
                const res = await fetch(`{{ url('admin/landing/reels') }}/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.editForm)),
                });
                if (res.ok) {
                    const idx = this.items.findIndex(i => i.id === item.id);
                    if (idx > -1) this.items[idx] = (await res.json()).data;
                    this.cancelEdit(); this.showToast('Reel updated.');
                } else {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        async remove(item) {
            if (!confirm(`Delete "${item.title?.en ?? 'this reel'}"?`)) return;
            try {
                const res = await fetch(`{{ url('admin/landing/reels') }}/${item.id}`, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':this.csrf()} });
                if (!res.ok) { this.showToast('Could not delete reel.', false); return; }
                this.items = this.items.filter(i => i.id !== item.id);
                this.showToast('Reel deleted.');
            } catch(e) { this.showToast('Network error.', false); }
        },

        showToast(msg, ok=true) { this.toast=msg; this.toastOk=ok; setTimeout(()=>this.toast='',3500); },
    };
}
</script>

@endsection
