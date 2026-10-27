<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>GeoJSON Map | EUDR</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/leaflet/leaflet.css') }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font: 15px/1.6 system-ui, sans-serif; color: #1c3541; background: #f5f8fa; }
        header { padding: 18px 24px; background: white; border-bottom: 1px solid #dbe4e8; }
        h1 { font-size: 23px; margin: 6px 0; }
        .filename { color: #57707b; overflow-wrap: anywhere; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
        button, .button { font: inherit; cursor: pointer; border: 1px solid #c7d8d0; padding: 8px 14px; background: white; border-radius: 6px; color: #17533d; text-decoration: none; }
        button:disabled { opacity: .5; cursor: default; }
        .button { background: #17533d; color: white; }
        main { display: grid; grid-template-columns: minmax(0, 1fr) 290px; }
        #map { height: calc(100dvh - 190px); min-height: 450px; background: #e7efeb; }
        aside { background: white; padding: 20px; overflow-wrap: anywhere; overflow-y: auto; max-height: calc(100dvh - 190px); }
        h2 { font-size: 17px; margin: 0 0 12px; }
        #status { margin: 0; padding: 12px 24px; background: #eaf2ee; }
        #status.error { background: #fff0ed; color: #9f2f20; }
        dt { font-weight: 600; margin-top: 8px; } dd { margin: 0; }
        .leaflet-popup-content { max-width: 260px; overflow-wrap: anywhere; }
        @media(max-width: 700px) { main { grid-template-columns: 1fr; } #map { height: 60dvh; min-height: 340px; } aside { max-height: none; } header { padding: 14px; } }
    </style>
</head>
<body>
<header>
    <a href="{{ $backUrl }}">← กลับหน้ารายงาน</a>
    <h1>แผนที่พื้นที่ต้นทาง / Origin Map</h1>
    <div class="filename">{{ $fileName }}</div>
    <div class="toolbar">
        <button id="fit" type="button" disabled>แสดงทุกแปลง</button>
        <button id="basemap" type="button" aria-pressed="true">ซ่อนแผนที่พื้นหลัง</button>
        <a class="button" href="{{ $downloadUrl }}">ดาวน์โหลด GeoJSON ต้นฉบับ</a>
    </div>
</header>
<p id="status" role="status" aria-live="polite">กำลังโหลดข้อมูลแผนที่…</p>
<main>
    <div id="map" aria-label="แผนที่พื้นที่ต้นทาง"></div>
    <aside><h2>รายละเอียดพื้นที่</h2><div id="details">คลิกขอบเขตแปลงหรือหมุดเพื่อดูรายละเอียด</div></aside>
</main>
<script src="{{ asset('assets/vendor/leaflet/leaflet.js') }}"></script>
<script>
(async function () {
    const status = document.getElementById('status');
    const details = document.getElementById('details');
    const fit = document.getElementById('fit');
    function message(text, error = false) { status.textContent = text; status.classList.toggle('error', error); }
    if (typeof L === 'undefined') { message('ไม่สามารถโหลดเครื่องมือแผนที่ได้ กรุณารีเฟรชหน้า', true); return; }
    const map = L.map('map').setView([13.5, 101], 6);
    let tileWarning = false;
    const basemapButton = document.getElementById('basemap');
    const basemap = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        referrerPolicy: 'strict-origin-when-cross-origin',
        maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).on('tileerror', function () {
        tileWarning = true;
        if (map.hasLayer(basemap)) map.removeLayer(basemap);
        basemapButton.setAttribute('aria-pressed', 'false');
        basemapButton.textContent = 'แสดงแผนที่พื้นหลัง';
        message('โหลดแผนที่พื้นหลังไม่ได้ แต่ยังดูขอบเขตและรายละเอียดพื้นที่ได้', true);
    }).addTo(map);
    basemapButton.addEventListener('click', function () {
        const visible = map.hasLayer(basemap);
        if (visible) map.removeLayer(basemap); else basemap.addTo(map);
        basemapButton.setAttribute('aria-pressed', String(!visible));
        basemapButton.textContent = visible ? 'แสดงแผนที่พื้นหลัง' : 'ซ่อนแผนที่พื้นหลัง';
    });
    function describe(feature, index) {
        const box = document.createElement('div');
        const title = document.createElement('strong');
        title.textContent = 'พื้นที่ / Feature ' + index;
        box.appendChild(title);
        const list = document.createElement('dl');
        const type = document.createElement('dt'); type.textContent = 'รูปแบบพื้นที่';
        const value = document.createElement('dd'); value.textContent = feature.geometry?.type || 'Geometry';
        list.append(type, value);
        Object.entries(feature.properties || {}).forEach(([key, property]) => {
            const label = document.createElement('dt'); label.textContent = key;
            const text = document.createElement('dd'); text.textContent = String(property);
            list.append(label, text);
        });
        box.appendChild(list);
        return box;
    }
    try {
        const response = await fetch(@json($dataUrl), { credentials: 'same-origin', headers: { Accept: 'application/json' }, cache: 'no-store' });
        if (response.status === 401 || response.status === 403) throw new Error('Session หมดอายุ กรุณากลับหน้ารายงานและเข้าสู่ระบบอีกครั้ง');
        if (response.status === 404) throw new Error('ไม่พบไฟล์ หรือสิทธิ์เข้าถึงรายงานหมดอายุแล้ว');
        if (!response.ok) throw new Error('ไม่สามารถแสดงไฟล์นี้ได้: ไฟล์ต้องเป็น GeoJSON ที่ถูกต้องและพิกัดเป็น longitude/latitude');
        const data = await response.json();
        let count = 0;
        const layer = L.geoJSON(data, {
            style: { color: '#137454', weight: 2.5, fillColor: '#2fa875', fillOpacity: .24 },
            pointToLayer: (feature, latlng) => L.circleMarker(latlng, { radius: 7, color: '#14543e', fillColor: '#45b984', fillOpacity: .9, weight: 2 }),
            onEachFeature: (feature, item) => {
                const index = ++count;
                item.bindPopup(() => describe(feature, index));
                item.on('click', () => details.replaceChildren(describe(feature, index)));
            }
        }).addTo(map);
        const bounds = layer.getBounds();
        if (!bounds.isValid()) { message('ไฟล์นี้ไม่มีพื้นที่หรือพิกัดที่แสดงบนแผนที่ได้'); return; }
        const fitAll = () => map.fitBounds(bounds, { padding: [28, 28], maxZoom: 17 });
        fitAll(); fit.disabled = false; fit.addEventListener('click', fitAll);
        if (!tileWarning) message('แสดง ' + count + ' พื้นที่ / features · คลิกแปลงหรือหมุดเพื่อดูรายละเอียด');
    } catch (error) { message(error.message || 'ไม่สามารถโหลดข้อมูลแผนที่ได้', true); }
})();
</script>
</body>
</html>
