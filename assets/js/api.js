const API_BASE = window.location.origin;

async function getCSRFToken() {
    try {
        const response = await fetch(`${API_BASE}/api/csrf-token.php`);
        const data = await response.json();
        return data.token;
    } catch (error) {
        console.error('Error getting CSRF token:', error);
        return null;
    }
}

async function apiRequest(endpoint, options = {}) {
    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'حدث خطأ في الاتصال بالخادم' };
    }
}

function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px; text-align: center;';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function formatPrice(price) {
    return parseFloat(price).toLocaleString('ar-MA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getProductStatusBadge(status) {
    const statuses = {
        'available': { text: 'متوفرة', class: 'status-available' },
        'reserved': { text: 'محجوزة', class: 'status-reserved' },
        'sold': { text: 'مباعة', class: 'status-sold' }
    };
    const statusInfo = statuses[status] || statuses['available'];
    return `<span class="product-status ${statusInfo.class}">${escapeHtml(statusInfo.text)}</span>`;
}
