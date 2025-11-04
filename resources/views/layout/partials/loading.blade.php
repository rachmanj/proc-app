<!-- Global Loading Overlay -->
<div id="globalLoadingOverlay" class="loading-overlay" style="display: none;">
    <div class="spinner-container">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2 text-white">Loading...</p>
    </div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-container {
    text-align: center;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

.btn-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.65;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
    to { transform: rotate(360deg); }
}
</style>

