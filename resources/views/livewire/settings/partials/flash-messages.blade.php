@if(session()->has('message'))
<div class="position-fixed" style="top: 20px; right: 20px; width: 40%; min-width: 250px; max-width: 450px; z-index: 9999;">
    <div class="flash-message alert alert-{{ session('message_type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show w-100"
        role="alert"
        x-data="{ show: true, progress: 100 }"
        x-init="setTimeout(() => show = false, 3000); 
                     const interval = setInterval(() => { progress = progress - (100/30); }, 100);
                     setTimeout(() => clearInterval(interval), 3000);"
        x-show="show"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100 transform scale-100 translate-x-0"
        x-transition:leave-end="opacity-0 transform scale-90 translate-x-full">

        <!-- Icon based on message type -->
        <div class="d-flex align-items-center">
            <div class="mr-3">
                @if(session('message_type') === 'success')
                <i class="fas fa-check-circle fa-2x text-success"></i>
                @else
                <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                @endif
            </div>
            <div class="flex-grow-1" style="word-break: break-word;">
                <strong>{{ session('message_type') === 'success' ? 'Success!' : 'Error!' }}</strong>
                <p class="mb-0 small">{{ session('message') }}</p>
            </div>
            <button type="button" class="close ml-3" @click="show = false" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Progress bar for auto-dismiss -->
        <div class="progress mt-2" style="height: 3px;">
            <div class="progress-bar bg-{{ session('message_type') === 'success' ? 'success' : 'danger' }}"
                role="progressbar"
                :style="'width: ' + progress + '%'"
                :aria-valuenow="progress"
                aria-valuemin="0"
                aria-valuemax="100">
            </div>
        </div>
    </div>
</div>
@endif

<!-- Alternative Floating Toast Style (uncomment to use instead) -->
{{--
@if(session()->has('message'))
    <div class="position-fixed" style="top: 20px; right: 20px; width: 30%; min-width: 250px; max-width: 350px; z-index: 9999;">
        <div class="toast show w-100" role="alert" aria-live="assertive" aria-atomic="true" 
             x-data="{ show: true, progress: 100 }" 
             x-init="setTimeout(() => show = false, 3000); 
                     const interval = setInterval(() => { progress = progress - (100/30); }, 100);
                     setTimeout(() => clearInterval(interval), 3000);"
             x-show="show"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full">
            
            <div class="toast-header bg-{{ session('message_type') === 'success' ? 'success' : 'danger' }} text-white">
<strong class="mr-auto">
    @if(session('message_type') === 'success')
    <i class="fas fa-check-circle mr-2"></i> Success
    @else
    <i class="fas fa-exclamation-circle mr-2"></i> Error
    @endif
</strong>
<button type="button" class="ml-2 mb-1 close text-white" @click="show = false">
    <span>&times;</span>
</button>
</div>
<div class="toast-body small">
    {{ session('message') }}
</div>
<div class="progress" style="height: 3px;">
    <div class="progress-bar bg-{{ session('message_type') === 'success' ? 'success' : 'danger' }}"
        role="progressbar"
        :style="'width: ' + progress + '%'">
    </div>
</div>
</div>
</div>
@endif
--}}

