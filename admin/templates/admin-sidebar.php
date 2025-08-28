<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse admin-sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'works.php' ? 'active' : ''; ?>" href="works.php">
                    <i class="fas fa-briefcase"></i>Works
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'blog.php' ? 'active' : ''; ?>" href="blog.php">
                    <i class="fas fa-blog"></i>Blog Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope"></i>Messages
                    <?php
                    $messageModel = new MessageModel();
                    $unreadCount = $messageModel->getUnreadCount();
                    if ($unreadCount > 0):
                    ?>
                        <span class="badge bg-danger ms-1"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'profile-setup.php' ? 'active' : ''; ?>" href="profile-setup.php">
                    <i class="fas fa-user-edit"></i>Profile Setup
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'skills.php' ? 'active' : ''; ?>" href="skills.php">
                    <i class="fas fa-cogs"></i>Skills
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                    <i class="fas fa-tags"></i>Categories
                </a>
            </li>
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users"></i>Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog"></i>Settings
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>