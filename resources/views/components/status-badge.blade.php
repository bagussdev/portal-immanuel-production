@props(['status', 'label' => null, 'size' => 'normal'])

@php
    $key = strtolower(trim((string) $status));
    $labels = [
        'draft' => 'Draft', 'sent' => 'Dikirim', 'approved' => 'Disetujui',
        'cancelled' => 'Dibatalkan', 'void' => 'Void', 'unpaid' => 'Belum dibayar',
        'partial' => 'Dibayar sebagian', 'paid' => 'Lunas', 'overpaid' => 'Kelebihan bayar',
        'overdue' => 'Terlambat', 'open' => 'Terbuka', 'closed' => 'Ditutup',
        'reopen' => 'Dibuka kembali', 'active' => 'Aktif', 'inactive' => 'Nonaktif',
        'available' => 'Tersedia', 'in_use' => 'Digunakan', 'maintenance' => 'Perawatan',
        'damaged' => 'Rusak', 'safe' => 'Aman', 'due_soon' => 'Segera jatuh tempo',
        'unknown' => 'Belum diisi',
        'pending' => 'Belum mulai', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai',
    ];
    $palette = match ($key) {
        'approved', 'paid', 'open', 'active', 'available', 'safe', 'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700 before:bg-emerald-500 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300',
        'sent', 'partial', 'in_use', 'in_progress' => 'border-sky-200 bg-sky-50 text-sky-700 before:bg-sky-500 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300',
        'draft', 'unpaid', 'reopen', 'maintenance', 'due_soon', 'pending' => 'border-amber-200 bg-amber-50 text-amber-700 before:bg-amber-500 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300',
        'overpaid' => 'border-violet-200 bg-violet-50 text-violet-700 before:bg-violet-500 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-300',
        'cancelled', 'void', 'overdue', 'inactive', 'damaged' => 'border-red-200 bg-red-50 text-red-700 before:bg-red-500 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300',
        default => 'border-slate-200 bg-slate-50 text-slate-600 before:bg-slate-400 dark:border-white/10 dark:bg-white/[.06] dark:text-slate-300',
    };
    $padding = $size === 'small' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-[11px]';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border font-extrabold uppercase tracking-wide before:h-1.5 before:w-1.5 before:shrink-0 before:rounded-full {$palette} {$padding}"]) }}>
    {{ $label ?: ($labels[$key] ?? ucfirst(str_replace('_', ' ', $key))) }}
</span>
