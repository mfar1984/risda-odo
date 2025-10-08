<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Left side - Copyright -->
            <div class="footer-left">
                <span class="footer-copyright">
                    © 1973 - {{ date('Y') }} Hak cipta terpelihara - Sistem Pengurusan Jejak Aset & Rekod Automatif
                </span>
            </div>

            <!-- Right side - Links -->
            <div class="footer-right">
                <div class="footer-links" x-data="{
                    disclaimerModal: false,
                    privacyModal: false,
                    termsModal: false,
                    sitemapModal: false
                }">
                    <a href="#" class="footer-link" @click.prevent="disclaimerModal = true">Penafian</a>
                    <span class="footer-separator">/</span>
                    <a href="#" class="footer-link" @click.prevent="privacyModal = true">Privasi</a>
                    <span class="footer-separator">/</span>
                    <a href="#" class="footer-link" @click.prevent="termsModal = true">Terma Penggunaan</a>
                    <span class="footer-separator">/</span>
                    <a href="#" class="footer-link" @click.prevent="sitemapModal = true">Peta Laman</a>

                    {{-- Inject modals --}}
                    @include('help.partials.policy-disclaimer-modal')
                    @include('help.partials.policy-privacy-modal')
                    @include('help.partials.policy-terms-modal')
                    @include('help.partials.policy-sitemap-modal')
                </div>
            </div>
        </div>
    </div>
</footer>
