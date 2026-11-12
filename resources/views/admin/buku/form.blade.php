@extends('admin.layouts.app')

@section('title', isset($buku) ? 'Edit Buku' : 'Tambah Buku')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cyan-700">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.buku.index') }}" class="hover:text-cyan-700">Katalog Buku</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">{{ isset($buku) ? 'Edit Koleksi' : 'Tambah Koleksi Baru' }}</span>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">{{ isset($buku) ? 'Edit Koleksi Buku' : 'Tambah Koleksi Buku' }}</h2>
    <p class="text-slate-500 text-sm">Lengkapi metadata buku untuk katalog perpustakaan. Kategori, penulis, dan penerbit dapat ditambahkan langsung bila belum tersedia.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
    <form action="{{ isset($buku) ? route('admin.buku.update', $buku) : route('admin.buku.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($buku)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Kolom Kiri: Informasi Pokok -->
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $buku->judul ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" placeholder="Contoh: Laskar Pelangi" required>
                    @error('judul')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn ?? '') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" placeholder="978-602-03-xxxx-x">
                    @error('isbn')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit ?? date('Y')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                        @error('tahun_terbit')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Total Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stok" value="{{ old('stok', $buku->stok ?? 1) }}" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                        @error('stok')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Deskripsi / Sinopsis</label>
                    <textarea name="deskripsi" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" placeholder="Ringkasan isi buku atau catatan katalog...">{{ old('deskripsi', $buku->deskripsi ?? '') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kolom Kanan: Relasi Master & Sampul -->
            <div class="space-y-6">
                <!-- Kategori -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase">Kategori <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openInlineModal('kategori', 'Kategori')" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 hover:underline inline-flex items-center gap-1">
                            <span>+ Tambah Baru</span>
                        </button>
                    </div>
                    <select id="select-kategori" name="kategori_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $cat)
                            <option value="{{ $cat->id }}" {{ (old('kategori_id', $buku->kategori_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penulis -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase">Penulis <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openInlineModal('penulis', 'Penulis')" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 hover:underline inline-flex items-center gap-1">
                            <span>+ Tambah Baru</span>
                        </button>
                    </div>
                    <select id="select-penulis" name="penulis_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                        <option value="">-- Pilih Penulis --</option>
                        @foreach($penulis as $p)
                            <option value="{{ $p->id }}" {{ (old('penulis_id', $buku->penulis_id ?? '') == $p->id) ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                    @error('penulis_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Penerbit -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase">Penerbit <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openInlineModal('penerbit', 'Penerbit')" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800 hover:underline inline-flex items-center gap-1">
                            <span>+ Tambah Baru</span>
                        </button>
                    </div>
                    <select id="select-penerbit" name="penerbit_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" required>
                        <option value="">-- Pilih Penerbit --</option>
                        @foreach($penerbit as $pub)
                            <option value="{{ $pub->id }}" {{ (old('penerbit_id', $buku->penerbit_id ?? '') == $pub->id) ? 'selected' : '' }}>{{ $pub->nama }}</option>
                        @endforeach
                    </select>
                    @error('penerbit_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sampul -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Sampul Buku (Opsional)</label>
                    @if(isset($buku) && $buku->sampul)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ route('admin.buku.sampul', $buku) }}" alt="Sampul saat ini" class="w-16 h-24 object-cover rounded border border-slate-200 shadow-xs" onerror="this.style.display='none'">
                            <span class="text-xs text-slate-500">Unggah file baru bila ingin mengganti sampul saat ini.</span>
                        </div>
                    @endif
                    <input type="file" name="sampul" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
                    @error('sampul')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.buku.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-lg transition">Batal</a>
            <button type="submit" class="bg-cyan-700 text-white px-7 py-2.5 rounded-lg text-sm font-semibold hover:bg-cyan-800 shadow-sm transition">
                {{ isset($buku) ? 'Simpan Perubahan' : 'Simpan Koleksi Buku' }}
            </button>
        </div>
    </form>
</div>

<!-- Modal Inline Add Master Data -->
<div id="inlineModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 id="modalTitle" class="text-base font-bold text-slate-800">Tambah Data Baru</h3>
            <button type="button" onclick="closeInlineModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="inlineModalForm" onsubmit="submitInlineMaster(event)" class="mt-4 space-y-4">
            <input type="hidden" id="modalType" value="">
            <div>
                <label id="modalInputLabel" class="block text-xs font-bold text-slate-600 uppercase mb-2">Nama</label>
                <input type="text" id="modalInputNama" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-cyan-500 focus:border-cyan-500" placeholder="Masukkan nama...">
                <p id="modalError" class="text-xs text-red-600 mt-1 hidden"></p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeInlineModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" id="modalSubmitBtn" class="bg-cyan-700 hover:bg-cyan-800 text-white px-5 py-2 text-xs font-semibold rounded-lg shadow-xs transition">
                    Simpan & Pilih
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentMasterType = '';

    function openInlineModal(type, label) {
        currentMasterType = type;
        document.getElementById('modalType').value = type;
        document.getElementById('modalTitle').textContent = 'Tambah ' + label + ' Baru';
        document.getElementById('modalInputLabel').textContent = 'Nama ' + label;
        document.getElementById('modalInputNama').value = '';
        document.getElementById('modalInputNama').placeholder = 'Contoh: nama ' + label.toLowerCase() + ' baru...';
        document.getElementById('modalError').classList.add('hidden');
        document.getElementById('modalError').textContent = '';
        document.getElementById('inlineModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('modalInputNama').focus(), 50);
    }

    function closeInlineModal() {
        document.getElementById('inlineModal').classList.add('hidden');
        currentMasterType = '';
    }

    async function submitInlineMaster(event) {
        event.preventDefault();
        const type = currentMasterType;
        const nama = document.getElementById('modalInputNama').value.trim();
        const errEl = document.getElementById('modalError');
        const submitBtn = document.getElementById('modalSubmitBtn');

        if (!nama) {
            errEl.textContent = 'Nama tidak boleh kosong.';
            errEl.classList.remove('hidden');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';
        errEl.classList.add('hidden');

        try {
            const token = document.querySelector('input[name="_token"]').value;
            const res = await fetch('/admin/master/' + encodeURIComponent(type), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ nama: nama })
            });

            const data = await res.json();

            if (!res.ok) {
                const message = (data.errors && data.errors.nama) ? data.errors.nama[0] : (data.message || 'Gagal menyimpan.');
                errEl.textContent = message;
                errEl.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan & Pilih';
                return;
            }

            // Sukses: sisipkan ke elemen select terkait dan pilih
            const select = document.getElementById('select-' + type);
            if (select && data.data) {
                const opt = document.createElement('option');
                opt.value = data.data.id;
                opt.textContent = data.data.nama;
                opt.selected = true;
                select.appendChild(opt);
                select.value = data.data.id;
            }

            closeInlineModal();
        } catch (e) {
            errEl.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
            errEl.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan & Pilih';
        }
    }
</script>
@endsection
