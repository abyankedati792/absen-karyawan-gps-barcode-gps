/**
 * Halaman QR Code Admin:
 * - menampilkan QR dari payload
 * - menghitung mundur masa berlaku
 * - mengambil token baru ke server saat QR mau habis
 */
(function () {
    'use strict';

    const kotak = document.getElementById('qrcode');
    if (!kotak || typeof QRCode === 'undefined') return;

    const elPayload = document.getElementById('payloadText');
    const elSisa = document.getElementById('sisaWaktu');
    const elInfo = document.getElementById('infoQr');
    const btnSalin = document.getElementById('btnSalin');

    let payload = window.QR_AWAL;
    let expiresAt = new Date(window.QR_EXPIRES).getTime();
    const ukuran = Math.min(320, Math.max(220, window.innerWidth * 0.6));

    function gambar(text) {
        kotak.innerHTML = '';
        new QRCode(kotak, {
            text: text,
            width: ukuran,
            height: ukuran,
            colorDark: '#0b1f33',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    function formatSisa(detik) {
        if (detik < 0) detik = 0;
        const m = Math.floor(detik / 60);
        const s = detik % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    async function ambilToken() {
        try {
            const res = await fetch(window.QR_URL, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.ok) return;

            if (data.payload !== payload) {
                payload = data.payload;
                expiresAt = new Date(data.expires_at).getTime();
                gambar(payload);
                if (elPayload) elPayload.value = payload;
            }
            if (elInfo) {
                elInfo.innerHTML = 'Token: <code>' + payload.split('|')[3] + '</code><br>' +
                    'Berlaku sampai ' + new Date(expiresAt).toLocaleTimeString('id-ID') + ' WIB';
            }
        } catch (e) {
            /* offline / server sibuk - coba lagi nanti */
        }
    }

    function detik() {
        return Math.ceil((expiresAt - Date.now()) / 1000);
    }

    function tik() {
        const sisa = detik();
        if (elSisa) {
            elSisa.textContent = formatSisa(sisa) + ' lagi';
            elSisa.className = 'badge fs-6 ' + (sisa <= 60 ? 'bg-danger' : 'bg-light text-dark border');
        }
        const st = document.getElementById('serverTime');
        if (st) st.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });

        // Ambil QR baru menjelang habis ( sisa <= 60 detik, minimal tiap 20 detik )
        if (sisa <= 60) {
            if (!tik._last || Date.now() - tik._last > 20000) {
                tik._last = Date.now();
                ambilToken();
            }
        }
    }

    if (btnSalin) {
        btnSalin.addEventListener('click', function () {
            elPayload.select();
            elPayload.setSelectionRange(0, 99999);
            if (navigator.clipboard) {
                navigator.clipboard.writeText(elPayload.value);
            } else {
                document.execCommand('copy');
            }
            btnSalin.textContent = 'Tersalin!';
            setTimeout(() => (btnSalin.textContent = 'Salin'), 1500);
        });
    }

    gambar(payload);
    tik();
    setInterval(tik, 1000);
    setInterval(ambilToken, 30000);
})();
