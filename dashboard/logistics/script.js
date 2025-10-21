// Role-local JS for Logistics & Transport Coordinator (frontend-only)
function showToast(msg) {
  const el = document.createElement('div');
  el.textContent = msg;
  el.style = 'position:fixed;right:18px;bottom:18px;background:#111;color:#fff;padding:8px 12px;border-radius:6px;';
  document.body.appendChild(el);
  setTimeout(function () {
    el.remove();
  }, 2200);
}

function loadRequests() {
  showToast('Requests refreshed (demo)');
}

function actionApprove(id) {
  showToast('Approved: ' + id);
}

function actionReject(id) {
  showToast('Rejected: ' + id);
}

function allocateBerth() {
  showToast('Berth allocated (demo)');
}

function startInspect(cid) {
  showToast('Inspection started: ' + cid);
}

function markStatus(sel, cid) {
  showToast('Marked ' + sel.value + ' for ' + cid);
}

function showDoc(name) {
  alert('This is a frontend demo. Replace with a file viewer. File: ' + name);
}

function assignStorage(cid) {
  showToast('Assign storage for ' + cid);
}

function assignStorageForm() {
  showToast('Storage assigned (demo)');
}

function assignTransport(el, cid) {
  showToast('Assigned ' + el.value + ' for ' + cid);
}

function generateInvoice() {
  var b = Number(document.getElementById('berthFee') ? document.getElementById('berthFee').value : 0);
  var s = Number(document.getElementById('storageFee') ? document.getElementById('storageFee').value : 0);
  var d = Number(document.getElementById('dutyFee') ? document.getElementById('dutyFee').value : 0);
  showToast('Invoice total: ' + (b + s + d));
}

function submitShipRequest() {
  showToast('Ship request submitted (demo)');
}

function uploadDoc() {
  showToast('Document uploaded (demo)');
}

function refreshStatus(cid) {
  showToast('Status refreshed for ' + cid);
}
