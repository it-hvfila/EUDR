    <style>
        /* Spinner overlay styles */
        .spinner-overlay {
            position: fixed;
            display: none;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 300px;
        }

        /* Optional: เพิ่มเงาให้ spinner card */
        .spinner-card {
            border-radius: 0.5rem;
        }
    </style>
    <!-- Spinner Overlay -->
    <div id="loadingSpinner" class="spinner-overlay">
        <div class="card shadow-sm spinner-card">
            <div class="card-body text-center mt-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>กรุณารอสักครู่...</p>
            </div>
        </div>
    </div>
