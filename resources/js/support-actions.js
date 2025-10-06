/**
 * Support Ticket Actions
 * Handles all support ticket operations (create, reply, assign, escalate, close, reopen, delete)
 */

// Global state management
window.currentTicket = null;
window.currentTicketId = null;

/**
 * Parse JSON or fallback to redirect
 */
function parseJsonOrFallback(response, text) {
    try {
        return JSON.parse(text);
    } catch (e) {
        if (text.includes('<!DOCTYPE') || text.includes('<html')) {
            window.location.reload();
            return { success: true, redirected: true };
        }
        throw new Error('Invalid response format');
    }
}

/**
 * Create new ticket
 */
window.createTicket = function() {
    const form = document.getElementById('create-ticket-form');
    const formData = new FormData(form);

    fetch('/help/tickets', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal mencipta tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal mencipta tiket: ' + error.message);
    });
};

/**
 * View ticket details
 */
window.viewTicket = function(ticketId) {
    window.currentTicketId = ticketId;
    
    fetch(`/help/tickets/${ticketId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.currentTicket = data.ticket;
            // Populate modal with ticket data
            populateViewTicketModal(data.ticket);
            // Open the view modal (works for both admin & staff views)
            const root = document.getElementById('supportTicketsRoot') || document.querySelector('[x-data]');
            if (root && root.__x && root.__x.$data) {
                root.__x.$data.viewTicketModal = true;
            } else {
                // Fallback: dispatch event that Alpine can listen to
                window.dispatchEvent(new CustomEvent('open-view-ticket-modal'));
            }

            // Show/hide buttons based on user type and ticket status
            const isAdmin = data.current_user && data.current_user.jenis_organisasi === 'semua';
            const isStaff = data.current_user && data.current_user.jenis_organisasi !== 'semua';
            const isAndroidTicket = data.ticket && data.ticket.source === 'android';
            const isClosedTicket = data.ticket && data.ticket.status === 'selesai';
            
            // Escalate button: Only for staff viewing Android tickets
            const escalateBtn = document.getElementById('btn-escalate');
            if (escalateBtn) {
                escalateBtn.style.display = (isStaff && isAndroidTicket && !isClosedTicket) ? 'inline-flex' : 'none';
            }
            
            // Assign/Tugaskan button: For both admin AND staff viewing open tickets
            // Staff can use this to reassign within their org and add participants
            const assignBtn = document.getElementById('btn-assign');
            if (assignBtn) {
                assignBtn.style.display = !isClosedTicket ? 'inline-flex' : 'none';
            }
            
            // Close button: Only for admin viewing open tickets
            const closeBtn = document.getElementById('btn-close');
            if (closeBtn) {
                closeBtn.style.display = (isAdmin && !isClosedTicket) ? 'inline-flex' : 'none';
            }
        } else {
            throw new Error(data.message || 'Gagal memuat tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat tiket: ' + error.message);
    });
};

/**
 * Populate view ticket modal with data
 */
function populateViewTicketModal(ticket) {
    // Update ticket header info
    document.getElementById('ticket-number-display').textContent = ticket.ticket_number;
    document.getElementById('ticket-opened-ago').textContent = ticket.opened_ago ? `Dibuka: ${ticket.opened_ago}` : '';
    document.getElementById('ticket-subject-display').textContent = ticket.subject;
    document.getElementById('ticket-status-badge').textContent = ticket.status_label || '-';
    document.getElementById('ticket-priority-badge').textContent = ticket.priority_label || '-';
    document.getElementById('ticket-category-display').textContent = ticket.category;
    
    // Update creator info
    document.getElementById('ticket-creator-name').textContent = (ticket.creator && ticket.creator.name) ? ticket.creator.name : 'N/A';
    document.getElementById('ticket-organization-display').textContent = ticket.organization_name || 'N/A';
    document.getElementById('ticket-message-count').textContent = `${ticket.message_count || 0} mesej`;
    
    // Context
    const ipEl = document.getElementById('ticket-ip-display');
    if (ipEl) ipEl.textContent = ticket.ip_address || '-';
    const devEl = document.getElementById('ticket-device-display');
    if (devEl) devEl.textContent = ticket.device || '-';
    const platEl = document.getElementById('ticket-platform-display');
    if (platEl) platEl.textContent = ticket.platform || '-';
    const locEl = document.getElementById('ticket-location-display');
    if (locEl) {
        if (ticket.latitude && ticket.longitude) {
            const lat = Number(ticket.latitude).toFixed(5);
            const lng = Number(ticket.longitude).toFixed(5);
            locEl.innerHTML = `<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="text-blue-600 hover:underline">${lat}, ${lng}</a>`;
        } else {
            locEl.textContent = '-';
        }
    }
    
    // Extract and display original message (first non-system message from user)
    const originalMsg = (ticket.messages || []).find(m => m.role !== 'sistem');
    const originalSection = document.getElementById('original-message-section');
    if (originalMsg && originalSection) {
        originalSection.style.display = 'block';
        document.getElementById('original-message-content').textContent = originalMsg.message || 'Tiada maklumat';
        document.getElementById('original-message-author').textContent = (originalMsg.user && originalMsg.user.name) ? originalMsg.user.name : 'Pengguna';
        document.getElementById('original-message-time').textContent = originalMsg.created_at ? new Date(originalMsg.created_at).toLocaleString('ms-MY') : '-';
    } else if (originalSection) {
        originalSection.style.display = 'none';
    }
    
    // Populate messages
    const messagesContainer = document.getElementById('ticket-messages-container');
    messagesContainer.innerHTML = '';
    
    (ticket.messages || []).forEach(message => {
        const messageElement = createMessageElement(message);
        messagesContainer.appendChild(messageElement);
    });
    
    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

/**
 * Create message element
 */
function createMessageElement(message) {
    const role = message.role || 'pengguna';
    const roleLabel = message.role_label || (role === 'admin' ? 'Administrator' : role === 'sistem' ? 'Sistem' : 'Pengguna');
    const badgeColor = role === 'admin' ? 'bg-blue-600 text-white' : (role === 'sistem' ? 'bg-gray-600 text-white' : 'bg-purple-100 text-purple-800');

    const wrapper = document.createElement('div');
    wrapper.className = 'flex gap-3';

    const avatar = document.createElement('div');
    avatar.className = 'flex-shrink-0';
    avatar.innerHTML = `
        <div class="w-8 h-8 rounded-sm ${role === 'admin' ? 'bg-blue-100' : role === 'sistem' ? 'bg-gray-100' : 'bg-purple-100'} flex items-center justify-center">
            <span class="material-symbols-outlined ${role === 'admin' ? 'text-blue-600' : role === 'sistem' ? 'text-gray-600' : 'text-purple-600'} text-[16px]">${role === 'admin' ? 'support_agent' : 'person'}</span>
        </div>
    `;

    const content = document.createElement('div');
    content.className = 'flex-1';
    content.innerHTML = `
        <div class="bg-gray-50 rounded-sm p-3 border border-gray-200" style="text-align: left !important;">
            <div class="flex justify-between items-start mb-1">
                <div>
                    <div class="text-[11px] font-semibold text-gray-900" style="font-family: Poppins, sans-serif !important;">
                        ${message.user && message.user.name ? message.user.name : 'Sistem'}
                    </div>
                    <div class="text-[9px] text-gray-500" style="font-family: Poppins, sans-serif !important;">
                        ${new Date(message.created_at).toLocaleString('ms-MY')}
                    </div>
                </div>
                <span class="inline-flex items-center h-4 px-2 text-[9px] font-medium rounded-sm ${badgeColor}">
                    ${roleLabel.toUpperCase()}
                </span>
            </div>
            <div class="message-body text-[11px] text-gray-900 leading-relaxed" style="font-family: Poppins, sans-serif !important; white-space: pre-line; text-align: left !important;"></div>
        </div>
    `;

    wrapper.appendChild(avatar);
    wrapper.appendChild(content);
    // Set message textContent to avoid indentation from template whitespace
    const bodyEl = content.querySelector('.message-body');
    if (bodyEl) {
        bodyEl.textContent = (message.message || '').trim();
    }
    return wrapper;
}

/**
 * Reply to ticket (inline form in view modal)
 */
window.replyTicket = function() {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    // Use inline form instead of separate modal
    const form = document.getElementById('inline-reply-ticket-form');
    if (!form) {
        alert('Form tidak dijumpai');
        return;
    }
    
    const formData = new FormData(form);

    fetch(`/help/tickets/${window.currentTicketId}/reply`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            // Refresh ticket view to show new message
            viewTicket(window.currentTicketId);
            // Reset inline form
            form.reset();
            // Show success feedback
            const textarea = form.querySelector('textarea');
            textarea.placeholder = 'Balasan berjaya dihantar! ✓';
            setTimeout(() => {
                textarea.placeholder = 'Taip balasan anda di sini...';
            }, 3000);
        } else {
            throw new Error(data.message || 'Gagal menghantar balasan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menghantar balasan: ' + error.message);
    });
};

/**
 * Open assign ticket modal
 */
window.openAssignModal = function() {
    console.log('openAssignModal called, currentTicketId:', window.currentTicketId);
    
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    // Fetch ticket data
    fetch(`/help/tickets/${window.currentTicketId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(ticketData => {
        console.log('Ticket data:', ticketData);
        
        if (ticketData.success && ticketData.ticket) {
            const ticket = ticketData.ticket;
            
            // Populate modal header
            document.getElementById('assign-modal-ticket-number').textContent = 
                `Assign Tiket: ${ticket.ticket_number}`;
            document.getElementById('assign-modal-subject').textContent = 
                ticket.subject;
            
            // Show current assignment status
            const assignedText = ticket.assigned_admin 
                ? `Tiket ini di-assign kepada <strong>${ticket.assigned_admin.name}</strong>`
                : 'Tiket ini belum di-assign kepada sesiapa.';
            document.getElementById('current-assigned-text').innerHTML = assignedText;
            
            // Populate user selects
            populateUserSelects(ticket.assigned_to);
            
            // Load current participants
            loadParticipants(ticket.id);
            
            // Trigger Alpine.js modal
            console.log('Dispatching open-assign-ticket-modal event');
            window.dispatchEvent(new CustomEvent('open-assign-ticket-modal'));
        } else {
            throw new Error('Gagal memuat data tiket');
        }
    })
    .catch(error => {
        console.error('Error opening assign modal:', error);
        alert('Gagal membuka modal assign: ' + error.message);
    });
};

/**
 * Populate user select dropdowns
 */
function populateUserSelects(currentAssignedId) {
    // For now, fetch all users (backend will filter based on permissions)
    fetch('/api/users/list', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const assignSelect = document.getElementById('assign-user-select');
        const participantSelect = document.getElementById('participant-user-select');
        
        // Clear existing options
        assignSelect.innerHTML = '<option value="">Pilih pengguna...</option>';
        participantSelect.innerHTML = '<option value="">Pilih pengguna...</option>';
        
        // Populate both selects
        if (data.users && data.users.length > 0) {
            data.users.forEach(user => {
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
    })
    .catch(error => {
        console.error('Error loading users:', error);
    });
}

/**
 * Load current participants
 */
function loadParticipants(ticketId) {
    fetch(`/help/tickets/${ticketId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.ticket && data.ticket.participants) {
            displayParticipants(data.ticket.participants);
        }
    })
    .catch(error => {
        console.error('Error loading participants:', error);
    });
}

/**
 * Display participants list
 */
function displayParticipants(participants) {
    const container = document.getElementById('participants-list');
    
    if (!participants || participants.length === 0) {
        container.innerHTML = `
            <div class="text-[10px] text-gray-500 italic py-2" style="font-family: Poppins, sans-serif !important;">
                Tiada peserta lain dalam tiket ini.
            </div>
        `;
        return;
    }
    
    container.innerHTML = participants.map(p => `
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
    `).join('');
}

/**
 * Assign ticket to user (form submission)
 */
window.assignTicket = function() {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    const form = document.getElementById('assign-ticket-form');
    const formData = new FormData(form);

    fetch(`/help/tickets/${window.currentTicketId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            // Close modal
            window.dispatchEvent(new CustomEvent('close-assign-ticket-modal'));
            // Show success message
            alert(data.message || 'Tiket berjaya ditugaskan');
            // Reload page to refresh ticket list
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menugaskan tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menugaskan tiket: ' + error.message);
    });
};

/**
 * Add participant to ticket
 */
window.addParticipantToTicket = function() {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    const select = document.getElementById('participant-user-select');
    const userId = select.value;
    
    if (!userId) {
        alert('Sila pilih pengguna');
        return;
    }
    
    fetch(`/help/tickets/${window.currentTicketId}/participants`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reset select
            select.value = '';
            // Reload participants list
            loadParticipants(window.currentTicketId);
            // Show success
            alert(data.message);
        } else {
            throw new Error(data.message || 'Gagal menambah participant');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menambah participant: ' + error.message);
    });
};

/**
 * Remove participant from ticket
 */
window.removeParticipantFromTicket = function(userId) {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    if (!confirm('Buang participant ini dari tiket?')) {
        return;
    }
    
    fetch(`/help/tickets/${window.currentTicketId}/participants/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload participants list
            loadParticipants(window.currentTicketId);
            // Show success
            alert(data.message);
        } else {
            throw new Error(data.message || 'Gagal membuang participant');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal membuang participant: ' + error.message);
    });
};

/**
 * Escalate ticket priority (NO CONFIRM - direct escalate)
 */
window.escalateTicket = function() {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }

    fetch(`/help/tickets/${window.currentTicketId}/escalate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal escalate tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal escalate tiket: ' + error.message);
    });
};

/**
 * Close ticket
 */
window.closeTicket = function() {
    if (!window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    const resolutionNote = prompt('Nota penyelesaian (optional):');
    if (resolutionNote === null) return; // User cancelled

    fetch(`/help/tickets/${window.currentTicketId}/close`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            resolution_note: resolutionNote
        })
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal menutup tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal menutup tiket: ' + error.message);
    });
};

