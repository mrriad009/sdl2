</div>
        </main>
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <p>&copy; <?php echo date('Y'); ?> Student Attendance Management System. Built for university projects.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="js/main.js"></script>
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
    
    <!-- Notification Container -->
    <div id="notification-container"></div>
    
    <style>
    .footer {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1rem 0;
        margin-top: auto;
    }
    
    .footer-content {
        text-align: center;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .nav-link.active {
        color: #4f46e5;
        background: rgba(79, 70, 229, 0.1);
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        color: white;
        font-weight: 600;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        background: #10b981;
    }
    
    .notification-error {
        background: #ef4444;
    }
    
    .notification-info {
        background: #3b82f6;
    }
    
    .field-error {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .form-input.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .tooltip {
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        z-index: 1000;
        pointer-events: none;
    }
    
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .modal.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    
    .modal.active .modal-content {
        transform: scale(1);
    }
    
    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
    </style>
</body>
</html>