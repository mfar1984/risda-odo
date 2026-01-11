/**
 * Audit Trail JavaScript Tracker
 * 
 * Tracks button clicks and form submissions for audit trail logging.
 * Add data-audit-track attribute to elements you want to track.
 * 
 * Usage:
 * <button data-audit-track="approve-program" data-audit-action="Luluskan Program">Lulus</button>
 * <form data-audit-form="create-program">...</form>
 */

(function() {
    'use strict';

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    /**
     * Send tracking data to server
     */
    async function sendTrackingData(endpoint, data) {
        if (!csrfToken) {
            console.warn('AuditTrail: CSRF token not found');
            return;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                console.warn('AuditTrail: Failed to send tracking data', response.status);
            }
        } catch (error) {
            // Silently fail - don't break user experience
            console.warn('AuditTrail: Error sending tracking data', error);
        }
    }

    /**
     * Track button clicks
     */
    function trackButtonClick(event) {
        const target = event.target.closest('[data-audit-track]');
        if (!target) return;

        const buttonId = target.dataset.auditTrack;
        const actionName = target.dataset.auditAction || target.textContent.trim() || buttonId;

        sendTrackingData('/pengurusan/audit-trail/click', {
            button_id: buttonId,
            action_name: actionName,
            url: window.location.href,
            route_name: document.body.dataset.routeName || null,
        });
    }

    /**
     * Track form submissions
     */
    function trackFormSubmit(event) {
        const form = event.target;
        if (!form.dataset.auditForm) return;

        const formName = form.dataset.auditForm;

        sendTrackingData('/pengurusan/audit-trail/form-submit', {
            form_name: formName,
            success: true, // Will be updated by server response
            url: window.location.href,
        });
    }

    /**
     * Initialize tracking
     */
    function init() {
        // Track button clicks
        document.addEventListener('click', trackButtonClick, true);

        // Track form submissions
        document.addEventListener('submit', trackFormSubmit, true);

        console.log('AuditTrail: Tracker initialized');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
