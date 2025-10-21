<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Cargo & Warehouse Manager') {
    header("Location: ../../login/login.php");
    exit();
}
$warehouse_manager_name = $_SESSION['user']['full_name'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cargo & Warehouse Manager</title>
  <link rel="stylesheet" href="../dashboard.css">
  <style>
    .table {
      table-layout: fixed;
      width: 100%;
    }
    
    .table th, .table td {
      padding: 14px 16px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
<header class="topbar">
    <div class="title">
        <span class="logo">neoport</span>
        <span class="divider">/</span>
        <span class="role">Cargo & Warehouse Manager</span>
    </div>
    <div class="user-info">
        <span class="who am i"><?php echo htmlspecialchars($warehouse_manager_name); ?></span>
        <a href="/pms/logout.php" class="btn ghost">Logout</a>
    </div>
</header>

<main class="page">

  <section class="card">
    <div class="card-head">
        <h3>Cleared Containers</h3>
        <p class="small">Only shipping requests that are <b>Approved by Customs</b>.</p>
    </div>
    <table id="clearedTable" class="table">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>Ship Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody><tr><td colspan="3">Loading…</td></tr></tbody>
    </table>
  </section>

  <section class="card">
    <div class="card-head">
        <h3>Storage Assignment</h3>
    </div>
    <form id="assignForm" style="display: flex; align-items: flex-end; gap: 16px;">
      <div style="flex-grow: 1;">
        <label for="requestId" style="display: block; margin-bottom: 4px;">Request ID</label>
        <select id="requestId" style="width: 100%;"></select>
      </div>

      <div style="flex-grow: 1;">
        <label for="shipName" style="display: block; margin-bottom: 4px;">Ship Name</label>
        <input id="shipName" readonly style="width: 100%;">
      </div>

      <div style="flex-grow: 1;">
        <label for="containerName" style="display: block; margin-bottom: 4px;">Container ID / Name</label>
        <input id="containerName" placeholder="e.g., CONT-101" required style="width: 100%;">
      </div>

      <div style="flex-grow: 1;">
        <label for="slotName" style="display: block; margin-bottom: 4px;">Storage Slot</label>
        <input id="slotName" placeholder="e.g., Slot-A1" required style="width: 100%;">
      </div>

      <button type="submit" class="btn">Assign</button>
    </form>
  </section>

  <section class="card">
    <div class="card-head">
        <h3>Warehouse Occupancy</h3>
    </div>
    <table id="occupancyTable" class="table">
      <thead>
        <tr>
          <th>Slot</th>
          <th>Container</th>
          <th>Ship</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody><tr><td colspan="4">Loading…</td></tr></tbody>
    </table>
  </section>

</main>

</main>

<footer class="footer"><p>Port Management System © <?= date('Y') ?></p></footer>

<script>
// tiny toast
function toast(msg){const el=document.createElement('div');el.textContent=msg;
el.style='position:fixed;right:18px;bottom:18px;background:#111;color:#fff;padding:8px 12px;border-radius:6px;z-index:9999';
document.body.appendChild(el);setTimeout(()=>el.remove(),2200);}

async function getJSON(url, opts){ const r = await fetch(url, opts); return r.json(); }

async function loadCleared(){
  const {success, data, message} = await getJSON('cargo_api.php?action=get_cleared');
  const tb = document.querySelector('#clearedTable tbody'); tb.innerHTML='';
  if(!success){ tb.innerHTML = `<tr><td colspan="3">${message||'Failed'}</td></tr>`; return; }
  if(!data.length){ tb.innerHTML = `<tr><td colspan="3">No data available.</td></tr>`; return; }
  data.forEach(r=>{
    tb.insertAdjacentHTML('beforeend', `<tr>
      <td>${r.id}</td>
      <td>${r.ship_name}</td>
      <td>approved</td>
    </tr>`);
  });
}

async function loadFormOptions(){
  const {success, data} = await getJSON('cargo_api.php?action=get_form_options');
  const sel = document.getElementById('requestId');
  const shipInput = document.getElementById('shipName');
  sel.innerHTML = '';
  if(!success || !data.length){
    sel.innerHTML = '<option value="">No unassigned approved requests</option>';
    shipInput.value = '';
    return;
  }
  data.forEach(r=>{
    const opt = document.createElement('option');
    opt.value = r.id;
    opt.textContent = `${r.id} — ${r.ship_name}`;
    opt.dataset.ship = r.ship_name;
    sel.appendChild(opt);
  });
  // set ship name for first item
  shipInput.value = sel.options.length ? sel.options[0].dataset.ship : '';
  sel.addEventListener('change', ()=>{ shipInput.value = sel.selectedOptions[0]?.dataset.ship || ''; });
}

async function loadOccupancy(){
  const {success, data, message} = await getJSON('cargo_api.php?action=get_occupancy');
  const tb = document.querySelector('#occupancyTable tbody'); tb.innerHTML='';
  if(!success){ tb.innerHTML = `<tr><td colspan="4">${message||'Failed'}</td></tr>`; return; }
  if(!data.length){ tb.innerHTML = `<tr><td colspan="4">No assignments yet.</td></tr>`; return; }
  data.forEach(r=>{
    tb.insertAdjacentHTML('beforeend', `<tr>
      <td>${r.storage_slot_name}</td>
      <td>${r.container_name}</td>
      <td>${r.ship_name}</td>
      <td>${r.status}</td>
    </tr>`);
  });
}

async function assignStorage(e){
  e.preventDefault();
  const requestId     = document.getElementById('requestId').value;
  const containerName = document.getElementById('containerName').value.trim();
  const slotName      = document.getElementById('slotName').value.trim();

  if(!requestId || !containerName || !slotName){ toast('Please fill all fields.'); return; }

  const body = new URLSearchParams({
    action:'assign_storage',
    shipping_request_id: requestId,
    container_name: containerName,
    storage_slot_name: slotName
  });

  const resp = await getJSON('cargo_api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body });
  if(!resp.success){ alert(resp.message||'Failed'); return; }

  toast('Assigned!');
  // refresh all blocks
  await Promise.all([loadCleared(), loadFormOptions(), loadOccupancy()]);
  // reset only container + slot
  document.getElementById('containerName').value='';
  document.getElementById('slotName').value='';
}

document.getElementById('assignForm').addEventListener('submit', assignStorage);

(async function init(){
  await Promise.all([loadCleared(), loadFormOptions(), loadOccupancy()]);
})();
</script>
</body>
</html>
