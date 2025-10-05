/**
 * Log Pemandu Actions - Centralized JavaScript for Deletion
 */

// ========================================
// DELETE LOG PEMANDU
// ========================================
function generateDeleteLogCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
}

function validateDeleteLogCode() {
    
