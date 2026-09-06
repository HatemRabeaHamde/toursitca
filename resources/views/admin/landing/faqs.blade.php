@extends('layouts.admin')

@section('page-title', 'FAQs')
@section('page-subtitle', 'Frequently asked questions shown on the homepage')

@section('content')

<div x-data="faqsManager()" x-init="load()" class="adm-grid-3-2">

    {{-- Add form --}}
    <div>
        <div class="adm-card">
            <div class="adm-card-header"><span class="adm-card-title">Add FAQ</span></div>
            <div class="adm-card-body" style="display:flex;flex-direction:column;gap:14px;">

                <div x-show="toast" x-transition
                    :class="toastOk ? 'adm-flash adm-flash--success' : 'adm-flash adm-flash--error'"
                    style="margin:0;" x-text="toast"></div>

                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Question (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <input type="text" x-model.trim="form.question_en" class="adm-input" placeholder="Is Morocco safe to visit?" required>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Question (FR)</label>
                    <input type="text" x-model="form.question_fr" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Question (NL)</label>
                    <input type="text" x-model="form.question_nl" class="adm-input">
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Answer (EN) <span style="color:var(--deal);font-size:.7rem;">*</span></label>
                    <textarea x-model.trim="form.answer_en" class="adm-textarea" rows="3" placeholder="Yes, Morocco is…" required></textarea>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Answer (FR)</label>
                    <textarea x-model="form.answer_fr" class="adm-textarea" rows="2"></textarea>
                </div>
                <div class="adm-form-row" style="margin:0;">
                    <label class="adm-label">Answer (NL)</label>
                    <textarea x-model="form.answer_nl" class="adm-textarea" rows="2"></textarea>
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
                    <span x-show="!saving">Add FAQ</span>
                    <span x-show="saving">Saving…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <span class="adm-card-title">All FAQs</span>
            <span style="font-size:.8rem;color:var(--adm-text-muted);" x-text="items.length + ' total'"></span>
        </div>

        <div x-show="loading" style="padding:40px;text-align:center;color:var(--adm-text-muted);">Loading…</div>
        <div x-show="!loading && items.length === 0" class="adm-empty"><p>No FAQs yet.</p></div>

        <div x-show="!loading && items.length > 0">
            <template x-for="item in items" :key="item.id">
                <div style="border-bottom:1px solid var(--adm-border);padding:16px 20px;">

                    <div x-show="editingId !== item.id">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                            <div style="flex:1;">
                                <div style="font-weight:600;font-size:.875rem;color:var(--adm-text);margin-bottom:4px;" x-text="item.question?.en || '—'"></div>
                                <div style="font-size:.8rem;color:var(--adm-text-muted);line-height:1.5;" x-text="(item.answer?.en || '').substring(0, 120) + (item.answer?.en?.length > 120 ? '…' : '')"></div>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                                <span :class="item.is_active ? 'adm-badge adm-badge--active' : 'adm-badge adm-badge--cancelled'"
                                      x-text="item.is_active ? 'Active' : 'Hidden'" style="font-size:.65rem;"></span>
                                <button @click="startEdit(item)" class="adm-btn adm-btn--ghost adm-btn--sm">Edit</button>
                                <button @click="remove(item)" class="adm-btn adm-btn--danger adm-btn--sm">Del</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="editingId === item.id" style="display:flex;flex-direction:column;gap:8px;">
                        <input type="text" x-model.trim="editForm.question_en" class="adm-input" placeholder="Question EN" required style="font-size:.82rem;padding:6px 10px;">
                        <input type="text" x-model="editForm.question_fr" class="adm-input" placeholder="Question FR" style="font-size:.82rem;padding:6px 10px;">
                        <input type="text" x-model="editForm.question_nl" class="adm-input" placeholder="Question NL" style="font-size:.82rem;padding:6px 10px;">
                        <textarea x-model.trim="editForm.answer_en" class="adm-textarea" rows="3" placeholder="Answer EN" required style="font-size:.82rem;padding:6px 10px;"></textarea>
                        <textarea x-model="editForm.answer_fr" class="adm-textarea" rows="2" placeholder="Answer FR" style="font-size:.82rem;padding:6px 10px;"></textarea>
                        <textarea x-model="editForm.answer_nl" class="adm-textarea" rows="2" placeholder="Answer NL" style="font-size:.82rem;padding:6px 10px;"></textarea>
                        <div style="display:flex;gap:8px;justify-content:flex-end;">
                            <button @click="cancelEdit()" class="adm-btn adm-btn--ghost adm-btn--sm">Cancel</button>
                            <button @click="update(item)" :disabled="saving" class="adm-btn adm-btn--success adm-btn--sm">Save Changes</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function faqsManager() {
    return {
        items: [], loading: true, saving: false, editingId: null,
        toast: '', toastOk: true, formErrors: [], editForm: {},
        form: { question_en:'', question_fr:'', question_nl:'', answer_en:'', answer_fr:'', answer_nl:'', sort_order:0, is_active:'1' },

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.landing.faqs.index') }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                });
                const json = await res.json();
                this.items = json.data?.data ?? json.data ?? [];
            } catch(e) { this.showToast('Failed to load.', false); }
            this.loading = false;
        },

        payload(f) {
            return {
                question: { en: f.question_en, fr: f.question_fr || f.question_en, nl: f.question_nl || f.question_en },
                answer: { en: f.answer_en, fr: f.answer_fr || f.answer_en, nl: f.answer_nl || f.answer_en },
                sort_order: f.sort_order ?? 0,
                is_active: f.is_active === '1' || f.is_active === true,
            };
        },

        async save() {
            this.formErrors = [];
            if (!this.form.question_en || !this.form.answer_en) {
                this.formErrors = ['Question and answer in English are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.landing.faqs.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.form)),
                });
                if (!res.ok) {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                } else {
                    this.items.push((await res.json()).data);
                    this.form = { question_en:'', question_fr:'', question_nl:'', answer_en:'', answer_fr:'', answer_nl:'', sort_order:0, is_active:'1' };
                    this.showToast('FAQ added.');
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        startEdit(item) {
            this.editingId = item.id;
            this.editForm = { question_en:item.question?.en??'', question_fr:item.question?.fr??'', question_nl:item.question?.nl??'', answer_en:item.answer?.en??'', answer_fr:item.answer?.fr??'', answer_nl:item.answer?.nl??'', sort_order:item.sort_order??0, is_active:item.is_active?'1':'0' };
        },
        cancelEdit() { this.editingId = null; this.editForm = {}; },

        async update(item) {
            this.formErrors = [];
            if (!this.editForm.question_en || !this.editForm.answer_en) {
                this.formErrors = ['Question and answer in English are required.']; return;
            }
            this.saving = true;
            try {
                const res = await fetch(`{{ url('admin/landing/faqs') }}/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                    body: JSON.stringify(this.payload(this.editForm)),
                });
                if (res.ok) {
                    const idx = this.items.findIndex(i => i.id === item.id);
                    if (idx > -1) this.items[idx] = (await res.json()).data;
                    this.cancelEdit(); this.showToast('FAQ updated.');
                } else {
                    const err = await res.json();
                    this.formErrors = Object.values(err.errors ?? {e:['Server error']}).flat();
                }
            } catch(e) { this.formErrors = ['Network error.']; }
            this.saving = false;
        },

        async remove(item) {
            if (!confirm('Delete this FAQ?')) return;
            try {
                const res = await fetch(`{{ url('admin/landing/faqs') }}/${item.id}`, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':this.csrf()} });
                if (!res.ok) { this.showToast('Could not delete FAQ.', false); return; }
                this.items = this.items.filter(i => i.id !== item.id);
                this.showToast('FAQ deleted.');
            } catch(e) { this.showToast('Network error.', false); }
        },

        showToast(msg, ok=true) { this.toast=msg; this.toastOk=ok; setTimeout(()=>this.toast='',3500); },
    };
}
</script>

@endsection
