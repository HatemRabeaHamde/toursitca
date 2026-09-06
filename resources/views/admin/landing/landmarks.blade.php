@extends('layouts.admin')

@section('page-title', 'Landmarks')
@section('page-subtitle', 'Notable landmarks linked to experiences and destinations')

@section('content')

<div x-data="landmarksManager()" x-init="load()" class="adm-grid-3-2">

    {{-- Add form --}}
    <div>
        <div class="adm-card">
            <div class="adm-card-header"><span class="adm-card-title">Add Landmark</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div x-show="toast" x-transition
                    :class="toastOk ? 'adm-flash adm-flash--success' : 'adm-flash adm-flash--error'"
                    style="margin:0;" x-text="toast"></div>

                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Slug <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.slug" class="adm-input" placeholder="jemaa-el-fna" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.name_en" class="adm-input" placeholder="Jemaa el-Fna" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (FR)</label>
                    <input type="text" x-model="form.name_fr" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (NL)</label>
                    <input type="text" x-model="form.name_nl" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">City <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.city" class="adm-input" placeholder="Marrakech" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Category</label>
                    <input type="text" x-model="form.category" class="adm-input" placeholder="square, mosque, market…">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Image URL</label>
                    <input type="url" x-model="form.image_url" class="adm-input" placeholder="https://…">
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
                    <span x-show="!saving">Add Landmark</span>
                    <span x-show="saving">Saving…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">All Landmarks</span>
            <span style="font-size:.8rem;color:var(--adm-text-muted);" x-text="items.length + ' total'"></span>
        </div>

        <div x-show="loading" style="padding:40px;text-align:center;color:var(--adm-text-muted);">Loading…</div>
        <div x-show="!loading && items.length === 0" class="adm-empty"><p>No landmarks yet.</p></div>

        <div x-show="!loading && items.length > 0" class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>#</th><th>Name / City</th><th>Category</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <template x-for="item in items" :key="item.id">
                        <tr>
                            <td style="font-size:.75rem;color:var(--adm-text-muted);" x-text="item.sort_order"></td>
                            <td>
                                <div x-show="editingId !== item.id">
                                    <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);" x-text="item.name?.en || '—'"></div>
                                    <div style="font-size:.7rem;color:var(--adm-text-muted);" x-text="item.city"></div>
                                </div>
                                <div x-show="editingId === item.id" style="display:flex;flex-direction:column;gap:5px;">
                                    <input type="text" x-model.trim="editForm.name_en" class="adm-input" placeholder="Name EN" required style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.name_fr" class="adm-input" placeholder="Name FR" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.name_nl" class="adm-input" placeholder="Name NL" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model.trim="editForm.city" class="adm-input" placeholder="City" required style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.category" class="adm-input" placeholder="Category" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="url" x-model="editForm.image_url" class="adm-input" placeholder="Image URL" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="number" x-model.number="editForm.sort_order" class="adm-input" placeholder="Sort" style="font-size:.8rem;padding:5px 8px;width:80px;">
                                </div>
                            </td>
                            <td style="font-size:.75rem;color:var(--adm-text-muted);" x-text="item.category || '—'"></td>
                            <td>
                                <span :class="item.is_active ? 'adm-badge adm-badge--active' : 'adm-badge adm-badge--cancelled'"
                                      x-text="item.is_active ? 'Active' : 'Hidden'"></span>
                            </td>
                            <td>
                                <div x-show="editingId !== item.id" style="display:flex;gap:4px;justify-content:flex-end;">
                                    <button @click="startEdit(item)" class="adm-btn adm-btn--ghost adm-btn--sm">Edit</button>
                                    <button @click="remove(item)" class="adm-btn adm-btn--danger adm-btn--sm">Del</button>
                                </div>
                                <div x-show="editingId === item.id" style="display:flex;gap:4px;justify-content:flex-end;">
                                    <button @click="update(item)" :disabled="saving" class="adm-btn adm-btn--success adm-btn--sm">Save</button>
                                    <button @click="cancelEdit()" class="adm-btn adm-btn--ghost adm-btn--sm">✕</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function landmarksManager() {
    return {
        items: [], loading: true, saving: false, editingId: null,
        toast: '', toastOk: true, formErrors: [], editForm: {},
        form: { slug:'', name_en:'', name_fr:'', name_nl:'', city:'', category:'', image_url:'', sort_order:0, is_active:'1' },

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.landing.landmarks.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                });
                const json = await res.json();
                this.items = json.data?.data ?? json.data ?? [];
            } catch(e) { this.showToast('Failed to load.', false); }
            this.loading = false;
        },

        payload(f, slug) {
            return {
                slug: slug ?? f.slug,
                name: { en: f.name_en, fr: f.name_fr || f.name_en, nl: f.name_nl || f.name_en },
                city: f.city,
                category: f.category || null,
                image_url: f.image_url || null,
                sort_order: f.sort_order ?? 0,
                is_active: f.is_active === '1' || f.is_active === true,
            };
        },

        async save() {
            this.formErrors = [];
            if (!this.form.slug || !this.form.name_en || !this.form.city) {
                this.formErrors = ['Slug, English name, and city are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.landing.landmarks.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.form)),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                } else {
                    this.items.push((await res.json()).data);
                    this.form = { slug:'', name_en:'', name_fr:'', name_nl:'', city:'', category:'', image_url:'', sort_order:0, is_active:'1' };
                    this.showToast('Landmark added.');
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        startEdit(item) {
            this.editingId = item.id;
            this.editForm = { name_en:item.name?.en??'', name_fr:item.name?.fr??'', name_nl:item.name?.nl??'', city:item.city??'', category:item.category??'', image_url:item.image_url??'', sort_order:item.sort_order??0, is_active:item.is_active?'1':'0' };
        },
        cancelEdit() { this.editingId = null; this.editForm = {}; },

        async update(item) {
            this.formErrors = [];
            if (!this.editForm.name_en || !this.editForm.city) {
                this.formErrors = ['English name and city are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch(`{{ url('admin/landing/landmarks') }}/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.editForm, item.slug)),
                });
                if (res.ok) {
                    const idx = this.items.findIndex(i => i.id === item.id);
                    if (idx > -1) this.items[idx] = (await res.json()).data;
                    this.cancelEdit(); this.showToast('Landmark updated.');
                } else {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        async remove(item) {
            if (!confirm(`Delete "${item.name?.en ?? item.slug}"?`)) return;
            try {
                const res = await fetch(`{{ url('admin/landing/landmarks') }}/${item.id}`, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':this.csrf()} });
                if (!res.ok) { this.showToast('Could not delete landmark.', false); return; }
                this.items = this.items.filter(i => i.id !== item.id);
                this.showToast('Landmark deleted.');
            } catch(e) { this.showToast('Network error.', false); }
        },

        showToast(msg, ok=true) { this.toast=msg; this.toastOk=ok; setTimeout(()=>this.toast='',3500); },
    };
}
</script>

@endsection
