@extends('layouts.admin')

@section('title', 'Quản lý Dự án — Admin LANDTEK')

@section('content')
<div class="min-h-[100vh] bg-slate-50/50 py-10">
    <div class="container mx-auto px-4 md:px-6 lg:px-8 max-w-6xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Admin · LANDTEK</p>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Quản lý dự án</h1>
                <p class="text-sm text-slate-500 mt-1">Chỉnh sửa nội dung trang <a href="{{ route('projects') }}" class="text-primary font-semibold hover:underline" target="_blank">/du-an</a></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.properties.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:border-primary/30">
                    <i class="fas fa-building"></i> Tin thuê
                </a>
                <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:opacity-90 shadow-sm">
                    <i class="fas fa-plus"></i> Thêm dự án
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm" id="projects-table">
                    <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-bold">Dự án</th>
                            <th class="px-4 py-3 font-bold">Khu vực</th>
                            <th class="px-4 py-3 font-bold">Tin</th>
                            <th class="px-4 py-3 font-bold">Giá từ</th>
                            <th class="px-4 py-3 font-bold">Thứ tự</th>
                            <th class="px-4 py-3 font-bold">Trạng thái</th>
                            <th class="px-4 py-3 font-bold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="projects-body">
                        <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Đang tải…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const body = document.getElementById('projects-body');

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    const load = () => {
        fetch(@json(route('admin.projects.index')), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(({ data }) => {
            if (!data.length) {
                body.innerHTML = '<tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Chưa có dự án nào</td></tr>';
                return;
            }
            body.innerHTML = data.map(p => `
                <tr class="hover:bg-slate-50/80">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3 min-w-[220px]">
                            <img src="${esc(p.image)}" alt="" class="h-12 w-16 rounded-lg object-cover bg-slate-100" onerror="this.src='/images/hero-nhatrang.jpg'">
                            <div>
                                <div class="font-bold text-slate-900">${esc(p.label)}</div>
                                <div class="text-[11px] text-slate-400">${esc(p.slug)}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">${esc(p.tagline || '')}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-600">${esc(p.district || '—')}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-slate-800">${p.listing_count}</div>
                        <div class="text-[11px] text-slate-400">DB: ${p.live_listing_count} tin thật</div>
                    </td>
                    <td class="px-4 py-3 text-slate-600">${esc(p.price_from || '—')}</td>
                    <td class="px-4 py-3 text-slate-600">${p.sort_order}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-lg px-2.5 py-1 text-[11px] font-bold ${p.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'}">
                            ${p.is_active ? 'Đang hiện' : 'Đã ẩn'}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap justify-end gap-1.5">
                            <a href="${p.route_edit}" class="rounded-lg bg-primary/10 px-2.5 py-1.5 text-[12px] font-bold text-primary hover:bg-primary/15">Sửa</a>
                            <a href="${p.route_public}" target="_blank" class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-[12px] font-bold text-slate-600 hover:bg-slate-200">Xem tin</a>
                            <button type="button" data-toggle="${p.route_toggle}" class="rounded-lg bg-amber-50 px-2.5 py-1.5 text-[12px] font-bold text-amber-800 hover:bg-amber-100">${p.is_active ? 'Ẩn' : 'Hiện'}</button>
                            <button type="button" data-delete="${p.route_delete}" class="rounded-lg bg-red-50 px-2.5 py-1.5 text-[12px] font-bold text-red-600 hover:bg-red-100">Xóa</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => {
            body.innerHTML = '<tr><td colspan="7" class="px-4 py-10 text-center text-red-500">Không tải được danh sách</td></tr>';
        });
    };

    body.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('[data-toggle]');
        const delBtn = e.target.closest('[data-delete]');
        if (toggleBtn) {
            fetch(toggleBtn.dataset.toggle, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        || '{{ csrf_token() }}'
                }
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    SwalSuccess.fire({ title: 'Thành công!', text: d.message, timer: 1600, showConfirmButton: false });
                    load();
                }
            });
        }
        if (delBtn) {
            SwalConfirm('Xóa dự án này? Không thể hoàn tác.', () => {
                fetch(delBtn.dataset.delete, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || '{{ csrf_token() }}'
                    }
                }).then(r => r.json()).then(d => {
                    if (d.success) {
                        SwalSuccess.fire({ title: 'Đã xóa', text: d.message, timer: 1600, showConfirmButton: false });
                        load();
                    }
                });
            });
        }
    });

    load();
});
</script>
@endsection
