function generateApprovalCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}
function validateApprovalCode() {
  const generated = document.getElementById("generatedApprovalCodeHidden").value;
  const entered = document.getElementById("approvalCodeConfirm").value;
  const msgEl = document.getElementById("approvalCodeMatchMsg");
  const submitBtn = document.getElementById("approvalSubmitBtn");
  if (entered.length === 6) {
    if (entered === generated) {
      msgEl.textContent = "✓ Kod sepadan";
      msgEl.className = "mt-2 text-[10px] text-green-600 font-medium";
      msgEl.classList.remove("hidden");
      submitBtn.disabled = false;
    } else {
      msgEl.textContent = "✗ Kod tidak sepadan";
      msgEl.className = "mt-2 text-[10px] text-red-600 font-medium";
      msgEl.classList.remove("hidden");
      submitBtn.disabled = true;
    }
  } else {
    msgEl.classList.add("hidden");
    submitBtn.disabled = true;
  }
}
function openApprovalModal(id) {
  const form = document.getElementById("approvalForm");
  form.action = `/laporan/laporan-tuntutan/${id}/approve`;
  document.getElementById("approvalCodeConfirm").value = "";
  const code = generateApprovalCode();
  document.getElementById("generatedApprovalCode").textContent = code;
  document.getElementById("generatedApprovalCodeHidden").value = code;
  document.getElementById("approvalCodeMatchMsg").classList.add("hidden");
  document.getElementById("approvalSubmitBtn").disabled = true;
  document.getElementById("approvalModal").classList.remove("hidden");
}
function closeApprovalModal() {
  document.getElementById("approvalModal").classList.add("hidden");
}
function openRejectModal(id) {
  const form = document.getElementById("rejectForm");
  form.action = `/laporan/laporan-tuntutan/${id}/reject`;
  document.getElementById("alasan_tolak").value = "";
  document.getElementById("rejectModal").classList.remove("hidden");
}
function closeRejectModal() {
  document.getElementById("rejectModal").classList.add("hidden");
}
function openCancelModal(id) {
  const form = document.getElementById("cancelForm");
  form.action = `/laporan/laporan-tuntutan/${id}/cancel`;
  document.getElementById("alasan_gantung").value = "";
  document.getElementById("cancelModal").classList.remove("hidden");
}
function closeCancelModal() {
  document.getElementById("cancelModal").classList.add("hidden");
}
function generateDeleteCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}
function validateDeleteCode() {
  const generated = document.getElementById("generatedDeleteCodeHidden").value;
  const entered = document.getElementById("deleteCodeConfirm").value;
  const msgEl = document.getElementById("deleteCodeMatchMsg");
  const submitBtn = document.getElementById("deleteSubmitBtn");
  if (entered.length === 6) {
    if (entered === generated) {
      msgEl.textContent = "✓ Kod sepadan";
      msgEl.className = "mt-2 text-[10px] text-green-600 font-medium";
      msgEl.classList.remove("hidden");
      submitBtn.disabled = false;
    } else {
      msgEl.textContent = "✗ Kod tidak sepadan";
      msgEl.className = "mt-2 text-[10px] text-red-600 font-medium";
      msgEl.classList.remove("hidden");
      submitBtn.disabled = true;
    }
  } else {
    msgEl.classList.add("hidden");
    submitBtn.disabled = true;
  }
}
function openDeleteModal(id) {
  window.currentDeleteId = id;
  document.getElementById("deleteCodeConfirm").value = "";
  const code = generateDeleteCode();
  document.getElementById("generatedDeleteCode").textContent = code;
  document.getElementById("generatedDeleteCodeHidden").value = code;
  document.getElementById("deleteCodeMatchMsg").classList.add("hidden");
  document.getElementById("deleteSubmitBtn").disabled = true;
  document.getElementById("deleteModal").classList.remove("hidden");
}
function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  window.currentDeleteId = null;
}
function deleteItem(id) {
  openDeleteModal(id);
}
function approveItem(id) {
  openApprovalModal(id);
}
window.approveItem = approveItem;
window.openApprovalModal = openApprovalModal;
window.closeApprovalModal = closeApprovalModal;
window.openRejectModal = openRejectModal;
window.closeRejectModal = closeRejectModal;
window.openCancelModal = openCancelModal;
window.closeCancelModal = closeCancelModal;
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.validateApprovalCode = validateApprovalCode;
window.validateDeleteCode = validateDeleteCode;
window.deleteItem = deleteItem;
window.generateApprovalCode = generateApprovalCode;
window.generateDeleteCode = generateDeleteCode;
document.addEventListener("DOMContentLoaded", function() {
  function parseJsonOrFallback(response) {
    const contentType = response.headers.get("content-type") || "";
    if (contentType.includes("application/json")) {
      return response.json();
    }
    return response.text().then(function() {
      return { success: true };
    });
  }
  const approvalForm = document.getElementById("approvalForm");
  if (approvalForm) {
    approvalForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch(this.action, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      }).then(parseJsonOrFallback).then((data) => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message || "Ralat berlaku");
        }
      }).catch((error) => {
        console.error("Error:", error);
        alert("Ralat berlaku. Sila cuba lagi.");
      });
    });
  }
  const rejectForm = document.getElementById("rejectForm");
  if (rejectForm) {
    rejectForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch(this.action, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      }).then(parseJsonOrFallback).then((data) => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message || "Ralat berlaku");
        }
      }).catch((error) => {
        console.error("Error:", error);
        alert("Ralat berlaku. Sila cuba lagi.");
      });
    });
  }
  const cancelForm = document.getElementById("cancelForm");
  if (cancelForm) {
    cancelForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch(this.action, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      }).then(parseJsonOrFallback).then((data) => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message || "Ralat berlaku");
        }
      }).catch((error) => {
        console.error("Error:", error);
        alert("Ralat berlaku. Sila cuba lagi.");
      });
    });
  }
  const deleteForm = document.getElementById("deleteForm");
  if (deleteForm) {
    deleteForm.addEventListener("submit", function(e) {
      e.preventDefault();
      if (!window.currentDeleteId) {
        alert("ID tuntutan tidak dijumpai");
        return;
      }
      const formData = new FormData();
      formData.append("_method", "DELETE");
      fetch(`/laporan/laporan-tuntutan/${window.currentDeleteId}`, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: formData
      }).then(parseJsonOrFallback).then((data) => {
        if (data.success) {
          window.location.href = "/laporan/laporan-tuntutan";
        } else {
          alert(data.message || "Ralat berlaku");
        }
      }).catch((error) => {
        console.error("Error:", error);
        alert("Ralat berlaku. Sila cuba lagi.");
      });
    });
  }
  document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
      closeApprovalModal();
      closeRejectModal();
      closeCancelModal();
      closeDeleteModal();
    }
  });
});
