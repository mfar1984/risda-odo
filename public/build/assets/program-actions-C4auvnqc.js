function generateApproveProgramCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}
function validateApproveProgramCode() {
  const generated = document.getElementById("generatedApproveProgramCodeHidden").value;
  const entered = document.getElementById("approveProgramCodeConfirm").value;
  const msgEl = document.getElementById("approveProgramCodeMatchMsg");
  const submitBtn = document.getElementById("approveProgramSubmitBtn");
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
function openApproveProgramModal(programId) {
  const form = document.getElementById("approveProgramForm");
  form.action = `/program/${programId}/approve`;
  document.getElementById("approveProgramCodeConfirm").value = "";
  const code = generateApproveProgramCode();
  document.getElementById("generatedApproveProgramCode").textContent = code;
  document.getElementById("generatedApproveProgramCodeHidden").value = code;
  document.getElementById("approveProgramCodeMatchMsg").classList.add("hidden");
  document.getElementById("approveProgramSubmitBtn").disabled = true;
  document.getElementById("approveProgramModal").classList.remove("hidden");
}
function closeApproveProgramModal() {
  document.getElementById("approveProgramModal").classList.add("hidden");
}
function approveProgramItem(id) {
  openApproveProgramModal(id);
}
function generateRejectProgramCode() {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";
  for (let i = 0; i < 6; i++) {
    code += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return code;
}
function validateRejectProgramCode() {
  const generated = document.getElementById("generatedRejectProgramCodeHidden").value;
  const entered = document.getElementById("rejectProgramCodeConfirm").value;
  const msgEl = document.getElementById("rejectProgramCodeMatchMsg");
  const submitBtn = document.getElementById("rejectProgramSubmitBtn");
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
function openRejectProgramModal(programId) {
  const form = document.getElementById("rejectProgramForm");
  form.action = `/program/${programId}/reject`;
  document.getElementById("rejectProgramCodeConfirm").value = "";
  const code = generateRejectProgramCode();
  document.getElementById("generatedRejectProgramCode").textContent = code;
  document.getElementById("generatedRejectProgramCodeHidden").value = code;
  document.getElementById("rejectProgramCodeMatchMsg").classList.add("hidden");
  document.getElementById("rejectProgramSubmitBtn").disabled = true;
  document.getElementById("rejectProgramModal").classList.remove("hidden");
}
function closeRejectProgramModal() {
  document.getElementById("rejectProgramModal").classList.add("hidden");
}
function rejectProgramItem(id) {
  openRejectProgramModal(id);
}
window.approveProgramItem = approveProgramItem;
window.openApproveProgramModal = openApproveProgramModal;
window.closeApproveProgramModal = closeApproveProgramModal;
window.rejectProgramItem = rejectProgramItem;
window.openRejectProgramModal = openRejectProgramModal;
window.closeRejectProgramModal = closeRejectProgramModal;
window.validateApproveProgramCode = validateApproveProgramCode;
window.validateRejectProgramCode = validateRejectProgramCode;
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
  const approveProgramForm = document.getElementById("approveProgramForm");
  if (approveProgramForm) {
    approveProgramForm.addEventListener("submit", function(e) {
      e.preventDefault();
      fetch(this.action, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: new FormData(this)
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
  const rejectProgramForm = document.getElementById("rejectProgramForm");
  if (rejectProgramForm) {
    rejectProgramForm.addEventListener("submit", function(e) {
      e.preventDefault();
      fetch(this.action, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json"
        },
        body: new FormData(this)
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
  document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
      closeApproveProgramModal();
      closeRejectProgramModal();
    }
  });
});
