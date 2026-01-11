window.currentTicket = null;
window.currentTicketId = null;
function parseJsonOrFallback(response, text) {
  try {
    return JSON.parse(text);
  } catch (e) {
    if (text.includes("<!DOCTYPE") || text.includes("<html")) {
      window.location.reload();
      return { success: true, redirected: true };
    }
    throw new Error("Invalid response format");
  }
}
window.createTicket = function() {
  const form = document.getElementById("create-ticket-form");
  const formData = new FormData(form);
  fetch("/help/tickets", {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    },
    body: formData
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      window.location.reload();
    } else {
      throw new Error(data.message || "Gagal mencipta tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal mencipta tiket: " + error.message);
  });
};
let messagePollingInterval = null;
let lastMessageCount = 0;
let typingTimeout = null;
window.viewTicket = function(ticketId) {
  window.currentTicketId = ticketId;
  if (messagePollingInterval) {
    clearInterval(messagePollingInterval);
    messagePollingInterval = null;
  }
  fetch(`/help/tickets/${ticketId}`, {
    method: "GET",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.json()).then((data) => {
    if (data.success) {
      window.currentTicket = data.ticket;
      populateViewTicketModal(data.ticket);
      const root = document.getElementById("supportTicketsRoot") || document.querySelector("[x-data]");
      if (root && root.__x && root.__x.$data) {
        root.__x.$data.viewTicketModal = true;
      } else {
        window.dispatchEvent(new CustomEvent("open-view-ticket-modal"));
      }
      const isAdmin = data.current_user && data.current_user.jenis_organisasi === "semua";
      const isStaff = data.current_user && data.current_user.jenis_organisasi !== "semua";
      const isAndroidTicket = data.ticket && data.ticket.source === "android";
      const isClosedTicket = data.ticket && (data.ticket.status === "selesai" || data.ticket.status === "ditutup");
      const isCreator = data.ticket && data.current_user && data.ticket.creator && parseInt(data.ticket.creator.id) === parseInt(data.current_user.id);
      const replySection = document.getElementById("inline-reply-section");
      if (replySection) {
        replySection.style.display = isClosedTicket ? "none" : "block";
      }
      const escalateBtn = document.getElementById("btn-escalate");
      if (escalateBtn) {
        escalateBtn.style.display = isStaff && isAndroidTicket && !isClosedTicket ? "inline-flex" : "none";
      }
      const assignBtn = document.getElementById("btn-assign");
      if (assignBtn) {
        assignBtn.style.display = !isClosedTicket ? "inline-flex" : "none";
      }
      const closeBtn = document.getElementById("btn-close");
      if (closeBtn) {
        closeBtn.style.display = isAdmin && !isClosedTicket ? "inline-flex" : "none";
      }
      const reopenBtn = document.getElementById("btn-reopen");
      if (reopenBtn) {
        reopenBtn.style.display = (isAdmin || isCreator) && isClosedTicket ? "inline-flex" : "none";
      }
      const exportBtn = document.getElementById("btn-export");
      if (exportBtn) {
        exportBtn.style.display = isClosedTicket ? "inline-flex" : "none";
      }
      if (!isClosedTicket) {
        lastMessageCount = data.ticket.messages.length;
        startMessagePolling(ticketId);
      }
    } else {
      throw new Error(data.message || "Gagal memuat tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal memuat tiket: " + error.message);
  });
};
function startMessagePolling(ticketId) {
  if (messagePollingInterval) {
    clearInterval(messagePollingInterval);
  }
  messagePollingInterval = setInterval(() => {
    fetch(`/help/tickets/${ticketId}`, {
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "Accept": "application/json"
      }
    }).then((response) => response.json()).then((data) => {
      if (data.success && data.ticket) {
        const newMessageCount = data.ticket.messages.length;
        if (newMessageCount > lastMessageCount) {
          lastMessageCount = newMessageCount;
          const container = document.getElementById("ticket-messages-container");
          if (container) {
            container.innerHTML = "";
            data.ticket.messages.forEach((msg) => {
              container.appendChild(createMessageElement(msg));
            });
            container.scrollIntoView({ behavior: "smooth", block: "end" });
            playNotificationSound();
          }
        }
        checkTypingStatus();
      }
    }).catch((error) => {
      console.error("Polling error:", error);
    });
  }, 3e3);
}
function playNotificationSound() {
  const audio = new Audio("data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKnl86xhGwU7k9n0yXgiBS16yO/ajj4IF14w4IuYIwU2jdLuxG8gAycF");
  audio.volume = 0.3;
  audio.play().catch((e) => console.log("Sound play failed:", e));
}
function populateViewTicketModal(ticket) {
  document.getElementById("ticket-number-display").textContent = ticket.ticket_number;
  document.getElementById("ticket-opened-ago").textContent = ticket.opened_ago ? `Dibuka: ${ticket.opened_ago}` : "";
  document.getElementById("ticket-subject-display").textContent = ticket.subject;
  document.getElementById("ticket-status-badge").textContent = ticket.status_label || "-";
  document.getElementById("ticket-priority-badge").textContent = ticket.priority_label || "-";
  document.getElementById("ticket-category-display").textContent = ticket.category;
  document.getElementById("ticket-creator-name").textContent = ticket.creator && ticket.creator.name ? ticket.creator.name : "N/A";
  document.getElementById("ticket-organization-display").textContent = ticket.organization_name || "N/A";
  document.getElementById("ticket-message-count").textContent = `${ticket.message_count || 0} mesej`;
  const ipEl = document.getElementById("ticket-ip-display");
  if (ipEl) ipEl.textContent = ticket.ip_address || "-";
  const devEl = document.getElementById("ticket-device-display");
  if (devEl) devEl.textContent = ticket.device || "-";
  const platEl = document.getElementById("ticket-platform-display");
  if (platEl) platEl.textContent = ticket.platform || "-";
  const locEl = document.getElementById("ticket-location-display");
  if (locEl) {
    if (ticket.latitude && ticket.longitude) {
      const lat = Number(ticket.latitude).toFixed(5);
      const lng = Number(ticket.longitude).toFixed(5);
      locEl.innerHTML = `<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="text-blue-600 hover:underline">${lat}, ${lng}</a>`;
    } else {
      locEl.textContent = "-";
    }
  }
  const originalMsg = (ticket.messages || []).find((m) => m.role !== "sistem");
  const originalSection = document.getElementById("original-message-section");
  if (originalMsg && originalSection) {
    originalSection.style.display = "block";
    document.getElementById("original-message-content").textContent = originalMsg.message || "Tiada maklumat";
    document.getElementById("original-message-author").textContent = originalMsg.user && originalMsg.user.name ? originalMsg.user.name : "Pengguna";
    document.getElementById("original-message-time").textContent = originalMsg.created_at ? new Date(originalMsg.created_at).toLocaleString("ms-MY") : "-";
  } else if (originalSection) {
    originalSection.style.display = "none";
  }
  const messagesContainer = document.getElementById("ticket-messages-container");
  messagesContainer.innerHTML = "";
  (ticket.messages || []).forEach((message) => {
    const messageElement = createMessageElement(message);
    messagesContainer.appendChild(messageElement);
  });
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
function createMessageElement(message) {
  const role = message.role || "pengguna";
  let badgeLabel = message.role_label || (role === "admin" ? "Administrator" : role === "sistem" ? "Sistem" : "Pengguna");
  if (message.organization_label && role !== "admin" && role !== "sistem") {
    badgeLabel = message.organization_label;
  }
  const badgeColor = role === "admin" ? "bg-blue-600 text-white" : role === "sistem" ? "bg-gray-600 text-white" : "bg-purple-100 text-purple-800";
  const wrapper = document.createElement("div");
  wrapper.className = "flex gap-3";
  const avatar = document.createElement("div");
  avatar.className = "flex-shrink-0";
  avatar.innerHTML = `
        <div class="w-8 h-8 rounded-sm ${role === "admin" ? "bg-blue-100" : role === "sistem" ? "bg-gray-100" : "bg-purple-100"} flex items-center justify-center">
            <span class="material-symbols-outlined ${role === "admin" ? "text-blue-600" : role === "sistem" ? "text-gray-600" : "text-purple-600"} text-[16px]">${role === "admin" ? "support_agent" : "person"}</span>
        </div>
    `;
  let attachmentsHtml = "";
  if (message.attachments && message.attachments.length > 0) {
    attachmentsHtml = `
            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                ${message.attachments.map((att) => {
      const fileName = att.split("/").pop();
      const fileExt = fileName.split(".").pop().toLowerCase();
      const isImage = ["jpg", "jpeg", "png", "gif"].includes(fileExt);
      return `
                        <div class="flex items-center gap-2 text-[10px] text-gray-600 hover:text-blue-600 cursor-pointer" onclick="viewAttachment('/storage/${att}', '${fileName}', ${isImage})">
                            <span class="material-symbols-outlined text-[14px]">${isImage ? "image" : "description"}</span>
                            <span style="font-family: Poppins, sans-serif !important;">${fileName}</span>
                        </div>
                    `;
    }).join("")}
            </div>
        `;
  }
  const content = document.createElement("div");
  content.className = "flex-1";
  let metaInfoHtml = `<span>${new Date(message.created_at).toLocaleString("ms-MY")}</span>`;
  if (message.ip_address) {
    metaInfoHtml += `
            <span class="text-gray-400 mx-1.5">•</span>
            <span class="inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[11px]" style="vertical-align: middle;">router</span>
                <span>${message.ip_address}</span>
            </span>
        `;
  }
  if (message.location) {
    const locationContent = message.latitude && message.longitude ? `<a href="https://www.google.com/maps?q=${message.latitude},${message.longitude}" target="_blank" class="inline-flex items-center gap-1 hover:text-blue-600">
                <span class="material-symbols-outlined text-[11px]" style="vertical-align: middle;">location_on</span>
                <span>${message.location}</span>
               </a>` : `<span class="inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[11px]" style="vertical-align: middle;">location_on</span>
                <span>${message.location}</span>
               </span>`;
    metaInfoHtml += `<span class="text-gray-400 mx-1.5">•</span>${locationContent}`;
  }
  content.innerHTML = `
        <div class="bg-gray-50 rounded-sm p-3 border border-gray-200" style="text-align: left !important;">
            <div class="flex justify-between items-start mb-1">
                <div>
                    <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                        ${message.user && message.user.name ? message.user.name : "Sistem"}
                    </div>
                    <div class="flex items-center text-[10px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                        ${metaInfoHtml}
                    </div>
                </div>
                <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm ${badgeColor}">
                    ${badgeLabel.toUpperCase()}
                </span>
            </div>
            <div class="message-body text-[11px] text-gray-900 leading-relaxed" style="font-family: Poppins, sans-serif !important; white-space: pre-line; text-align: left !important;"></div>
            ${attachmentsHtml}
        </div>
    `;
  wrapper.appendChild(avatar);
  wrapper.appendChild(content);
  const bodyEl = content.querySelector(".message-body");
  if (bodyEl) {
    bodyEl.textContent = (message.message || "").trim();
  }
  return wrapper;
}
window.replyTicket = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  const form = document.getElementById("inline-reply-ticket-form");
  if (!form) {
    alert("Form tidak dijumpai");
    return;
  }
  const formData = new FormData(form);
  fetch(`/help/tickets/${window.currentTicketId}/reply`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    },
    body: formData
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      viewTicket(window.currentTicketId);
      form.reset();
      document.getElementById("reply-file-list").innerHTML = "";
      const textarea = form.querySelector("textarea");
      textarea.placeholder = "Balasan berjaya dihantar! ✓";
      setTimeout(() => {
        textarea.placeholder = "Taip balasan anda di sini...";
      }, 3e3);
    } else {
      throw new Error(data.message || "Gagal menghantar balasan");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal menghantar balasan: " + error.message);
  });
};
window.openAssignModal = function() {
  console.log("openAssignModal called, currentTicketId:", window.currentTicketId);
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  fetch(`/help/tickets/${window.currentTicketId}`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((r) => r.json()).then((ticketData) => {
    console.log("Ticket data:", ticketData);
    if (ticketData.success && ticketData.ticket) {
      const ticket = ticketData.ticket;
      document.getElementById("assign-modal-ticket-number").textContent = `Assign Tiket: ${ticket.ticket_number}`;
      document.getElementById("assign-modal-subject").textContent = ticket.subject;
      const assignedText = ticket.assigned_admin ? `Tiket ini di-assign kepada <strong>${ticket.assigned_admin.name}</strong>` : "Tiket ini belum di-assign kepada sesiapa.";
      document.getElementById("current-assigned-text").innerHTML = assignedText;
      populateUserSelects(ticket.assigned_to);
      loadParticipants(ticket.id);
      console.log("Dispatching open-assign-ticket-modal event");
      window.dispatchEvent(new CustomEvent("open-assign-ticket-modal"));
    } else {
      throw new Error("Gagal memuat data tiket");
    }
  }).catch((error) => {
    console.error("Error opening assign modal:", error);
    alert("Gagal membuka modal assign: " + error.message);
  });
};
function populateUserSelects(currentAssignedId) {
  fetch("/api/users/list", {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.json()).then((data) => {
    const assignSelect = document.getElementById("assign-user-select");
    const participantSelect = document.getElementById("participant-user-select");
    assignSelect.innerHTML = '<option value="">Pilih pengguna...</option>';
    participantSelect.innerHTML = '<option value="">Pilih pengguna...</option>';
    if (data.users && data.users.length > 0) {
      data.users.forEach((user) => {
        const option1 = new Option(
          `${user.name} (${user.email})`,
          user.id,
          false,
          user.id == currentAssignedId
        );
        const option2 = new Option(
          `${user.name} (${user.email})`,
          user.id
        );
        assignSelect.add(option1);
        participantSelect.add(option2);
      });
    }
  }).catch((error) => {
    console.error("Error loading users:", error);
  });
}
function loadParticipants(ticketId) {
  fetch(`/help/tickets/${ticketId}`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.json()).then((data) => {
    if (data.success && data.ticket && data.ticket.participants) {
      displayParticipants(data.ticket.participants);
    }
  }).catch((error) => {
    console.error("Error loading participants:", error);
  });
}
function displayParticipants(participants) {
  const container = document.getElementById("participants-list");
  if (!participants || participants.length === 0) {
    container.innerHTML = `
            <div class="text-[10px] text-gray-500 italic py-2" style="font-family: Poppins, sans-serif !important;">
                Tiada peserta lain dalam tiket ini.
            </div>
        `;
    return;
  }
  container.innerHTML = participants.map((p) => `
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-sm border border-gray-200">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-600 text-[14px]">person</span>
                <span class="text-[10px] font-medium" style="font-family: Poppins, sans-serif !important;">${p.name}</span>
                <span class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">(${p.email})</span>
            </div>
            <button type="button" onclick="removeParticipantFromTicket(${p.id})" class="text-red-600 hover:text-red-800">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    `).join("");
}
window.assignTicket = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  const form = document.getElementById("assign-ticket-form");
  const formData = new FormData(form);
  fetch(`/help/tickets/${window.currentTicketId}/assign`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    },
    body: formData
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      window.dispatchEvent(new CustomEvent("close-assign-ticket-modal"));
      alert(data.message || "Tiket berjaya ditugaskan");
      window.location.reload();
    } else {
      throw new Error(data.message || "Gagal menugaskan tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal menugaskan tiket: " + error.message);
  });
};
window.addParticipantToTicket = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  const select = document.getElementById("participant-user-select");
  const userId = select.value;
  if (!userId) {
    alert("Sila pilih pengguna");
    return;
  }
  fetch(`/help/tickets/${window.currentTicketId}/participants`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ user_id: userId })
  }).then((response) => response.json()).then((data) => {
    if (data.success) {
      select.value = "";
      loadParticipants(window.currentTicketId);
      alert(data.message);
    } else {
      throw new Error(data.message || "Gagal menambah participant");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal menambah participant: " + error.message);
  });
};
window.removeParticipantFromTicket = function(userId) {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  if (!confirm("Buang participant ini dari tiket?")) {
    return;
  }
  fetch(`/help/tickets/${window.currentTicketId}/participants/${userId}`, {
    method: "DELETE",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.json()).then((data) => {
    if (data.success) {
      loadParticipants(window.currentTicketId);
      alert(data.message);
    } else {
      throw new Error(data.message || "Gagal membuang participant");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal membuang participant: " + error.message);
  });
};
window.escalateTicket = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  fetch(`/help/tickets/${window.currentTicketId}/escalate`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      window.location.reload();
    } else {
      throw new Error(data.message || "Gagal escalate tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal escalate tiket: " + error.message);
  });
};
window.closeTicket = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  const resolutionNote = prompt("Nota penyelesaian (optional):");
  if (resolutionNote === null) return;
  fetch(`/help/tickets/${window.currentTicketId}/close`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      resolution_note: resolutionNote
    })
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      window.location.reload();
    } else {
      throw new Error(data.message || "Gagal menutup tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal menutup tiket: " + error.message);
  });
};
window.reopenTicket = function(ticketId) {
  if (!ticketId && !window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  const id = ticketId || window.currentTicketId;
  if (!confirm("Adakah anda pasti untuk membuka semula tiket ini?")) {
    return;
  }
  fetch(`/help/tickets/${id}/reopen`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.text()).then((text) => {
    const data = parseJsonOrFallback(null, text);
    if (data.redirected) return;
    if (data.success) {
      window.location.reload();
    } else {
      throw new Error(data.message || "Gagal membuka semula tiket");
    }
  }).catch((error) => {
    console.error("Error:", error);
    alert("Gagal membuka semula tiket: " + error.message);
  });
};
window.deleteSupportTicket = function(ticketId, ticketNumber) {
  openDeleteConfirmModal({
    title: "Padam Tiket Sokongan",
    message: `Adakah anda pasti untuk memadam tiket <strong>${ticketNumber}</strong>?`,
    warning: "AMARAN: Tindakan ini akan memadam tiket dan semua mesej secara kekal. Data tidak boleh dipulihkan semula.",
    buttonText: "Padam Tiket",
    url: `/help/tickets/${ticketId}`,
    redirectUrl: "/help/hubungi-sokongan"
  });
};
window.exportChatHistory = function() {
  if (!window.currentTicketId) {
    alert("Tiket tidak dijumpai");
    return;
  }
  window.open(`/help/tickets/${window.currentTicketId}/export`, "_blank");
};
window.sendTypingStatus = function() {
  if (!window.currentTicketId) return;
  clearTimeout(typingTimeout);
  fetch(`/help/tickets/${window.currentTicketId}/typing`, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "Content-Type": "application/json"
    }
  }).catch((e) => console.log("Typing status error:", e));
  typingTimeout = setTimeout(() => {
  }, 3e3);
};
function checkTypingStatus() {
  if (!window.currentTicketId) return;
  fetch(`/help/tickets/${window.currentTicketId}/typing`, {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Accept": "application/json"
    }
  }).then((response) => response.json()).then((data) => {
    const indicator = document.getElementById("typing-indicator");
    const userName = document.getElementById("typing-user-name");
    if (data.success && data.typing_users && data.typing_users.length > 0) {
      if (indicator && userName) {
        userName.textContent = data.typing_users[0].user_name;
        indicator.style.display = "block";
      }
    } else {
      if (indicator) {
        indicator.style.display = "none";
      }
    }
  }).catch((e) => console.log("Check typing error:", e));
}
window.viewAttachment = function(url, filename, isImage) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-[10000] overflow-y-auto";
  modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="this.parentElement.remove()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-sm shadow-xl w-full max-w-3xl max-h-[85vh] my-8 flex flex-col">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white text-[20px]">${isImage ? "image" : "description"}</span>
                        <h3 class="text-white font-semibold" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">
                            ${filename}
                        </h3>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-white hover:text-gray-200">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1 flex items-center justify-center bg-gray-50">
                    ${isImage ? `<img src="${url}" alt="${filename}" class="max-w-full max-h-[60vh] object-contain rounded-sm shadow-lg" />` : `<div class="text-center">
                            <span class="material-symbols-outlined text-gray-400 text-[64px]">description</span>
                            <p class="text-[12px] text-gray-600 mt-4" style="font-family: Poppins, sans-serif !important;">${filename}</p>
                            <a href="${url}" download="${filename}" class="inline-block mt-4 h-8 px-4 text-[11px] font-medium rounded-sm bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                <span class="material-symbols-outlined text-[14px] align-middle mr-1">download</span>
                                Muat Turun
                            </a>
                        </div>`}
                </div>
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <a href="${url}" download="${filename}" class="h-8 px-4 text-[11px] rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">download</span>
                        Muat Turun
                    </a>
                    <button onclick="this.closest('.fixed').remove()" class="h-8 px-4 text-[11px] rounded-sm bg-gray-700 text-white hover:bg-gray-800 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    `;
  document.body.appendChild(modal);
};
document.addEventListener("DOMContentLoaded", function() {
  const createForm = document.getElementById("create-ticket-form");
  if (createForm) {
    createForm.addEventListener("submit", function(e) {
      e.preventDefault();
      createTicket();
    });
  }
  document.addEventListener("submit", function(e) {
    if (e.target && e.target.id === "inline-reply-ticket-form") {
      e.preventDefault();
      replyTicket();
    }
  });
  const assignForm = document.getElementById("assign-ticket-form");
  if (assignForm) {
    assignForm.addEventListener("submit", function(e) {
      e.preventDefault();
      assignTicket();
    });
  }
});
