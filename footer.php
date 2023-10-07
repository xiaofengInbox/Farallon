<?php global $farallonSetting; ?>
<footer class="site--footer">
    <div class="site--footer__content">
        <nav>
            <?php wp_nav_menu(array('theme_location' => 'farallon_footer', 'menu_class' => 'footer--nav', 'container' => 'ul', 'fallback_cb' => 'link_to_menu_editor')); ?>
        </nav>
        <div class="copyright">
            <?php if ($farallonSetting->get_setting('copyright')) : ?>
                <?php echo $farallonSetting->get_setting('copyright'); ?>
            <?php else : ?>
                © <?php echo date('Y'); ?> . All rights reserved.
            <?php endif; ?>
            <svg class="icon icon--copryrights" viewBox="0 0 1040 1024" width="16" height="16">
                <path d="M717.056236 383.936299l-51.226708 0c-28.2893 0-51.226708 22.936385-51.226708 51.225685l0 128.062678c0 28.2893 22.937408 51.225685 51.226708 51.225685l51.226708 0c28.2893 0 51.225685-22.936385 51.225685-51.225685L768.281921 435.161984C768.281921 406.872684 745.345536 383.936299 717.056236 383.936299zM717.056236 537.611308c0 14.158465-11.480472 25.612331-25.613354 25.612331-14.132882 0-25.612331-11.453866-25.612331-25.612331l0-76.835969c0-14.158465 11.480472-25.613354 25.612331-25.613354 14.133905 0 25.613354 11.453866 25.613354 25.613354L717.056236 537.611308zM1013.977739 426.580538 859.776751 165.30079c-8.888438-15.063067-22.294772-25.975605-37.57171-32.080649-32.708959-34.856879-79.187527-56.638975-130.762159-56.638975L332.862064 76.581166c-51.575656 0-98.0532 21.782096-130.761136 56.639998-15.276938 6.105045-28.683273 17.017582-37.572734 32.079626L10.327206 426.580538c-21.26021 36.069497-8.655124 82.217537 28.239158 103.028515l115.00836 64.967664 0 199.163015c0 99.024318 80.264045 153.678078 179.287339 153.678078l358.580818 0c99.024318 0 179.290409-80.266092 179.290409-179.290409L870.733291 594.575694l115.00836-64.966641C1022.63184 508.798075 1035.238972 462.650035 1013.977739 426.580538zM153.574724 536.518417l-67.058278-37.875632c-24.589025-13.907755-33.019021-44.647873-18.809391-68.684312l85.86767-145.555074L153.574724 536.518417zM646.620024 127.807874c0 56.5786-60.205197 102.45137-134.467551 102.45137-74.261331 0-134.466528-45.873794-134.466528-102.45137L646.620024 127.807874zM819.507606 742.515071c0 84.893482-68.810179 153.677055-153.678078 153.677055L358.475418 896.192126c-84.8679 0-153.675008-68.783573-153.675008-153.677055l0-461.030142c0-76.150354 55.402821-139.361001 128.093377-151.545508 1.332345 83.883479 81.06734 151.545508 179.258687 151.545508 98.19237 0 177.926342-67.662029 179.25971-151.545508 72.690556 12.183484 128.096447 75.394131 128.096447 151.545508L819.508629 742.515071zM937.791569 498.642784l-67.058278 37.875632 0-252.111948 85.86767 145.552004C970.807521 453.995935 962.377524 484.736053 937.791569 498.642784z" p-id="4007"></path>
            </svg>
        </div>
    </div>
</footer>
</div>
</body>
<?php wp_footer(); ?>

</html>