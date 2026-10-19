/**
 * Halaman Absensi - GPS + Pindai QR Code ( kamera )
 * Tidak butuh library build: memakai Geolocation API + jsQR.
 */
(function () {
    'use strict';

    const box = document.getElementById('absensiBox');
    if (!box) return;

    const LAT_KANTOR = parseFloat(box.dataset.latKantor);
    const LNG_KANTOR = parseFloat(box.dataset.lngKantor);
    const RADIUS = parseInt(box.dataset.radius || '150', 10);

    const inputLat = document.getElementById('inputLat');
    const inputLng = document.getElementById('inputLng');
    const inputAlamat = document.getElementById('inputAlamat');
    const inputQr = document.getElementById('inputQr');
    const inputTipe = document.getElementById('inputTipe');
    const inputFoto = document.getElementById('inputFoto');
    const badgeLokasi = document.getElementById('badgeLokasi');
    const infoLokasi = document.getElementById('infoLokasi');
    const btnLokasi = document.getElementById('btnLokasi');
    const btnKirim = document.getElementById('btnKirim');
    const form = document.getElementById('formAbsensi');

    /* ---------------------------------------------------------------
     *  Jam berjalan di pojok kanan atas
     * ------------------------------------------------------------- */
    const jamEl = document.getElementById('jamSekarang');
    if (jamEl) {
        const tik = () => {
            const d = new Date();
            jamEl.textContent = d.toLocaleTimeString('id-ID', { hour12: false });
        };
        tik();
        setInterval(tik, 1000);
    }

    /* ---------------------------------------------------------------
     *  Tab masuk / pulang
     * ------------------------------------------------------------- */
    const tabMasuk = document.getElementById('tab-masuk');
    const tabPulang = document.getElementById('tab-pulang');
    if (tabMasuk && tabPulang) {
        const setTipe = () => {
            inputTipe.value = tabPulang.classList.contains('active') ? 'pulang' : 'masuk';
            cekBisaKirim();
        };
        tabMasuk.addEventListener('shown.bs.tab', setTipe);
        tabPulang.addEventListener('shown.bs.tab', setTipe);
        setTipe();
    }

    /* ---------------------------------------------------------------
     *  Ambil lokasi GPS
     * ------------------------------------------------------------- */
    let lokasiSiap = false;

    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const toRad = (v) => (v * Math.PI) / 180;
        const dLat = toRad(lat2 - lat1);
        const dLng = toRad(lng2 - lng1);
        const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
        return 2 * R * Math.asin(Math.min(1, Math.sqrt(a)));
    }

    function tampilkanLokasi(lat, lng) {
        inputLat.value = lat.toFixed(7);
        inputLng.value = lng.toFixed(7);
        lokasiSiap = true;

        let teks = 'Lat ' + lat.toFixed(5) + ', Lng ' + lng.toFixed(5);

        if (!isNaN(LAT_KANTOR) && !isNaN(LNG_KANTOR)) {
            const jarak = Math.round(haversine(lat, lng, LAT_KANTOR, LNG_KANTOR));
            teks += ' &middot; jarak <strong>' + jarak + ' m</strong> dari kantor';

            if (jarak <= RADIUS) {
                badgeLokasi.className = 'badge align-self-center bg-success';
                badgeLokasi.textContent = '✅ Dalam radius (' + jarak + ' m)';
            } else {
                badgeLokasi.className = 'badge align-self-center bg-danger';
                badgeLokasi.textContent = '❌ Luar radius (' + jarak + ' m)';
            }
        }

        infoLokasi.innerHTML = teks;
        ambilAlamat(lat, lng);
        cekBisaKirim();
    }

    function ambilAlamat(lat, lng) {
        // Reverse geocode ( opsional, dibungkus try agar offline tetap jalan )
        const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' +
            lat + '&lon=' + lng + '&zoom=18&addressdetails=0';
        fetch(url, { headers: { Accept: 'application/json' } })
            .then((r) => (r.ok ? r.json() : Promise.reject()))
            .then((j) => {
                if (j && j.display_name) {
                    inputAlamat.value = j.display_name.substring(0, 250);
                }
            })
            .catch(() => { /* abaikan, alamat bersifat opsional */ });
    }

    btnLokasi.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Perangkat/browser Anda tidak mendukung GPS.');
            return;
        }
        btnLokasi.disabled = true;
        badgeLokasi.className = 'badge align-self-center bg-secondary';
        badgeLokasi.textContent = 'Mengambil lokasi…';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                btnLokasi.disabled = false;
                tampilkanLokasi(pos.coords.latitude, pos.coords.longitude);
            },
            (err) => {
                btnLokasi.disabled = false;
                badgeLokasi.className = 'badge align-self-center bg-danger';
                badgeLokasi.textContent = 'Lokasi gagal';
                infoLokasi.textContent =
                    'Gagal mengambil lokasi (' + err.message + '). Izinkan akses lokasi lalu coba lagi.';
                lokasiSiap = false;
                cekBisaKirim();
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });

    /* ---------------------------------------------------------------
     *  Kamera + jsQR
     * ------------------------------------------------------------- */
    const modalEl = document.getElementById('modalPindai');
    const btnPindai = document.getElementById('btnPindai');
    const btnManual = document.getElementById('btnManual');
    const video = document.getElementById('videoPindai');
    const canvas = document.getElementById('canvasPindai');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const statusPindai = document.getElementById('statusPindai');
    const garis = document.getElementById('garisPindai');
    let stream = null;
    let rafId = null;
    let modal = null;
    let selesaiPindai = false;

    async function bukaKamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Kamera tidak didukung di browser ini. Gunakan input token manual.');
            return;
        }
        try {
            selesaiPindai = false;
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 } },
                audio: false
            });
            video.srcObject = stream;
            await video.play();
            statusPindai.textContent = 'Arahkan kamera ke QR Code…';
            garis.classList.remove('d-none');
            modal = new bootstrap.Modal(modalEl);
            modal.show();
            rafId = requestAnimationFrame(titikPindai);
        } catch (e) {
            alert('Kamera tidak dapat dibuka: ' + e.message +
                '\nPastikan izin kamera diberikan, atau gunakan input token manual.');
        }
    }

    function titikPindai() {
        if (selesaiPindai || !video.videoWidth) {
            rafId = requestAnimationFrame(titikPindai);
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        let hasil = null;
        try {
            hasil = window.jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'dontInvert'
            });
        } catch (e) { /* frame tidak terbaca - lanjut */ }

        if (hasil && hasil.data) {
            selesaiPindai = true;
            inputQr.value = hasil.data;
            statusPindai.textContent = '✅ QR Code terbaca!';
            garis.classList.add('d-none');
            tutupKamera();
            if (modal) modal.hide();
            cekBisaKirim();
            return;
        }

        rafId = requestAnimationFrame(titikPindai);
    }

    function tutupKamera() {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;
        if (stream) {
            stream.getTracks().forEach((t) => t.stop());
            stream = null;
        }
        video.srcObject = null;
        garis.classList.add('d-none');
    }

    if (btnPindai) btnPindai.addEventListener('click', bukaKamera);
    if (btnManual) {
        btnManual.addEventListener('click', function () {
            inputQr.focus();
            inputQr.select();
        });
    }
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', tutupKamera);
    }
    document.getElementById('btnTutupKamera').addEventListener('click', tutupKamera);

    /* ---------------------------------------------------------------
     *  Preview foto
     * ------------------------------------------------------------- */
    const preview = document.getElementById('previewFoto');
    if (inputFoto && preview) {
        inputFoto.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                preview.classList.add('d-none');
                return;
            }
            const img = preview.querySelector('img');
            img.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        });
    }

    /* ---------------------------------------------------------------
     *  Tombol kirim hanya aktif bila GPS + QR sudah terisi
     * ------------------------------------------------------------- */
    function cekBisaKirim() {
        const bisa = lokasiSiap && inputQr.value.trim().length > 0;
        btnKirim.disabled = !bisa;
    }
    if (inputQr) inputQr.addEventListener('input', cekBisaKirim);
    cekBisaKirim();

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!lokasiSiap) {
                e.preventDefault();
                alert('Lokasi GPS belum didapatkan. Tekan "Ambil Lokasi" terlebih dahulu.');
                return;
            }
            if (!inputQr.value.trim()) {
                e.preventDefault();
                alert('QR Code belum dipindai. Pindai QR atau isi token manual.');
                return;
            }
            btnKirim.disabled = true;
            btnKirim.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Memproses…';
        });
    }
})();
