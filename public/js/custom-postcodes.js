/**
 * Custom Postcodes Extension for Malaysia Postcodes
 * 
 * This file contains additional postcodes that are missing from the main
 * malaysia-postcodes library. These will be checked first before falling
 * back to the main library.
 */

window.customPostcodes = {
    // Sarawak - Missing postcodes
    '96010': { city: 'Selangau', state: 'Sarawak' },
    
    // Add more custom postcodes here as needed
    // Format: 'postcode': { city: 'City Name', state: 'State Name' }
};

/**
 * Enhanced postcode finder that checks custom postcodes first
 * @param {string} postcode - 5-digit postcode to search
 * @returns {object} - Result object with found, city, and state properties
 */
window.findPostcodeEnhanced = function(postcode) {
    // Check custom postcodes first
    if (window.customPostcodes && window.customPostcodes[postcode]) {
        return {
            found: true,
            city: window.customPostcodes[postcode].city,
            state: window.customPostcodes[postcode].state
        };
    }
    
    // Fallback to main malaysia-postcodes library
    if (window.malaysiaPostcodes && typeof window.malaysiaPostcodes.findPostcode === 'function') {
        return window.malaysiaPostcodes.findPostcode(postcode);
    }
    
    // If neither available, return not found
    return {
        found: false,
        city: '',
        state: ''
    };
};
