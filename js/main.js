// Main JavaScript for Student Attendance System

// DOM Ready
document.addEventListener(\'DOMContentLoaded\', function() {
    initializeApp();
});

function initializeApp() {
    // Initialize all components
    initializeSearch();
    initializeFilters();
    initializeForms();
    initializeCharts();
    initializeModals();
    initializeTooltips();
}

// Search Functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll(\".search-input\");
    
    searchInputs.forEach(input => {
        input.addEventListener(\'input\', debounce(function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const targetTable = e.target.getAttribute(\'data-target\');
            
            if (targetTable) {
                filterTable(targetTable, searchTerm);
            }
        }, 300));
    });
}

// Table Filtering
function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll(\'tbody tr\');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = \'\';
        } else {
            row.style.display = \'none\';
        }
    });
}

// Filter Functionality
function initializeFilters() {
    const filterSelects = document.querySelectorAll(\".filter-select\");
    
    filterSelects.forEach(select => {
        select.addEventListener(\'change\', function(e) {
            const filterValue = e.target.value;
            const targetTable = e.target.getAttribute(\'data-target\');
            const filterColumn = e.target.getAttribute(\'data-column\');
            
            if (targetTable && filterColumn) {
                filterTableByColumn(targetTable, filterColumn, filterValue);
            }
        });
    });
}

function filterTableByColumn(tableId, columnIndex, filterValue) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll(\'tbody tr\');
    
    rows.forEach(row => {
        const cell = row.cells[columnIndex];
        if (!cell) return;
        
        const cellText = cell.textContent.trim();
        
        if (filterValue === \'\' || cellText === filterValue) {
            row.style.display = \'\';
        } else {
            row.style.display = \'none\';
        }
    });
}

// Form Enhancements
function initializeForms() {
    // Form validation
    const forms = document.querySelectorAll(\'form\');
    forms.forEach(form => {
        form.addEventListener(\'submit\', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            } else {
                showLoadingState(form);
            }
        });
    });
    
    // Select all functionality
    const selectAllCheckboxes = document.querySelectorAll(\".select-all\");
    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener(\'change\', function(e) {
            const targetCheckboxes = document.querySelectorAll(e.target.getAttribute(\'data-target\'));
            targetCheckboxes.forEach(cb => {
                cb.checked = e.target.checked;
            });
        });
    });
    
    // Real-time validation
    const inputs = document.querySelectorAll(\".form-input\");
    inputs.forEach(input => {
        input.addEventListener(\'blur\', function(e) {
            validateField(e.target);
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll(\'[required]\');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    let isValid = true;
    let errorMessage = \'\';
    
    // Remove existing error
    removeFieldError(field);
    
    // Required validation
    if (field.hasAttribute(\'required\') && !value) {
        isValid = false;
        errorMessage = \'This field is required\';
    }
    
    // Email validation
    if (fieldType === \'email\' && value) {
        const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = \'Please enter a valid email address\';
        }
    }
    
    // Number validation
    if (fieldType === \'number\' && value) {
        if (isNaN(value) || value < 0) {
            isValid = false;
            errorMessage = \'Please enter a valid number\';
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add(\'error\');
    const errorDiv = document.createElement(\'div\');
    errorDiv.className = \'field-error\';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function removeFieldError(field) {
    field.classList.remove(\'error\');
    const existingError = field.parentNode.querySelector(\'.field-error\');
    if (existingError) {
        existingError.remove();
    }
}

function showLoadingState(form) {
    const submitBtn = form.querySelector(\'button[type="submit"]\');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = \'<span class="loading-spinner"></span> Processing...\';
    }
}

// Charts (using Chart.js if available)
function initializeCharts() {
    // Attendance Overview Chart
    const attendanceChartCanvas = document.getElementById(\'attendanceChart\');
    if (attendanceChartCanvas && typeof Chart !== \'undefined\') {
        createAttendanceChart(attendanceChartCanvas);
    }
    
    // Department Comparison Chart
    const departmentChartCanvas = document.getElementById(\'departmentChart\');
    if (departmentChartCanvas && typeof Chart !== \'undefined\') {
        createDepartmentChart(departmentChartCanvas);
    }
}

function createAttendanceChart(canvas) {
    const ctx = canvas.getContext(\'2d\');
    
    // Sample data - replace with actual data from PHP
    const data = {
        labels: [\'Mon\', \'Tue\', \'Wed\', \'Thu\', \'Fri\', \'Sat\'],
        datasets: [{
            label: \'Attendance Rate\',
            data: [85, 92, 78, 88, 95, 82],
            borderColor: \'#4f46e5\',
            backgroundColor: \'rgba(79, 70, 229, 0.1)\',
            tension: 0.4
        }]
    };
    
    new Chart(ctx, {
        type: \'line\',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function createDepartmentChart(canvas) {
    const ctx = canvas.getContext(\'2d\');
    
    const data = {
        labels: [\'Computer Science\', \'Electrical Engineering\', \'Mechanical Engineering\'],
        datasets: [{
            data: [87, 92, 78],
            backgroundColor: [\'#4f46e5\', \'#10b981\', \'#f59e0b\']
        }]
    };
    
    new Chart(ctx, {
        type: \'doughnut\',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: \'bottom\'
                }
            }
        }
    });
}

// Modal Functionality
function initializeModals() {
    const modalTriggers = document.querySelectorAll(\'[data-modal]\');
    const modals = document.querySelectorAll(\".modal\");
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener(\'click\', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute(\'data-modal\');
            openModal(modalId);
        });
    });
    
    modals.forEach(modal => {
        const closeBtn = modal.querySelector(\".modal-close\");
        if (closeBtn) {
            closeBtn.addEventListener(\'click\', function() {
                closeModal(modal.id);
            });
        }
        
        modal.addEventListener(\'click\', function(e) {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add(\'active\');
        document.body.style.overflow = \'hidden\';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove(\'active\');
        document.body.style.overflow = \'\';
    }
}

// Tooltip Functionality
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll(\'[data-tooltip]\');
    
    tooltipElements.forEach(element => {
        element.addEventListener(\'mouseenter\', showTooltip);
        element.addEventListener(\'mouseleave\', hideTooltip);
    });
}

function showTooltip(e) {
    const text = e.target.getAttribute(\'data-tooltip\');
    const tooltip = document.createElement(\'div\');
    tooltip.className = \'tooltip\';
    tooltip.textContent = text;
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + \'px\';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + \'px\';
}

function hideTooltip() {
    const tooltip = document.querySelector(\".tooltip\");
    if (tooltip) {
        tooltip.remove();
    }
}

// Export Functionality
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll(\'tr\');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll(\'td, th\');
        const rowData = Array.from(cols).map(col => {
            return \'"\' + col.textContent.replace(/"/g, \'""\') + \'"\';
        });
        csv.push(rowData.join(\'\',\'));
    });
    
    downloadCSV(csv.join(\'\\n\'), filename);
}

function downloadCSV(content, filename) {
    const blob = new Blob([content], { type: \'text/csv\' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement(\'a\');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = \'info\') {
    const notification = document.createElement(\'div\');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add(\'show\');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove(\'show\');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Progress Bar Animation
function animateProgressBar(element, targetPercentage) {
    let currentPercentage = 0;
    const increment = targetPercentage / 50;
    
    const timer = setInterval(() => {
        currentPercentage += increment;
        if (currentPercentage >= targetPercentage) {
            currentPercentage = targetPercentage;
            clearInterval(timer);
        }
        element.style.width = currentPercentage + \'%\';
    }, 20);
}

// Auto-refresh functionality
function startAutoRefresh(interval = 30000) {
    setInterval(() => {
        const refreshElements = document.querySelectorAll(\'[data-auto-refresh]\');
        refreshElements.forEach(element => {
            refreshElement(element);
        });
    }, interval);
}

function refreshElement(element) {
    const url = element.getAttribute(\'data-refresh-url\');
    if (url) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                element.innerHTML = html;
            })
            .catch(error => {
                console.error(\'Refresh failed:\', error);
            });
    }
}

