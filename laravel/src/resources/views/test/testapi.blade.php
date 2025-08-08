<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Guests Data Report</title>
  <style>
    body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial; padding: 24px; max-width: 720px; margin: auto; }
    label { display:block; margin-top:12px; font-weight:600; }
    input { padding:8px; width:100%; box-sizing:border-box; margin-top:6px; }
    button { margin-top:16px; padding:10px 16px; font-size:16px; cursor:pointer; }
    pre { background:#f6f8fa; padding:12px; border-radius:6px; margin-top:16px; overflow:auto; }
    .hint { color:#666; font-size:13px; margin-top:6px; }
  </style>
</head>
<body>
  <h1>Generate Guests Data Report</h1>

  <form id="reportForm">
    <label for="startdate">Start Date</label>
    <input id="startdate" name="startdate" type="date" required />

    <label for="enddate">End Date</label>
    <input id="enddate" name="enddate" type="date" required />

    <div class="hint">Tanggal harus format YYYY-MM-DD. Start date tidak boleh lebih besar dari end date.</div>

    <button type="submit">Submit JSON</button>
    <button type="button" id="downloadCsvBtn">Download CSV</button>
  </form>

  <pre id="result" aria-live="polite">Hasil akan muncul di sini...</pre>

  <script>
    const form = document.getElementById('reportForm');
    const resultEl = document.getElementById('result');
    const downloadBtn = document.getElementById('downloadCsvBtn');

    const API_JSON = 'http://localhost:8888/api/guests/';
    const API_CSV  = 'http://localhost:8888/api/guests/export-csv';

    function validateDates(startVal, endVal) {
      if (!startVal || !endVal) {
        resultEl.textContent = 'Isi kedua tanggal terlebih dahulu.';
        return false;
      }
      const startDate = new Date(startVal);
      const endDate = new Date(endVal);
      if (startDate > endDate) {
        resultEl.textContent = 'Start date tidak boleh lebih besar dari end date.';
        return false;
      }
      return true;
    }

    form.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      resultEl.textContent = 'Mengirim...';

      const startVal = document.getElementById('startdate').value;
      const endVal = document.getElementById('enddate').value;

      if (!validateDates(startVal, endVal)) return;

      const payload = { startdate: startVal, enddate: endVal };

      try {
        const res = await fetch(API_JSON, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-API-KEY':'45780843-GJOJXKQJ-KO91K2E8' },
          body: JSON.stringify(payload)
        });

        const text = await res.text();
        try {
          const json = JSON.parse(text);
          resultEl.textContent = JSON.stringify({ status: res.status, body: json }, null, 2);
        } catch {
          resultEl.textContent = `HTTP ${res.status}\n\n${text}`;
        }
      } catch (err) {
        resultEl.textContent = 'Request gagal: ' + String(err);
      }
    });

    downloadBtn.addEventListener('click', async () => {
      resultEl.textContent = '';
      const startVal = document.getElementById('startdate').value;
      const endVal = document.getElementById('enddate').value;

      if (!validateDates(startVal, endVal)) return;

      const payload = { startdate: startVal, enddate: endVal };

      try {
        const res = await fetch(API_CSV, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-API-KEY':'45780843-GJOJXKQJ-KO91K2E8' },
          body: JSON.stringify(payload)
        });

        if (!res.ok) {
          const errText = await res.text();
          resultEl.textContent = `Gagal download CSV: ${res.status} ${errText}`;
          return;
        }

        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `guests_${startVal}_to_${endVal}.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);

        resultEl.textContent = 'Download CSV dimulai.';
      } catch (err) {
        resultEl.textContent = 'Request CSV gagal: ' + String(err);
      }
    });
  </script>
</body>
</html>
