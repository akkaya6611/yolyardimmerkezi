(function () {
    'use strict';
    const root = document.getElementById('mis360-firma-tools');
    if (!root) return;
    const el = (id) => document.getElementById(id);
    const start = el('mis-delete-start');
    const stop = el('mis-delete-stop');
    const cancel = el('mis-delete-cancel');
    const refresh = el('mis-refresh');
    const trash = el('mis-include-trash');
    const status = el('mis-status');
    const number = (value) => Number(value).toLocaleString('tr-TR');
    let job = null;
    let counts = null;
    let running = false;
    let busy = false;
    let uncertain = false;
    let timer = null;
    const activeJob = () => job && ['ready', 'error'].includes(job.phase);

    function message(text, error) {
        status.textContent = text;
        status.classList.toggle('mis360-error', !!error);
    }

    function render() {
        const active = activeJob();
        const total = counts ? (trash.checked ? counts.all : counts.active) : 0;
        start.disabled = busy || running || uncertain || !counts || (active ? !job.owned : total === 0);
        start.textContent = active ? (job.phase === 'error' ? 'Yeniden dene' : 'Devam et') : 'Tüm Firmaları Sil';
        stop.disabled = !running;
        cancel.hidden = !active || !job.owned;
        cancel.disabled = busy || running || uncertain;
        refresh.disabled = busy || running;
        trash.disabled = busy || running || !!active;
        if (counts) {
            el('mis-scope').textContent = 'Son kontrolde toplam firma: ' + number(counts.all) + ' · Çöp kutusunda: ' + number(counts.all - counts.active);
        }
        if (job) {
            el('mis-total').textContent = number(job.total);
            el('mis-deleted').textContent = number(job.deleted);
            el('mis-remaining').textContent = number(job.remaining);
            el('mis-progress').value = job.percent;
            el('mis-percent').textContent = '%' + job.percent;
            el('mis-changed').hidden = !job.changed;
            el('mis-changed').textContent = number(job.changed) + ' kayıt işlem dışında silinmiş veya kapsamdan çıkmış.';
        } else {
            el('mis-total').textContent = counts ? number(total) : '—';
            el('mis-deleted').textContent = '0';
            el('mis-remaining').textContent = counts ? number(total) : '—';
            el('mis-progress').value = 0;
            el('mis-percent').textContent = '%0';
            el('mis-changed').hidden = true;
        }
    }

    async function request(operation, data) {
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 45000);
        try {
            const response = await fetch(mis360Firma.ajaxUrl, {
                method: 'POST', credentials: 'same-origin', cache: 'no-store',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams(Object.assign({ action: 'mis360_firma_v3_' + operation, nonce: mis360Firma.nonce }, data || {})).toString(),
                signal: controller.signal
            });
            let result;
            try { result = await response.json(); }
            catch (_) { throw new Error('Sunucudan geçerli yanıt alınamadı. Oturumunuzu ve sunucu hata günlüğünü kontrol edin.'); }
            if (!response.ok || !result || !result.success || !result.data) {
                throw new Error(result && result.data && result.data.message ? result.data.message : 'İstek tamamlanamadı. Oturumunuzu kontrol edin.');
            }
            return result.data;
        } catch (error) {
            if (error.name === 'AbortError') throw new Error('Sunucu yanıtı zaman aşımına uğradı. İşlem sunucuda devam ediyor olabilir.');
            throw error;
        } finally { window.clearTimeout(timeout); }
    }

    function fail(error) {
        running = false;
        uncertain = true;
        message(error.message + ' Devam etmeden önce “Durumu yenile” düğmesine basın.', true);
    }

    function accept(data) {
        if (!data.job || typeof data.job.remaining !== 'number') throw new Error('İşlem durumu okunamadı.');
        job = data.job;
        if (activeJob()) trash.checked = job.includeTrash;
    }

    async function loadStatus() {
        if (busy || running) return;
        busy = true;
        render();
        try {
            const data = await request('status');
            if (typeof data.all !== 'number' || typeof data.active !== 'number') throw new Error('Firma sayısı okunamadı.');
            counts = data;
            job = data.job;
            uncertain = false;
            if (activeJob()) {
                trash.checked = job.includeTrash;
                message(!job.owned ? 'Başka bir yöneticiye ait silme işlemi var. İşlemi başlatan yönetici devam edebilir.' :
                    (job.phase === 'error' ? job.message : 'İşlem duraklatılmış. Devam et düğmesiyle sürdürebilirsiniz.'), job.phase === 'error');
            } else {
                message(job ? job.message || 'Tamamlandı. Silinecek firma yok.' : 'Hazır. Silme işlemini başlatabilirsiniz.');
            }
        } catch (error) { fail(error); }
        finally { busy = false; render(); }
    }

    async function batch() {
        if (!running || busy || !activeJob()) return;
        busy = true;
        render();
        try {
            accept(await request('batch', { job_id: job.id, sequence: job.sequence + 1 }));
            if (job.phase === 'completed' || job.phase === 'cancelled') {
                running = false;
                message(job.message);
            } else if (job.phase === 'error') {
                running = false;
                message(job.message, true);
            } else {
                message(running ? 'Siliniyor… ' + number(job.deleted) + ' firma silindi; ' + number(job.remaining) + ' kaldı.' : 'Duraklatıldı. Devam et düğmesiyle sürdürebilirsiniz.');
            }
        } catch (error) { fail(error); }
        finally { busy = false; render(); }
        if (running) timer = window.setTimeout(batch, 200);
        else if (job && job.phase === 'completed' && !uncertain) await loadStatus();
    }

    start.addEventListener('click', async function () {
        if (start.disabled) return;
        if (!activeJob()) {
            const total = trash.checked ? counts.all : counts.active;
            if (!window.confirm(number(total) + ' firma kalıcı olarak silinecek.\n' + (trash.checked ? 'Çöp kutusundaki firmalar dahildir.' : 'Çöp kutusundaki firmalar korunur.') + '\nVeritabanı yedeğinizi aldıysanız devam edin.')) return;
        }
        running = true;
        message('Silme işlemi başlatılıyor…');
        if (!activeJob()) {
            busy = true;
            render();
            try {
                accept(await request('start', { confirm: 'SIL', include_trash: trash.checked ? '1' : '0' }));
                if (job.phase === 'completed') { running = false; message('Silinecek firma yok.'); }
            } catch (error) { fail(error); }
            finally { busy = false; }
        }
        render();
        if (running) await batch();
        else if (!uncertain && activeJob()) message('Duraklatıldı. Devam et düğmesiyle sürdürebilirsiniz.');
    });

    stop.addEventListener('click', function () {
        running = false;
        window.clearTimeout(timer);
        message(busy ? 'Duraklatılıyor… Devam eden küçük grubun tamamlanması bekleniyor.' : 'Duraklatıldı. Devam et düğmesiyle sürdürebilirsiniz.');
        render();
    });

    cancel.addEventListener('click', async function () {
        if (cancel.disabled || !activeJob()) return;
        if (!window.confirm('Kalan firmaların silinmesini sonlandırmak istiyor musunuz? Daha önce silinen firmalar geri alınmaz.')) return;
        busy = true;
        render();
        try { accept(await request('cancel', { job_id: job.id })); }
        catch (error) { fail(error); }
        finally { busy = false; render(); }
        if (!uncertain) await loadStatus();
    });

    trash.addEventListener('change', function () { if (!activeJob()) job = null; render(); });
    refresh.addEventListener('click', loadStatus);
    window.addEventListener('beforeunload', function (event) {
        if (running || busy) { event.preventDefault(); event.returnValue = ''; }
    });
    if (typeof mis360Firma === 'undefined') {
        message('Araç ayarları yüklenemedi. Güncellemedeki functions.php ve inc/admin-tools.php dosyalarını birlikte yükleyin.', true);
        refresh.disabled = true;
        return;
    }
    loadStatus();
}());