/**
 * Reopen closed ticket
 */
window.reopenTicket = function(ticketId) {
    if (!ticketId && !window.currentTicketId) {
        alert('Tiket tidak dijumpai');
        return;
    }
    
    const id = ticketId || window.currentTicketId;
    
    if (!confirm('Adakah anda pasti untuk membuka semula tiket ini?')) {
        return;
    }

    fetch(`/help/tickets/${id}/reopen`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.text())
    .then(text => {
        const data = parseJsonOrFallback(null, text);
        if (data.redirected) return;
        
        if (data.success) {
            window.location.reload();
        } else {
            throw new Error(data.message || 'Gagal membuka semula tiket');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal membuka semula tiket: ' + error.message);
    });
};

/**
 * Delete ticket (Admin only, with verification code)
 */
window.deleteSupportTicket = function(ticketId, ticketNumber) {
    openDeleteConfirmModal(
        'Padam Tiket Sokongan',
        `Adakah anda pasti untuk memadam tiket <strong>${ticketNumber}</strong>?`,
        'Padam Tiket',
        `/help/tickets/${ticketId}`,
        '/help/hubungi-sokongan'
    );
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Create ticket form submission
    const createForm = document.getElementById('create-ticket-form');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            createTicket();
        });
    }
    
    // Inline reply form submission (in ticket view modal)
    // Note: Form might not exist on page load, so we use event delegation
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'inline-reply-ticket-form') {
            e.preventDefault();
            replyTicket();
        }
    });
    
    // Assign form submission
    const assignForm = document.getElementById('assign-ticket-form');
    if (assignForm) {
        assignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            assignTicket();
        });
    }
});