<style>
    /* Main container styling */
    .position-fixed {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 30%;
        min-width: 280px;
        max-width: 380px;
        z-index: 9999;
        filter: drop-shadow(0 10px 15px -3px rgba(0, 0, 0, 0.1));
    }

    /* Flash message base styling */
    .flash-message {
        border: none;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        margin-bottom: 1rem;
        padding: 1.25rem;
        animation: slideInRight 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Success state - modern and fresh */
    .flash-message.alert-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-left: 4px solid #34d399;
        color: white;
    }

    /* Error state - bold but not aggressive */
    .flash-message.alert-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-left: 4px solid #f87171;
        color: white;
    }

    /* Icon container */
    .flash-message .mr-3 {
        margin-right: 1rem;
        display: flex;
        align-items: center;
    }

    /* Icon styling */
    .flash-message .fa-2x {
        font-size: 1.75rem;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    /* Success icon color - adjusted for better contrast */
    .flash-message.alert-success .fa-check-circle {
        color: rgba(255, 255, 255, 0.95) !important;
    }

    /* Error icon color */
    .flash-message.alert-danger .fa-exclamation-circle {
        color: rgba(255, 255, 255, 0.95) !important;
    }

    /* Title styling */
    .flash-message strong {
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        text-transform: uppercase;
        display: block;
        margin-bottom: 0.25rem;
        color: rgba(255, 255, 255, 0.95);
    }

    /* Message text styling */
    .flash-message p {
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.4;
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.9);
        word-break: break-word;
        hyphens: auto;
    }

    /* Close button styling */
    .flash-message .close {
        opacity: 0.7;
        transition: all 0.2s ease;
        color: white;
        text-shadow: none;
        font-size: 1.5rem;
        font-weight: 300;
        line-height: 1;
        padding: 0;
        margin-left: 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .flash-message .close:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .flash-message .close:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    .flash-message .close span {
        display: block;
        line-height: 1;
        margin-top: -2px;
    }

    /* Progress bar container */
    .flash-message .progress {
        background-color: rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        overflow: hidden;
        height: 4px;
        margin-top: 1rem;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Progress bar fill */
    .flash-message .progress-bar {
        transition: width 0.1s linear;
        border-radius: 999px;
    }

    .flash-message.alert-success .progress-bar {
        background-color: rgba(255, 255, 255, 0.9);
    }

    .flash-message.alert-danger .progress-bar {
        background-color: rgba(255, 255, 255, 0.9);
    }

    /* Toast style (alternative) */
    .toast {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        animation: slideInRight 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        background: white;
    }

    .toast-header {
        padding: 0.75rem 1rem;
        border-bottom: none;
        font-weight: 600;
    }

    .toast-header.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white;
    }

    .toast-header.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white;
    }

    .toast-body {
        padding: 1rem;
        font-size: 0.875rem;
        line-height: 1.5;
        color: #1f2937;
        background: white;
    }

    .toast .progress {
        height: 4px;
        background-color: #e5e7eb;
        border-radius: 0;
    }

    .toast .progress-bar {
        transition: width 0.1s linear;
    }

    .toast-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
        font-size: 1.25rem;
        font-weight: 300;
        background: rgba(255, 255, 255, 0.2);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .toast-header .close:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.3);
    }

    /* Animations */
    @keyframes slideInRight {
        0% {
            transform: translate3d(100%, 0, 0) scale(0.8);
            opacity: 0;
        }

        50% {
            transform: translate3d(-10%, 0, 0) scale(1.02);
        }

        75% {
            transform: translate3d(5%, 0, 0) scale(0.99);
        }

        100% {
            transform: translate3d(0, 0, 0) scale(1);
            opacity: 1;
        }
    }

    /* Hover effect - pause auto-dismiss */
    .flash-message:hover .progress-bar,
    .toast:hover .progress-bar {
        animation-play-state: paused;
    }

    /* Additional polish for the flex container */
    .d-flex {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .position-fixed {
            width: calc(100% - 32px) !important;
            min-width: auto;
            max-width: none;
            right: 16px;
            left: 16px;
            top: 16px;
        }

        .flash-message {
            padding: 1rem;
        }

        .flash-message strong {
            font-size: 0.9375rem;
        }

        .flash-message p {
            font-size: 0.8125rem;
        }

        .flash-message .fa-2x {
            font-size: 1.5rem;
        }
    }

    /* Small phones */
    @media (max-width: 480px) {
        .flash-message .d-flex {
            gap: 0.5rem;
        }

        .flash-message .mr-3 {
            margin-right: 0.5rem;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .flash-message.alert-success {
            background: #059669;
            border: 2px solid #047857;
        }

        .flash-message.alert-danger {
            background: #dc2626;
            border: 2px solid #b91c1c;
        }

        .flash-message .progress {
            background-color: rgba(0, 0, 0, 0.3);
        }
    }

    /* Reduced motion preference */
    @media (prefers-reduced-motion: reduce) {

        .flash-message,
        .toast {
            animation: none;
        }

        .flash-message .progress-bar {
            transition: none;
        }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized for flash messages');
    });
</script>