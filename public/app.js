// Vortex PHP Demo - Client-side JavaScript

let currentUser = null;

// Check auth status on load
window.onload = function() {
    checkAuthStatus();
};

function checkAuthStatus() {
    fetch('/api/auth/me')
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Not authenticated');
        })
        .then(user => {
            currentUser = user;
            showAuthenticatedSections();
            showResult('login-result', `Logged in as ${user.email}`, 'success');
        })
        .catch(() => {
            currentUser = null;
            hideAuthenticatedSections();
        });
}

function showAuthenticatedSections() {
    document.getElementById('jwt-section').style.display = 'block';
    document.getElementById('invitations-section').style.display = 'block';
}

function hideAuthenticatedSections() {
    document.getElementById('jwt-section').style.display = 'none';
    document.getElementById('invitations-section').style.display = 'none';
}

function login() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
        showResult('login-result', 'Please enter email and password', 'error');
        return;
    }

    fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentUser = data.user;
            showAuthenticatedSections();
            showResult('login-result', `Successfully logged in as ${data.user.email}`, 'success');
        } else {
            showResult('login-result', data.error || 'Login failed', 'error');
        }
    })
    .catch(error => {
        showResult('login-result', `Login error: ${error.message}`, 'error');
    });
}

function getDemoUsers() {
    fetch('/api/auth/users')
        .then(response => response.json())
        .then(users => {
            let html = '<h4>Demo Users:</h4><ul>';
            users.forEach(user => {
                html += `<li><strong>${user.email}</strong> / ${user.password}</li>`;
            });
            html += '</ul>';
            showResult('login-result', html, 'info');
        })
        .catch(error => {
            showResult('login-result', `Error: ${error.message}`, 'error');
        });
}

function generateJWT() {
    if (!currentUser) {
        showResult('jwt-result', 'Please login first', 'error');
        return;
    }

    fetch('/api/vortex/jwt', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.jwt) {
            showResult('jwt-result', `<strong>JWT Generated:</strong><br><code>${data.jwt}</code>`, 'success');
        } else {
            showResult('jwt-result', data.error || 'JWT generation failed', 'error');
        }
    })
    .catch(error => {
        showResult('jwt-result', `JWT error: ${error.message}`, 'error');
    });
}

function getInvitations() {
    if (!currentUser) {
        showResult('invitations-result', 'Please login first', 'error');
        return;
    }

    const targetType = document.getElementById('target-type').value;
    const targetValue = document.getElementById('target-value').value;

    if (!targetValue) {
        showResult('invitations-result', 'Please enter a target value', 'error');
        return;
    }

    const url = `/api/vortex/invitations?targetType=${encodeURIComponent(targetType)}&targetValue=${encodeURIComponent(targetValue)}`;

    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.invitations) {
            showResult('invitations-result',
                `<strong>Found ${data.invitations.length} invitation(s)</strong><br><pre>${JSON.stringify(data.invitations, null, 2)}</pre>`,
                'success');
        } else {
            showResult('invitations-result', data.error || 'Request failed', 'error');
        }
    })
    .catch(error => {
        showResult('invitations-result', `Request error: ${error.message}`, 'error');
    });
}

function showResult(elementId, message, type) {
    const element = document.getElementById(elementId);
    element.innerHTML = message;
    element.className = `result ${type}`;
    element.style.display = 'block';
}
