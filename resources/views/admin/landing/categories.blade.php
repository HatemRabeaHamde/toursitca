@extends('layouts.admin')

@section('page-title', 'Experience Categories')
@section('page-subtitle', 'Manage the category tiles shown on the homepage')

@section('content')

<div
    x-data="categoriesManager()"
    x-init="load()"
    class="adm-grid-3-2"
>
    {{-- Add form --}}
    <div>
        <div class="adm-card">
            <div class="adm-card-header"><span class="adm-card-title">Add Category</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div x-show="toast" x-transition
                    :class="toastOk ? 'adm-flash adm-flash--success' : 'adm-flash adm-flash--error'"
                    style="margin:0;" x-text="toast"></div>

                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Slug <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.slug" class="adm-input" placeholder="desert-experiences" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.name_en" class="adm-input" placeholder="Desert Experiences" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (FR)</label>
                    <input type="text" x-model="form.name_fr" class="adm-input" placeholder="Expériences du désert">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Name (NL)</label>
                    <input type="text" x-model="form.name_nl" class="adm-input" placeholder="Woestijnervaringen">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Image URL</label>
                    <input type="url" x-model="form.image_url" class="adm-input" placeholder="https://…">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="adm-form-row" style="margin:0;">
                        <label class="adm-label">Sort Order</label>
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

                <div x-show="formErrors.length" style="margin:0;">
                    <div class="adm-flash adm-flash--error" style="margin:0;">
                        <template x-for="e in formErrors"><div x-text="e"></div></template>
                    </div>
                </div>

                <button @click="save()" :disabled="saving" class="adm-btn adm-btn--primary">
                    <span x-show="!saving">Add Category</span>
                    <span x-show="saving">Saving…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">All Categories</span>
            <span style="font-size:.8rem;color:var(--adm-text-muted);" x-text="items.length + ' total'"></span>
        </div>

        <div x-show="loading" style="padding:40px;text-align:center;color:var(--adm-text-muted);">Loading…</div>

        <div x-show="!loading && items.length === 0" class="adm-empty">
            <p>No categories yet. Add one on the left.</p>
        </div>

        <div x-show="!loading && items.length > 0" class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name (EN)</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in items" :key="item.id">
                        <tr>
                            <td style="font-size:.75rem;color:var(--adm-text-muted);" x-text="item.sort_order"></td>
                            <td>
                                <div x-show="editingId !== item.id">
                                    <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);"
                                         x-text="item.name?.en || item.name || '—'"></div>
                                    <div x-show="item.image_url" style="font-size:.7rem;color:var(--adm-text-muted);margin-top:2px;">
                                        <span x-text="item.image_url ? '🖼 image set' : ''"></span>
                                    </div>
                                </div>
                                <div x-show="editingId === item.id" style="display:flex;flex-direction:column;gap:6px;">
                                    <input type="text" x-model.trim="editForm.name_en" class="adm-input" placeholder="Name EN" required style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.name_fr" class="adm-input" placeholder="Name FR" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.name_nl" class="adm-input" placeholder="Name NL" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="text" x-model="editForm.image_url" class="adm-input" placeholder="Image URL" style="font-size:.8rem;padding:5px 8px;">
                                    <input type="number" x-model.number="editForm.sort_order" class="adm-input" placeholder="Sort" style="font-size:.8rem;padding:5px 8px;width:80px;">
                                </div>
                            </td>
                            <td style="font-size:.75rem;font-family:monospace;color:var(--adm-text-muted);" x-text="item.slug"></td>
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
function categoriesManager() {
    return {
        items: [],
        loading: true,
        saving: false,
        editingId: null,
        toast: '',
        toastOk: true,
        formErrors: [],
        form: { slug: '', name_en: '', name_fr: '', name_nl: '', image_url: '', sort_order: 0, is_active: '1' },
        editForm: {},

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.landing.categories.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                });
                const json = await res.json();
                this.items = json.data?.data ?? json.data ?? [];
            } catch(e) { this.showToast('Failed to load categories.', false); }
            this.loading = false;
        },

        async save() {
            this.formErrors = [];
            if (!this.form.slug || !this.form.name_en) {
                this.formErrors = ['Slug and English name are required.'];
                return;
            }
            this.saving = true;
            try {
                const body = {
                    slug: this.form.slug,
                    name: { en: this.form.name_en, fr: this.form.name_fr || this.form.name_en, nl: this.form.name_nl || this.form.name_en },
                    image_url: this.form.image_url || null,
                    sort_order: this.form.sort_order || 0,
                    is_active: this.form.is_active === '1' || this.form.is_active === true,
                };
                const res = await fetch('{{ route('admin.landing.categories.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(body),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {err: ['Server error']}).flat();
                } else {
                    const json = await res.json();
                    this.items.push(json.data);
                    this.form = { slug: '', name_en: '', name_fr: '', name_nl: '', image_url: '', sort_order: 0, is_active: '1' };
                    this.showToast('Category added.');
                }
            } catch(e) { this.formErrors = ['Network error. Please try again.']; }
            this.saving = false;
        },

        startEdit(item) {
            this.editingId = item.id;
            this.editForm = {
                name_en: item.name?.en ?? '',
                name_fr: item.name?.fr ?? '',
                name_nl: item.name?.nl ?? '',
                image_url: item.image_url ?? '',
                sort_order: item.sort_order ?? 0,
                is_active: item.is_active,
            };
        },

        cancelEdit() { this.editingId = null; this.editForm = {}; },

        async update(item) {
            this.formErrors = [];
            if (!this.editForm.name_en) {
                this.formErrors = ['English name is required.'];
                return;
            }
            this.saving = true;
            try {
                const body = {
                    slug: item.slug,
                    name: { en: this.editForm.name_en, fr: this.editForm.name_fr || this.editForm.name_en, nl: this.editForm.name_nl || this.editForm.name_en },
                    image_url: this.editForm.image_url || null,
                    sort_order: this.editForm.sort_order ?? 0,
                    is_active: this.editForm.is_active,
                };
                const url = `{{ url('admin/landing/categories') }}/${item.id}`;
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(body),
                });
                if (res.ok) {
                    const json = await res.json();
                    const idx = this.items.findIndex(i => i.id === item.id);
                    if (idx > -1) this.items[idx] = json.data;
                    this.cancelEdit();
                    this.showToast('Category updated.');
                } else {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {err: ['Server error']}).flat();
                }
            } catch(e) { this.formErrors = ['Network error. Please try again.']; }
            this.saving = false;
        },

        async remove(item) {
            if (!confirm(`Delete "${item.name?.en ?? item.slug}"?`)) return;
            const url = `{{ url('admin/landing/categories') }}/${item.id}`;
            try {
                const res = await fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() } });
                if (!res.ok) {
                    this.showToast('Could not delete category.', false);
                    return;
                }
                this.items = this.items.filter(i => i.id !== item.id);
                this.showToast('Category deleted.');
            } catch(e) {
                this.showToast('Network error. Please try again.', false);
            }
        },

        showToast(msg, ok = true) {
            this.toast = msg; this.toastOk = ok;
            setTimeout(() => this.toast = '', 3500);
        },
    };
}
</script>

@endsection
