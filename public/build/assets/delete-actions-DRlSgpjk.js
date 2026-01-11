function generateDeleteConfirmCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}
function validateDeleteConfirmCode() {
  const generated = document.getElementById("generatedDeleteConfirmCodeHidden").value;
  const entered = document.getElementById("deleteConfirmCodeInput").value;
  const msgEl = document.getElementById("deleteConfirmCodeMatchMsg");
  const submitBtn = document.getElementById("deleteConfirmSubmitBtn");
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
function openDeleteConfirmModal(options) {
  const form = document.getElementById("deleteConfirmForm");
  form.action = options.url;
  document.getElementById("deleteModalTitle").textContent = options.title || "Padam Item";
  document.getElementById("deleteModalWarning").textContent = options.warning || "AMARAN: Tindakan ini akan memadam item secara kekal dari sistem. Data tidak boleh dipulihkan semula.";
  document.getElementById("deleteModalBtnText").textContent = options.buttonText || "Padam";
  window.deleteRedirectUrl = options.redirectUrl || null;
  document.getElementById("deleteConfirmCodeInput").value = "";
  const code = generateDeleteConfirmCode();
  document.getElementById("generatedDeleteConfirmCode").textContent = code;
  document.getElementById("generatedDeleteConfirmCodeHidden").value = code;
  document.getElementById("deleteConfirmCodeMatchMsg").classList.add("hidden");
  document.getElementById("deleteConfirmSubmitBtn").disabled = true;
  document.getElementById("deleteConfirmModal").classList.remove("hidden");
}
function closeDeleteConfirmModal() {
  document.getElementById("deleteConfirmModal").classList.add("hidden");
  window.deleteRedirectUrl = null;
}
function deleteTuntutanItem(id) {
  openDeleteConfirmModal({
    url: `/laporan/laporan-tuntutan/${id}`,
    title: "Padam Tuntutan",
    warning: "AMARAN: Tindakan ini akan memadam tuntutan secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Tuntutan",
    redirectUrl: "/laporan/laporan-tuntutan"
  });
}
function deleteProgramItem(id) {
  openDeleteConfirmModal({
    url: `/program/${id}`,
    title: "Padam Program",
    warning: "AMARAN: Tindakan ini akan memadam program secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Program",
    redirectUrl: "/program"
  });
}
function deleteLogPemanduItem(id) {
  openDeleteConfirmModal({
    url: `/log-pemandu/${id}`,
    title: "Padam Log Pemandu",
    warning: "AMARAN: Tindakan ini akan memadam log pemandu secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Log",
    redirectUrl: "/log-pemandu"
  });
}
function deleteBahagianItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-risda/bahagian/${id}`,
    title: "Padam RISDA Bahagian",
    warning: "AMARAN: Tindakan ini akan memadam RISDA Bahagian secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Bahagian",
    redirectUrl: "/pengurusan/senarai-risda?tab=bahagian"
  });
}
function deleteStesenItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-risda/stesen/${id}`,
    title: "Padam RISDA Stesen",
    warning: "AMARAN: Tindakan ini akan memadam RISDA Stesen secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Stesen",
    redirectUrl: "/pengurusan/senarai-risda?tab=stesen"
  });
}
function deleteStafItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-risda/staf/${id}`,
    title: "Padam RISDA Staf",
    warning: "AMARAN: Tindakan ini akan memadam RISDA Staf secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Staf",
    redirectUrl: "/pengurusan/senarai-risda?tab=staf"
  });
}
function deleteKumpulanItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-kumpulan/${id}`,
    title: "Padam Kumpulan",
    warning: "AMARAN: Tindakan ini akan memadam kumpulan secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Kumpulan",
    redirectUrl: "/pengurusan/senarai-kumpulan"
  });
}
function deletePenggunaItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-pengguna/${id}`,
    title: "Padam Pengguna",
    warning: "AMARAN: Tindakan ini akan memadam pengguna secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Pengguna",
    redirectUrl: "/pengurusan/senarai-pengguna"
  });
}
function deleteKenderaanItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-kenderaan/${id}`,
    title: "Padam Kenderaan",
    warning: "AMARAN: Tindakan ini akan memadam kenderaan secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Kenderaan",
    redirectUrl: "/pengurusan/senarai-kenderaan"
  });
}
function deleteSelenggaraItem(id) {
  openDeleteConfirmModal({
    url: `/pengurusan/senarai-selenggara/${id}`,
    title: "Padam Selenggara",
    warning: "AMARAN: Tindakan ini akan memadam rekod selenggara secara kekal dari sistem. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Selenggara",
    redirectUrl: "/pengurusan/senarai-selenggara"
  });
}
window.openDeleteConfirmModal = openDeleteConfirmModal;
window.closeDeleteConfirmModal = closeDeleteConfirmModal;
window.validateDeleteConfirmCode = validateDeleteConfirmCode;
window.deleteTuntutanItem = deleteTuntutanItem;
window.deleteProgramItem = deleteProgramItem;
window.deleteLogPemanduItem = deleteLogPemanduItem;
window.deleteBahagianItem = deleteBahagianItem;
window.deleteStesenItem = deleteStesenItem;
window.deleteStafItem = deleteStafItem;
window.deleteKumpulanItem = deleteKumpulanItem;
window.deletePenggunaItem = deletePenggunaItem;
window.deleteKenderaanItem = deleteKenderaanItem;
window.deleteSelenggaraItem = deleteSelenggaraItem;
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
  const deleteConfirmForm = document.getElementById("deleteConfirmForm");
  if (deleteConfirmForm) {
    deleteConfirmForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append("_method", "DELETE");
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
          if (window.deleteRedirectUrl) {
            window.location.href = window.deleteRedirectUrl;
          } else {
            window.location.reload();
          }
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
      closeDeleteConfirmModal();
    }
  });
});
