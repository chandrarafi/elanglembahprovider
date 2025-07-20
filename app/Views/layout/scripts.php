<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom Scripts -->
<script>
    // Toast notification function
    function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Confirmation dialog function
    function confirmAction(title, text, confirmText, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    // Adding animation class
    $(document).ready(function() {
        // Add fade-in animation to elements with .animated class
        $('.animated').addClass('animate__animated animate__fadeIn');

        // Add bounce animation to buttons
        $('.btn-animate').addClass('animate__animated animate__pulse');
    });
</script>

<!-- WebSocket for Payment Timer -->
<script>
    class PaymentTimer {
        constructor(paymentId, bookingId, expirationTime, timerElementId, progressElementId) {
            this.paymentId = paymentId;
            this.bookingId = bookingId;
            this.expirationTime = expirationTime;
            this.timerElement = document.getElementById(timerElementId);
            this.progressElement = document.getElementById(progressElementId);
            this.socket = null;

            // Check if we have a stored time in sessionStorage
            const storedTime = sessionStorage.getItem('payment_remaining_' + this.bookingId);
            const storedExp = sessionStorage.getItem('payment_expiration_' + this.bookingId);

            // If we have a stored time and it matches our expiration, use it
            if (storedTime && storedExp && storedExp === this.expirationTime) {
                console.log('Using stored time from session:', storedTime);

                // Calculate actual remaining time based on stored expiration
                const now = new Date();
                const expDate = new Date(storedExp);
                const calculatedTime = Math.max(0, Math.floor((expDate - now) / 1000));

                // Use the minimum value to prevent time manipulation
                this.timeLeft = Math.min(parseInt(storedTime), calculatedTime);
            } else {
                // Otherwise calculate based on expiration time
                const now = new Date();
                const expDate = new Date(this.expirationTime);
                this.timeLeft = Math.max(0, Math.floor((expDate - now) / 1000));
            }

            console.log('Payment Timer initialized with time left:', this.timeLeft);

            // Update the display immediately
            this.updateDisplay();
        }

        updateDisplay() {
            if (!this.timerElement || !this.progressElement) return;

            const minutes = Math.floor(this.timeLeft / 60);
            const seconds = this.timeLeft % 60;

            // Update timer text
            this.timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            // Update progress bar (assume 10 minute total)
            const totalTime = 600; // 10 minutes
            const percentage = (this.timeLeft / totalTime) * 100;
            this.progressElement.style.width = `${percentage}%`;

            // Update appearance based on time left
            this.updateAppearance();
        }

        updateAppearance() {
            if (!this.timerElement || !this.progressElement) return;

            if (this.timeLeft <= 60) { // Last minute
                this.timerElement.classList.remove('text-red-600');
                this.timerElement.classList.add('animate__animated', 'animate__flash', 'animate__infinite', 'text-red-700', 'font-extrabold');
                this.progressElement.classList.add('bg-red-700');
            } else if (this.timeLeft <= 180) { // Last 3 minutes
                this.timerElement.classList.remove('text-red-600');
                this.timerElement.classList.add('text-red-700');
                this.progressElement.classList.add('bg-red-700');
            }
        }

        connect() {
            console.log('Connecting to timer services...');

            // Start standalone timer immediately (as backup)
            this.startStandaloneTimer();

            // Check if WebSocket is supported
            if ('WebSocket' in window) {
                // Connect to WebSocket server
                try {
                    const wsProtocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
                    const wsHost = window.location.hostname || 'localhost';
                    const wsPort = 8080; // Your WebSocket server port
                    const wsUrl = `${wsProtocol}${wsHost}:${wsPort}`;

                    console.log('Connecting to WebSocket server:', wsUrl);
                    this.socket = new WebSocket(wsUrl);

                    const self = this;

                    // Connection opened
                    this.socket.onopen = function(event) {
                        console.log('Connected to payment WebSocket server');
                        // Initialize with payment details
                        self.socket.send(JSON.stringify({
                            type: 'init',
                            paymentId: self.paymentId,
                            bookingId: self.bookingId,
                            expiration: self.expirationTime,
                            remainingSeconds: self.timeLeft
                        }));
                    };

                    // Listen for messages
                    this.socket.onmessage = function(event) {
                        try {
                            const data = JSON.parse(event.data);
                            console.log('WebSocket message received:', data);

                            if (data.type === 'countdown') {
                                // Update timer display
                                const minutes = String(data.minutes).padStart(2, '0');
                                const seconds = String(data.seconds).padStart(2, '0');

                                if (self.timerElement) {
                                    self.timerElement.textContent = `${minutes}:${seconds}`;
                                }

                                // Update progress bar
                                if (self.progressElement) {
                                    self.progressElement.style.width = `${data.percentage}%`;
                                }

                                self.timeLeft = data.timeLeft || (data.minutes * 60 + data.seconds);

                                // Store current time in sessionStorage
                                sessionStorage.setItem('payment_remaining_' + self.bookingId, self.timeLeft.toString());

                                // Update appearance
                                self.updateAppearance();
                            } else if (data.type === 'expired') {
                                // Payment expired
                                self.handleExpired();
                            }
                        } catch (e) {
                            console.error('Error parsing WebSocket message:', e);
                        }
                    };

                    // Socket closed
                    this.socket.onclose = function(event) {
                        console.log('WebSocket connection closed:', event);
                    };

                    // Handle errors
                    this.socket.onerror = function(error) {
                        console.error('WebSocket error:', error);
                    };
                } catch (error) {
                    console.error('Error creating WebSocket:', error);
                }
            } else {
                console.log('WebSocket not supported. Using standalone timer only.');
            }

            // Also start AJAX polling as a backup
            this.fallbackToAjax();
        }

        startStandaloneTimer() {
            console.log('Starting standalone timer with', this.timeLeft, 'seconds');

            // Clear any existing timers
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }

            // Update display immediately
            this.updateDisplay();

            // Start the timer
            const self = this; // Simpan referensi this untuk digunakan dalam interval
            this.interval = setInterval(function() {
                self.timeLeft--;

                // Store in session storage
                sessionStorage.setItem('payment_remaining_' + self.bookingId, self.timeLeft.toString());
                console.log('Timer tick: ' + self.timeLeft + ' seconds left');

                if (self.timeLeft <= 0) {
                    clearInterval(self.interval);
                    self.interval = null;
                    self.handleExpired();
                } else {
                    self.updateDisplay();
                }
            }, 1000);

            console.log('Timer interval started:', this.interval ? 'Yes' : 'No');
        }

        fallbackToAjax() {
            console.log('Starting AJAX polling for payment status...');

            // Clear any existing AJAX polling
            if (this.ajaxInterval) {
                clearInterval(this.ajaxInterval);
                this.ajaxInterval = null;
            }

            // Use AJAX polling instead
            const self = this;
            const checkExpiration = function() {
                console.log('Polling server for payment status...');
                $.ajax({
                    url: baseUrl + 'booking/checkPaymentExpiration/' + self.bookingId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log('AJAX poll response:', data);
                        if (data.status) {
                            if (data.expired) {
                                // Payment expired
                                console.log('Server reported payment expired');
                                self.handleExpired();

                                // Stop polling
                                if (self.ajaxInterval) {
                                    clearInterval(self.ajaxInterval);
                                    self.ajaxInterval = null;
                                }
                            } else {
                                // Update timer with remaining time
                                const serverTimeLeft = parseInt(data.remaining);
                                console.log('Server reported remaining time:', serverTimeLeft);

                                // Check if booking status has changed
                                if (data.booking_status && data.booking_status === 'cancelled') {
                                    console.log('Booking status is cancelled');
                                    self.handleExpired();
                                    return;
                                }

                                // Only use server time if it's different by more than 5 seconds
                                // This prevents small variations from causing jumps in the timer
                                if (Math.abs(serverTimeLeft - self.timeLeft) > 5) {
                                    console.log('Updating timer from server time');
                                    self.timeLeft = serverTimeLeft;

                                    // Store in session storage
                                    sessionStorage.setItem('payment_remaining_' + self.bookingId, self.timeLeft.toString());

                                    // Update display
                                    self.updateDisplay();
                                }

                                // Adjust polling frequency based on remaining time
                                if (serverTimeLeft < 60) { // Less than 1 minute
                                    if (self.ajaxInterval) {
                                        clearInterval(self.ajaxInterval);
                                        self.ajaxInterval = setInterval(checkExpiration, 2000); // Poll every 2 seconds
                                        console.log('Increased polling frequency to every 2 seconds');
                                    }
                                } else if (serverTimeLeft < 180) { // Less than 3 minutes
                                    if (self.ajaxInterval) {
                                        clearInterval(self.ajaxInterval);
                                        self.ajaxInterval = setInterval(checkExpiration, 5000); // Poll every 5 seconds
                                        console.log('Increased polling frequency to every 5 seconds');
                                    }
                                }
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking payment expiration:', error);
                    }
                });
            };

            // Check immediately
            checkExpiration();

            // Then check every 10 seconds by default
            this.ajaxInterval = setInterval(checkExpiration, 10000);
            console.log('AJAX polling started with interval ID:', this.ajaxInterval);
        }

        handleExpired() {
            console.log('Payment expired, handling expiration');

            // Clear timers
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
                console.log('Cleared interval timer');
            }

            if (this.ajaxInterval) {
                clearInterval(this.ajaxInterval);
                this.ajaxInterval = null;
                console.log('Cleared AJAX interval');
            }

            // Clear session storage
            sessionStorage.removeItem('payment_remaining_' + this.bookingId);
            sessionStorage.removeItem('payment_expiration_' + this.bookingId);
            console.log('Cleared session storage');

            // Update UI
            if (this.timerElement) {
                this.timerElement.textContent = '00:00';
                console.log('Updated timer display to 00:00');
            }

            if (this.progressElement) {
                this.progressElement.style.width = '0%';
                console.log('Updated progress bar to 0%');
            }

            // First notify server to update the status to cancelled
            console.log('Notifying server about expiration');
            fetch(baseUrl + 'booking/checkPaymentExpiration/' + this.bookingId)
                .then(response => response.json())
                .then(data => {
                    console.log('Server response for expiration:', data);

                    // Show alert after server update
                    console.log('Showing payment expired alert');
                    Swal.fire({
                        title: 'Waktu Pembayaran Habis',
                        text: 'Pemesanan Anda telah dibatalkan karena waktu pembayaran telah habis.',
                        icon: 'error',
                        confirmButtonText: 'Kembali ke Daftar Pemesanan',
                    }).then(() => {
                        console.log('Redirecting to booking history');
                        window.location.href = baseUrl + 'booking/history';
                    });
                })
                .catch(error => {
                    console.error('Error notifying server:', error);

                    // Still show alert even if server notification fails
                    Swal.fire({
                        title: 'Waktu Pembayaran Habis',
                        text: 'Pemesanan Anda telah dibatalkan karena waktu pembayaran telah habis.',
                        icon: 'error',
                        confirmButtonText: 'Kembali ke Daftar Pemesanan',
                    }).then(() => {
                        window.location.href = baseUrl + 'booking/history';
                    });
                });
        }

        disconnect() {
            if (this.socket && this.socket.readyState === WebSocket.OPEN) {
                this.socket.close();
            }

            if (this.interval) {
                clearInterval(this.interval);
            }

            if (this.ajaxInterval) {
                clearInterval(this.ajaxInterval);
            }
        }
    }

    // Set base URL for AJAX requests
    const baseUrl = '<?= base_url() ?>';
</script>