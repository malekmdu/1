<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; <?php echo date('Y'); ?> <?php echo getSiteSetting('copyright_text', 'GeoPortfolio. All rights reserved.'); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="social-links">
                    <?php if (getSiteSetting('twitter_url')): ?>
                        <a href="<?php echo getSiteSetting('twitter_url'); ?>" class="text-white me-3" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (getSiteSetting('github_url')): ?>
                        <a href="<?php echo getSiteSetting('github_url'); ?>" class="text-white me-3" target="_blank">
                            <i class="fab fa-github"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (getSiteSetting('linkedin_url')): ?>
                        <a href="<?php echo getSiteSetting('linkedin_url'); ?>" class="text-white me-3" target="_blank">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>