<div id="samsatModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
        <div class="flex items-start justify-between"><div><p class="ip-kicker">Dokumen kendaraan</p><h2 class="mt-1 text-xl font-extrabold">Catat perpanjangan STNK</h2><p class="mt-1 text-sm text-slate-500">Isi hasil proses Samsat. Riwayat lama tetap tersimpan.</p></div><button type="button" onclick="closeSamsatModal()" class="text-2xl text-slate-400">&times;</button></div>
        <form id="samsatForm" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4"><?php echo csrf_field(); ?>
            <div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-medium">Tanggal proses<input type="date" name="processed_at" value="<?php echo e(today()->format('Y-m-d')); ?>" required class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900"></label><label class="text-sm font-medium">Berlaku sampai<input type="date" name="new_expired_at" required class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900"></label></div>
            <label class="block text-sm font-medium">Biaya<input name="cost" inputmode="numeric" placeholder="Rp 0" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900"></label>
            <label class="block text-sm font-medium">Bukti STNK<input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm"></label>
            <label class="block text-sm font-medium">Catatan<textarea name="notes" rows="3" class="mt-1 w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900"></textarea></label>
            <div class="flex justify-end gap-2"><button type="button" onclick="closeSamsatModal()" class="ip-btn-secondary">Batal</button><button class="ip-btn-primary">Simpan riwayat</button></div>
        </form>
    </div>
</div>
<?php /**PATH D:\Work\Project\immanuel-production-v2\resources\views\components\samsat-modal.blade.php ENDPATH**/ ?>